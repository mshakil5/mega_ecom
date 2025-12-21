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
use App\Models\Size;
use App\Mail\OrderMail;
use Illuminate\Support\Facades\Mail;
use App\Models\ContactEmail;

class CheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        $sessionCart = $request->session()->get('cart', []);
        // dd($sessionCart);
        if (!is_array($sessionCart) || empty($sessionCart)) {
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
                $color = Color::find($colorId);
                $colorName = $color->color ?? null;
                $sizeName = $item['sizeName'] ?? null;
                $size = Size::where('size', $sizeName)->first();
                $frontImageRow = $product->images()
                    ->where('image_type', 'front')
                    ->when($colorId, fn($q) => $q->where('color_id', $colorId))
                    ->latest()
                    ->first();
                $frontImage = $frontImageRow->image_path ?? null;
            }

            $cartItems[] = [
                'key' => $key,
                'product_id' => $pid,
                'product' => $product,
                'product_name' => $item['product_name'] ?? ($product->name ?? 'Unknown Product'),
                'product_image' => $frontImage ?? ($item['product_image'] ?? $product->feature_image ?? null),
                'ean' => $item['ean'] ?? null,
                'size_id' => $size->id ?? null,
                'sizeName' => $sizeName ?? null,
                'color_id' => $colorId ?? null,
                'colorName' => $colorName,
                'quantity' => $quantity,
                'price' => $price,
                'subtotal' => $subtotal,
                'customization' => $item['customization'] ?? [],
            ];

            // dd($cartItems);
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
            return $this->processPaypal($request, $totals);
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
            $order = $this->createOrder($request->all(), $totals);
            
            $this->createOrderDetails($request->cart_items, $order);
            
            $this->handleCustomizations($request->cart_items, $order);
            
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

    protected function processPaypal(Request $request, $totals)
    {
        $payPalCredentials = $this->getPayPalCredentials();

        if (!$payPalCredentials) {
            return response()->json(['error' => 'PayPal credentials not found'], 404);
        }

        $gateway = Omnipay::create('PayPal_Rest');
        $gateway->setClientId($payPalCredentials->clientid);
        $gateway->setSecret($payPalCredentials->secretid);
        $gateway->setTestMode($payPalCredentials->mode);

        try {
            $amount = $request->total_amount ?? $totals['total_amount'];
            
            $response = $gateway->purchase([
                'amount' => number_format($amount, 2, '.', ''),
                'currency' => 'GBP',
                'returnUrl' => route('paypal.success'),
                'cancelUrl' => route('payment.cancel')
            ])->send();

            if ($response->isRedirect()) {
                session()->put('paypal_order_data', $request->all());
                session()->put('paypal_totals', [
                    'subtotal' => $request->subtotal,
                    'shipping_charge' => $request->shipping_charge,
                    'vat_percent' => $request->vat_amount ? (($request->vat_amount / ($request->subtotal + $request->shipping_charge)) * 100) : 20,
                    'vat_amount' => $request->vat_amount,
                    'total_amount' => $request->total_amount
                ]);
                return response()->json(['redirectUrl' => $response->getRedirectUrl()]);
            } else {
                return response()->json(['error' => $response->getMessage()], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function paypalSuccess(Request $request)
    {
        // \Log::info('PayPal Success Called', [
        //     'request_params' => $request->all(),
        // ]);

        $payPalCredentials = $this->getPayPalCredentials();
        $gateway = Omnipay::create('PayPal_Rest');
        $gateway->setClientId($payPalCredentials->clientid);
        $gateway->setSecret($payPalCredentials->secretid);
        $gateway->setTestMode($payPalCredentials->mode);

        try {
            $response = $gateway->completePurchase([
                'transactionReference' => $request->input('paymentId'),
                'payerId' => $request->input('PayerID')
            ])->send();

            // \Log::info('PayPal Response', [
            //     'isSuccessful' => $response->isSuccessful(),
            //     'message' => $response->getMessage()
            // ]);

            if ($response->isSuccessful()) {
                $orderData = session()->get('paypal_order_data');
                $totals = session()->get('paypal_totals');

                DB::beginTransaction();

                try {
                    $order = $this->createOrder($orderData, $totals);
                    $order->payment_method = 'paypal';
                    $order->save();

                    $this->createOrderDetails($orderData['cart_items'], $order);
                    $this->handleCustomizations($orderData['cart_items'], $order);

                    $this->sendOrderEmails($order);

                    DB::commit();
                    $request->session()->forget('cart');
                    session()->forget('paypal_order_data');
                    session()->forget('paypal_totals');

                    return redirect()->route('order.success', ['order_id' => $order->id]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
            } else {
                // \Log::error('PayPal payment not successful', ['message' => $response->getMessage()]);
                return redirect()->route('payment.cancel');
            }
        } catch (\Exception $e) {
            // \Log::error('PayPal Exception', ['error' => $e->getMessage()]);
            DB::rollBack();
            return redirect()->route('payment.cancel');
        }
    }

    public function paypalCancel()
    {
        return view('frontend.order.cancel');
    }

    protected function validateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shipping_method' => 'required|in:0,1',
            'payment_method' => 'required|in:cash_on_delivery,bank_transfer,paypal,stripe',
            'cart_items' => 'required|array',
        ]);

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

    protected function createOrder($data, $totals)
    {
        $data = is_array($data) ? $data : $data->all();

        $order = new Order();
        $order->invoice = 'ORD-' . time() . rand(1000, 9999);
        $order->purchase_date = now()->format('Y-m-d');
        $order->user_id = auth()->id() ?? null;
        $order->name = $data['first_name'] ?? '';
        $order->surname = $data['company_name'] ?? '';
        $order->email = $data['email'] ?? '';
        $order->phone = $data['phone'] ?? '';
        $order->address_first_line = $data['address_first_line'] ?? '';
        $order->address_second_line = $data['address_second_line'] ?? '';
        $order->address_third_line = $data['address_third_line'] ?? '';
        $order->town = $data['city'] ?? '';
        $order->postcode = $data['postcode'] ?? '';
        $order->note = $data['order_notes'] ?? '';
        $order->payment_method = $data['payment_method'] ?? '';
        $order->subtotal_amount = $totals['subtotal'];
        $order->shipping_amount = $totals['shipping_charge'];
        $order->vat_percent = $totals['vat_percent'];
        $order->vat_amount = $totals['vat_amount'];
        $order->net_amount = $totals['total_amount'];
        $order->discount_amount = 0;
        $order->order_type = 0;
        $order->status = 1;
        $order->due_status = 0;
        $order->admin_notify = 1;
        
        $order->save();
        
        return $order;
    }

    protected function createOrderDetails($cartItems, $order)
    {
        foreach ($cartItems as $item) {
            $orderDetail = new OrderDetails();
            $orderDetail->order_id = $order->id;
            $orderDetail->product_id = $item['product_id'] ?? null;
            $orderDetail->quantity = $item['quantity'];
            $orderDetail->price_per_unit = $item['price'];
            $orderDetail->total_price = $item['subtotal'];
            $orderDetail->total_price_with_vat = $item['subtotal'];
            $orderDetail->size = $item['sizeName'] ?? null;
            $orderDetail->color = $item['colorName'] ?? null;
            $orderDetail->save();
        }
    }

    protected function handleCustomizations($cartItems, $order)
    {
        foreach ($cartItems as $itemIndex => $item) {
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
        $pdfUrl = route('generate-pdf', ['encoded_order_id' => base64_encode($order->id)]);
        return view('frontend.order.success', compact('pdfUrl'));
    }

    public function orderCancel()
    {
        return view('frontend.order.cancel');
    }

    protected function getPayPalCredentials()
    {
        return PaymentGateway::where('name', 'paypal')
            ->where('status', 1)
            ->first();
    }

    protected function sendOrderEmails(Order $order)
    {
        try {
            $order = $order->load('orderDetails');
            
            // Send customer immediately
            Mail::to($order->email)->send(new OrderMail($order, 'customer'));
            \Log::info('✓ Customer email sent to: ' . $order->email);

            // Send admin emails immediately (no queue)
            $adminEmails = ContactEmail::where('status', 1)->pluck('email');
            
            foreach ($adminEmails as $adminEmail) {
                Mail::to($adminEmail)->send(new OrderMail($order, 'admin'));
                \Log::info('✓ Admin email sent to: ' . $adminEmail);
            }

        } catch (\Exception $e) {
            \Log::error('Error: ' . $e->getMessage());
        }
    }

}