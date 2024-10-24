<?php

namespace App\Http\Controllers;

use App\Models\Color;
use App\Models\OrderDetails;
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
use App\Models\Size;
use App\Models\StockHistory;
use App\Models\SupplierTransaction;
use App\Models\Transaction;
use App\Models\Warehouse;
use Carbon\Carbon;

class StockController extends Controller
{
    public function getStock()
    {
        $products = Product::select('id','name','product_code')->orderBy('id', 'DESC')->get();
        $warehouses = Warehouse::select('id', 'name','location')->where('status', 1)->get();
        return view('admin.stock.index', compact('warehouses','products'));
    }

    public function getStocks(Request $request)
    {
        // $query = Stock::query();
        $query = Stock::select('product_id', 'size','color',  \DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_id', 'size','color');
        if ($request->has('warehouse_id') && $request->warehouse_id != '') {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->has('product_id') && $request->product_id != '') {
            $query->where('product_id', $request->product_id);
        }
       $data = $query->orderBy('id', 'DESC')->get();
       
        return DataTables::of($data)
            ->addColumn('sl', function($row) {
                static $i = 1;
                return $i++;
            })
            ->addColumn('product_name', function ($row) {
                return $row->product ? $row->product->name : 'N/A';
            })
            ->addColumn('product_code', function ($row) {
                return $row->product ? $row->product->product_code : 'N/A';
            })
            ->addColumn('quantity_formatted', function ($row) {
                return $row->total_quantity ? number_format($row->total_quantity, 0) : 'N/A';
            })
            // ->addColumn('warehouse', function ($row) {
            //     $warehouseDtl = '<b>'.$row->warehouse ? $row->warehouse->name .'-'. $row->warehouse->location : 'N/A'.'</b>';
            //     return $warehouseDtl;
            // })
            // ->addColumn('action', function ($row) {
            // return '<button class="btn btn-sm btn-danger" onclick="openLossModal('.$row->id.')">System Loss</button>';
            // })
            ->addColumn('action', function ($data) {
                $btn = '<div class="table-actions"> <button class="btn btn-sm btn-danger btn-open-loss-modal" data-size="'.$data->size.'" data-color="'.$data->color.'" data-id="'.$data->product->id.'" >System Loss</button> ';  
                if (Auth::user()) {
                    $url = route('admin.product.purchasehistory', ['id' => $data->product->id, 'size' => $data->size, 'color' => $data->color]);
                    $btn .= '<a href="'.$url.'" class="btn btn-sm btn-primary">History</a>';
                }
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function stockingHistory()
    {
        $products = Product::select('id','name','product_code')->orderBy('id', 'DESC')->get();
        $warehouses = Warehouse::select('id', 'name','location')->where('status', 1)->get();

        return view('admin.stock.stockhistory', compact('warehouses','products'));
    }

    public function getStockingHistory(Request $request)
    {
        
        
        $query = StockHistory::select('date', 'stockid', 'purchase_id', 'product_id', 'stock_id', 'warehouse_id', 'quantity', 'selling_qty', 'size','color','systemloss_qty','purchase_price');
        if ($request->has('warehouse_id') && $request->warehouse_id != '') {
            $query->where('warehouse_id', $request->warehouse_id);
        }
        if ($request->has('product_id') && $request->product_id != '') {
            $query->where('product_id', $request->product_id);
        }
        $data = $query->orderBy('id', 'DESC')->get();

        return DataTables::of($data)
            ->addColumn('sl', function($row) {
                static $i = 1;
                return $i++;
            })
            ->addColumn('product_name', function ($row) {
                return $row->product ? $row->product->name : 'N/A';
            })
            ->addColumn('product_code', function ($row) {
                return $row->product ? $row->product->product_code : 'N/A';
            })
            ->addColumn('quantity_formatted', function ($row) {
                return $row->quantity ? number_format($row->quantity, 0) : 'N/A';
            })
            
            ->addColumn('purchase_price', function ($row) {
                return $row->purchase_price ? $row->purchase_price : 'N/A';
            })
            

            ->addColumn('warehouse', function ($row) {
                return $row->warehouse ? $row->warehouse->name : 'N/A';
            })
            ->addColumn('action', function ($data) {
                $btn = '<div class="table-actions"> ';  
                if (Auth::user()) {
                    $url = route('admin.product.purchasehistory', ['id' => $data->product->id, 'size' => $data->size, 'color' => $data->color]);
                    $btn .= '<a href="'.$url.'" class="btn btn-sm btn-primary">History</a>';
                }
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    
    public function getsingleProductHistory(Request $request, $id, $size = null, $color = null)
    {
        if ($request->fromDate || $request->toDate) {
            $request->validate([
                'fromDate' => 'nullable|date', 
                'toDate' => 'required_with:fromDate|date|after_or_equal:fromDate', 
            ]);

            $fromDate = Carbon::parse($request->input('fromDate'))->startOfDay();
            $toDate = Carbon::parse($request->input('toDate'))->endOfDay();   
        }else{
            $fromDate = '';
            $toDate = '';
        }
        
        $product = Product::select('id', 'name','product_code')->where('id', $id)->first();
        $warehouses = Warehouse::where('status', 1)->get();

        $purchaseHistories = PurchaseHistory::where('product_id', $id)
                            ->when($fromDate, function ($query) use ($fromDate, $toDate) {
                                $query->whereBetween('created_at', [$fromDate, $toDate]);
                            })
                            ->where('product_size', $size)
                            ->where('product_color', $color)
                            ->orderby('id','DESC')
                            ->get();

        $salesHistories = OrderDetails::where('product_id', $id)
                            ->when($fromDate, function ($query) use ($fromDate, $toDate) {
                                $query->whereBetween('created_at', [$fromDate, $toDate]);
                            })
                            ->when($request->input('warehouse_id'), function ($query) use ($request) {
                                $query->where("warehouse_id",$request->input('warehouse_id'));
                            })
                            ->where('size', $size)
                            ->where('color', $color)
                            ->orderby('id','DESC')
                            ->whereHas('order', function ($query) {
                                $query->whereIn('order_type', ['0','1']);
                            })->get();


        return view('admin.stock.single_product_history', compact('purchaseHistories','salesHistories','product','warehouses', 'id', 'size', 'color'));
    }

    public function addstock()
    {
        $products = Product::orderby('id','DESC')->select('id', 'name','price', 'product_code')->get();
        $suppliers = Supplier::where('status', 1)->select('id', 'name')->orderby('id','DESC')->get();
        $colors = Color::where('status', 1)->select('id', 'color')->orderby('id','DESC')->get();
        $sizes = Size::where('status', 1)->select('id', 'size')->orderby('id','DESC')->get();
        $warehouses = Warehouse::select('id', 'name','location')->where('status', 1)->get();
        return view('admin.stock.create', compact('products', 'suppliers', 'colors', 'sizes','warehouses'));
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
        $purchase->direct_cost = $request->direct_cost;
        $purchase->cnf_cost = $request->cnf_cost;
        $purchase->cost_b = $request->cost_b;
        $purchase->cost_a = $request->cost_a;
        $purchase->other_cost = $request->other_cost;
        $purchase->total_vat_amount = $request->total_vat_amount;
        $purchase->net_amount = $request->net_amount;
        $purchase->paid_amount = $request->cash_payment + $request->bank_payment;
        $purchase->due_amount = $request->net_amount - $request->cash_payment - $request->bank_payment;
        $purchase->created_by = Auth::user()->id;
        $purchase->save();

        

        // $supplier = new SupplierTransaction();
        // $supplier->date = $request->purchase_date;
        // $supplier->supplier_id = $request->supplier_id;
        // $supplier->purchase_id = $purchase->id;
        // $supplier->table_type = "Purchase";
        // $supplier->payment_type = "Credit";
        // $supplier->amount = $request->total_amount;
        // $supplier->vat = $request->total_vat_amount;
        // $supplier->discount = $request->discount ?? 0.00;
        // $supplier->total_amount = $request->net_amount;
        // $supplier->save();

        // if ($request->cash_payment) {
        //     $cashpayment = new SupplierTransaction();
        //     $cashpayment->date = $request->purchase_date;
        //     $cashpayment->supplier_id = $request->supplier_id;
        //     $cashpayment->purchase_id = $purchase->id;
        //     $cashpayment->table_type = "Purchase";
        //     $cashpayment->payment_type = "Cash";
        //     $cashpayment->amount = $request->cash_payment;
        //     $cashpayment->total_amount = $request->cash_payment;
        //     $cashpayment->save();
        // }

        // if ($request->bank_payment) {
        //     $bankpayment = new SupplierTransaction();
        //     $bankpayment->date = $request->purchase_date;
        //     $bankpayment->supplier_id = $request->supplier_id;
        //     $bankpayment->purchase_id = $purchase->id;
        //     $bankpayment->table_type = "Purchase";
        //     $bankpayment->payment_type = "Bank";
        //     $bankpayment->amount = $request->bank_payment;
        //     $bankpayment->total_amount = $request->bank_payment;
        //     $bankpayment->save();
        // }

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
            if ($request->warehouse_id) {
                $purchaseHistory->remaining_product_quantity = 0;
                $purchaseHistory->transferred_product_quantity = $product['quantity'];
            }else{
                $purchaseHistory->remaining_product_quantity = $product['quantity'];
                $purchaseHistory->transferred_product_quantity = 0;
            }

            $purchaseHistory->created_by = Auth::user()->id;
            $purchaseHistory->save();

            $existingProduct = Product::find($product['product_id']);
            if ($existingProduct){   
                $existingProduct->price = $product['unit_price'];
                $existingProduct->save();
            }

            if ($request->warehouse_id) {
                $stock = Stock::where('product_id', $product['product_id'])
                      ->where('size', $product['product_size'])
                      ->where('color', $product['product_color'])
                      ->where('warehouse_id', $request->warehouse_id)
                      ->first();
                if ($stock) {
                    $stock->quantity += $product['quantity'];
                    $stock->updated_by = Auth::user()->id;
                    $stock->save();
                } else {
                    $newStock = new Stock();
                    $newStock->warehouse_id = $request->warehouse_id;
                    $newStock->product_id = $product['product_id'];
                    $newStock->quantity = $product['quantity'];
                    $newStock->size = $product['product_size'];
                    $newStock->color = $product['product_color'];
                    $newStock->created_by = Auth::user()->id;
                    $newStock->save();
                }
            }

            

        }

        $suppliertran = new Transaction();
        $suppliertran->date = $request->purchase_date;
        $suppliertran->supplier_id = $request->supplier_id;
        $suppliertran->purchase_id = $purchase->id;
        $suppliertran->table_type = "Purchase";
        $suppliertran->description = "Purchase";
        $suppliertran->payment_type = "Credit";
        $suppliertran->transaction_type = "Due";
        $suppliertran->amount = $request->total_amount;
        $suppliertran->vat_amount = $request->total_vat_amount;
        $suppliertran->discount = $request->discount ?? 0.00;
        $suppliertran->at_amount = $request->net_amount;
        $suppliertran->save();
        $suppliertran->tran_id = 'SL' . date('ymd') . str_pad($suppliertran->id, 4, '0', STR_PAD_LEFT);
        $suppliertran->save();
        
        if ($request->cash_payment) {
            $cashpayment = new Transaction();
            $cashpayment->date = $request->purchase_date;
            $cashpayment->supplier_id = $request->supplier_id;
            $cashpayment->purchase_id = $purchase->id;
            $cashpayment->table_type = "Purchase";
            $cashpayment->description = "Purchase";
            $cashpayment->payment_type = "Cash";
            $cashpayment->transaction_type = "Current";
            $cashpayment->amount = $request->cash_payment;
            $cashpayment->at_amount = $request->cash_payment;
            $cashpayment->save();
            $cashpayment->tran_id = 'SL' . date('ymd') . str_pad($cashpayment->id, 4, '0', STR_PAD_LEFT);
            $cashpayment->save();
        }

        if ($request->bank_payment) {
            $bankpayment = new Transaction();
            $bankpayment->date = $request->purchase_date;
            $bankpayment->supplier_id = $request->supplier_id;
            $bankpayment->purchase_id = $purchase->id;
            $bankpayment->table_type = "Purchase";
            $bankpayment->description = "Purchase";
            $bankpayment->payment_type = "Bank";
            $bankpayment->transaction_type = "Current";
            $bankpayment->amount = $request->bank_payment;
            $bankpayment->at_amount = $request->bank_payment;
            $bankpayment->save();
            $bankpayment->tran_id = 'SL' . date('ymd') . str_pad($bankpayment->id, 4, '0', STR_PAD_LEFT);
            $bankpayment->save();
        }

        

        return response()->json([
            'status' => 'success',
            'message' => 'Purchased Successfully',
        ]);
    }

    public function purchaseTransaction(Request $request, $purchase)
    {
        $supplier = new Transaction();
        $supplier->date = $request->purchase_date;
        $supplier->supplier_id = $request->supplier_id;
        $supplier->purchase_id = $purchase->id;
        $supplier->table_type = "Purchase";
        $supplier->description = "Purchase";
        $supplier->payment_type = "Credit";
        $supplier->transaction_type = "Due";
        $supplier->amount = $request->total_amount;
        $supplier->vat_amount = $request->total_vat_amount;
        $supplier->discount = $request->discount ?? 0.00;
        $supplier->at_amount = $request->net_amount;
        $supplier->save();
        $supplier->tran_id = 'SL' . date('ymd') . str_pad($supplier->id, 4, '0', STR_PAD_LEFT);
        $supplier->save();

        if ($request->cash_payment) {
            $cashpayment = new Transaction();
            $cashpayment->date = $request->purchase_date;
            $cashpayment->supplier_id = $request->supplier_id;
            $cashpayment->purchase_id = $purchase->id;
            $cashpayment->table_type = "Purchase";
            $cashpayment->description = "Purchase";
            $cashpayment->payment_type = "Cash";
            $cashpayment->transaction_type = "Current";
            $cashpayment->amount = $request->cash_payment;
            $cashpayment->total_amount = $request->cash_payment;
            $cashpayment->save();
            $cashpayment->tran_id = 'SL' . date('ymd') . str_pad($cashpayment->id, 4, '0', STR_PAD_LEFT);
            $cashpayment->save();
        }

        if ($request->bank_payment) {
            $bankpayment = new Transaction();
            $bankpayment->date = $request->purchase_date;
            $bankpayment->supplier_id = $request->supplier_id;
            $bankpayment->purchase_id = $purchase->id;
            $bankpayment->table_type = "Purchase";
            $bankpayment->description = "Purchase";
            $bankpayment->payment_type = "Bank";
            $bankpayment->transaction_type = "Current";
            $bankpayment->amount = $request->bank_payment;
            $bankpayment->total_amount = $request->bank_payment;
            $bankpayment->save();
            $bankpayment->tran_id = 'SL' . date('ymd') . str_pad($bankpayment->id, 4, '0', STR_PAD_LEFT);
            $bankpayment->save();
        }

        return;

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
        $warehouses = Warehouse::select('id', 'name','location')->where('status', 1)->get();
        return view('admin.stock.edit_purchase_history', compact('purchase', 'products', 'suppliers', 'warehouses'));
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

                // $stock = Stock::where('product_id', $purchaseHistory->product_id)
                //             ->where('size', $purchaseHistory->product_size)
                //             ->where('color', $purchaseHistory->product_color)
                //             ->first();

                // if ($stock) {
                //     $stock->quantity -= $purchaseHistory->quantity;
                //     $stock->updated_by = Auth::user()->id;
                //     $stock->save();
                // }

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
                    // $stock = Stock::where('product_id', $purchaseHistory->product_id)
                    //             ->where('size', $purchaseHistory->product_size)
                    //             ->where('color', $purchaseHistory->product_color)
                    //             ->first();

                    // if ($stock) {
                    //     $stock->quantity -= $purchaseHistory->quantity;
                    // }

                    $purchaseHistory->product_id = $product['product_id'];
                    $purchaseHistory->quantity = $product['quantity'];
                    $purchaseHistory->remaining_product_quantity = $product['quantity'];
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

                    // if ($stock) {
                    //     $stock->quantity += $product['quantity'];
                    //     $stock->save();
                    // }
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

                // $stock = Stock::where('product_id', $product['product_id'])
                //             ->where('size', $product['product_size'])
                //             ->where('color', $product['product_color'])
                //             ->first();

                // if ($stock) {
                //     $stock->quantity += $product['quantity'];
                //     $stock->updated_by = Auth::user()->id;
                //     $stock->save();
                // } else {
                //     $newStock = new Stock();
                //     $newStock->product_id = $product['product_id'];
                //     $newStock->quantity = $product['quantity'];
                //     $newStock->size = $product['product_size'];
                //     $newStock->color = $product['product_color'];
                //     $newStock->created_by = Auth::user()->id;
                //     $newStock->save();
                // }
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
                
                $purchaseHistory = PurchaseHistory::find($product['purchase_history_id']);
    
                if (!$purchaseHistory) {
                    continue;
                }
    
                $purchaseHistory->remaining_product_quantity -= $product['return_quantity'];
                $purchaseHistory->save();

            }
        });

        return response()->json(['message' => 'Purchase return saved successfully'], 200);
    }

    public function stockReturnHistory()
    {
        $purchaseReturns = PurchaseReturn::with('product', 'purchaseHistory') ->orderBy('id', 'desc')->get();
        return view('admin.stock.stock_return_history', compact('purchaseReturns'));
    }

    public function processSystemLoss(Request $request)
    {
        $validatedData = $request->validate([
            'productId' => 'required|exists:stocks,product_id', 
            'warehouse' => 'required', 
            'lossQuantity' => 'required|numeric|min:1', 
            'lossReason' => 'nullable|string|max:255',
        ]);

        $stock = Stock::where('product_id', $validatedData['productId'])->where('size',$request->size)->where('color',$request->color)->where('warehouse_id', $request->warehouse)->first();

        if (!$stock) {
            return response()->json(['message' => 'Stock record not found.'], 404);
        }
        if ($validatedData['lossQuantity'] > $stock->quantity) {
            return response()->json(['message' => 'Loss quantity cannot be more than current stock quantity.'], 422);
        }

        $newQuantity = $stock->quantity - $validatedData['lossQuantity'];
        $stock->update(['quantity' => $newQuantity]);

        $systemLoss = new SystemLose();
        $systemLoss->warehouse_id = $validatedData['warehouse'];
        $systemLoss->product_id = $validatedData['productId'];
        $systemLoss->quantity = $validatedData['lossQuantity'];
        $systemLoss->reason = $validatedData['lossReason'];
        $systemLoss->created_by = Auth::user()->id;
        $systemLoss->save();

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

    public function updateStatus(Request $request)
    {
        $request->validate([
            'purchase_id' => 'required|integer|exists:purchases,id',
            'status' => 'required|integer|in:1,2,3,4'
        ]);

        $purchase = Purchase::find($request->purchase_id);
        $purchase->status = $request->status;
        $purchase->save();

        return response()->json(['success' => true]);
    }

    public function missingProduct($id)
    {
        $purchase = Purchase::with('purchaseHistory.product')->findOrFail($id);
        $warehouses = Warehouse::orderby('id','DESC')->where('status', 1)->get();
        $purchaseCount = PurchaseHistory::where('purchase_id', $id)->count();
        return view('admin.stock.missing_product', compact('purchase', 'warehouses', 'purchaseCount'));
    }

    public function missingPurchaseProduct(Request $request, $purchaseId)
    {
        $request->validate([
            'quantities.*' => 'required|array',
        ]);
    
        foreach ($request->quantities as $historyId => $quantities) {
            foreach ($quantities as $index => $quantity) {
    
                $purchaseHistory = PurchaseHistory::find($historyId);
    
                if (!$purchaseHistory) {
                    continue;
                }
    
                $purchaseHistory->remaining_product_quantity -= $quantity;
                $purchaseHistory->missing_product_quantity += $quantity;
                $purchaseHistory->save();

                $size = $request->sizes[$historyId][0];
                $color = $request->colors[$historyId][0];

                $missing = new SystemLose();
                $missing->product_id = $purchaseHistory->product_id;
                $missing->purchase_id = $purchaseHistory->purchase_id;
                $missing->quantity = $quantity;
                $missing->reason = "Product Missing";
                $missing->save();
    
                
            }
        }
    
        return redirect()->back()->with('success', 'Missing product recorded successfully.');
    }

}
