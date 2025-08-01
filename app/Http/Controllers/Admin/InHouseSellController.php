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
use App\Mail\QuotaionEmail;
use Illuminate\Support\Facades\Auth;

class InHouseSellController extends Controller
{
    public function inHouseSell()
    {
        // $products = Product::whereHas('stock', function ($query) {
        //     $query->where('quantity', '>', 0);
        // })
        // ->orderby('id','DESC')->select('id', 'name','price', 'product_code')->get();

        $products = Product::with(['stock', 'types:id,name'])->orderby('id','DESC')->select('id', 'name','price', 'product_code')->get();

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
            'invoice' => 'required',
            'warehouse_id' => 'required',
            'ref' => 'nullable|string',
            'remarks' => 'nullable|string',
            'discount' => 'nullable',
            'products' => 'required|json',
        ], [
            'user_id.required' => 'Please choose a wholesaler.',
            'user_id.exists' => 'Please choose a valid wholesaler.',
        ]);

        $products = json_decode($validated['products'], true);

        if(!$products){
            return response()->json(['message' => 'Please add at least one product'], 422);
        }

        $itemTotalAmount = array_reduce($products, function ($carry, $product) {
            return $carry + $product['total_price'];
        }, 0);

        $netAmount = $itemTotalAmount - $validated['discount'] + $request->vat;

        foreach ($products as $product) {
            $stock = Stock::where('product_id', $product['product_id'])
                ->where('size', $product['product_size'])
                ->where('color', $product['product_color'])
                ->where('zip', $product['zip'])
                ->where('warehouse_id', $request->warehouse_id)
                ->where('type_id', $product['type_id'])
                ->first();
        
            if (!$stock || $stock->quantity < $product['quantity']) {
                $warehouse = Warehouse::find($request->warehouse_id);
                return response()->json([
                    'message' => 'Not enough stock available for product ' . $product['product_name'] . 
                                 ' with size ' . $product['product_size'] . 
                                 ' and color ' . $product['product_color'] . 
                                 ' in warehouse: ' . ($warehouse->name ?? '') . 
                                 ' (Location: ' . ($warehouse->location ?? '') . ')',
                                 'stock_data' => $stock,
                ], 422);
            }
        } 

        $latestOrder = Order::where('invoice', 'like', "STL-{$request->invoice}-" . date('Y') . '-%')
        ->orderBy('invoice', 'desc')
        ->first();

        $nextNumber = $latestOrder ? (intval(substr($latestOrder->invoice, -5)) + 1) : 1;
        $invoice = "STL-{$request->invoice}-" . date('Y') . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        $order = new Order();
        $order->invoice = $invoice;
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
        $order->status = 2;
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
            $orderDetail->type_id = $product['type_id'] ?? null;
            $orderDetail->zip = $product['zip'];
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
                ->where('type_id', $product['type_id'])
                ->where('zip', $product['zip'])
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
                    $stock->zip = $product['zip'];
                    $stock->type_id = $product['type_id'];
                    $stock->quantity = -$product['quantity'];
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
            'invoice' => 'required',
            'ref' => 'nullable|string',
            'remarks' => 'nullable|string',
            'discount' => 'nullable',
            'products' => 'required|json',
            'warehouse_id' => 'nullable',
        ]);

        $products = json_decode($validated['products'], true);

        if(!$products){
            return response()->json(['message' => 'Please add at least one product'], 422);
        }

        $itemTotalAmount = array_reduce($products, function ($carry, $product) {
            return $carry + $product['total_price'];
        }, 0);

        $netAmount = $itemTotalAmount - $validated['discount'] + $request->vat;

        $latestOrder = Order::where('invoice', 'like', "STL-{$request->invoice}-" . date('Y') . '-%')
        ->orderBy('invoice', 'desc')
        ->first();

        $nextNumber = $latestOrder ? (intval(substr($latestOrder->invoice, -5)) + 1) : 1;
        $invoice = "STL-{$request->invoice}-" . date('Y') . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        $order = new Order();
        $order->invoice = $invoice;
        $order->purchase_date = $validated['purchase_date'];
        $order->user_id = $validated['user_id'];
        $order->payment_method = $validated['payment_method'];
        $order->ref = $validated['ref'];
        $order->remarks = $validated['remarks'];
        $order->discount_amount = $validated['discount'];
        $order->net_amount = $netAmount;
        $order->vat_amount = $request->vat;
        $order->subtotal_amount = $itemTotalAmount;
        $order->warehouse_id = $validated['warehouse_id'];
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
            $orderDetail->zip = $product['zip'];
            $orderDetail->type_id = $product['type_id'];
            $orderDetail->price_per_unit = $product['unit_price'];
            $orderDetail->total_price = $product['total_price'];
            $orderDetail->vat_percent = $product['vat_percent'];
            $orderDetail->total_vat = $product['total_vat'];
            $orderDetail->total_price_with_vat = $product['total_price_with_vat'];
            $orderDetail->status = 1;
            $orderDetail->save();
        }

        $encoded_order_id = base64_encode($order->id);
        $pdfUrl = route('orders.download-pdf', ['encoded_order_id' => $encoded_order_id]);

        // Mail::to($order->user->email)->send(new OrderConfirmation($order, $pdfUrl));

        // $contactEmails = ContactEmail::where('status', 1)->pluck('email');

        // foreach ($contactEmails as $email) {
        //     Mail::to($email)->send(new OrderConfirmation($order, $pdfUrl));
        // }

        return response()->json(['message' => 'Quotation created successfully', 'order_id' => $order->id], 201);
    }

    public function allquotations()
    {
        // $warehouseIds = json_decode(Auth::user()->warehouse_ids, true);

        $inHouseOrders = Order::with('user')
            ->where('order_type', 2)
            // ->when(!empty($warehouseIds), function ($query) use ($warehouseIds) {
            //     return $query->whereIn('warehouse_id', $warehouseIds);
            // })
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

        
        return response()->json([
            'in_stock' => $stock->quantity > 0,
            'stock_quantity' => $stock->quantity
        ]);
    }  
    
    
    public function getStock(Request $request)
    {
        $productId   = $request->input('product_id');
        $warehouseId = $request->input('warehouse_id');
        $selectedSize = $request->input('size');
        $selectedColor = $request->input('color');
        $typeId = $request->input('type_id');

        $getStock = Stock::with(['warehouse', 'product', 'type'])
            ->where('product_id', $productId)
            ->where('quantity', '>', 0)
            ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
            ->when($selectedSize, fn($q) => $q->where('size', $selectedSize))
            ->when($selectedColor, fn($q) => $q->where('color', $selectedColor))
            ->when($typeId, fn($q) => $q->where('type_id', $typeId))
            ->get();

        $getStockcount = $getStock->count();
        $totalQuantity = $getStock->sum('quantity');

        $prop = '';
        foreach ($getStock as $item) {
            $typeName = $item->type->name ?? '-';
            $prop .= '<tr>
                        <td>' . $item->product->product_code . '-' . $item->product->name . '</td>
                        <td>' . $item->warehouse->name . '</td>
                        <td>' . $item->size . '</td>
                        <td>' . $item->color . '</td>
                        <td>' . $typeName . '</td>
                        <td>' . intval($item->quantity) . '</td>
                      </tr>';
        }

        return response()->json([
            'stock' => $prop,
            'getStockcount' => $getStockcount,
            'totalQuantity' => $totalQuantity
        ]);
    }


    public function editOrder($orderId)
    {
        $order = Order::with(['user','orderDetails','transactions'])->findOrFail($orderId);
        $cashAmount = $order->transactions->where('payment_type', 'Cash')->first();
        $bankAmount = $order->transactions->where('payment_type', 'Bank')->first();
        $discountAmount = $order->transactions->where('transaction_type', 'Current')->where('discount', '>', 0)->first();
        $customers = User::where('is_type', '0')->where('status', 1)->orderby('id','asc')->get();
        // $products = Product::whereHas('stock', function ($query) {
        //     $query->where('quantity', '>', 0);
        // })
        // ->orderby('id','DESC')->select('id', 'name','price', 'product_code')->get();
        $products = Product::with(['stock', 'types:id,name'])->orderby('id','DESC')->select('id', 'name','price', 'product_code')->get();
        
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

        $products = json_decode($validated['products'], true);

        if(!$products){
            return response()->json(['message' => 'Please add at least one product'], 422);
        }

        foreach ($products as $product) {
            $previousQty = OrderDetails::where('order_id', $order->id)
                ->where('product_id', $product['product_id'])
                ->where('size', $product['product_size'])
                ->where('color', $product['product_color'])
                ->where('zip', $product['zip'])
                ->where('type_id', $product['type_id'])
                ->sum('quantity');

            $quantityDifference = $product['quantity'] - $previousQty;

            if ($order->order_type != 2) {
                if ($quantityDifference > 0) {
                    $stock = Stock::where('product_id', $product['product_id'])
                        ->where('size', $product['product_size'])
                        ->where('color', $product['product_color'])
                        ->where('zip', $product['zip'])
                        ->where('type_id', $product['type_id'])
                        ->where('warehouse_id', $request->warehouse_id)
                        ->first();
            
                    if (!$stock || $stock->quantity < $quantityDifference) {
                        $warehouse = Warehouse::find($request->warehouse_id);
                        return response()->json([
                            'message' => 'Not enough stock available for product ' . $product['product_name'] . 
                                            ' with size ' . $product['product_size'] . 
                                            ' and color ' . $product['product_color'] . 
                                            ' in warehouse: ' . ($warehouse->name ?? '') . 
                                            ' (Location: ' . ($warehouse->location ?? '') . ')',
                        ], 422);
                    }
                }
            } else {
                $stock = Stock::where('product_id', $product['product_id'])
                    ->where('size', $product['product_size'])
                    ->where('color', $product['product_color'])
                    ->where('zip', $product['zip'])
                    ->where('type_id', $product['type_id'])
                    ->where('warehouse_id', $request->warehouse_id)
                    ->first();
        
                if (!$stock || $stock->quantity < $product['quantity']) {
                    $warehouse = Warehouse::find($request->warehouse_id);
                    return response()->json([
                        'message' => 'Not enough stock available for product ' . $product['product_name'] . 
                                        ' with size ' . $product['product_size'] . 
                                        ' and color ' . $product['product_color'] . 
                                        ' in warehouse: ' . ($warehouse->name ?? '') . 
                                        ' (Location: ' . ($warehouse->location ?? '') . ')',
                    ], 422);
                }
            }
        }        

        $order = Order::findOrFail($validated['id']);

        $itemTotalAmount = array_reduce($products, function ($carry, $product) {
            return $carry + $product['total_price'];
        }, 0);

        $netAmount = $itemTotalAmount - $validated['discount'] + $request->vat;

        //Transaction Update
        if ($order->order_type != 2) {
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
        } else {
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
        }

        if ($order->order_type != 2) {
            $existingOrderDetails = OrderDetails::where('order_id', $order->id)->get();
            $existingOrderDetailIds = $existingOrderDetails->pluck('id')->toArray();
            $incomingOrderDetailIds = [];
        
            foreach ($products as $product) {
                if (isset($product['order_details_id'])) {
                    $orderDetail = OrderDetails::find($product['order_details_id']);
                    if ($orderDetail) {

                        $orderDetail->size = $product['product_size'];
                        $orderDetail->color = $product['product_color'];
                        $orderDetail->zip = $product['zip'];
                        $orderDetail->type_id = $product['type_id'];
                        $orderDetail->price_per_unit = $product['unit_price'];
                        $orderDetail->total_price = $product['total_price'];
                        $orderDetail->vat_percent = $product['vat_percent'];
                        $orderDetail->total_vat = $product['total_vat'];
                        $orderDetail->total_price_with_vat = $product['total_price_with_vat'];
                        $orderDetail->warehouse_id = $request->warehouse_id;
                        
                        $incomingOrderDetailIds[] = $orderDetail->id;
        
                        $stock = Stock::where('product_id', $product['product_id'])
                            ->where('size', $product['product_size'])
                            ->where('color', $product['product_color'])
                            ->where('zip', $product['zip'])
                            ->where('type_id', $product['type_id'])
                            ->where('warehouse_id', $request->warehouse_id)
                            ->first();
        
                        if ($stock) {
                            $quantityDifference = $product['quantity'] - $orderDetail->quantity;
                            if ($quantityDifference < 0) {
                                $stock->quantity += abs($quantityDifference);
                                $stock->save();
                            } else {
                                $stock->quantity -= $quantityDifference;
                                $stock->save();
                            }

                            if ($quantityDifference != 0) {

                                $latestStockHistory = StockHistory::where('stock_id', $stock->id)
                                ->orderBy('created_at', 'desc')
                                ->first();

                                if ($latestStockHistory) {
                                    if ($quantityDifference < 0) {
                                        $latestStockHistory->available_qty += abs($quantityDifference);
                                        $latestStockHistory->selling_qty -= abs($quantityDifference);
                                    } else {
                                        $latestStockHistory->available_qty -= $quantityDifference;
                                        $latestStockHistory->selling_qty += $quantityDifference;
                                    }
                                    $latestStockHistory->save();
                                }
                            }
                        }
                        $orderDetail->quantity = $product['quantity'];
                        $orderDetail->save();
                    }
                } else {
                    $orderDetail = new OrderDetails();
                    $orderDetail->order_id = $order->id;
                    $orderDetail->warehouse_id = $request->warehouse_id;
                    $orderDetail->product_id = $product['product_id'];
                    $orderDetail->quantity = $product['quantity'];
                    $orderDetail->size = $product['product_size'];
                    $orderDetail->color = $product['product_color'];
                    $orderDetail->zip = $product['zip'];
                    $orderDetail->type_id = $product['type_id'];
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
                    ->where('zip', $product['zip'])
                    ->where('type_id', $product['type_id'])
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
                        $stock->zip = $product['zip'];
                        $stock->type_id = $product['type_id'];
                        $stock->quantity = -$product['quantity'];
                        $stock->created_by = auth()->user()->id;
                        $stock->save();
                    }

                    $stockHistories = StockHistory::where('stock_id', $stock->id)
                        ->where('available_qty', '>', 0)
                        ->orderBy('created_at', 'asc')
                        ->get();

                    $requiredQty = $product['quantity'];
                    
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
        
            foreach ($existingOrderDetails as $existingOrderDetail) {
                if (!in_array($existingOrderDetail->id, $incomingOrderDetailIds)) {
                    $stock = Stock::where('product_id', $existingOrderDetail->product_id)
                        ->where('size', $existingOrderDetail->size)
                        ->where('color', $existingOrderDetail->color)
                        ->where('zip', $existingOrderDetail->zip)
                        ->where('type_id', $existingOrderDetail->type_id)
                        ->where('warehouse_id', $request->warehouse_id)
                        ->first();
            
                    if ($stock) {
                        $stock->quantity += $existingOrderDetail->quantity;
                        $stock->save();
            
                        $latestStockHistory = StockHistory::where('stock_id', $stock->id)
                            ->orderBy('created_at', 'desc')
                            ->first();
            
                        if ($latestStockHistory) {
                            $latestStockHistory->available_qty += $existingOrderDetail->quantity;
                            $latestStockHistory->selling_qty -= $existingOrderDetail->quantity;
                            $latestStockHistory->save();
                        }
                    }
                    $existingOrderDetail->delete();
                }
            }
        }

        else {
            // For order type 2 - Order Details and Stock Handling
            $existingOrderDetails = OrderDetails::where('order_id', $order->id)->get();
            $existingOrderDetailIds = $existingOrderDetails->pluck('id')->toArray();
            $incomingOrderDetailIds = [];

            foreach ($products as $product) {
                // Handle Order Details
                if (isset($product['order_details_id'])) {
                    $orderDetail = OrderDetails::find($product['order_details_id']);
                    if ($orderDetail) {
                        $orderDetail->update([
                            'size' => $product['product_size'],
                            'color' => $product['product_color'],
                            'zip' => $product['zip'],
                            'type_id' => $product['type_id'],
                            'price_per_unit' => $product['unit_price'],
                            'total_price' => $product['total_price'],
                            'vat_percent' => $product['vat_percent'],
                            'total_vat' => $product['total_vat'],
                            'total_price_with_vat' => $product['total_price_with_vat'],
                            'warehouse_id' => $request->warehouse_id,
                            'quantity' => $product['quantity']
                        ]);
                        $incomingOrderDetailIds[] = $orderDetail->id;
                    }
                } else {
                    $orderDetail = OrderDetails::create([
                        'order_id' => $order->id,
                        'warehouse_id' => $request->warehouse_id,
                        'product_id' => $product['product_id'],
                        'quantity' => $product['quantity'],
                        'size' => $product['product_size'],
                        'color' => $product['product_color'],
                        'zip' => $product['zip'],
                        'type_id' => $product['type_id'],
                        'price_per_unit' => $product['unit_price'],
                        'total_price' => $product['total_price'],
                        'vat_percent' => $product['vat_percent'],
                        'total_vat' => $product['total_vat'],
                        'total_price_with_vat' => $product['total_price_with_vat'],
                        'status' => 1
                    ]);
                }

                // Handle Stock for each product (moved inside the loop)
                $requiredQty = $product['quantity'];
                if ($request->warehouse_id) {
                    $stock = Stock::where('product_id', $product['product_id'])
                        ->where('size', $product['product_size'])
                        ->where('color', $product['product_color'])
                        ->where('zip', $product['zip'])
                        ->where('type_id', $product['type_id'])
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
                        $stock->zip = $product['zip'];
                        $stock->type_id = $product['type_id'];
                        $stock->quantity = -$product['quantity'];
                        $stock->created_by = auth()->user()->id;
                        $stock->save();
                    }

                    $stockHistories = StockHistory::where('stock_id', $stock->id)
                        ->where('available_qty', '>', 0)
                        ->orderBy('created_at', 'asc')
                        ->get();

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
            }

            // Clean up removed order details
            foreach ($existingOrderDetails as $existingOrderDetail) {
                if (!in_array($existingOrderDetail->id, $incomingOrderDetailIds)) {
                    $existingOrderDetail->delete();
                }
            }
        }

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
        $order->warehouse_id = $request->warehouse_id;
        $order->status = 2;
        if ($order->order_type != 0) {
            $order->order_type = 1;
            $order->save();
        }
        $order->save();    

        $encoded_order_id = base64_encode($order->id);
        $pdfUrl = route('in-house-sell.generate-pdf', ['encoded_order_id' => $encoded_order_id]);

        return response()->json([
            'pdf_url' => $pdfUrl,
            'message' => 'Order updated successfully'
        ], 200);
    }

    public function quotationEmailForm($orderId)
    {
        $order = Order::find($orderId);

        return view('admin.in_house_sell.quotation_mail', compact('order'));
    }

    public function sendQuotationEmail(Request $request, $orderId)
    {
        $order = Order::find($orderId);

        $suject = $request->subject ?? "Quotation";
        $body = $request->body ?? "Thank you for requesting a quotation from us. Please find the details of your quotation below.";

        $admin_mail = ContactEmail::orderby('id', 'DESC')->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }


        $user = $order->user;
        $downloadLink = route('orders.download-pdf', ['encoded_order_id' => base64_encode($order->id)]);

        Mail::to($user->email)
            ->cc($admin_mail->email)
            ->send(new QuotaionEmail($order, $downloadLink, $suject, $body));

        return redirect()->back()->with('success', 'Email sent successfully');
    }

    public function generateDeliveryNote($encoded_order_id)
    {
        $order_id = base64_decode($encoded_order_id);
        $order = Order::with(['orderDetails', 'user'])->findOrFail($order_id);

        $data = [
            'order' => $order,
            'currency' => CompanyDetails::value('currency'),
        ];

        $pdf = PDF::loadView('admin.in_house_sell.in_house_sell_delivery_note', $data);

        return $pdf->stream('order_' . $order->id . '.pdf');
    }

}
