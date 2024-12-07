<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Stock;
use App\Models\CompanyDetails;
use PDF;
use App\Models\Color;
use App\Models\Size;
use App\Models\StockHistory;
use App\Models\Warehouse;
use App\Models\Transaction;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmation;
use App\Models\ContactEmail;

class InHouseSellController extends Controller
{
    public function inHouseSell()
    {
        $products = Product::orderby('id','DESC')->select('id', 'name','price', 'product_code')->get();
        $colors = Color::where('status', 1)->select('id', 'color')->orderby('id','DESC')->get();
        $sizes = Size::where('status', 1)->select('id', 'size')->orderby('id','DESC')->get();
        $warehouses = Warehouse::select('id', 'name','location')->where('status', 1)->get();
        $customers = User::where('is_type', '0')->where('status', 1)->orderby('id','DESC')->get();
        return view('admin.in_house_sell.create', compact('customers', 'products', 'colors', 'sizes','warehouses'));
    }

    public function inHouseSellStore(Request $request)
    {
        $validated = $request->validate([
            'purchase_date' => 'required|date',
            'user_id' => 'required|exists:users,id',
            'payment_method' => 'required|string',
            'ref' => 'nullable|string',
            'remarks' => 'nullable|string',
            'discount' => 'nullable',
            'products' => 'required|json',
        ], [
            'user_id.required' => 'Please choose a wholesaler.',
            'user_id.exists' => 'Please choose a valid wholesaler.',
        ]);

        $products = json_decode($validated['products'], true);

        $itemTotalAmount = array_reduce($products, function ($carry, $product) {
            return $carry + $product['total_price'];
        }, 0);

        $netAmount = $itemTotalAmount - $validated['discount'] + $request->vat;

        $order = new Order();
        $order->invoice = random_int(100000, 999999);
        $order->warehouse_id = $request->warehouse_id;
        $order->purchase_date = $validated['purchase_date'];
        $order->user_id = $validated['user_id'];
        $order->payment_method = $validated['payment_method'];
        $order->ref = $validated['ref'];
        $order->remarks = $validated['remarks'];
        $order->discount_amount = $validated['discount'];
        $order->net_amount = $netAmount;
        $order->vat_amount = $request->vat;
        $order->paid_amount = $request->cash_payment + $request->bank_payment;
        $order->due_amount = $netAmount - $request->cash_payment - $request->bank_payment;
        $order->subtotal_amount = $itemTotalAmount;
        $order->order_type = 1;
        $order->status = 1;
        $order->save();

        $transaction = new Transaction();
        $transaction->date = $validated['purchase_date'];
        $transaction->customer_id = $validated['user_id'];
        $transaction->order_id = $order->id;
        $transaction->table_type = "Sales";
        $transaction->ref = $validated['ref'];
        $transaction->payment_type = "Credit";
        $transaction->transaction_type = "Current";
        $transaction->amount = $itemTotalAmount;
        $transaction->vat_amount = $request->vat;
        $transaction->discount = $validated['discount'] ?? 0.00;
        $transaction->at_amount = $netAmount;
        $transaction->save();
        $transaction->tran_id = 'SL' . date('Ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
        $transaction->save();

        if ($request->cash_payment) {
            $cashtransaction = new Transaction();
            $cashtransaction->date = $validated['purchase_date'];
            $cashtransaction->customer_id = $validated['user_id'];
            $cashtransaction->order_id = $order->id;
            $cashtransaction->table_type = "Sales";
            $cashtransaction->ref = $validated['ref'];
            $cashtransaction->payment_type = "Cash";
            $cashtransaction->transaction_type = "Received";
            $cashtransaction->amount = $request->cash_payment;
            $cashtransaction->at_amount = $request->cash_payment;
            $cashtransaction->save();
            $cashtransaction->tran_id = 'SL' . date('Ymd') . str_pad($cashtransaction->id, 4, '0', STR_PAD_LEFT);
            $cashtransaction->save();
        }

        if ($request->bank_payment) {
            $banktransaction = new Transaction();
            $banktransaction->date = $validated['purchase_date'];
            $banktransaction->customer_id = $validated['user_id'];
            $banktransaction->order_id = $order->id;
            $banktransaction->table_type = "Sales";
            $banktransaction->ref = $validated['ref'];
            $banktransaction->payment_type = "Bank";
            $banktransaction->transaction_type = "Received";
            $banktransaction->amount = $request->bank_payment;
            $banktransaction->at_amount = $request->bank_payment;
            $banktransaction->save();
            $banktransaction->tran_id = 'SL' . date('Ymd') . str_pad($banktransaction->id, 4, '0', STR_PAD_LEFT);
            $banktransaction->save();
        }

        foreach ($products as $product) {
            $orderDetail = new OrderDetails();
            $orderDetail->order_id = $order->id;
            $orderDetail->warehouse_id = $request->warehouse_id;
            $orderDetail->product_id = $product['product_id'];
            $orderDetail->quantity = $product['quantity'];
            $orderDetail->size = $product['product_size'];
            $orderDetail->color = $product['product_color'];
            $orderDetail->price_per_unit = $product['unit_price'];
            $orderDetail->total_price = $product['total_price'];
            $orderDetail->vat_percent = $product['vat_percent'];
            $orderDetail->total_vat = $product['total_vat'];
            $orderDetail->total_price_with_vat = $product['total_price_with_vat'];
            $orderDetail->status = 1;
            $orderDetail->save();

            $requiredQty = $product['quantity'];
            if ($request->warehouse_id) {
                $stock = Stock::where('product_id', $product['product_id'])
                ->where('size', $product['product_size'])
                ->where('color', $product['product_color'])
                ->where('warehouse_id', $request->warehouse_id)
                ->first();
                if ($stock) {
                    $stock->quantity -= $product['quantity'];
                    $stock->save();
                }else {
                    $stock = new Stock();
                    $stock->warehouse_id = $request->warehouse_id;
                    $stock->product_id = $product['product_id'];
                    $stock->size = $product['product_size'];
                    $stock->color = $product['product_color'];
                    $stock->quantity = - $product['quantity'];
                    $stock->created_by = auth()->user()->id;
                    $stock->save();
                }

                $stockHistories = StockHistory::where('stock_id', $stock->id)
                        ->where('available_qty', '>', 0)
                        ->orderBy('created_at', 'asc')
                        ->get();

                        foreach ($stockHistories as $stockHistorie) {
                            if ($requiredQty > 0) {
                                if ($stockHistorie->available_qty >= $requiredQty) {
                                    // Reduce the quantity from the current stock entry
                                    $stockHistorie->available_qty -= $requiredQty;
                                    $stockHistorie->selling_qty += $requiredQty;
                                    $stockHistorie->save();
                                    $requiredQty = 0; // All required quantity is reduced
                                } else {
                                    // If the stock quantity is less than required, deduct all from this entry
                                    $requiredQty -= $stockHistorie->available_qty;
                                    $stockHistorie->available_qty = 0;
                                    $stockHistorie->selling_qty += $requiredQty;
                                    $stockHistorie->save();
                                }
                            } else {
                                break; // Exit loop if all the quantity is reduced
                            }
                        }


            }
            
        }

        $encoded_order_id = base64_encode($order->id);
        $pdfUrl = route('in-house-sell.generate-pdf', ['encoded_order_id' => $encoded_order_id]);

        // Mail::to($order->user->email)->send(new OrderConfirmation($order, $pdfUrl));

        // $contactEmails = ContactEmail::where('status', 1)->pluck('email');

        // foreach ($contactEmails as $email) {
        //     Mail::to($email)->send(new OrderConfirmation($order, $pdfUrl));
        // }

        return response()->json([
            'pdf_url' => $pdfUrl,
            'message' => 'Order placed successfully'
        ], 200);

        return response()->json(['message' => 'Order created successfully', 'order_id' => $order->id], 201);
    }

    public function inHouseQuotationSellStore(Request $request)
    {
        $data = Order::find($request->order_id);
        $data->order_type = 1;
        $data->save();
        
        return back()->with('success', 'Order create successfully!');
    }

    public function generatePDF($encoded_order_id)
    {
        $order_id = base64_decode($encoded_order_id);
        $order = Order::with(['orderDetails', 'user'])->findOrFail($order_id);

        $data = [
            'order' => $order,
            'currency' => CompanyDetails::value('currency'),
        ];

        $pdf = PDF::loadView('admin.in_house_sell.in_house_sell_order_pdf', $data);

        return $pdf->stream('order_' . $order->id . '.pdf');
    }

    public function generateDownloadPDF($encoded_order_id)
    {
        $order_id = base64_decode($encoded_order_id);
        $order = Order::with(['orderDetails', 'user'])->findOrFail($order_id);

        $data = [
            'order' => $order,
            'currency' => CompanyDetails::value('currency'),
        ];

        $pdf = PDF::loadView('admin.in_house_sell.quotation_pdf', $data);

        return $pdf->stream('order_' . $order->id . '.pdf');
    }


    public function makeQuotationStore(Request $request)
    {
        $validated = $request->validate([
            'purchase_date' => 'required|date',
            'user_id' => 'required|exists:users,id',
            'payment_method' => 'required|string',
            'ref' => 'nullable|string',
            'remarks' => 'nullable|string',
            'discount' => 'nullable',
            'products' => 'required|json',
        ]);

        $products = json_decode($validated['products'], true);

        $itemTotalAmount = array_reduce($products, function ($carry, $product) {
            return $carry + $product['total_price'];
        }, 0);

        $netAmount = $itemTotalAmount - $validated['discount'] + $request->vat;

        $order = new Order();
        $order->invoice = random_int(100000, 999999);
        $order->purchase_date = $validated['purchase_date'];
        $order->user_id = $validated['user_id'];
        $order->payment_method = $validated['payment_method'];
        $order->ref = $validated['ref'];
        $order->remarks = $validated['remarks'];
        $order->discount_amount = $validated['discount'];
        $order->net_amount = $netAmount;
        $order->vat_amount = $request->vat;
        $order->subtotal_amount = $itemTotalAmount;
        $order->order_type = 2;
        $order->status = 1;
        $order->save();

        foreach ($products as $product) {
            $orderDetail = new OrderDetails();
            $orderDetail->order_id = $order->id;
            $orderDetail->product_id = $product['product_id'];
            $orderDetail->quantity = $product['quantity'];
            $orderDetail->size = $product['product_size'];
            $orderDetail->color = $product['product_color'];
            $orderDetail->price_per_unit = $product['unit_price'];
            $orderDetail->total_price = $product['total_price'];
            $orderDetail->vat_percent = $product['vat_percent'];
            $orderDetail->total_vat = $product['total_vat'];
            $orderDetail->total_price_with_vat = $product['total_price_with_vat'];
            $orderDetail->status = 1;
            $orderDetail->save();
        }

        $encoded_order_id = base64_encode($order->id);
        $pdfUrl = route('in-house-sell.generate-pdf', ['encoded_order_id' => $encoded_order_id]);

        Mail::to($order->user->email)->send(new OrderConfirmation($order, $pdfUrl));

        $contactEmails = ContactEmail::where('status', 1)->pluck('email');

        foreach ($contactEmails as $email) {
            Mail::to($email)->send(new OrderConfirmation($order, $pdfUrl));
        }

        return response()->json(['message' => 'Quotation created successfully', 'order_id' => $order->id], 201);
    }

    public function allquotations()
    {
        $inHouseOrders = Order::with('user')
        ->where('order_type', 2) 
        ->orderBy('id', 'desc') 
        ->get();

        return view('admin.in_house_sell.quotations', compact('inHouseOrders'));
    }

    public function checkStock(Request $request)
    {
        $warehouseId = $request->input('warehouse_id');
        $productId = $request->input('product_id');
        $size = $request->input('size');
        $color = $request->input('color');

        if (empty($warehouseId) || empty($productId)) {
            return response()->json(['error' => 'Warehouse ID and Product ID are required'], 400);
        }

        $stock = Stock::where('warehouse_id', $warehouseId)
            ->where('product_id', $productId)
            ->where(function($query) use ($size, $color) {
                if ($size) {
                    $query->where('size', $size);
                }
                if ($color) {
                    $query->where('color', $color);
                }
            })
            ->first();

        if (!$stock) {
            return response()->json(['in_stock' => false]);
        }

        return response()->json(['in_stock' => $stock->quantity > 0]);
    }

    public function editOrder($orderId)
    {
        $order = Order::with(['user','orderDetails','transactions'])->findOrFail($orderId);
        $cashAmount = $order->transactions->where('payment_type', 'Cash')->first();
        $bankAmount = $order->transactions->where('payment_type', 'Bank')->first();
        $discountAmount = $order->transactions->where('transaction_type', 'Current')->where('discount', '>', 0)->first();

        $customers = User::where('is_type', '0')->where('status', 1)->orderby('id','asc')->get();
        $products = Product::orderby('id','DESC')->get();
        $colors = Color::orderby('id','DESC')->where('status', 1)->get();
        $sizes = Size::orderby('id','DESC')->where('status', 1)->get();
        $warehouses = Warehouse::select('id', 'name','location')->where('status', 1)->get();
        return view('admin.in_house_sell.edit_order', compact('customers', 'products', 'colors', 'sizes','warehouses', 'order', 'cashAmount', 'bankAmount', 'discountAmount'));
    }

    public function updateOrder(Request $request)
    {
        $order = Order::findOrFail($request->id);

        $userIdRule = $request->user_id ? 'required|exists:users,id' : 'nullable';
        
        $validated = $request->validate([
            'id' => 'required|exists:orders,id',
            'purchase_date' => 'required|date',
            'warehouse_id' => 'required|exists:warehouses,id',
            'payment_method' => 'required|string',
            'ref' => 'nullable|string',
            'remarks' => 'nullable|string',
            'discount' => 'nullable|numeric',
            'products' => 'required|json',

            'user_id' => $userIdRule,
        ], [
            'user_id.required' => 'Please choose a wholesaler.',
            'user_id.exists' => 'Please choose a valid wholesaler.',
        ]);

        $order = Order::findOrFail($validated['id']);
        
        $products = json_decode($validated['products'], true);

        $itemTotalAmount = array_reduce($products, function ($carry, $product) {
            return $carry + $product['total_price'];
        }, 0);

        $netAmount = $itemTotalAmount - $validated['discount'] + $request->vat;

        $order->purchase_date = $validated['purchase_date'];
        $order->user_id = $validated['user_id'];
        $order->payment_method = $validated['payment_method'];
        $order->ref = $validated['ref'];
        $order->remarks = $validated['remarks'];
        $order->discount_amount = $validated['discount'];
        $order->net_amount = $netAmount;
        $order->vat_amount = $request->vat;
        $order->paid_amount = $request->cash_payment + $request->bank_payment;
        $order->due_amount = $netAmount - $request->cash_payment - $request->bank_payment;
        $order->subtotal_amount = $itemTotalAmount;
        $order->status = 1;
        if ($order->order_type != 0) {
            $order->order_type = 1;
            $order->save();
        }
        $order->save();

        $transaction = Transaction::where('order_id', $order->id)->where('transaction_type', 'Current')->where('payment_type', 'Credit')->first();
        if ($transaction) {
            $transaction->date = $validated['purchase_date'];
            $transaction->customer_id = $validated['user_id'];
            $transaction->amount = $itemTotalAmount;
            $transaction->vat_amount = $request->vat;
            $transaction->discount = $validated['discount'] ?? 0.00;
            $transaction->at_amount = $netAmount;
            $transaction->save();
        }

        if ($request->cash_payment) {
            $cashtransaction = Transaction::where('order_id', $order->id)->where('payment_type', 'Cash')->first();
            if ($cashtransaction) {
                $cashtransaction->amount = $request->cash_payment;
                $cashtransaction->at_amount = $request->cash_payment;
                $cashtransaction->save();
            }
        }

        if ($request->bank_payment) {
            $banktransaction = Transaction::where('order_id', $order->id)->where('payment_type', 'Bank')->first();
            if ($banktransaction) {
                $banktransaction->amount = $request->bank_payment;
                $banktransaction->at_amount = $request->bank_payment;
                $banktransaction->save();
            }
        }

        OrderDetails::where('order_id', $order->id)->delete();

        foreach ($products as $product) {
            $orderDetail = new OrderDetails();
            $orderDetail->order_id = $order->id;
            $orderDetail->warehouse_id = $request->warehouse_id;
            $orderDetail->product_id = $product['product_id'];
            $orderDetail->quantity = $product['quantity'];
            $orderDetail->size = $product['product_size'];
            $orderDetail->color = $product['product_color'];
            $orderDetail->price_per_unit = $product['unit_price'];
            $orderDetail->total_price = $product['total_price'];
            $orderDetail->vat_percent = $product['vat_percent'];
            $orderDetail->total_vat = $product['total_vat'];
            $orderDetail->total_price_with_vat = $product['total_price_with_vat'];
            $orderDetail->status = 1;
            $orderDetail->save();

            $stock = Stock::where('product_id', $product['product_id'])
                ->where('size', $product['product_size'])
                ->where('color', $product['product_color'])
                ->where('warehouse_id', $request->warehouse_id)
                ->first();
            if ($stock) {
                $stock->quantity -= $product['quantity'];
                $stock->save();
            } else {
                $stock = new Stock();
                $stock->warehouse_id = $request->warehouse_id;
                $stock->product_id = $product['product_id'];
                $stock->size = $product['product_size'];
                $stock->color = $product['product_color'];
                $stock->quantity = -$product['quantity'];
                $stock->created_by = auth()->user()->id;
                $stock->save();
            }
        }

        $encoded_order_id = base64_encode($order->id);
        $pdfUrl = route('in-house-sell.generate-pdf', ['encoded_order_id' => $encoded_order_id]);

        return response()->json([
            'pdf_url' => $pdfUrl,
            'message' => 'Order updated successfully'
        ], 200);
    }

}
