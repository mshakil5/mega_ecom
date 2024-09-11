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

class InHouseSellController extends Controller
{
    public function inHouseSell()
    {
        $customers = User::where('is_type', '0')->orderby('id','DESC')->get();
        $products = Product::orderby('id','DESC')->get();
        return view('admin.in_house_sell.create', compact('customers', 'products'));
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

        $netAmount = $itemTotalAmount - $validated['discount'];

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
        $order->order_type = 1;
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

            $stock = Stock::where('product_id', $product['product_id'])
                ->where('size', $product['product_size'])
                ->where('color', $product['product_color'])
                ->first();

            if ($stock) {
                $stock->quantity -= $product['quantity'];
                $stock->save();
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

    public function index()
    {
        $inHouseOrders = Order::with('user')
        ->where('order_type', 1) 
        ->orderBy('id', 'desc') 
        ->get();

        return view('admin.in_house_sell.index', compact('inHouseOrders'));
    }

}
