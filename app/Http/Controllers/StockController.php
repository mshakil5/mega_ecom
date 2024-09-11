<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use App\Models\PurchaseHistory;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseReturn;
use DataTables;
use App\Models\SystemLose;
use App\Models\OrderReturn;

class StockController extends Controller
{
    public function getStock()
    {
        return view('admin.stock.index');
    }

    public function getStocks()
    {
       $data = Stock::orderBy('id', 'DESC')->get();

        return DataTables::of($data)
            ->addColumn('sl', function($row) {
                static $i = 1;
                return $i++;
            })
            ->addColumn('product_name', function ($row) {
                return $row->product ? $row->product->name : 'N/A';
            })
            ->addColumn('quantity_formatted', function ($row) {
                return number_format($row->quantity, 0);
            })
            ->addColumn('action', function ($row) {
            return '<button class="btn btn-sm btn-danger" onclick="openLossModal('.$row->id.')">System Loss</button>';
            })
            ->rawColumns(['action'])

            ->make(true);
    }

    public function addstock()
    {
        $products = Product::orderby('id','DESC')->get();
        $suppliers = Supplier::orderby('id','DESC')->get();
        return view('admin.stock.create', compact('products', 'suppliers'));
    }

    public function stockStore(Request $request)
    {

        $validatedData = $request->validate([
            'invoice' => 'required',
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'purchase_type' => 'required',
            'ref' => 'nullable|string',
            'vat_reg' => 'nullable|string',
            'remarks' => 'nullable|string',
            'total_amount' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'total_vat_amount' => 'required|numeric',
            'net_amount' => 'required|numeric',
            'due_amount' => 'required|numeric',
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.product_size' => 'nullable|string',
            'products.*.product_color' => 'nullable|string',
            'products.*.unit_price' => 'required|numeric',
            'products.*.vat_percent' => 'required|numeric',
            'products.*.vat_amount' => 'required|numeric',
            'products.*.total_price_with_vat' => 'required|numeric',
        ]);

        $data = $request->all();
        $purchase = new Purchase();
        $purchase->invoice = $request->invoice;
        $purchase->supplier_id = $request->supplier_id;
        $purchase->purchase_date = $request->purchase_date;
        $purchase->purchase_type = $request->purchase_type;
        $purchase->ref = $request->ref;
        $purchase->vat_reg = $request->vat_reg;
        $purchase->remarks = $request->remarks;
        $purchase->total_amount = $request->total_amount;
        $purchase->discount = $request->discount;
        $purchase->total_vat_amount = $request->total_vat_amount;
        $purchase->net_amount = $request->net_amount;
        $purchase->paid_amount = $request->paid_amount;
        $purchase->due_amount = $request->due_amount;
        $purchase->created_by = Auth::user()->id;
        $purchase->save();

        $supplier = Supplier::find($request->supplier_id);
        if ($supplier) {
            $supplier->balance -= $request->due_amount;
            $supplier->save();
        }

        foreach ($request->products as $product) {
            $purchaseHistory = new PurchaseHistory();
            $purchaseHistory->purchase_id = $purchase->id;
            $purchaseHistory->product_id = $product['product_id'];
            $purchaseHistory->quantity = $product['quantity'];
            $purchaseHistory->product_size = $product['product_size'];
            $purchaseHistory->product_color = $product['product_color'];
            $purchaseHistory->purchase_price = $product['unit_price'];
            $purchaseHistory->vat_percent = $product['vat_percent'];
            $purchaseHistory->vat_amount_per_unit = $product['vat_amount'] / $product['quantity'];
            $purchaseHistory->total_vat = $purchaseHistory->vat_amount_per_unit * $product['quantity'];
            $purchaseHistory->total_amount = $product['unit_price'] * $product['quantity'];
            $purchaseHistory->total_amount_with_vat = $product['total_price_with_vat'];

            $purchaseHistory->created_by = Auth::user()->id;
            $purchaseHistory->save();

            $existingProduct = Product::find($product['product_id']);
            if ($existingProduct){   
                $existingProduct->price = $product['unit_price'];
                $existingProduct->save();
            }

            $stock = Stock::where('product_id', $product['product_id'])
                      ->where('size', $product['product_size'])
                      ->where('color', $product['product_color'])
                      ->first();

            if ($stock) {
                $stock->quantity += $product['quantity'];
                $stock->updated_by = Auth::user()->id;
                $stock->save();
            } else {
                $newStock = new Stock();
                $newStock->product_id = $product['product_id'];
                $newStock->quantity = $product['quantity'];
                $newStock->size = $product['product_size'];
                $newStock->color = $product['product_color'];
                $newStock->created_by = Auth::user()->id;
                $newStock->save();
            }

        }

        return response()->json([
            'status' => 'success',
            'message' => 'Stock Added Successfully',
        ]);
    }

