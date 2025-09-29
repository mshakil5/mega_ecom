<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\User;
use App\Models\Stock;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\OrderDetails;
use App\Models\StockHistory;

class WholesaleController extends Controller
{

    public function index()
    {
        $inHouseOrders = Order::with('user')
            ->where('order_type', 3)
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.in_house_sell.quotations', compact('inHouseOrders'));
    }

    public function create()
    {
        $products = Product::with(['stock', 'types:id,name'])->orderby('id','DESC')->select('id', 'name','price', 'product_code')->get();
        $warehouses = Warehouse::select('id', 'name','location')->where('status', 1)->get();
        $customers = User::where('is_type', '0')->where('status', 1)->orderby('id','DESC')->get();
        return view('admin.whole_sale.create', compact('customers', 'products','warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'purchase_date' => 'required|date',
            'user_id' => 'required|exists:users,id',
            'payment_method' => 'required|string',
            'invoice' => 'required',
            'warehouse_id' => 'required',
            'ref' => 'nullable|string',
            'remarks' => 'nullable|string',
            'discount' => 'nullable|numeric',
            'vat' => 'nullable|numeric',
            'cash_payment' => 'nullable|numeric',
            'bank_payment' => 'nullable|numeric',
            'products' => 'required|json',
        ], [
            'user_id.required' => 'Please choose a wholesaler.',
            'user_id.exists' => 'Please choose a valid wholesaler.',
        ]);

        $products = json_decode($validated['products'], true);
        if(!$products){
            return response()->json(['message' => 'Please add at least one product'], 422);
        }

        $itemTotalAmount = array_reduce($products, fn($carry, $p) => $carry + $p['total_price'], 0);
        $netAmount = $itemTotalAmount - ($validated['discount'] ?? 0) + ($validated['vat'] ?? 0);

        // Generate invoice
        $latestOrder = Order::where('invoice', 'like', "STL-{$request->invoice}-" . date('Y') . '-%')
            ->orderBy('invoice', 'desc')->first();
        $nextNumber = $latestOrder ? (intval(substr($latestOrder->invoice, -5)) + 1) : 1;
        $invoice = "STL-{$request->invoice}-" . date('Y') . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        // Create order
        $order = new Order();
        $order->invoice = $invoice;
        $order->warehouse_id = $request->warehouse_id;
        $order->purchase_date = $validated['purchase_date'];
        $order->user_id = $validated['user_id'];
        $order->payment_method = $validated['payment_method'];
        $order->ref = $validated['ref'];
        $order->remarks = $validated['remarks'];
        $order->discount_amount = $validated['discount'] ?? 0;
        $order->net_amount = $netAmount;
        $order->vat_amount = $validated['vat'] ?? 0;
        $order->paid_amount = ($validated['cash_payment'] ?? 0) + ($validated['bank_payment'] ?? 0);
        $order->due_amount = $netAmount - $order->paid_amount;
        $order->subtotal_amount = $itemTotalAmount;
        $order->order_type = 3; // wholesale
        $order->status = 2;
        $order->save();

        // Transaction for total
        $transaction = new Transaction();
        $transaction->date = $validated['purchase_date'];
        $transaction->customer_id = $validated['user_id'];
        $transaction->order_id = $order->id;
        $transaction->table_type = "Sales";
        $transaction->ref = $validated['ref'];
        $transaction->payment_type = "Credit";
        $transaction->transaction_type = "Current";
        $transaction->amount = $itemTotalAmount;
        $transaction->vat_amount = $validated['vat'] ?? 0;
        $transaction->discount = $validated['discount'] ?? 0;
        $transaction->at_amount = $netAmount;
        $transaction->tran_id = 'SL' . date('Ymd') . str_pad($transaction->id ?? 1, 4, '0', STR_PAD_LEFT);
        $transaction->save();

        // Cash & Bank payments
        foreach (['cash_payment' => 'Cash', 'bank_payment' => 'Bank'] as $key => $type) {
            if(!empty($validated[$key]) && $validated[$key] > 0){
                $pay = new Transaction();
                $pay->date = $validated['purchase_date'];
                $pay->customer_id = $validated['user_id'];
                $pay->order_id = $order->id;
                $pay->table_type = "Sales";
                $pay->ref = $validated['ref'];
                $pay->payment_type = $type;
                $pay->transaction_type = "Received";
                $pay->amount = $validated[$key];
                $pay->at_amount = $validated[$key];
                $pay->tran_id = 'SL' . date('Ymd') . str_pad($pay->id ?? 1, 4, '0', STR_PAD_LEFT);
                $pay->save();
            }
        }

        // Order details & stock update
        foreach ($products as $product) {
            $orderDetail = new OrderDetails();
            $orderDetail->order_id = $order->id;
            $orderDetail->warehouse_id = $request->warehouse_id;
            $orderDetail->product_id = $product['product_id'];
            $orderDetail->quantity = $product['quantity'];
            $orderDetail->size = $product['product_size'];
            $orderDetail->color = $product['product_color'];
            $orderDetail->type_id = $product['type_id'] ?? null;
            $orderDetail->price_per_unit = $product['unit_price'];
            $orderDetail->total_price = $product['total_price'];
            $orderDetail->vat_percent = $product['vat_percent'];
            $orderDetail->total_vat = $product['total_vat'];
            $orderDetail->total_price_with_vat = $product['total_price_with_vat'];
            $orderDetail->status = 1;
            $orderDetail->save();

            // Reduce stock
            $requiredQty = $product['quantity'];
            $stock = Stock::where([
                ['product_id', $product['product_id']],
                ['size', $product['product_size']],
                ['color', $product['product_color']],
                ['type_id', $product['type_id']],
                ['warehouse_id', $request->warehouse_id],
            ])->first();

            if($stock){
                $stock->quantity -= $requiredQty;
                $stock->save();

                $histories = StockHistory::where('stock_id', $stock->id)
                    ->where('available_qty', '>', 0)
                    ->orderBy('created_at','asc')->get();

                foreach($histories as $history){
                    if($requiredQty <= 0) break;

                    if($history->available_qty >= $requiredQty){
                        $history->available_qty -= $requiredQty;
                        $history->selling_qty += $requiredQty;
                        $history->save();
                        $requiredQty = 0;
                    } else {
                        $requiredQty -= $history->available_qty;
                        $history->selling_qty += $history->available_qty;
                        $history->available_qty = 0;
                        $history->save();
                    }
                }
            }
        }

        $encoded_order_id = base64_encode($order->id);
        $pdfUrl = route('in-house-sell.generate-pdf', ['encoded_order_id' => $encoded_order_id]);

        return response()->json([
            'pdf_url' => $pdfUrl,
            'message' => 'Wholesale order placed successfully'
        ]);
    }

