<?php

namespace App\Http\Controllers;

use App\Models\Color;
use App\Models\Order;
use App\Models\OrderCustomisation;
use App\Models\Product;
use App\Models\CompanyDetails;
use App\Models\OrderDetails;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Omnipay\Omnipay;

class CheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        $sessionCart = $request->session()->get('cart', []);


        if (!is_array($sessionCart) || empty($sessionCart)) {
            // **TEMPORARILY COMMENT OUT THE REDIRECT**
            // return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
            
            // **Instead, define the variables to see the checkout page with an empty list.**
            $cartItems = [];
            $total = 0;
            return view('frontend.checkout', [
                'cartItems' => $cartItems,
                'total' => $total,
                'currency' => '£',
            ]);
        }

        $productIds = collect($sessionCart)->pluck('product_id')->unique()->filter()->values()->all();
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        // dd($products);


        $cartItems = [];
        $total = 0;

        foreach ($sessionCart as $key => $item) {

            $pid = (int)($item['product_id'] ?? 0);
            $product = $products->get($pid);

            $price = $product ? ($product->price ?? 0) : 0;
            $quantity = isset($item['quantity']) ? (int)$item['quantity'] : 1;
            $subtotal = $price * $quantity;
            $total += $subtotal;

            $frontImage = null;
            if ($product) {
                $colorId = $item['color_id'] ?? null;
            

                $frontImageRow = $product->images()
                    ->where('image_type', 'front')
                    ->when($colorId, fn($q) => $q->where('color_id', $colorId))
                    ->latest()
                    ->first();
                $frontImage = $frontImageRow->image_path ?? null;
            }
                $colorName = Color::where('id', $item['color_id'])->first()->color;

            $cartItems[] = [
                'key' => $key,
                'product_id' => $pid,
                'product' => $product,
                'product_name' => $item['product_name'] ?? ($product->name ?? 'Unknown Product'),
                'product_image' => $frontImage ?? ($item['product_image'] ?? $product->feature_image ?? null),
                'ean' => $item['ean'] ?? null,
                'size_id' => $item['size_id'] ?? null,
                'color_id' => $item['color_id'] ?? null,
                'colorName' => $colorName,
                'quantity' => $quantity,
                'price' => $price,
                'subtotal' => $subtotal,
                'customization' => $item['customization'] ?? [],
            ];
        }

        return view('frontend.checkout', [
            'cartItems' => $cartItems,
            'total' => $total,
            'currency' => '£',
        ]);
    }


    public function processOrder(Request $request)
    {
        $validator = $this->validateOrder($request);
        
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $totals = $this->calculateTotals($request);
        
        if ($request->payment_method === 'cash_on_delivery') {
            return $this->processCashOnDelivery($request, $totals);
        } elseif ($request->payment_method === 'bank_transfer') {
            return $this->processBankTransfer($request, $totals);
        } elseif ($request->payment_method === 'paypal') {
            return $this->initiatePayPalPayment($request, $totals);
        } elseif ($request->payment_method === 'stripe') {
            return $this->processStripe($request, $totals);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invalid payment method'
            ], 400);
        }
    }

    protected function processCashOnDelivery(Request $request, $totals)
    {
        DB::beginTransaction();
        
        try {
            $order = $this->createOrder($request, $totals);
            
            $this->createOrderDetails($request, $order);
            
            $this->handleCustomizations($request, $order);
            
            DB::commit();
            $request->session()->forget('cart');
            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'redirect_url' => route('order.success', ['order_id' => $order->id])
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error processing order: ' . $e->getMessage()
            ], 500);
        }
    }

    protected function validateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shipping_method' => 'required|in:0,1',
            'payment_method' => 'required|in:cash_on_delivery,bank_transfer,paypal,stripe',
            'cart_items' => 'required|array',
        ]);

        // Only require shipping address for "Ship" method
        if ($request->shipping_method === '0') {
            $validator->addRules([
                'first_name' => 'required|string|max:64',
                'email' => 'required|email',
                'phone' => 'required|string|max:15',
                'address_first_line' => 'required|string|max:128',
                'city' => 'required|string|max:128',
                'postcode' => 'required|string|max:10',
            ]);
        }

        // Always require billing address for pickup OR when different billing is selected
        if ($request->shipping_method === '1' || $request->is_billing_same == '0') {
            $validator->addRules([
                'billing_first_name' => 'required|string|max:64',
                'billing_email' => 'required|email',
                'billing_phone' => 'required|string|max:15',
                'billing_address_first_line' => 'required|string|max:128',
                'billing_city' => 'required|string|max:128',
                'billing_postcode' => 'required|string|max:10',
            ]);
        }

        return $validator;
    }

    protected function calculateTotals(Request $request)
    {
        $subtotal = 0;
        
        foreach ($request->cart_items as $item) {
            $subtotal += $item['subtotal'];
        }

        $shippingCharge = $this->calculateShipping($request->shipping_method, $subtotal);
        $vatPercent = CompanyDetails::first()->vat_percent ?? 20;
        $vatAmount = (($subtotal + $shippingCharge) * $vatPercent) / 100;
        $totalAmount = $subtotal + $shippingCharge + $vatAmount;

        return [
            'subtotal' => $subtotal,
            'shipping_charge' => $shippingCharge,
            'vat_percent' => $vatPercent,
            'vat_amount' => $vatAmount,
            'total_amount' => $totalAmount
        ];
    }

    protected function calculateShipping($shippingMethod, $subtotal)
    {
        if ($shippingMethod === '0') {
            if ($subtotal < 50) return 5.99;
            if ($subtotal < 100) return 3.99;
            return 0;
        }
        return 0;
    }

    protected function createOrder(Request $request, $totals)
    {
        $order = new Order();
        $order->order_number = 'ORD-' . time() . rand(1000, 9999);
        $order->user_id = auth()->id();
        $order->shipping_method = $request->shipping_method;
        $order->full_name = $request->first_name;
        $order->company_name = $request->company_name;
        $order->email = $request->email;
        $order->phone = $request->phone;
        $order->address_first_line = $request->address_first_line;
        $order->address_second_line = $request->address_second_line;
        $order->address_third_line = $request->address_third_line;
        $order->city = $request->city;
        $order->postcode = $request->postcode;
        $order->order_notes = $request->order_notes;
        
        if ($request->is_billing_same == '1') {
            $order->billing_full_name = $request->first_name;
            $order->billing_company_name = $request->company_name;
            $order->billing_email = $request->email;
            $order->billing_phone = $request->phone;
            $order->billing_address_first_line = $request->address_first_line;
            $order->billing_address_second_line = $request->address_second_line;
            $order->billing_address_third_line = $request->address_third_line;
            $order->billing_city = $request->city;
            $order->billing_postcode = $request->postcode;
        } else {
            $order->billing_full_name = $request->billing_first_name;
            $order->billing_company_name = $request->billing_company_name;
            $order->billing_email = $request->billing_email ?? $request->email;
            $order->billing_phone = $request->billing_phone;
            $order->billing_address_first_line = $request->billing_address_first_line;
            $order->billing_address_second_line = $request->billing_address_second_line;
            $order->billing_address_third_line = $request->billing_address_third_line;
            $order->billing_city = $request->billing_city;
            $order->billing_postcode = $request->billing_postcode;
        }

        $order->payment_method = $request->payment_method;
        $order->subtotal = $totals['subtotal'];
        $order->shipping_charge = $totals['shipping_charge'];
        $order->vat_percent = $totals['vat_percent'];
        $order->vat_amount = $totals['vat_amount'];
        $order->total_amount = $totals['total_amount'];
        $order->status = 'pending';
        
        $order->save();
        
        return $order;
    }

    protected function createOrderDetails(Request $request, $order)
    {
        foreach ($request->cart_items as $item) {
            $orderDetail = new OrderDetails();
            $orderDetail->order_id = $order->id;
            $orderDetail->product_id = $item['product_id'] ?? null;
            $orderDetail->quantity = $item['quantity'];
            $orderDetail->price = $item['price'];
            $orderDetail->subtotal = $item['subtotal'];
            $orderDetail->ean = $item['ean'] ?? null;
            $orderDetail->size_id = $item['size_id'] ?? null;
            $orderDetail->color_id = $item['color_id'] ?? null;
            $orderDetail->save();
        }
    }

    protected function handleCustomizations(Request $request, $order)
    {
        foreach ($request->cart_items as $itemIndex => $item) {
            if (!empty($item['customization'])) {
                $orderDetail = OrderDetails::where('order_id', $order->id)
                    ->skip($itemIndex)
                    ->first();

                if (!$orderDetail) continue;

                foreach ($item['customization'] as $customization) {
                    $orderCustomization = new OrderCustomisation();
                    $orderCustomization->order_details_id = $orderDetail->id;
                    $orderCustomization->product_id = $item['product_id'] ?? null;
                    $orderCustomization->size_id = $item['size_id'] ?? null;
                    $orderCustomization->color_id = $item['color_id'] ?? null;
                    $orderCustomization->customization_type = $customization['type'] ?? 'text';
                    $orderCustomization->method = $customization['method'] ?? '';
                    $orderCustomization->position = $customization['position'] ?? '';
                    $orderCustomization->z_index = $customization['zIndex'] ?? null;
                    $orderCustomization->layer_id = $customization['layerId'] ?? null;

                    $orderCustomization->data = json_encode($customization['data'] ?? []);

                    $orderCustomization->save();
                }
            }
        }
    }

    public function orderSuccess($order_id)
    {
        $order = Order::with(['orderDetails.orderCustomisations'])->findOrFail($order_id);
        return view('frontend.order.success', compact('order'));
    }

    public function orderCancel()
    {
        return view('frontend.order.cancel');
    }

    public function showInvoice(Order $order)
    {
        $order->load('orderDetails');

        $html = view('frontend.order.invoice', compact('order'))->render();

        return response($html)
            ->header('Content-Type', 'text/html');
    }

    protected function initiatePayPalPayment($formData, $totals)
    {
        $payPalCredentials = $this->getPayPalCredentials();

        if (!$payPalCredentials) {
            return response()->json(['error' => 'PayPal credentials not found'], 404);
        }

        $gateway = Omnipay::create('PayPal_Rest');
        $gateway->setClientId($payPalCredentials->clientid);
        $gateway->setSecret($payPalCredentials->secretid);
        $gateway->setTestMode($payPalCredentials->mode);

        $netAmount = 100;
        try {
            $response = $gateway->purchase([
                'amount' => number_format($netAmount, 2, '.', ''),
                'currency' => 'GBP',
                'returnUrl' => route('payment.success'),
                'cancelUrl' => route('payment.cancel')
            ])->send();

            if ($response->isRedirect()) {
                session()->put('order_data', $formData);
                return response()->json(['redirectUrl' => $response->getRedirectUrl()]);
            } else {
                return response()->json(['error' => $response->getMessage()], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }

    }

        protected function getPayPalCredentials()
    {
        return PaymentGateway::where('name', 'paypal')
            ->where('status', 1)
            ->first();
    }


}