    public function productPurchaseHistory()
    {
        $purchases = Purchase::with('purchaseHistory.product','supplier')->orderby('id','DESC')->get();
        return view('admin.stock.purchase_history', compact('purchases'));
    }

    public function getPurchaseHistory(Purchase $purchase)
    {
        $purchase = Purchase::with(['supplier', 'purchaseHistory.product'])
            ->select([
                'id', 
                'purchase_date', 
                'invoice', 
                'supplier_id', 
                'purchase_type', 
                'ref', 
                'net_amount', 
                'paid_amount', 
                'due_amount'
            ])
            ->findOrFail($purchase->id);

        return response()->json($purchase);
    }

    public function editPurchaseHistory(Purchase $purchase)
    {
        $purchase = Purchase::with('supplier', 'purchaseHistory.product')->findOrFail($purchase->id);
        $products = Product::orderby('id','DESC')->get();
        $suppliers = Supplier::orderby('id','DESC')->get();
        return view('admin.stock.edit_purchase_history', compact('purchase', 'products', 'suppliers'));
    }

    public function stockUpdate(Request $request)
    {
        $purchase = Purchase::find($request->purchase_id);

        if (!$purchase) {
            return response()->json([
                'status' => 'error',
                'message' => 'Purchase not found.',
            ], 404);
        }

        $existingPurchaseHistoryIds = $purchase->purchaseHistory->pluck('id')->toArray();
        $updatedPurchaseHistoryIds = array_column($request->products, 'purchase_history_id');

        $removedPurchaseHistoryIds = array_diff($existingPurchaseHistoryIds, $updatedPurchaseHistoryIds);

        foreach ($removedPurchaseHistoryIds as $removedId) {
            $purchaseHistory = PurchaseHistory::find($removedId);
            if ($purchaseHistory) {

                $stock = Stock::where('product_id', $purchaseHistory->product_id)
                            ->where('size', $purchaseHistory->product_size)
                            ->where('color', $purchaseHistory->product_color)
                            ->first();

                if ($stock) {
                    $stock->quantity -= $purchaseHistory->quantity;
                    $stock->updated_by = Auth::user()->id;
                    $stock->save();
                }

                $purchaseHistory->delete();
            }
        }

        $totalAmount = 0;
        $totalVatAmount = 0;
        $discount = $request->discount;

        foreach ($request->products as $product) {
            if (isset($product['purchase_history_id'])) {
                $purchaseHistory = PurchaseHistory::find($product['purchase_history_id']);
                if ($purchaseHistory) {
                    $stock = Stock::where('product_id', $purchaseHistory->product_id)
                                ->where('size', $purchaseHistory->product_size)
                                ->where('color', $purchaseHistory->product_color)
                                ->first();

                    if ($stock) {
                        $stock->quantity -= $purchaseHistory->quantity;
                    }

                    $purchaseHistory->product_id = $product['product_id'];
                    $purchaseHistory->quantity = $product['quantity'];
                    $purchaseHistory->product_size = $product['product_size'];
                    $purchaseHistory->product_color = $product['product_color'];
                    $purchaseHistory->purchase_price = $product['unit_price'];
                    $purchaseHistory->vat_percent = $product['vat_percent'];
                    $purchaseHistory->vat_amount_per_unit = $product['vat_amount'] / $product['quantity'];
                    $purchaseHistory->total_vat = $purchaseHistory->vat_amount_per_unit * $product['quantity'];
                    $purchaseHistory->total_amount = $product['unit_price'] * $product['quantity'];
                    $purchaseHistory->total_amount_with_vat = $product['total_price_with_vat'];
                    $purchaseHistory->updated_by = Auth::user()->id;
                    $purchaseHistory->save();

                    if ($stock) {
                        $stock->quantity += $product['quantity'];
                        $stock->save();
                    }
                }
            } else {
                $purchaseHistory = new PurchaseHistory();
                $purchaseHistory->purchase_id = $request->purchase_id;
                $purchaseHistory->product_id = $product['product_id'];
                $purchaseHistory->quantity = $product['quantity'];
                $purchaseHistory->product_size = $product['product_size'];
                $purchaseHistory->product_color = $product['product_color'];
                $purchaseHistory->purchase_price = $product['unit_price'];
                $purchaseHistory->vat_percent = $product['vat_percent'];
                $purchaseHistory->vat_amount_per_unit = $product['vat_amount'] / $product['quantity'];
                $purchaseHistory->total_vat = $product['vat_amount'];
                $purchaseHistory->total_amount = $product['unit_price'] * $product['quantity'];
                $purchaseHistory->total_amount_with_vat = $product['total_price_with_vat'];
                $purchaseHistory->created_by = Auth::user()->id;
                $purchaseHistory->save();

                $stock = Stock::where('product_id', $product['product_id'])
                            ->where('size', $product['product_size'])
                            ->where('color', $product['product_color'])
                            ->first();

                if ($stock) {
                    $stock->quantity += $product['quantity'];
                    $stock->updated_by = Auth::user()->id;
                    $stock->save();
                } else {
                    $newStock = new Stock();
                    $newStock->product_id = $product['product_id'];
                    $newStock->quantity = $product['quantity'];
                    $newStock->size = $product['product_size'];
                    $newStock->color = $product['product_color'];
                    $newStock->created_by = Auth::user()->id;
                    $newStock->save();
                }
            }

            $totalAmount += $purchaseHistory->total_amount;
            $totalVatAmount += $purchaseHistory->total_vat;
        }


        $netAmount = $totalAmount + $totalVatAmount - $discount;
        $paidAmount = $request->paid_amount;
        $dueAmount = $netAmount - $paidAmount;

        $purchase->invoice = $request->invoice;
        $purchase->supplier_id = $request->supplier_id;
        $purchase->purchase_date = $request->purchase_date;
        $purchase->purchase_type = $request->purchase_type;
        $purchase->ref = $request->ref;
        $purchase->vat_reg = $request->vat_reg;
        $purchase->remarks = $request->remarks;
        $purchase->total_amount = $totalAmount;
        $purchase->discount = $discount;
        $purchase->total_vat_amount = $totalVatAmount;
        $purchase->net_amount = $netAmount;
        $purchase->paid_amount = $paidAmount;
        $purchase->due_amount = $dueAmount;
        $purchase->updated_by = Auth::user()->id;
        $purchase->save();

        $supplier = Supplier::find($request->supplier_id);
        if ($supplier) {
            $supplier->balance = $supplier->balance - $dueAmount + $request->previous_purchase_due;
            $supplier->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Stock Updated Successfully',
        ]);
    }

    public function returnProduct(Purchase $purchase)
    {
        $products = Product::orderby('id','DESC')->get();
        $suppliers = Supplier::orderby('id','DESC')->get();
        $purchase = Purchase::with('supplier', 'purchaseHistory.product')->findOrFail($purchase->id);
        return view('admin.stock.return_product', compact('purchase', 'products', 'suppliers'));
    }

    public function returnStore(Request $request)
    {
        DB::transaction(function () use ($request) {

            $date = $request->date;
            $reason = $request->reason;
            $supplierId = $request->supplierId;
            $products = $request->products;

            foreach ($products as $product) {
                $purchaseReturn = new PurchaseReturn();
                $purchaseReturn->date = $date;
                $purchaseReturn->reason = $reason;
                $purchaseReturn->supplier_id = $supplierId;
                $purchaseReturn->purchase_history_id = $product['purchase_history_id'];
                $purchaseReturn->product_id = $product['product_id'];
                $purchaseReturn->return_quantity = $product['return_quantity'];
                $purchaseReturn->status = 'pending'; 
                $purchaseReturn->created_by = auth()->user()->id;
                $purchaseReturn->save();

                $product_id = $product['product_id'];
                $return_quantity = $product['return_quantity'];

                $stock = Stock::where('product_id', $product_id);

                if (isset($product['size']) && isset($product['color'])) {
                    $stock->where('size', $product['size'])
                        ->where('color', $product['color']);
                }

                $stock->decrement('quantity', $return_quantity);

            }
        });

        return response()->json(['message' => 'Purchase return saved successfully'], 200);
    }

    public function stockReturnHistory()
    {
        $purchaseReturns = PurchaseReturn::with('product') ->orderBy('id', 'desc')->get();
        return view('admin.stock.stock_return_history', compact('purchaseReturns'));
    }

    public function processSystemLoss(Request $request)
    {
        $validatedData = $request->validate([
            'productId' => 'required|exists:stocks,product_id', 
            'lossQuantity' => 'required|numeric|min:1', 
            'lossReason' => 'nullable|string|max:255',
        ]);

        $systemLoss = new SystemLose();
        $systemLoss->product_id = $validatedData['productId'];
        $systemLoss->quantity = $validatedData['lossQuantity'];
        $systemLoss->reason = $validatedData['lossReason'];
        $systemLoss->created_by = Auth::user()->id;
        $systemLoss->save();


        $stock = Stock::where('product_id', $validatedData['productId'])->first();

        if (!$stock) {
            return response()->json(['message' => 'Stock record not found.'], 404);
        }

        if ($validatedData['lossQuantity'] > $stock->quantity) {
            return response()->json(['message' => 'Loss quantity cannot be more than current stock quantity.'], 422);
        }

        $newQuantity = $stock->quantity - $validatedData['lossQuantity'];
        $stock->update(['quantity' => $newQuantity]);

        return response()->json(['message' => 'System loss processed successfully.']);
    }

    public function systemLosses()
    {
        $systemLosses = SystemLose::with('product')->latest()->get();
        return view('admin.stock.system_losses', compact('systemLosses'));
    }

    public function sendToStock(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
        ]);

        $product_id = $request->input('product_id');
        $quantity = $request->input('quantity');

        $stock = Stock::where('product_id', $product_id)->first();

        if ($stock) {
            $stock->quantity += $quantity;
            $stock->updated_by = auth()->user()->id;
            $stock->save();
        } else {
            $newStock = new Stock();
            $newStock->product_id = $product_id;
            $newStock->quantity = $quantity;
            $newStock->created_by = auth()->user()->id;
            $newStock->save();
        }

        $orderReturn = OrderReturn::where('product_id', $product_id)
            ->where('order_id', $request->order_id)
            ->first();

        if ($orderReturn) {
            $orderReturn->new_quantity -= $quantity;
            $orderReturn->return_stock = $quantity;
            $orderReturn->save();
        }


        return redirect()->back()->with('success', 'Stock updated successfully.');
    }

    public function sendToSystemLose(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
        ]);

        $product_id = $request->input('product_id');
        $quantity = $request->input('quantity');


        $systemLoss = new SystemLose();
        $systemLoss->product_id = $product_id;
        $systemLoss->order_id = $request->order_id;
        $systemLoss->quantity = $quantity;
        $systemLoss->reason = $request->input('reason');
        $systemLoss->created_by = auth()->user()->id;
        $systemLoss->save();


        $orderReturn = OrderReturn::where('product_id', $product_id)
            ->where('order_id', $request->order_id)
            ->first();
            

        if ($orderReturn) {
            $orderReturn->new_quantity -= $quantity;
            $orderReturn->system_lose = $quantity;
            $orderReturn->save();
        }

        return redirect()->back()->with('success', 'Sent to system lose successfully.');
    }

}