    public function getProductRows(Request $request)
    {
        $request->validate([
            'product_id'   => 'required|integer',
            'warehouse_id' => 'required|integer',
        ]);

        $productId   = $request->input('product_id');
        $warehouseId = $request->input('warehouse_id');

        $getStock = Stock::with(['warehouse', 'product', 'type'])
            ->where('product_id', $productId)
            ->where('quantity', '>', 0)
            ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
            ->get();

        $rows = [];
        foreach ($getStock as $item) {
            $rows[] = [
                'product_id'        => $item->product_id,
                'product_code'      => $item->product->product_code ?? '',
                'product_name'      => $item->product->name ?? '',
                'type_id'           => $item->type_id ?? '',
                'type_name'         => $item->type->name ?? '',
                'size'              => $item->size,
                'color'             => $item->color,
                'max_quantity'      => intval($item->quantity),
                // pricing from stock record (fall back to 0)
                'selling_price'     => floatval($item->selling_price ?? 0),
                'ground_price'      => floatval($item->ground_price_per_unit ?? 0),
                'profit_margin'     => floatval($item->profit_margin ?? 0),
                'considerable_margin'=> floatval($item->considerable_margin ?? 0),
                'considerable_price'=> floatval($item->considerable_price ?? 0),
            ];
        }

        return response()->json(['rows' => $rows]);
    }

}
