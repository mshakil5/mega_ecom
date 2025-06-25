<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetails;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Stock;
use PDF;
use App\Models\CompanyDetails;
use App\Models\SpecialOfferDetails;
use App\Models\FlashSellDetails;
use App\Models\DeliveryMan;
use DataTables;
use App\Models\CancelledOrder;
use App\Models\OrderReturn;
use Illuminate\Support\Facades\Validator;
use App\Models\SupplierStock;
use Illuminate\Support\Facades\Auth;
use App\Models\BuyOneGetOne;
use App\Models\BundleProduct;
use App\Models\PaymentGateway;
use Omnipay\Omnipay;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Models\CampaignRequestProduct;
use App\Models\Transaction;
use Carbon\Carbon;
use App\Models\Warehouse;
use App\Models\StockHistory;
use App\Models\ContactEmail;
use App\Mail\OrderConfirmation;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderStatusChangedMail;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Mail\AdminNotificationMail;
use Stripe\Checkout\Session;

class OrderController extends Controller
{
    public function placeOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
          'name' => 'nullable|string|max:255',
          'company_name' => 'nullable|string|max:255',
          // 'surname' => 'nullable|string|max:255',
          'email' => 'nullable|email|max:255',
          'phone' => 'nullable|string|max:20',
          // 'house_number' => 'nullable|string|max:255',
          // 'street_name' => 'nullable|string|max:255',
          'address_first_line' => 'nullable|string|max:255',
          'address_second_line' => 'nullable|string|max:255',
          'address_third_line' => 'nullable|string|max:255',
          'town' => 'nullable|string|max:255',
          'postcode' => 'nullable|string|max:20',
          'billing_name' => 'nullable|string|max:255',
          'billing_company_name' => 'nullable|string|max:255',
          // 'billing_surname' => 'nullable|string|max:255',
          'billing_email' => 'nullable|email|max:255',
          'billing_phone' => 'nullable|string|max:20',
          // 'billing_house_number' => 'nullable|string|max:255',
          // 'billing_street_name' => 'nullable|string|max:255',
          'billing_address_first_line' => 'nullable|string|max:255',
          'billing_address_second_line' => 'nullable|string|max:255',
          'billing_address_third_line' => 'nullable|string|max:255',
          'billing_town' => 'nullable|string|max:255',
          'billing_postcode' => 'nullable|string|max:20',
          'note' => 'nullable|string|max:255',
          'payment_method' => 'required',
          'order_summary.*.quantity' => 'required|numeric|min:1',
          'order_summary.*.size' => 'nullable|string|max:255',
          'order_summary.*.color' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Please enter your name.',
            'surname.required' => 'Please enter your last name.',
            'email.required' => 'Please enter your email.',
            'phone.required' => 'Please enter your phone number.',
            'house_number.required' => 'Please enter your house number.',
            'street_name.required' => 'Please enter your street name.',
            'town.required' => 'Please enter your town.',
            'postcode.required' => 'Please enter your postcode.',
        ]);

        if ($validator->fails()) {
          return response()->json([
            'errors' => $validator->errors()
          ], 422);
        }

        $formData = $request->all();
        $pdfUrl = null;
        $subtotal = 0.00;
        $discountAmount = 0.00;

        foreach ($formData['order_summary'] as $item) {
            $isBundle = isset($item['bundleId']);
            $entity = $isBundle ? BundleProduct::findOrFail($item['bundleId']) : Product::findOrFail($item['productId']);

            if ($isBundle) {
                $bundlePrice = $entity->price ?? 0;
                $totalPrice = (float) $item['quantity'] * $bundlePrice;
            } else {
                if (isset($item['supplierId']) && $item['supplierId'] !== null) {
                    $supplierStock = SupplierStock::where('product_id', $item['productId'])
                        ->where('supplier_id', $item['supplierId'])
                        ->first();

                    if ($supplierStock) {
                        $totalPrice = (float) $item['quantity'] * (float) $supplierStock->price;
                    }
                } elseif (isset($item['campaignId']) && $item['campaignId'] !== null) {
                    $campaign = CampaignRequestProduct::where('product_id', $item['productId'])
                        ->first();

                    if ($campaign) {
                        $totalPrice = (float) $item['quantity'] * (float) $campaign->campaign_price;
                    }
                } elseif (isset($item['bogoId']) && $item['bogoId'] !== null) {
                    $buyOneGetOne = BuyOneGetOne::where('product_id', $item['productId'])
                        ->first();

                    if ($buyOneGetOne) {
                        $totalPrice = (float) $item['quantity'] * (float) $buyOneGetOne->price;
                    }
                } elseif (isset($item['offerId']) && $item['offerId'] == 1) {
                    $specialOfferDetail = SpecialOfferDetails::where('product_id', $item['productId'])
                        ->where('status', 1)
                        ->first();

                    if ($specialOfferDetail) {
                        $totalPrice = (float) $item['quantity'] * (float) $specialOfferDetail->offer_price;
                    } else {
                        
                        $sellingPrice = Product::find($item['productId'])->stock()
                            ->where('quantity', '>', 0)
                            ->where('size', $item['size'])
                            ->where('color', $item['color'])
                            ->orderBy('id', 'desc')
                            ->value('selling_price');

                        $totalPrice = (float) $item['quantity'] * (float) ($sellingPrice ?? $entity->price);
                    }
                } elseif (isset($item['offerId']) && $item['offerId'] == 2) {
                    $flashSellDetail = FlashSellDetails::where('product_id', $item['productId'])
                        ->where('status', 1)
                        ->first();

                    if ($flashSellDetail) {
                        $totalPrice = (float) $item['quantity'] * (float) $flashSellDetail->flash_sell_price;
                    } else {
                        $sellingPrice = Product::find($item['productId'])->stock()
                            ->where('quantity', '>', 0)
                            ->where('size', $item['size'])
                            ->where('color', $item['color'])
                            ->orderBy('id', 'desc')
                            ->value('selling_price');
                        $totalPrice = (float) $item['quantity'] * (float) ($sellingPrice ?? $entity->price);
                    }
                } else {
                    $sellingPrice = Product::find($item['productId'])->stock()
                            ->where('quantity', '>', 0)
                            ->where('size', $item['size'])
                            ->where('color', $item['color'])
                            ->orderBy('id', 'desc')
                            ->value('selling_price');
                    $totalPrice = (float) $item['quantity'] * (float) ($sellingPrice ?? $entity->price);
                }
            }

            $subtotal += $totalPrice;
        }

        $discountPercentage = (float)($formData['discount_percentage'] ?? 0.00);
        $discountAmount = (float)($formData['discount_amount'] ?? 0.00);

        if ($discountPercentage > 0) {
            $discountAmount = ($subtotal * $discountPercentage) / 100;
        }

        $vat_percent = 5;
        $vat_amount = ($subtotal * $vat_percent) / 100;
        $shippingAmount = $formData['shipping'] ?? 0;
        $netAmount = $subtotal - $discountAmount + $vat_amount + $shippingAmount;

        if ($formData['payment_method'] === 'paypal') {
            return $this->initiatePayPalPayment($formData, $discountAmount, $subtotal, $vat_percent, $vat_amount, $shippingAmount, $netAmount);
        } elseif ($formData['payment_method'] === 'stripe') {
            return $this->initiateStripePayment($formData, $discountAmount, $subtotal, $vat_percent, $vat_amount, $shippingAmount, $netAmount);
        } elseif ($formData['payment_method'] === 'cashOnDelivery') {
            $order = $this->createOrder($formData, $discountAmount, $subtotal, $vat_percent, $vat_amount, $shippingAmount, $netAmount);
            $this->sendOrderEmail($order);
            $pdfUrl = route('generate-pdf', ['encoded_order_id' => base64_encode($order->id)]);
        }

        return response()->json([
            'success' => true,
            'redirectUrl' => route('order.success', ['pdfUrl' => $pdfUrl])
        ]);
    }

    protected function createOrder($formData, $discountAmount, $subtotal, $vat_percent, $vat_amount, $shippingAmount, $netAmount)
    {
      return DB::transaction(function () use ($formData, $discountAmount, $subtotal, $vat_percent, $vat_amount, $shippingAmount, $netAmount) {

        $order = new Order();
        if (auth()->check()) {
            $order->user_id = auth()->user()->id;
        }
        $order->invoice = random_int(100000, 999999);
        $order->purchase_date = date('Y-m-d');
        $order->name = $formData['name'] ?? null;
        $order->surname = $formData['surname'] ?? null;
        $order->email = $formData['email'] ?? null;
        $order->phone = $formData['phone'] ?? null;
        $order->house_number = $formData['house_number'] ?? null;
        $order->street_name = $formData['street_name'] ?? null;
        $order->town = $formData['town'] ?? null;
        $order->postcode = $formData['postcode'] ?? null;
        $order->note = $formData['note'] ?? null;
        $order->payment_method = $formData['payment_method'] ?? null;
        $order->shipping_amount = $formData['shipping'] ?? 0;
        $order->status = 1;
        $order->admin_notify = 1;
        $order->order_type = 0;
        $order->discount_amount = $discountAmount;
        $order->subtotal_amount = $subtotal;
        $order->vat_percent = $vat_percent;
        $order->vat_amount = $vat_amount;
        $order->net_amount = $netAmount;
        if (auth()->check()) { 
            $order->created_by = auth()->user()->id;
        }

        $order->save();

        if ($discountAmount > 0 && isset($formData['coupon_id'])) {
            $couponUsage = new CouponUsage();
            $couponUsage->coupon_id = $formData['coupon_id'];
            $couponUsage->order_id = $order->id;
        
            if (auth()->check()) {
                $couponUsage->user_id = auth()->user()->id;
            } else {
                $couponUsage->guest_name = $formData['name'] ?? null;
                $couponUsage->guest_email = $formData['email'] ?? null;
                $couponUsage->guest_phone = $formData['phone'] ?? null;
            }
        
            $couponUsage->save();

            Coupon::where('id', $formData['coupon_id'])->increment('times_used', 1);
        }

        if (isset($formData['order_summary']) && is_array($formData['order_summary'])) {
            foreach ($formData['order_summary'] as $item) {
                $isBundle = isset($item['bundleId']);
                $entity = $isBundle ? BundleProduct::findOrFail($item['bundleId']) : Product::findOrFail($item['productId']);

                $totalPrice = 0;
                $orderDetail = new OrderDetails();
                $orderDetail->order_id = $order->id;
                $orderDetail->product_id = $isBundle ? null : $item['productId'];
                $orderDetail->quantity = $item['quantity'];
                $orderDetail->size = $item['size'] ?? null;
                $orderDetail->color = $item['color'] ?? null;
                $orderDetail->type_id = $item['typeId'] ?? null;

                if ($isBundle) {
                    $bundlePrice = $entity->price ?? 0;
                    $totalPrice = (float) $item['quantity'] * $bundlePrice;
                    $orderDetail->price_per_unit = $bundlePrice;
                    $orderDetail->total_price = $totalPrice;
                    $orderDetail->bundle_product_ids = $entity->product_ids;
                } else {
                    if (isset($item['supplierId']) && $item['supplierId'] !== null) {
                        $supplierStock = SupplierStock::where('product_id', $item['productId'])
                            ->where('supplier_id', $item['supplierId'])
                            ->first();

                        if ($supplierStock) {
                            $totalPrice = (float) $item['quantity'] * (float) $supplierStock->price;
                            // $supplierStock->quantity -= $item['quantity'];
                            $supplierStock->save();
                        }
                        $orderDetail->supplier_id = $item['supplierId'];

                    } elseif (isset($item['campaignId']) && $item['campaignId'] !== null) {
                        $campaign = CampaignRequestProduct::where('product_id', $item['productId'])
                            ->first();

                        if ($campaign) {
                            $totalPrice = (float) $item['quantity'] * (float) $campaign->campaign_price;
                            // $campaign->quantity -= $item['quantity'];
                            $campaign->save();
                        }
                        $orderDetail->campaign_request_product_id = $item['campaignId'];
                    } else if (isset($item['bogoId']) && $item['bogoId'] !== null) {
                        $buyOneGetOne = BuyOneGetOne::where('product_id', $item['productId'])
                            ->first();

                        if ($buyOneGetOne) {
                            $totalPrice = (float) $item['quantity'] * (float) $buyOneGetOne->price;
                            $buyOneGetOne->quantity -= $item['quantity'];
                            $buyOneGetOne->save();
                        }
                        $orderDetail->buy_one_get_ones_id  = $item['bogoId'];

                    } else {
                        if (isset($item['offerId']) && $item['offerId'] == 1) {
                            $specialOfferDetail = SpecialOfferDetails::where('product_id', $item['productId'])
                                ->where('status', 1)
                                ->first();

                            if ($specialOfferDetail) {
                                $totalPrice = (float) $item['quantity'] * (float) $specialOfferDetail->offer_price;
                            } else {
                                $sellingPrice = Product::find($item['productId'])->stock()
                                    ->where('quantity', '>', 0)
                                    ->where('size', $item['size'])
                                    ->where('color', $item['color'])
                                    ->orderBy('id', 'desc')
                                    ->value('selling_price');
                                $totalPrice = (float) $item['quantity'] * (float) ($sellingPrice ?? $entity->price);
                            }
                            $orderDetail->supplier_id = $item['supplierId'];

                        } elseif (isset($item['offerId']) && $item['offerId'] == 2) {
                            $flashSellDetail = FlashSellDetails::where('product_id', $item['productId'])
                                ->where('status', 1)
                                ->first();

                            if ($flashSellDetail) {
                                $totalPrice = (float) $item['quantity'] * (float) $flashSellDetail->flash_sell_price;
                            } else {
                                $sellingPrice = Product::find($item['productId'])->stock()
                                    ->where('quantity', '>', 0)
                                    ->where('size', $item['size'])
                                    ->where('color', $item['color'])
                                    ->orderBy('id', 'desc')
                                    ->value('selling_price');
                                $totalPrice = (float) $item['quantity'] * (float) ($sellingPrice ?? $entity->price);
                            }
                        } else {
                            $sellingPrice = Product::find($item['productId'])->stock()
                                ->where('quantity', '>', 0)
                                ->where('size', $item['size'])
                                ->where('color', $item['color'])
                                ->where('type_id', $item['typeId'])
                                ->orderBy('id', 'desc')
                                ->value('selling_price');
                            $totalPrice = (float) $item['quantity'] * (float) ($sellingPrice ?? $entity->price);
                        }
                        // if ($entity->stock) {
                        //     $entity->stock->quantity -= $item['quantity'];
                        //     $entity->stock->save();
                        // }
                    }
                    $orderDetail->price_per_unit = $totalPrice / $item['quantity'];
                    $orderDetail->total_price = $totalPrice;
                }

                $orderDetail->save();
            }
        }

        return $order;

      });
    }

    private function initiateStripePayment($formData, $discountAmount, $subtotal, $vat_percent, $vat_amount, $shippingAmount, $netAmount)
    {
        session([
            'payment_data' => [
                'formData' => $formData,
                'discountAmount' => $discountAmount,
                'subtotal' => $subtotal,
                'vat_percent' => $vat_percent,
                'vat_amount' => $vat_amount,
                'shippingAmount' => $shippingAmount,
                'netAmount' => $netAmount,
            ]
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));
        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'GBP',
                        'product_data' => ['name' => 'Order Payment'],
                        'unit_amount' => intval($netAmount * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('stripe.resumeOrderFlow'),
                'cancel_url' => route('payment.cancel'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json([
            'success' => true,
            'redirectUrl' => $session->url,
        ]);
    }

    public function resumeOrderFlow(Request $request)
    {
        $paymentData = session('payment_data');
        
        $formData = $paymentData['formData'];
        $discountAmount = $paymentData['discountAmount'];
        $subtotal = $paymentData['subtotal'];
        $vat_percent = $paymentData['vat_percent'];
        $vat_amount = $paymentData['vat_amount'];
        $shippingAmount = $paymentData['shippingAmount'];
        $netAmount = $paymentData['netAmount'];

        $pdfUrl = null;
        $order = $this->createOrder($formData, $discountAmount, $subtotal, $vat_percent, $vat_amount, $shippingAmount, $netAmount);

        $pdfUrl = route('generate-pdf', ['encoded_order_id' => base64_encode($order->id)]);
        $this->sendOrderEmail($order);

        session()->forget('payment_data');

        return view('frontend.order.success', compact('pdfUrl'));
    }

    protected function getPayPalCredentials()
    {
        return PaymentGateway::where('name', 'paypal')
            ->where('status', 1)
            ->first();
    }

    protected function initiatePayPalPayment($formData, $discountAmount, $subtotal, $vat_percent, $vat_amount, $shippingAmount, $netAmount)
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
            $response = $gateway->purchase([
                'amount' => number_format($netAmount, 2, '.', ''),
                'currency' => 'GBP',
                'returnUrl' => route('payment.success'),
                'cancelUrl' => route('payment.cancel')
            ])->send();

            if ($response->isRedirect()) {
                session()->put('order_data', $formData);
                session()->put('order_discount_amount', $discountAmount);
                session()->put('order_subtotal', $subtotal);
                session()->put('order_vat_percent', $vat_percent);
                session()->put('order_vat_amount', $vat_amount);
                session()->put('order_shipping_amount', $shippingAmount);
                session()->put('order_net_amount', $netAmount);
                return response()->json(['redirectUrl' => $response->getRedirectUrl()]);
            } else {
                return response()->json(['error' => $response->getMessage()], 400);
            }
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }

    }

    public function paymentSuccess(Request $request)
    {
        $formData = session('order_data');
        $discountAmount = session('order_discount_amount');
        $subtotal = session('order_subtotal');
        $vatPercent = session('order_vat_percent');
        $vatAmount = session('order_vat_amount');
        $shippingAmount = session('order_shipping_amount');
        $netAmount = session('order_net_amount');
        $order = $this->createOrder($formData, $discountAmount, $subtotal, $vatPercent, $vatAmount, $shippingAmount, $netAmount);
        $pdfUrl = route('generate-pdf', ['encoded_order_id' => base64_encode($order->id)]);

        $this->sendOrderEmail($order);
        session()->forget('order_data', 'order_discount_amount', 'order_subtotal', 'order_vat_percent', 'order_vat_amount', 'order_shipping_amount', 'order_net_amount');

        return view('frontend.order.success', compact('pdfUrl'));
    }

    protected function sendOrderEmail($order)
    {
        $encoded_order_id = base64_encode($order->id);
        $pdfUrl = route('generate-pdf', ['encoded_order_id' => $encoded_order_id]);

        Mail::to($order->email)->send(new OrderConfirmation($order, $pdfUrl));

        $contactEmails = ContactEmail::where('status', 1)->pluck('email');
        foreach ($contactEmails as $email) {
            Mail::to($email)->send(new OrderConfirmation($order, $pdfUrl));
        }
    }

    public function paymentCancel()
    {
        return view('frontend.order.cancel');
    }

    public function orderSuccess(Request $request)
    {
        $pdfUrl = $request->input('pdfUrl');
        return view('frontend.order.success', compact('pdfUrl'));
    }

    public function generatePDF($encoded_order_id)
    {
        $order_id = base64_decode($encoded_order_id);
        $order = Order::with('orderDetails')->findOrFail($order_id);

        $data = [
            'order' => $order,
            'currency' => CompanyDetails::value('currency'),
            'bundleProduct' => $order->bundle_product_id ? BundleProduct::find($order->bundle_product_id) : null,
        ];

        $pdf = PDF::loadView('frontend.order_pdf', $data);

        return $pdf->stream('order_' . $order->id . '.pdf');
    }

    public function generatePDFForSupplier($encoded_order_id)
    {
        $order_id = base64_decode($encoded_order_id);
        $supplierId = Auth::guard('supplier')->user()->id;

        $orderDetails = OrderDetails::where('order_id', $order_id)
            ->where('supplier_id', $supplierId)
            ->with(['product', 'order.user'])
            ->get();

        $order = $orderDetails->first()->order ?? null;
        
        if (!$order) {
            abort(404, 'Order not found for the supplier.');
        }

        $data = [
            'order' => $order,
            'orderDetails' => $orderDetails,
            'currency' => CompanyDetails::value('currency'),
        ];

        $pdf = PDF::loadView('supplier.order_pdf_supplier', $data);

        return $pdf->stream('order_' . $order->id . '.pdf');
    }

    public function getOrders()
    {
        $orders = Order::where('user_id', auth()->user()->id)
                ->orderBy('id', 'desc')
                ->get();
        return view('user.orders', compact('orders'));
    }

    public function getAllOrder(Request $request, $userId = null)
    {
        if ($request->ajax()) {
            $userId = $request->get('userId') ?? $userId;

            if ($userId) {
                $ordersQuery = Order::with('user')->where('user_id', $userId)
                    ->whereIn('order_type', [0, 1]);
            } else {
                $ordersQuery = Order::with('user')->whereIn('order_type', [0, 1]);
            }
            
            $ordersQuery->whereNotIn('status', [6, 7]);

            return DataTables::of($ordersQuery->orderBy('id', 'desc'))
                ->addColumn('action', function($order) {
                    $invoiceButton = '';
                    if ($order->order_type === 0) {
                        $invoiceButton = '<a href="' . route('generate-pdf', ['encoded_order_id' => base64_encode($order->id)]) . '" class="btn btn-success btn-round btn-shadow" target="_blank">
                                            <i class="fas fa-receipt"></i> Invoice
                                        </a>';
                    } elseif ($order->order_type === 1) {
                        $invoiceButton = '<a href="' . route('in-house-sell.generate-pdf', ['encoded_order_id' => base64_encode($order->id)]) . '" class="btn btn-success btn-round btn-shadow" target="_blank">
                                            <i class="fas fa-receipt"></i> Invoice
                                        </a>';
                    }
                
                    $detailsButton = '<a href="' . route('admin.orders.details', ['orderId' => $order->id]) . '" class="btn btn-info btn-round btn-shadow">
                                        <i class="fas fa-info-circle"></i> Details
                                    </a>
                                    <a href="' . route('order-edit', ['orderId' => $order->id]) . '" class="btn btn-warning btn-round btn-shadow">
                                        <i class="fas fa-edit"></i> Edit
                                    </a> ';
                    $deliveryNoteButton = '<a href="' . route('in-house-sell.generate-delivery-note', ['encoded_order_id' => base64_encode($order->id)]) . '" class="btn btn-primary btn-round btn-shadow" target="_blank">
                                    <i class="fas fa-truck"></i> Delivery Note
                                </a>';                     
                
                    return $invoiceButton . ' ' . $deliveryNoteButton . ' ' . $detailsButton;
                })            
                ->editColumn('subtotal_amount', function ($order) {
                    return number_format($order->subtotal_amount, 2);
                })
                ->editColumn('paid_amount', function ($order) {
                    return number_format($order->paid_amount, 2);
                })
                ->editColumn('due_amount', function ($order) {
                    return number_format($order->due_amount, 2);
                })
                ->editColumn('discount_amount', function ($order) {
                    return number_format($order->discount_amount, 2);
                })
                ->editColumn('net_amount', function ($order) {
                    return number_format($order->net_amount, 2);
                })
                ->editColumn('payment_method', function ($order) {
                    if ($order->payment_method === 'cashOnDelivery') {
                        return 'Cash On Delivery';
                    } elseif ($order->payment_method === 'paypal') {
                        return 'PayPal';
                    } elseif ($order->payment_method === 'stripe') {
                        return 'Stripe';
                    } else {
                        return $order->payment_method;
                    }
                })
                ->editColumn('status', function ($order) {
                    $statusLabels = [
                        1 => 'Pending',
                        2 => 'Processing',
                        3 => 'Packed',
                        4 => 'Shipped',
                        5 => 'Delivered',
                        6 => 'Returned',
                        7 => 'Cancelled'
                    ];
                    return isset($statusLabels[$order->status]) ? $statusLabels[$order->status] : 'Unknown';
                })
                ->addColumn('purchase_date', function ($order) {
                    return Carbon::parse($order->purchase_date)->format('d-m-Y');
                })
                ->addColumn('name', function ($order) {
                    if ($order->user) {
                        return ($order->user->name ?? '') . '<br>' . 
                               ($order->user->email ?? '') . '<br>' . 
                               ($order->user->phone ?? '');
                    } else {
                        return ($order->name ?? '') . '<br>' . 
                               ($order->email ?? '') . '<br>' . 
                               ($order->phone ?? '');
                    }
                })
                ->addColumn('type', function ($order) {
                    return $order->order_type == 0 ? 'Frontend' : 'In-house Sale';
                })
                ->rawColumns(['action','name'])
                ->make(true);
        }
        return view('admin.orders.all', compact('userId'));
    }

    public function getInHouseOrder(Request $request, $userId = null)
    {
        if ($request->ajax()) {
            $userId = $request->get('userId') ?? $userId;

            // $warehouseIds = json_decode(Auth::user()->warehouse_ids, true);
            if ($userId) {
                $ordersQuery = Order::with('user')->where('user_id', $userId)
                    ->whereIn('order_type', [1]);
            } else {
                $ordersQuery = Order::with('user')->whereIn('order_type', [1]);
            }
            
            $ordersQuery->whereNotIn('status', [6, 7]);


            // if (!empty($warehouseIds)) {
            //     $ordersQuery->whereIn('warehouse_id', $warehouseIds);
            // }

            return DataTables::of($ordersQuery->orderBy('id', 'desc'))
                ->addColumn('action', function($order) {
                    $invoiceButton = '';
                    if ($order->order_type === 0) {
                        $invoiceButton = '<a href="' . route('generate-pdf', ['encoded_order_id' => base64_encode($order->id)]) . '" class="btn btn-success btn-round btn-shadow" target="_blank">
                                            <i class="fas fa-receipt"></i> Invoice
                                        </a>';
                    } elseif ($order->order_type === 1) {
                        $invoiceButton = '<a href="' . route('in-house-sell.generate-pdf', ['encoded_order_id' => base64_encode($order->id)]) . '" class="btn btn-success btn-round btn-shadow" target="_blank">
                                            <i class="fas fa-receipt"></i> Invoice
                                        </a>';
                    }
                
                    $detailsButton = '<a href="' . route('admin.orders.details', ['orderId' => $order->id]) . '" class="btn btn-info btn-round btn-shadow">
                                        <i class="fas fa-info-circle"></i> Details
                                    </a>
                                    <a href="' . route('order-edit', ['orderId' => $order->id]) . '" class="btn btn-warning btn-round btn-shadow">
                                        <i class="fas fa-edit"></i> Edit
                                    </a> ';
                     $deliveryNoteButton = '<a href="' . route('in-house-sell.generate-delivery-note', ['encoded_order_id' => base64_encode($order->id)]) . '" class="btn btn-primary btn-round btn-shadow" target="_blank">
                                            <i class="fas fa-truck"></i> Delivery Note
                                        </a>';               
                
                    return $invoiceButton . ' ' . $deliveryNoteButton . ' ' . $detailsButton;
                })            
                ->editColumn('subtotal_amount', function ($order) {
                    return number_format($order->subtotal_amount, 2);
                })
                ->editColumn('paid_amount', function ($order) {
                    return number_format($order->paid_amount, 2);
                })
                ->editColumn('due_amount', function ($order) {
                    return number_format($order->due_amount, 2);
                })
                ->editColumn('discount_amount', function ($order) {
                    return number_format($order->discount_amount, 2);
                })
                ->editColumn('net_amount', function ($order) {
                    return number_format($order->net_amount, 2);
                })
                ->editColumn('status', function ($order) {
                    $statusLabels = [
                        1 => 'Pending',
                        2 => 'Processing',
                        3 => 'Packed',
                        4 => 'Shipped',
                        5 => 'Delivered',
                        6 => 'Returned',
                        7 => 'Cancelled'
                    ];
                    return isset($statusLabels[$order->status]) ? $statusLabels[$order->status] : 'Unknown';
                })
                ->addColumn('purchase_date', function ($order) {
                    return Carbon::parse($order->purchase_date)->format('d-m-Y');
                })
                ->addColumn('name', function ($order) {
                    if ($order->user) {
                        return ($order->user->name ?? '') . '<br>' . 
                               ($order->user->email ?? '') . '<br>' . 
                               ($order->user->phone ?? '');
                    } else {
                        return ($order->name ?? '') . '<br>' . 
                               ($order->email ?? '') . '<br>' . 
                               ($order->phone ?? '');
                    }
                })
                ->addColumn('type', function ($order) {
                    return $order->order_type == 0 ? 'Frontend' : 'In-house Sale';
                })
                ->rawColumns(['action','name'])
                ->make(true);
        }
        return view('admin.orders.inhouse', compact('userId'));
    }

    public function getAllOrderByCoupon(Request $request, $couponId)
    {
        if ($request->ajax()) {
            $couponUsages = CouponUsage::where('coupon_id', $couponId)
            ->pluck('order_id'); 
            
            $ordersQuery = Order::with('user')->whereIn('id', $couponUsages)->whereIn('order_type', [0, 1])->where('status', '!=', 7);

            return DataTables::of($ordersQuery->orderBy('id', 'desc'))
                ->addColumn('action', function($order) {
                    $invoiceButton = '';
                    if ($order->order_type === 0) {
                        $invoiceButton = '<a href="' . route('generate-pdf', ['encoded_order_id' => base64_encode($order->id)]) . '" class="btn btn-success btn-round btn-shadow" target="_blank">
                                            <i class="fas fa-receipt"></i> Invoice
                                        </a>';
                    } elseif ($order->order_type === 1) {
                        $invoiceButton = '<a href="' . route('in-house-sell.generate-pdf', ['encoded_order_id' => base64_encode($order->id)]) . '" class="btn btn-success btn-round btn-shadow" target="_blank">
                                            <i class="fas fa-receipt"></i> Invoice
                                        </a>';
                    }
                
                    $detailsButton = '<a href="' . route('admin.orders.details', ['orderId' => $order->id]) . '" class="btn btn-info btn-round btn-shadow">
                                        <i class="fas fa-info-circle"></i> Details
                                    </a>
                                    <a href="' . route('order-edit', ['orderId' => $order->id]) . '" class="btn btn-warning btn-round btn-shadow">
                                        <i class="fas fa-edit"></i> Edit
                                    </a> ';
                
                    return $invoiceButton . ' ' . $detailsButton;
                })            
                ->editColumn('subtotal_amount', function ($order) {
                    return number_format($order->subtotal_amount, 2);
                })
                ->editColumn('paid_amount', function ($order) {
                    return number_format($order->paid_amount, 2);
                })
                ->editColumn('due_amount', function ($order) {
                    return number_format($order->due_amount, 2);
                })
                ->editColumn('discount_amount', function ($order) {
                    return number_format($order->discount_amount, 2);
                })
                ->editColumn('net_amount', function ($order) {
                    return number_format($order->net_amount, 2);
                })
                ->editColumn('status', function ($order) {
                    $statusLabels = [
                        1 => 'Pending',
                        2 => 'Processing',
                        3 => 'Packed',
                        4 => 'Shipped',
                        5 => 'Delivered',
                        6 => 'Returned',
                        7 => 'Cancelled'
                    ];
                    return isset($statusLabels[$order->status]) ? $statusLabels[$order->status] : 'Unknown';
                })
                ->addColumn('purchase_date', function ($order) {
                    return Carbon::parse($order->purchase_date)->format('d-m-Y');
                })
                ->addColumn('name', function ($order) {
                    if ($order->user) {
                        return ($order->user->name ?? '') . '<br>' . 
                               ($order->user->email ?? '') . '<br>' . 
                               ($order->user->phone ?? '');
                    } else {
                        return ($order->name ?? '') . '<br>' . 
                               ($order->email ?? '') . '<br>' . 
                               ($order->phone ?? '');
                    }
                })
                ->addColumn('type', function ($order) {
                    return $order->order_type == 0 ? 'Frontend' : 'In-house Sale';
                })
                ->rawColumns(['action','name'])
                ->make(true);
        }
        return view('admin.orders.coupon', compact('couponId'));
    }

    public function pendingOrders()
    {
        $orders = Order::with('user','warehouse')
                ->whereIn('order_type', [0, 1])
                ->where('status', 1)
                ->orderBy('id', 'desc')
                ->get();
        $warehouses = Warehouse::select('id', 'name','location')->where('status', 1)->get();   
        return view('admin.orders.index', compact('orders', 'warehouses'));
    }

    public function processingOrders()
    {
        $orders = Order::with('user')
                ->whereIn('order_type', [0, 1])
                ->where('status', 2)
                ->orderBy('id', 'desc')
                ->get();
        $warehouses = Warehouse::select('id', 'name','location')->where('status', 1)->get();
        return view('admin.orders.index', compact('orders', 'warehouses'));
    }
    public function packedOrders()
    {
        $orders = Order::with('user')
                ->whereIn('order_type', [0, 1])
                ->where('status', 3)
                ->orderBy('id', 'desc')
                ->get();
        $warehouses = Warehouse::select('id', 'name','location')->where('status', 1)->get();
        return view('admin.orders.index', compact('orders', 'warehouses'));
    }
    public function shippedOrders()
    {
        $orders = Order::with('user')
                ->whereIn('order_type', [0, 1])
                ->where('status', 4)
                ->orderBy('id', 'desc')
                ->get();
         $deliveryMen = DeliveryMan::orderBy('id', 'desc')
                ->get(); 
        $warehouses = Warehouse::select('id', 'name','location')->where('status', 1)->get();
        return view('admin.orders.index', compact('orders', 'deliveryMen', 'warehouses'));
    }
    public function deliveredOrders()
    {
        $orders = Order::with('user')
                ->where('status', 5)
                ->whereIn('order_type', [0, 1])
                ->orderBy('id', 'desc')
                ->get();
        $warehouses = Warehouse::select('id', 'name','location')->where('status', 1)->get();
        return view('admin.orders.index', compact('orders', 'warehouses'));
    }
    public function returnedOrders()
    {
        $orders = Order::with(['user', 'orderReturns.product', 'orderReturns.orderDetails'])
                    ->where('status', 6)
                    ->whereIn('order_type', [0, 1])
                    ->orderBy('id', 'desc')
                    ->get();
        $warehouses = Warehouse::select('id', 'name','location')->where('status', 1)->get();
        return view('admin.orders.returned', compact('orders', 'warehouses'));
    }
    public function cancelledOrders()
    {
        $orders = Order::with('user', 'cancelledOrder')
                ->whereIn('order_type', [0, 1])
                ->where('status', 7)
                ->orderBy('id', 'desc')
                ->get();
        $warehouses = Warehouse::select('id', 'name','location')->where('status', 1)->get();
        return view('admin.orders.cancelled', compact('orders', 'warehouses'));
    }

    public function updateStatus(Request $request)
    {
        $order = Order::find($request->order_id);
        if ($order) {
            $order->status = $request->status;
            $order->save();

            if ($request->status == 6) {
                $transaction = new Transaction();
                $transaction->date = now();
                $transaction->customer_id = $order->user_id;
                $transaction->order_id = $order->id;
                $transaction->table_type = 'Sales';
                $transaction->amount = $order->net_amount;
                $transaction->at_amount = $order->net_amount;
                $transaction->transaction_type = 'Return';
                $transaction->payment_type = 'Return';
                $transaction->created_by = auth()->user()->id;
                $transaction->created_ip = request()->ip();
                $transaction->save();
            
                $orderDetails = $order->orderDetails;
                foreach ($orderDetails as $detail) {
                    $orderReturn = new OrderReturn();
                    $orderReturn->product_id = $detail->product_id;
                    $orderReturn->order_details_id = $detail->id;
                    $orderReturn->order_id = $order->id;
                    $orderReturn->quantity = $detail->quantity ?? 0;
                    $orderReturn->new_quantity = $detail->quantity ?? 0;
                    $orderReturn->returned_by = auth()->user()->id;
                    $orderReturn->save();
            
                    $stock = Stock::where('product_id', $detail->product_id)
                        ->where('size', $detail->size)
                        ->where('color', $detail->color)
                        ->where('warehouse_id', $detail->warehouse_id)
                        ->first();
            
                    if ($stock) {
                        $remainingQty = $detail->quantity;
                        $stockHistories = StockHistory::where('stock_id', $stock->id)
                            ->orderBy('created_at', 'desc') // Start from the most recent history
                            ->get();
            
                        foreach ($stockHistories as $history) {
                            if ($remainingQty > 0) {
                                if ($history->selling_qty >= $remainingQty) {
                                    $history->selling_qty -= $remainingQty;
                                    $history->save();
                                    $remainingQty = 0;
                                } else {
                                    $remainingQty -= $history->selling_qty;
                                    $history->selling_qty = 0;
                                    $history->save();
                                }
                            } else {
                                break;
                            }
                        }
                    }
                }
            }            

            if ($request->status == 7) {
                $orderDetails = OrderDetails::where('order_id', $order->id)->get();
            
                foreach ($orderDetails as $detail) {
                    $stock = Stock::where('product_id', $detail->product_id)
                        ->where('size', $detail->size)
                        ->where('color', $detail->color)
                        ->where('warehouse_id', $detail->warehouse_id)
                        ->first();
            
                    if ($stock) {
                        $stock->quantity += $detail->quantity;
                        $stock->save();
                    }
            
                    $remainingQty = $detail->quantity; // Quantity to reverse
                    $stockHistories = StockHistory::where('stock_id', $stock->id)
                        ->orderBy('created_at', 'desc') // Start from the most recent history
                        ->get();
            
                    foreach ($stockHistories as $history) {
                        if ($remainingQty > 0) {
                            if ($history->selling_qty >= $remainingQty) {
                                // Partially reverse this history
                                $history->selling_qty -= $remainingQty;
                                $history->available_qty += $remainingQty;
                                $history->save();
                                $remainingQty = 0;
                            } else {
                                // Fully reverse this history and move to the next one
                                $remainingQty -= $history->selling_qty;
                                $history->available_qty += $history->selling_qty;
                                $history->selling_qty = 0;
                                $history->save();
                            }
                        } else {
                            break;
                        }
                    }
                }
            }            

            $emailToSend = $order->email ?? $order->user->email;

            // if ($emailToSend) {
            //     Mail::to($emailToSend)->send(new OrderStatusChangedMail($order));
            // }

            $contactEmails = ContactEmail::where('status', 1)->pluck('email');

            // foreach ($contactEmails as $email) {
            //     Mail::to($email)->send(new OrderStatusChangedMail($order));
            // }


            return response()->json(['success' => true, 'message' => 'Order status updated successfully!']);
        }

        return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
    }

    public function updateDeliveryMan(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'delivery_man_id' => 'required|exists:delivery_men,id',
        ]);

        $order = Order::findOrFail($request->order_id);
        $deliveryMan = DeliveryMan::findOrFail($request->delivery_man_id);
        $order->delivery_man_id = $deliveryMan->id;
        $order->save();
        return response()->json(['success' => true], 200);
    }

    public function showOrder($orderId)
    {
        $order = Order::with(['user', 'orderDetails.product', 'orderDetails.buyOneGetOne', 'bundleProduct'])
            ->where('id', $orderId)
            ->firstOrFail();
        return view('admin.orders.details', compact('order'));
    }

    public function markAsNotified(Request $request)
    {
        $order = Order::find($request->order_id);

        if ($order) {
            $order->admin_notify = 0;
            $order->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    public function showOrderUser($orderId)
    {
        $order = Order::with(['user', 'orderDetails.product'])
            ->where('id', $orderId)
            ->firstOrFail();
        return view('user.order_details', compact('order'));
    }

    public function cancel(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        if (in_array($order->status, [4, 5, 6, 7])) {
            return response()->json(['error' => 'Order cannot be cancelled.'], 400);
        }

        $order->status = 7;
        $order->save();

        $orderDetails = OrderDetails::where('order_id', $order->id)->get();

        foreach ($orderDetails as $detail) {
            $stock = Stock::where('product_id', $detail->product_id)
                        ->where('color', $detail->color)
                        ->first();

            // if ($stock) {
            //     $stock->quantity += $detail->quantity;
            //     $stock->save();
            // }
        }

        CancelledOrder::create([
            'order_id' => $order->id,
            'reason' => $request->input('reason'),
            'cancelled_by' => auth()->id(),
        ]);

        return response()->json(['success' => true]);
    }

    public function getOrderDetailsModal(Request $request)
    {
        $orderId = $request->get('order_id');
        $order = Order::with('orderDetails.product')->findOrFail($orderId);
        
        return response()->json([
            'order' => $order,
            'orderDetails' => $order->orderDetails
        ]);
    }

    public function returnStore(Request $request)
    {
        $data = $request->all();

        $order_id = $data['order_id'];

        $order = Order::find($order_id);
        $order->status = 6;
        $order->save();

        $return_items = $data['return_items'];

        foreach ($return_items as $item) {
            $orderReturn = new OrderReturn();
            $orderReturn->product_id = $item['product_id'];
            $orderReturn->order_id = $order_id;
            $orderReturn->quantity = $item['return_quantity'];
            $orderReturn->new_quantity = $item['return_quantity'];
            $orderReturn->reason = $item['return_reason'] ?? '';
            $orderReturn->returned_by = auth()->user()->id;
            $orderReturn->save();
        }

        return response()->json(['message' => 'Order return submitted successfully'], 200);
    }

    public function assignWarehouse(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'warehouse_id' => 'required|exists:warehouses,id',
        ]);

        $order = Order::findOrFail($request->order_id);
        $order->warehouse_id = $request->warehouse_id;
        $order->save();
    
        $orderDetails = $order->orderDetails;
    
        foreach ($orderDetails as $orderDetail) {
            $stock = Stock::where('product_id', $orderDetail->product_id)
                ->where('size', $orderDetail->size)
                ->where('color', $orderDetail->color)
                ->where('warehouse_id', $request->warehouse_id)
                ->first();
    
            if ($stock) {
                $stock->quantity -= $orderDetail->quantity;
                $stock->save();
            } else {
                $stock = new Stock();
                $stock->warehouse_id = $request->warehouse_id;
                $stock->product_id = $orderDetail->product_id;
                $stock->size = $orderDetail->size;
                $stock->color = $orderDetail->color;
                $stock->quantity = -$orderDetail->quantity;
                $stock->created_by = auth()->user()->id; 
                $stock->save();
            }
    
            $stockHistories = StockHistory::where('stock_id', $stock->id)
                ->where('available_qty', '>', 0)
                ->orderBy('created_at', 'asc')
                ->get();
    
            $requiredQty = $orderDetail->quantity;
    
            foreach ($stockHistories as $stockHistory) {
                if ($requiredQty > 0) {
                    if ($stockHistory->available_qty >= $requiredQty) {
                        $stockHistory->available_qty -= $requiredQty;
                        $stockHistory->selling_qty += $requiredQty;
                        $stockHistory->save();
                        $requiredQty = 0; 
                    } else {
                        $requiredQty -= $stockHistory->available_qty;
                        $stockHistory->selling_qty += $stockHistory->available_qty;
                        $stockHistory->available_qty = 0;
                        $stockHistory->save();
                    }
                } else {
                    break;
                }
            }
        }
    
        return response()->json(['message' => 'Warehouse assigned and stock updated successfully!']);
    }
    
    public function sendMailToAdmin(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'orderId' => 'required|exists:orders,id',
        ]);

        $message = $validated['message'];
        $orderId = $validated['orderId'];

        $order = Order::findOrFail($orderId);

        $contactEmail = ContactEmail::orderBy('id', 'asc')->first();

        if (!$contactEmail) {
            return response()->json(['success' => false, 'message' => 'No contact email found.']);
        }

        Mail::to($contactEmail->email)->send(new AdminNotificationMail($message, $order));
        return response()->json(['success' => true, 'message' => 'Mail sent successfully!']);

    }

    public function orderDueList($userId)
    {
        $orders = Order::where('user_id', $userId)
                    ->where('due_amount', '>', 0)
                    ->where('status', '!=', 7)
                    ->latest()
                    ->get();

        return view('admin.orders.due_orders', compact('orders'));
    }

}
