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
use App\Models\Warehouse;
use App\Models\Transaction;

class InHouseSellController extends Controller
{
    public function inHouseSell()
    {
        $customers = User::where('is_type', '0')->orderby('id','DESC')->get();
        $products = Product::orderby('id','DESC')->get();
        $colors = Color::orderby('id','DESC')->get();
        $sizes = Size::orderby('id','DESC')->get();
        $warehouses = Warehouse::select('id', 'name','location')->where('status', 1)->get();
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
        $transaction->amount = $netAmount;
        $transaction->vat_amount = $request->vat;
        $transaction->discount = $validated['discount'] ?? 0.00;
        $transaction->at_amount = $itemTotalAmount;
        $transaction->save();
        $transaction->tran_id = 'SL' . date('Ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
        $transaction->save();

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
            $orderDetail->status = 1;
            $orderDetail->save();

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
            }
            
        }

        $encoded_order_id = base64_encode($order->id);
        $pdfUrl = route('in-house-sell.generate-pdf', ['encoded_order_id' => $encoded_order_id]);

        return response()->json([
            'pdf_url' => $pdfUrl,
            'message' => 'Order placed successfully'
        ], 200);

        return response()->json(['message' => 'Order created successfully', 'order_id' => $order->id], 201);
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
            $orderDetail->status = 1;
            $orderDetail->save();
        }

        return response()->json(['message' => 'Quotation created successfully', 'order_id' => $order->id], 201);
    }

    public function allquotations()
    {
        $inHouseOrders = Order::with('user')
        ->where('order_type', 1) 
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

}
