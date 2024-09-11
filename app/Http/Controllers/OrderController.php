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

class OrderController extends Controller
{
    public function placeOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'house_number' => 'required|string|max:255',
            'street_name' => 'required|string|max:255',
            'town' => 'required|string|max:255',
            'postcode' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'payment_method' => 'required',
            'order_summary.*.quantity' => 'required|numeric|min:1',
            'order_summary.*.size' => 'nullable|string|max:255',
            'order_summary.*.color' => 'nullable|string|max:255',
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
                        $totalPrice = (float) $item['quantity'] * (float) $entity->price;
                    }
                } elseif (isset($item['offerId']) && $item['offerId'] == 2) {
                    $flashSellDetail = FlashSellDetails::where('product_id', $item['productId'])
                        ->where('status', 1)
                        ->first();

                    if ($flashSellDetail) {
                        $totalPrice = (float) $item['quantity'] * (float) $flashSellDetail->flash_sell_price;
                    } else {
                        $totalPrice = (float) $item['quantity'] * (float) $entity->price;
                    }
                } else {
                    $totalPrice = (float) $item['quantity'] * (float) $entity->price;
                }
            }

            $subtotal += $totalPrice;
        }

        $discountPercentage = (float)($formData['discount_percentage'] ?? 0.00);
        $discountAmount = (float)($formData['discount_amount'] ?? 0.00);

        if ($discountPercentage > 0) {
            $discountAmount = ($subtotal * $discountPercentage) / 100;
        }

        $netAmount = $subtotal - $discountAmount;

        if ($formData['payment_method'] === 'paypal') {
            return $this->initiatePayPalPayment($netAmount, $formData);
        }elseif ($formData['payment_method'] === 'stripe') {
            return $this->initiateStripePayment($netAmount, $formData);
        }

        DB::transaction(function () use ($formData, &$pdfUrl) {
            $subtotal = 0.00;

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
            $order->address = $formData['address'] ?? null;
            $order->payment_method = $formData['payment_method'] ?? null;
            $order->shipping_amount = $formData['delivery_location'] === 'insideDhaka' ? 0.00 : 60.00;
            $order->status = 1;
            $order->admin_notify = 1;
            $order->order_type = 0;

            foreach ($formData['order_summary'] as $item) {
                $isBundle = isset($item['bundleId']);
                $entity = $isBundle ? BundleProduct::findOrFail($item['bundleId']) : Product::findOrFail($item['productId']);

                if ($isBundle) {
                    $bundlePrice = $entity->price ?? 0;
                    $totalPrice = (float) $item['quantity'] * $bundlePrice;
                    $order->bundle_product_id = $entity->id;
                    $entity->quantity -= $item['quantity'];
                    $entity->save();
                } else {
                    if (isset($item['supplierId']) && $item['supplierId'] !== null) {
                        $supplierStock = SupplierStock::where('product_id', $item['productId'])
                            ->where('supplier_id', $item['supplierId'])
                            ->first();

                        if ($supplierStock) {
                            $totalPrice = (float) $item['quantity'] * (float) $supplierStock->price;
                            $supplierStock->quantity -= $item['quantity'];
                            $supplierStock->save();
                        }
                    } elseif (isset($item['campaignId']) && $item['campaignId'] !== null) {
                        $campaign = CampaignRequestProduct::where('product_id', $item['productId'])
                            ->first();

                        if ($campaign) {
                            $totalPrice = (float) $item['quantity'] * (float) $campaign->campaign_price;
                            $campaign->quantity -= $item['quantity'];
                            $campaign->save();
                        }
                    } else if (isset($item['bogoId']) && $item['bogoId'] !== null) {
                        $buyOneGetOne = BuyOneGetOne::where('product_id', $item['productId'])
                            ->first();

                        if ($buyOneGetOne) {
                            $totalPrice = (float) $item['quantity'] * (float) $buyOneGetOne->price;
                            $buyOneGetOne->quantity -= $item['quantity'];
                            $buyOneGetOne->save();
                        }
                    } else {
                        if (isset($item['offerId']) && $item['offerId'] == 1) {
                            $specialOfferDetail = SpecialOfferDetails::where('product_id', $item['productId'])
                                ->where('status', 1)
                                ->first();

                            if ($specialOfferDetail) {
                                $totalPrice = (float) $item['quantity'] * (float) $specialOfferDetail->offer_price;
                            } else {
                                $totalPrice = (float) $item['quantity'] * (float) $entity->price;
                            }
                        } elseif (isset($item['offerId']) && $item['offerId'] == 2) {
                            $flashSellDetail = FlashSellDetails::where('product_id', $item['productId'])
                                ->where('status', 1)
                                ->first();

                            if ($flashSellDetail) {
                                $totalPrice = (float) $item['quantity'] * (float) $flashSellDetail->flash_sell_price;
                            } else {
                                $totalPrice = (float) $item['quantity'] * (float) $entity->price;
                            }
                        } else {
                            $totalPrice = (float) $item['quantity'] * (float) $entity->price;
                        }
                    }
                }

                $subtotal += $totalPrice;
            }

            $discountPercentage = $formData['discount_percentage'] ?? null;
            $discountAmount = $formData['discount_amount'] ?? null;

            if ($discountPercentage !== null) {
                $discountPercent = (float) $discountPercentage;
                $discountAmount = ($subtotal * $discountPercent) / 100;
            } elseif ($discountAmount === null) {
                $discountAmount = 0.00;
            }
            
            $order->discount_amount = $discountAmount;
            $order->subtotal_amount = $subtotal;
            $order->vat_percent = 0;
            $order->vat_amount = 0.00;
            $order->net_amount = $subtotal + $order->vat_amount + $order->shipping_amount - $discountAmount;

            if (auth()->check()) { 
                $order->created_by = auth()->user()->id;
            }

            $order->save();

            $encoded_order_id = base64_encode($order->id);
            $pdfUrl = route('generate-pdf', ['encoded_order_id' => $encoded_order_id]);

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
                                    $totalPrice = (float) $item['quantity'] * (float) $entity->price;
                                }
                                $orderDetail->supplier_id = $item['supplierId'];

                            } elseif (isset($item['offerId']) && $item['offerId'] == 2) {
                                $flashSellDetail = FlashSellDetails::where('product_id', $item['productId'])
                                    ->where('status', 1)
                                    ->first();

                                if ($flashSellDetail) {
                                    $totalPrice = (float) $item['quantity'] * (float) $flashSellDetail->flash_sell_price;
                                } else {
                                    $totalPrice = (float) $item['quantity'] * (float) $entity->price;
                                }
                            } else {
                                $totalPrice = (float) $item['quantity'] * (float) $entity->price;
                            }
                            if ($entity->stock) {
                                $entity->stock->quantity -= $item['quantity'];
                                $entity->stock->save();
                            }
                        }
                        $orderDetail->price_per_unit = $totalPrice / $item['quantity'];
                        $orderDetail->total_price = $totalPrice;
                    }

                    $orderDetail->save();
                }
            }
        });

        return response()->json(['redirectUrl' => $pdfUrl]);
    }

    private function initiateStripePayment($netAmount, $formData)
    {
        $totalamt = $netAmount;
        // $stripecommission = $totalamt * 1.5 / 100;
        // $fixedFee = 0.20;
        // $amt = $netAmount;

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $totalamt * 100,
                'currency' => 'GBP',
                'payment_method' =>  $formData['payment_method_id'],
                'description' => 'Order payment',
                'confirm' => false,
                'confirmation_method' => 'automatic',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        $pdfUrl = null;

        DB::transaction(function () use ($formData, &$pdfUrl) {
            $subtotal = 0.00;

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
            $order->address = $formData['address'] ?? null;
            $order->payment_method = $formData['payment_method'] ?? null;
            $order->shipping_amount = $formData['delivery_location'] === 'insideDhaka' ? 0.00 : 60.00;
            $order->status = 1;
            $order->admin_notify = 1;
            $order->order_type = 0;

            foreach ($formData['order_summary'] as $item) {
                $isBundle = isset($item['bundleId']);
                $entity = $isBundle ? BundleProduct::findOrFail($item['bundleId']) : Product::findOrFail($item['productId']);

                if ($isBundle) {
                    $bundlePrice = $entity->price ?? 0;
                    $totalPrice = (float) $item['quantity'] * $bundlePrice;
                    $order->bundle_product_id = $entity->id;
                    $entity->quantity -= $item['quantity'];
                    $entity->save();
                } else {
                    if (isset($item['supplierId']) && $item['supplierId'] !== null) {
                        $supplierStock = SupplierStock::where('product_id', $item['productId'])
                            ->where('supplier_id', $item['supplierId'])
                            ->first();

                        if ($supplierStock) {
                            $totalPrice = (float) $item['quantity'] * (float) $supplierStock->price;
                            $supplierStock->quantity -= $item['quantity'];
                            $supplierStock->save();
                        }
                    } elseif (isset($item['campaignId']) && $item['campaignId'] !== null) {
                        $campaign = CampaignRequestProduct::where('product_id', $item['productId'])
                            ->first();

                        if ($campaign) {
                            $totalPrice = (float) $item['quantity'] * (float) $campaign->campaign_price;
                            $campaign->quantity -= $item['quantity'];
                            $campaign->save();
                        }
                    } else if (isset($item['bogoId']) && $item['bogoId'] !== null) {
                        $buyOneGetOne = BuyOneGetOne::where('product_id', $item['productId'])
                            ->first();

                        if ($buyOneGetOne) {
                            $totalPrice = (float) $item['quantity'] * (float) $buyOneGetOne->price;
                            $buyOneGetOne->quantity -= $item['quantity'];
                            $buyOneGetOne->save();
                        }
                    } else {
                        if (isset($item['offerId']) && $item['offerId'] == 1) {
                            $specialOfferDetail = SpecialOfferDetails::where('product_id', $item['productId'])
                                ->where('status', 1)
                                ->first();

                            if ($specialOfferDetail) {
                                $totalPrice = (float) $item['quantity'] * (float) $specialOfferDetail->offer_price;
                            } else {
                                $totalPrice = (float) $item['quantity'] * (float) $entity->price;
                            }
                        } elseif (isset($item['offerId']) && $item['offerId'] == 2) {
                            $flashSellDetail = FlashSellDetails::where('product_id', $item['productId'])
                                ->where('status', 1)
                                ->first();

                            if ($flashSellDetail) {
                                $totalPrice = (float) $item['quantity'] * (float) $flashSellDetail->flash_sell_price;
                            } else {
                                $totalPrice = (float) $item['quantity'] * (float) $entity->price;
                            }
                        } else {
                            $totalPrice = (float) $item['quantity'] * (float) $entity->price;
                        }
                    }
                }

                $subtotal += $totalPrice;
            }

            $discountPercentage = $formData['discount_percentage'] ?? null;
            $discountAmount = $formData['discount_amount'] ?? null;

            if ($discountPercentage !== null) {
                $discountPercent = (float) $discountPercentage;
                $discountAmount = ($subtotal * $discountPercent) / 100;
            } elseif ($discountAmount === null) {
                $discountAmount = 0.00;
            }
            
            $order->discount_amount = $discountAmount;
            $order->subtotal_amount = $subtotal;
            $order->vat_percent = 0;
            $order->vat_amount = 0.00;
            $order->net_amount = $subtotal + $order->vat_amount + $order->shipping_amount - $discountAmount;

            if (auth()->check()) { 
                $order->created_by = auth()->user()->id;
            }

            $order->save();

            $encoded_order_id = base64_encode($order->id);
            $pdfUrl = route('generate-pdf', ['encoded_order_id' => $encoded_order_id]);

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
                                    $totalPrice = (float) $item['quantity'] * (float) $entity->price;
                                }
                                $orderDetail->supplier_id = $item['supplierId'];

                            } elseif (isset($item['offerId']) && $item['offerId'] == 2) {
                                $flashSellDetail = FlashSellDetails::where('product_id', $item['productId'])
                                    ->where('status', 1)
                                    ->first();

                                if ($flashSellDetail) {
                                    $totalPrice = (float) $item['quantity'] * (float) $flashSellDetail->flash_sell_price;
                                } else {
                                    $totalPrice = (float) $item['quantity'] * (float) $entity->price;
                                }
                            } else {
                                $totalPrice = (float) $item['quantity'] * (float) $entity->price;
                            }
                            if ($entity->stock) {
                                $entity->stock->quantity -= $item['quantity'];
                                $entity->stock->save();
                            }
                        }
                        $orderDetail->price_per_unit = $totalPrice / $item['quantity'];
                        $orderDetail->total_price = $totalPrice;
                    }

                    $orderDetail->save();
                }
            }
        });

        return response()->json([
            'client_secret' => $paymentIntent->client_secret,
            'redirectUrl' => $pdfUrl
        ]);
    }

    protected function getPayPalCredentials()
    {
        return PaymentGateway::where('name', 'paypal')
            ->where('status', 1)
            ->first();
    }

    protected function initiatePayPalPayment($netAmount, $formData)
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
        DB::transaction(function () use ($formData, &$pdfUrl) {
            $subtotal = 0.00;

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
            $order->address = $formData['address'] ?? null;
            $order->payment_method = $formData['payment_method'] ?? null;
            $order->shipping_amount = $formData['delivery_location'] === 'insideDhaka' ? 0.00 : 60.00;
            $order->status = 1;
            $order->admin_notify = 1;
            $order->order_type = 0;

            foreach ($formData['order_summary'] as $item) {
                $isBundle = isset($item['bundleId']);
                $entity = $isBundle ? BundleProduct::findOrFail($item['bundleId']) : Product::findOrFail($item['productId']);

                if ($isBundle) {
                    $bundlePrice = $entity->price ?? 0;
                    $totalPrice = (float) $item['quantity'] * $bundlePrice;
                    $order->bundle_product_id = $entity->id;
                    $entity->quantity -= $item['quantity'];
                    $entity->save();
                } else {
                    if (isset($item['supplierId']) && $item['supplierId'] !== null) {
                        $supplierStock = SupplierStock::where('product_id', $item['productId'])
                            ->where('supplier_id', $item['supplierId'])
                            ->first();

                        if ($supplierStock) {
                            $totalPrice = (float) $item['quantity'] * (float) $supplierStock->price;
                            $supplierStock->quantity -= $item['quantity'];
                            $supplierStock->save();
                        }
                    } elseif (isset($item['campaignId']) && $item['campaignId'] !== null) {
                        $campaign = CampaignRequestProduct::where('product_id', $item['productId'])
                            ->first();

                        if ($campaign) {
                            $totalPrice = (float) $item['quantity'] * (float) $campaign->campaign_price;
                            $campaign->quantity -= $item['quantity'];
                            $campaign->save();
                        }
                    } else if (isset($item['bogoId']) && $item['bogoId'] !== null) {
                        $buyOneGetOne = BuyOneGetOne::where('product_id', $item['productId'])
                            ->first();

                        if ($buyOneGetOne) {
                            $totalPrice = (float) $item['quantity'] * (float) $buyOneGetOne->price;
                            $buyOneGetOne->quantity -= $item['quantity'];
                            $buyOneGetOne->save();
                        }
                    } else {
                        if (isset($item['offerId']) && $item['offerId'] == 1) {
                            $specialOfferDetail = SpecialOfferDetails::where('product_id', $item['productId'])
                                ->where('status', 1)
                                ->first();

                            if ($specialOfferDetail) {
                                $totalPrice = (float) $item['quantity'] * (float) $specialOfferDetail->offer_price;
                            } else {
                                $totalPrice = (float) $item['quantity'] * (float) $entity->price;
                            }
                        } elseif (isset($item['offerId']) && $item['offerId'] == 2) {
                            $flashSellDetail = FlashSellDetails::where('product_id', $item['productId'])
                                ->where('status', 1)
                                ->first();

                            if ($flashSellDetail) {
                                $totalPrice = (float) $item['quantity'] * (float) $flashSellDetail->flash_sell_price;
                            } else {
                                $totalPrice = (float) $item['quantity'] * (float) $entity->price;
                            }
                        } else {
                            $totalPrice = (float) $item['quantity'] * (float) $entity->price;
                        }
                    }
                }

                $subtotal += $totalPrice;
            }

            $discountPercentage = $formData['discount_percentage'] ?? null;
            $discountAmount = $formData['discount_amount'] ?? null;

            if ($discountPercentage !== null) {
                $discountPercent = (float) $discountPercentage;
                $discountAmount = ($subtotal * $discountPercent) / 100;
            } elseif ($discountAmount === null) {
                $discountAmount = 0.00;
            }
            
            $order->discount_amount = $discountAmount;
            $order->subtotal_amount = $subtotal;
            $order->vat_percent = 0;
            $order->vat_amount = 0.00;
            $order->net_amount = $subtotal + $order->vat_amount + $order->shipping_amount - $discountAmount;

            if (auth()->check()) { 
                $order->created_by = auth()->user()->id;
            }

            $order->save();

            $encoded_order_id = base64_encode($order->id);
            $pdfUrl = route('generate-pdf', ['encoded_order_id' => $encoded_order_id]);

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
                                    $totalPrice = (float) $item['quantity'] * (float) $entity->price;
                                }
                                $orderDetail->supplier_id = $item['supplierId'];

                            } elseif (isset($item['offerId']) && $item['offerId'] == 2) {
                                $flashSellDetail = FlashSellDetails::where('product_id', $item['productId'])
                                    ->where('status', 1)
                                    ->first();

                                if ($flashSellDetail) {
                                    $totalPrice = (float) $item['quantity'] * (float) $flashSellDetail->flash_sell_price;
                                } else {
                                    $totalPrice = (float) $item['quantity'] * (float) $entity->price;
                                }
                            } else {
                                $totalPrice = (float) $item['quantity'] * (float) $entity->price;
                            }
                            if ($entity->stock) {
                                $entity->stock->quantity -= $item['quantity'];
                                $entity->stock->save();
                            }
                        }
                        $orderDetail->price_per_unit = $totalPrice / $item['quantity'];
                        $orderDetail->total_price = $totalPrice;
                    }

                    $orderDetail->save();
                }
            }
        });

        session()->forget('order_data');

        return redirect($pdfUrl);
    }

    public function paymentCancel()
    {
        return redirect()->route('home');
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

    public function allOrders()
    {
        return view('admin.orders.all');
    }

    public function allOrder()
    {
        return DataTables::of(Order::with('user')
                        ->where('order_type',0)
                        ->orderBy('id', 'desc'))
                        ->addColumn('action', function($order){
                            return '<a href="'.route('admin.orders.details', ['orderId' => $order->id]).'" class="btn btn-primary">Details</a>';
                        })
                        ->editColumn('subtotal_amount', function ($order) {
                            return number_format($order->subtotal_amount, 2);
                        })
                        ->editColumn('shipping_amount', function ($order) {
                            return number_format($order->shipping_amount, 2);
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
                        ->addColumn('name', function ($order) {
                            return $order->name;
                        })
                        ->addColumn('email', function ($order) {
                            return $order->email;
                        })
                        ->addColumn('phone', function ($order) {
                            return $order->phone;
                        })
                        ->rawColumns(['action'])
                        ->make(true);
    }

    public function pendingOrders()
    {
        $orders = Order::with('user')
                ->where('status', 1)
                ->orderBy('id', 'desc')
                ->get();
        return view('admin.orders.index', compact('orders'));
    }

    public function processingOrders()
    {
        $orders = Order::with('user')
                ->where('status', 2)
                ->orderBy('id', 'desc')
                ->get();
        return view('admin.orders.index', compact('orders'));
    }
    public function packedOrders()
    {
        $orders = Order::with('user')
                ->where('status', 3)
                ->orderBy('id', 'desc')
                ->get();
        return view('admin.orders.index', compact('orders'));
    }
    public function shippedOrders()
    {
        $orders = Order::with('user')
                ->where('status', 4)
                ->orderBy('id', 'desc')
                ->get();
         $deliveryMen = DeliveryMan::orderBy('id', 'desc')
                ->get(); 
        return view('admin.orders.index', compact('orders', 'deliveryMen'));
    }
    public function deliveredOrders()
    {
        $orders = Order::with('user')
                ->where('status', 5)
                ->orderBy('id', 'desc')
                ->get();
        return view('admin.orders.index', compact('orders'));
    }
    public function returnedOrders()
    {
        $orders = Order::with(['user', 'orderReturns.product'])
                    ->where('status', 6)
                    ->orderBy('id', 'desc')
                    ->get();

        return view('admin.orders.returned', compact('orders'));
    }
    public function cancelledOrders()
    {
        $orders = Order::with('user', 'cancelledOrder')
                ->where('status', 7)
                ->orderBy('id', 'desc')
                ->get();
        return view('admin.orders.cancelled', compact('orders'));
    }

    public function updateStatus(Request $request)
    {
        $order = Order::find($request->order_id);
        if ($order) {
            $order->status = $request->status;
            $order->save();

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
            // dd($order);
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

            if ($stock) {
                $stock->quantity += $detail->quantity;
                $stock->save();
            }
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

}
