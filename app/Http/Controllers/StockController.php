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
use App\Models\ShipmentDetails;
use App\Models\StockTransferRequest;
use App\Models\CompanyDetails;
use PDF;
use App\Models\Type;

class StockController extends Controller
{
    public function getStock()
    {
        $products = Product::whereHas('stock', function ($query) {
            $query->where('quantity', '>', 0);
        })
        ->orderby('id','DESC')->select('id', 'name','price', 'product_code')->get();
        $user = Auth::user();
        // $warehouseIds = json_decode($user->warehouse_ids, true);
        $warehouses = Warehouse::where('status', 1)->get();
        $sizes = Stock::distinct()->pluck('size')->filter();
        $colors = Stock::distinct()->pluck('color')->filter();
        $types = Type::where('status', 1)->get();
        $filteredWarehouses = Warehouse::select('id', 'name','location')->where('status', 1)->get();
        return view('admin.stock.index', compact('warehouses','products','filteredWarehouses','sizes','colors','types'));
    }

    public function getStocks(Request $request)
    {
        $user = Auth::user();
        // $warehouseIds = json_decode($user->warehouse_ids, true);
        
        $query = Stock::with('warehouse', 'product', 'type');
        
        // $query = Stock::with('warehouse', 'product')
        //     ->select('product_id', 'size', 'color', 'warehouse_id', 'quantity');

        if ($request->has('warehouse_id') && $request->warehouse_id != '') {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->has('product_id') && $request->product_id != '') {
            $query->where('product_id', $request->product_id);
        }

        if ($request->has('color') && $request->color != '') {
            $query->where('color', $request->color);
        }

        if ($request->has('size') && $request->size != '') {
            $query->where('size', $request->size);
        }

        if ($request->has('type_id') && $request->type_id != '') {
            $query->where('type_id', $request->type_id);
        }

        if ($request->has('zip') && $request->zip != '') {
            $query->where('zip', $request->zip)
                  ->whereHas('product', function ($q) {
                      $q->whereHas('types', function ($subQ) {
                          $subQ->where('slug', 'zip');
                      });
                  });
        }

        $data = $query->orderBy('id', 'DESC')->get();

        return DataTables::of($data)
            ->addColumn('sl', function ($row) {
                static $i = 1;
                return $i++;
            })
            ->addColumn('product_details', function ($row) {
                if ($row->product) {
                    $productCode = $row->product->product_code;
                    $productName = $row->product->name;
                    return "$productCode - $productName";
                }
                return 'N/A';
            })
            ->addColumn('warehouse_name', function ($row) {
                return $row->warehouse ? $row->warehouse->name : 'N/A';
            })
            ->addColumn('quantity', function ($row) {
                return $row->quantity ? number_format($row->quantity, 0) : 'N/A';
            })
            ->addColumn('zip_status', function ($row) {
                if (!$row->product || !$row->product->is_zip) return '-';
                return $row->zip == 1 ? 'Yes' : 'No';
            })
            ->addColumn('type', function ($row) {
                return $row->type ? $row->type->name : '-';
            })
            ->addColumn('action', function ($data) {
                $btn = '<div class="table-actions"> 
                            <button class="btn btn-sm btn-danger btn-open-loss-modal mr-2" data-size="'.$data->size.'" data-color="'.$data->color.'" data-warehouse="'.$data->warehouse_id.'" data-id="'.$data->product->id.'" data-type-id="'.($data->type_id ?? '').'"  data-zip="'.$data->zip.'">System Loss</button>';  
                if (Auth::user()) {
                    $url = route('admin.product.purchasehistory', ['id' => $data->product->id, 'size' => $data->size, 'color' => $data->color, 'warehouse_id' => $data->warehouse_id, 'type_id' => $data->type_id]);
                    $btn .= '<a href="'.$url.'" class="btn btn-sm btn-primary">History</a>';
                }
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getTotalStock(Request $request)
    {
        $user = Auth::user();
        // $warehouseIds = json_decode($user->warehouse_ids, true);
    
        $data = Stock::selectRaw('product_id, size, color, type_id, SUM(quantity) as total_quantity')
            ->groupBy('product_id', 'size', 'color', 'type_id')
            ->with(['product', 'type'])
            ->orderByDesc('product_id')
            ->get();
    
        return DataTables::of($data)

            ->addColumn('sl', function ($row) {
                static $i = 1;
                return $i++;
            })
            ->addColumn('product_details', function ($row) {
                return $row->product ? "{$row->product->product_code} - {$row->product->name}" : 'N/A';
            })
            ->addColumn('quantity', function ($row) {
                return number_format($row->total_quantity, 0);
            })
            ->addColumn('type', function ($row) {
                return $row->type ? $row->type->name : '-';
            })
            ->addColumn('zip_status', function ($row) {
                if (!$row->product || !$row->product->is_zip) return '-';
                return $row->zip == 1 ? 'Yes' : 'No';
            })
            ->make(true);
    }

    public function stockingHistory()
    {
        $products = Product::whereHas('stock', function ($query) {
            $query->where('quantity', '>', 0);
        })
        ->orderby('id','DESC')->select('id', 'name','price', 'product_code')->get();

        $warehouses = Warehouse::select('id', 'name','location')->where('status', 1)->get();
        $sizes = Stock::distinct()->pluck('size')->filter();
        $colors = Stock::distinct()->pluck('color')->filter();
        $types = Type::where('status', 1)->get();
        return view('admin.stock.stockhistory', compact('warehouses','products','sizes','colors','types'));
    }

    public function getStockingHistory(Request $request)
    {  
        // $warehouseIds = json_decode(Auth::user()->warehouse_ids, true);

        $query = StockHistory::with('type')->select('date', 'stockid', 'purchase_id', 'product_id', 'stock_id', 'warehouse_id', 'quantity', 'selling_qty', 'available_qty', 'size', 'color', 'systemloss_qty', 'purchase_price', 'selling_price', 'received_quantity', 'ground_price_per_unit', 'zip', 'type_id');
        // ->when(!empty($warehouseIds), function ($query) use ($warehouseIds) {
        //     return $query->whereIn('warehouse_id', $warehouseIds);
        // })
        
        if ($request->has('warehouse_id') && $request->warehouse_id != '') {
            $query->where('warehouse_id', $request->warehouse_id);
        }
        if ($request->has('product_id') && $request->product_id != '') {
            $query->where('product_id', $request->product_id);
        }
        if ($request->has('size') && $request->size != '') {
            $query->where('size', $request->size);
        }
        if ($request->has('color') && $request->color != '') {
            $query->where('color', $request->color);
        }
        if ($request->has('type_id') && $request->type_id != '') {
            $query->where('type_id', $request->type_id);
        }
        if ($request->has('zip') && $request->zip != '') {
            $query->where('zip', $request->zip)
                  ->whereHas('product', function ($q) {
                      $q->whereHas('types', function ($subQ) {
                          $subQ->where('slug', 'zip');
                      });
                  });
        }

        $data = $query->orderBy('available_qty', 'DESC')->get();

        return DataTables::of($data)
            ->addColumn('sl', function($row) {
                static $i = 1;
                return $i++;
            })
            ->addColumn('date', function ($row) {
                return $row->date ? Carbon::parse($row->date)->format('d-m-Y') : 'N/A';
            })
            ->addColumn('product_details', function ($row) {
                if ($row->product) {
                    $productCode = $row->product->product_code;
                    $productName = $row->product->name;
                    return "$productCode - $productName";
                }
                return 'N/A';
            })            
            ->addColumn('quantity_formatted', function ($row) {
                $totalQuantity = ($row->quantity ?? 0);
                return number_format($totalQuantity, 0);
            })
            ->addColumn('selling_price', function ($row) {
                $selling_price = ($row->selling_price ?? 0) ;
                return number_format($selling_price, 2);
            })
            ->addColumn('type', function ($row) {
                return $row->type ? $row->type->name : '-';
            })
            
            ->addColumn('selling_qty', function ($row) {
                return $row->selling_qty ? $row->selling_qty : '0';
            })
            
            ->addColumn('available_qty', function ($row) {
                return $row->available_qty ? $row->available_qty : '0';
            })
            
            ->addColumn('ground_price_per_unit', function ($row) {
                return $row->ground_price_per_unit ? $row->ground_price_per_unit : '';
            })
            
            ->addColumn('warehouse', function ($row) {
                return $row->warehouse ? $row->warehouse->name : '';
            })
            ->addColumn('zip_status', function ($row) {
                if (!$row->product || !$row->product->is_zip) {
                    return '-';
                }
                return $row->zip == 1 ? 'Yes' : 'No';
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

    public function getStockLedger()
    {
        // $warehouseIds = json_decode(Auth::user()->warehouse_ids, true);
        $totalQty = Stock::sum('quantity');
        
        $totalProduct = Stock::count('product_id');

        // $totalQty = Stock::when(!empty($warehouseIds), function ($query) use ($warehouseIds) {
        //     return $query->whereIn('warehouse_id', $warehouseIds);
        // })->sum('quantity');

        // $totalProduct = Stock::when(!empty($warehouseIds), function ($query) use ($warehouseIds) {
        //     return $query->whereIn('warehouse_id', $warehouseIds);
        // })->count('product_id');

        $totalSupplier = Supplier::count();

        $products = Product::whereHas('stock', function ($query) {
            $query->where('quantity', '>', 0);
        })
        ->orderby('id','DESC')->select('id', 'name','price', 'product_code')->get();
        $warehouses = Warehouse::select('id', 'name','location')->where('status', 1)->get();
        return view('admin.stock.stockledger', compact('warehouses','products','totalQty','totalProduct'));
    }

    // public function getproductHistory()
    // {
    //     $today = Carbon::today();
    //     $yesterday = Carbon::yesterday();

    //     $products = DB::table('products')
    //                     ->leftJoin('purchase_histories', 'products.id', '=', 'purchase_histories.product_id')
    //                     ->leftJoin('stock_histories', 'products.id', '=', 'stock_histories.product_id')
    //                     ->leftJoin('order_details', 'products.id', '=', 'order_details.product_id')
    //                     ->leftJoin('order_returns', 'products.id', '=', 'order_returns.product_id')
    //                     ->leftJoin('purchase_returns', 'products.id', '=', 'purchase_returns.product_id')
    //                     ->select('products.id', 'products.name',
    //                         DB::raw("SUM(CASE WHEN purchase_histories.created_at <= '{$yesterday}' THEN purchase_histories.quantity ELSE 0 END) as previous_day_purchase"),
    //                         DB::raw("SUM(CASE WHEN purchase_histories.created_at = '{$today}' THEN purchase_histories.quantity ELSE 0 END) as today_purchase_qty"),
    //                         DB::raw("SUM(CASE WHEN stock_histories.created_at <= '{$yesterday}' THEN stock_histories.quantity ELSE 0 END) as previous_day_stock"),
    //                         DB::raw("SUM(CASE WHEN stock_histories.created_at = '{$today}' THEN stock_histories.quantity ELSE 0 END) as today_stock_qty"),
    //                         DB::raw("SUM(CASE WHEN order_details.created_at = '{$today}' THEN order_details.quantity ELSE 0 END) as today_sales_qty"),
    //                         DB::raw("SUM(CASE WHEN order_details.created_at <= '{$yesterday}' THEN order_details.quantity ELSE 0 END) as previous_sales_qty"),
    //                         DB::raw("SUM(CASE WHEN purchase_returns.created_at = '{$today}' THEN purchase_returns.return_quantity ELSE 0 END) as today_purchase_return_qty"),
    //                         DB::raw("SUM(CASE WHEN purchase_returns.created_at <= '{$yesterday}' THEN purchase_returns.return_quantity ELSE 0 END) as previous_purchase_return_qty"),
    //                         DB::raw("SUM(CASE WHEN order_returns.created_at = '{$today}' THEN order_returns.quantity ELSE 0 END) as today_sales_return_qty"),
    //                         DB::raw("SUM(CASE WHEN order_returns.created_at <= '{$yesterday}' THEN order_returns.quantity ELSE 0 END) as previous_sales_return_qty")
    //                     )
    //                     ->groupBy('products.id', 'products.name')
    //                     ->get();
                        
                        

    //     // $products = Product::select('id','name','product_code')->orderBy('id', 'DESC')->get();
    //     $warehouses = Warehouse::select('id', 'name','location')->where('status', 1)->get();
    //     return view('admin.stock.getproducthistory', compact('warehouses','products'));
    // }

    public function getproductHistory()
    {
        // $warehouseIds = json_decode(Auth::user()->warehouse_ids, true);

        $products = Product::with([
            'orderDetails' => function ($query) {
                $query->whereHas('order', function ($query) {
                    $query->whereIn('order_type', [0, 1]);
                });
            },
            'stock',
            'shipmentDetails'
        ])->get();

        // ->with(['stock' => function ($query) use ($warehouseIds) {
        //     if (!empty($warehouseIds)) {
        //         $query->whereIn('warehouse_id', $warehouseIds);
        //     }
        // }])

        $products = $products->map(function ($product) {
            $product->total_quantity = number_format($product->stock ? $product->stock->quantity : 0, 0);
            return $product;
        });
    
        return view('admin.stock.getproducthistory', compact('products'));
    }

    public function getsingleProductHistory(Request $request, $id, $size = null, $color = null, $warehouse_id = null, $type_id = null)
    {
      if ($request->fromDate || $request->toDate) {
        $request->validate([
          'fromDate' => 'nullable|date',
          'toDate' => 'required_with:fromDate|date|after_or_equal:fromDate',
        ]);

        $fromDate = Carbon::parse($request->input('fromDate'))->startOfDay();
        $toDate = Carbon::parse($request->input('toDate'))->endOfDay();
      } else {
        $fromDate = '';
        $toDate = '';
      }

      $product = Product::select('id', 'name', 'product_code')->where('id', $id)->first();
      $warehouses = Warehouse::where('status', 1)->get();

      $shipmentDetails = ShipmentDetails::where('product_id', $id)
        ->when($fromDate, function ($query) use ($fromDate, $toDate) {
          $query->whereBetween('created_at', [$fromDate, $toDate]);
        })
        ->where('size', $size)
        ->where('color', $color)
        ->where('warehouse_id', $warehouse_id)
        ->where('type_id', $type_id)
        ->orderby('id', 'DESC')
        ->get();

      $salesHistories = OrderDetails::where('product_id', $id)
        ->when($fromDate, function ($query) use ($fromDate, $toDate) {
          $query->whereBetween('created_at', [$fromDate, $toDate]);
        })
        ->where('size', $size)
        ->where('color', $color)
        ->where('warehouse_id', $warehouse_id)
        ->where('type_id', $type_id)
        ->orderby('id', 'DESC')
        ->whereHas('order', function ($query) {
          $query->whereIn('order_type', ['0', '1'])
            ->whereNotNull('warehouse_id')
            // ->whereNotIn('status', [6, 7])
          ;
        })->get();

      $stockTransferRequests = StockTransferRequest::where('product_id', $id)

        ->where(function ($query) use ($warehouse_id) {
          $query->where('from_warehouse_id', $warehouse_id)
            ->orWhere('to_warehouse_id', $warehouse_id);
        })
        ->when($size, function ($query) use ($size) {
          $query->where('size', $size);
        })
        ->when($color, function ($query) use ($color) {
          $query->where('color', $color);
        })
        ->when($fromDate, function ($query) use ($fromDate, $toDate) {
          $query->whereBetween('created_at', [$fromDate, $toDate]);
        })
        ->where('status', 1)
        ->orderBy('id', 'DESC')
        ->get();

      $systemLosses = SystemLose::with('product')
        ->where('product_id', $id)
        ->where('size', $size)
        ->where('warehouse_id', $warehouse_id)
        ->where('color', $color)
        ->latest()
        ->get();

      $type = null;
      if ($type_id) {
        $type = Type::find($type_id);
      }

      return view('admin.stock.single_product_history', compact('shipmentDetails', 'salesHistories', 'stockTransferRequests', 'product', 'warehouses', 'id', 'size', 'color', 'warehouse_id', 'type', 'systemLosses'));
    }

    public function addstock()
    {
        $products = Product::with(['stock', 'types:id,name'])->orderBy('id','DESC')->select('id', 'name', 'price', 'product_code')->where('active_status', 1)->get();
        $suppliers = Supplier::where('status', 1)->select('id', 'name')->orderby('id','DESC')->get();
        $colors = Color::where('status', 1)->select('id', 'color')->orderby('id','DESC')->get();
        $sizes = Size::where('status', 1)->select('id', 'size')->orderby('id','DESC')->get();
        $warehouses = Warehouse::select('id', 'name','location')->where('status', 1)->get();
        return view('admin.stock.create', compact('products', 'suppliers', 'colors', 'sizes','warehouses'));
    }

    public function stockStore(Request $request)
    {

        $validatedData = $request->validate([
            'season' => 'required',
            'invoice' => 'required',
            'supplier_id' => 'required|exists:suppliers,id',
            // 'warehouse_id' => 'required|exists:warehouses,id',
            'purchase_date' => 'required|date',
            // 'purchase_type' => 'required',
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
            'products.*.type_id' => 'nullable|exists:types,id',
            'products.*.unit_price' => 'required|numeric',
            'products.*.vat_percent' => 'nullable|numeric',
            'products.*.vat_amount' => 'nullable|numeric',
            'products.*.total_price_with_vat' => 'required|numeric',
        ]);

        $season = $request->input('season');
        $invoice = $request->input('invoice');
    
        $invoicePrefix = "STL-{$season}-" . date('Y') . "-{$invoice}";

        $data = $request->all();
        $purchase = new Purchase();
        $purchase->invoice = $invoicePrefix;
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

        if ($request->warehouse_id) {
            $purchase->status = 4;
            $purchase->save();
        }
        
        foreach ($request->products as $product) {
            $purchaseHistory = new PurchaseHistory();
            $purchaseHistory->purchase_id = $purchase->id;
            $purchaseHistory->product_id = $product['product_id'];
            $purchaseHistory->type_id = $product['type_id'] ?? null;
            $purchaseHistory->quantity = $product['quantity'];
            
            $purchaseHistory->product_size = $product['product_size'];
            $purchaseHistory->product_color = $product['product_color'];
            $purchaseHistory->zip = $product['zip'];
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

            // if ($request->warehouse_id) {
            //     $stock = Stock::where('product_id', $product['product_id'])
            //           ->where('size', $product['product_size'])
            //           ->where('color', $product['product_color'])
            //           ->where('warehouse_id', $request->warehouse_id)
            //           ->first();
            //     if ($stock) {
            //         $stock->quantity += $product['quantity'];
            //         $stock->updated_by = Auth::user()->id;
            //         $stock->save();
            //     } else {
            //         $newStock = new Stock();
            //         $newStock->warehouse_id = $request->warehouse_id;
            //         $newStock->product_id = $product['product_id'];
            //         $newStock->quantity = $product['quantity'];
            //         $newStock->size = $product['product_size'];
            //         $newStock->color = $product['product_color'];
            //         $newStock->created_by = Auth::user()->id;
            //         $newStock->save();
            //     }
            //     // calculate every additional cost per product
            //     $additionalCost = $purchase->direct_cost + $purchase->cnf_cost + $purchase->cost_a + $purchase->cost_b + $purchase->other_cost;
            //     $qty = $purchaseHistory->quantity - $purchaseHistory->missing_product_quantity;
            //     $countItem = Purchase::withCount('purchaseHistory')->where('id', $purchaseHistory->purchase_id)->first();
            //     $additionalCostPerProduct = $additionalCost/$countItem->purchase_history_count;
            //     $additionalCostPerUnit = $additionalCostPerProduct/$qty;
            //     // calculate every additional cost per product

            //     $warehouseId = $request->warehouse_id;
            //     $stockhistory = new StockHistory();
            //     $stockhistory->product_id = $purchaseHistory->product_id;
            //     $stockhistory->purchase_id = $purchaseHistory->purchase_id;
            //     if ($stock) {
            //         $stockhistory->stock_id = $stock->id;
            //     } else {
            //         $stockhistory->stock_id = $newStock->id;
            //     }
                
            //     $stockhistory->warehouse_id = $warehouseId;
            //     $stockhistory->selling_qty = 0;
            //     $stockhistory->quantity = $product['quantity'];
            //     $stockhistory->available_qty = $product['quantity'];
            //     $stockhistory->size = $purchaseHistory->product_size;
            //     $stockhistory->color = $purchaseHistory->product_color;
            //     $stockhistory->date = date('Y-m-d');
            //     $stockhistory->stockid = date('mds').$warehouseId.str_pad($purchaseHistory->id, 4, '0', STR_PAD_LEFT);

            //     $stockhistory->purchase_price = $purchaseHistory->purchase_price + $additionalCostPerUnit;
            //     $stockhistory->selling_price = $stockhistory->purchase_price + $stockhistory->purchase_price * .2;
            //     $stockhistory->created_by = Auth::user()->id;
            //     $stockhistory->save();
            // }
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
        $suppliertran->additional_cost = $request->direct_cost + $request->cnf_cost + $request->cost_b + $request->cost_a + $request->other_cost;
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

    public function newPurchaseHistory()
    {
        $purchases = Purchase::with('purchaseHistory.product', 'supplier')
        ->whereHas('purchaseHistory', function ($query) {
            $query->selectRaw('purchase_id, SUM(quantity) as total_quantity, SUM(shipped_quantity) as total_shipped')
                ->groupBy('purchase_id')
                ->havingRaw('SUM(shipped_quantity) = 0')
                ->where('quantity', '>', 0);
        })
        ->orderBy('id', 'DESC')
        ->get();

        return view('admin.stock.purchase_history', compact('purchases'));
    }

    public function livePurchaseHistory()
    {
        $purchases = Purchase::with('purchaseHistory.product', 'supplier')
        ->whereHas('purchaseHistory', function ($query) {
            $query->selectRaw('purchase_id, SUM(quantity) as total_quantity, SUM(shipped_quantity) as total_shipped')
                ->groupBy('purchase_id')
                ->havingRaw('SUM(quantity) != SUM(shipped_quantity)')
                ->where('quantity', '>', 0)
                ->where('shipped_quantity', '>', 0);
        })
        ->orderBy('id', 'DESC')
        ->get();

        return view('admin.stock.purchase_history', compact('purchases'));
    }

    public function completedPurchaseHistory()
    {
            $purchases = Purchase::with('purchaseHistory.product', 'supplier')
            ->whereHas('purchaseHistory', function ($query) {
                $query->havingRaw('SUM(quantity) = SUM(shipped_quantity)')
                    ->where('quantity', '>', 0)
                    ->where('shipped_quantity', '>', 0);
            })
            ->orderBy('id', 'DESC')
            ->get();

        return view('admin.stock.purchase_history', compact('purchases'));
    }

    public function getPurchaseHistory(Purchase $purchase)
    {
        $purchase = Purchase::with(['supplier', 'purchaseHistory.product.types', 'purchaseHistory.type'])
            ->findOrFail($purchase->id);

        return response()->json($purchase);
    }

    public function editPurchaseHistory(Purchase $purchase)
    {
        $purchase = Purchase::with('supplier', 'purchaseHistory.product', 'purchaseHistory.type')->findOrFail($purchase->id);
        $products = Product::with('types:id,name')->orderby('id','DESC')->get();
        $suppliers = Supplier::orderby('id','DESC')->get();
        $warehouses = Warehouse::select('id', 'name','location')->where('status', 1)->get();
        $cashAmount = $purchase->transactions->where('payment_type', 'Cash')->first();
        $bankAmount = $purchase->transactions->where('payment_type', 'Bank')->first();
        $colors = Color::where('status', 1)->select('id', 'color')->orderby('id','DESC')->get();
        $sizes = Size::where('status', 1)->select('id', 'size')->orderby('id','DESC')->get();
        return view('admin.stock.edit_purchase_history', compact('purchase', 'products', 'suppliers', 'warehouses', 'cashAmount', 'bankAmount', 'colors', 'sizes'));
    }

    public function stockUpdate(Request $request)
    {
        $validatedData = $request->validate([
            'purchase_id' => 'required|exists:purchases,id',
            'invoice' => 'required',
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            // 'purchase_type' => 'required',
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
            'products.*.type_id' => 'nullable|exists:types,id',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.product_size' => 'nullable|string',
            'products.*.product_color' => 'nullable|string',
            'products.*.unit_price' => 'required|numeric',
            'products.*.vat_percent' => 'nullable|numeric',
            'products.*.total_price_with_vat' => 'required|numeric',
            'cash_payment' => 'nullable|numeric',
            'bank_payment' => 'nullable|numeric',
        ]);
    
        $purchase = Purchase::find($request->purchase_id);
    
        if (!$purchase) {
            return response()->json([
                'status' => 'error',
                'message' => 'Purchase not found.',
            ], 404);
        }
    
        // Update Purchase Info
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
        $purchase->updated_by = Auth::user()->id;
        $purchase->save();
    
        // Handling Purchase History Updates
        $existingPurchaseHistoryIds = $purchase->purchaseHistory->pluck('id')->toArray();
        $updatedPurchaseHistoryIds = array_column($request->products, 'purchase_history_id');
    
        $removedPurchaseHistoryIds = array_diff($existingPurchaseHistoryIds, $updatedPurchaseHistoryIds);
    
        // Removing Deleted Purchase History
        foreach ($removedPurchaseHistoryIds as $removedId) {
            $purchaseHistory = PurchaseHistory::find($removedId);
            if ($purchaseHistory) {
                $purchaseHistory->delete();
            }
        }
    
        // Updating/Creating Purchase History
        foreach ($request->products as $product) {
            if (isset($product['purchase_history_id'])) {
                $purchaseHistory = PurchaseHistory::find($product['purchase_history_id']);
                if ($purchaseHistory) {
                    $purchaseHistory->product_id = $product['product_id'];
                    $purchaseHistory->quantity = $product['quantity'];
                    $purchaseHistory->product_size = $product['product_size'];
                    $purchaseHistory->product_color = $product['product_color'];
                    $purchaseHistory->type_id = $product['type_id'] ?? null;
                    $purchaseHistory->purchase_price = $product['unit_price'];
                    $purchaseHistory->vat_percent = $product['vat_percent'];
                    $purchaseHistory->vat_amount_per_unit = $product['vat_amount'] / $product['quantity'];
                    $purchaseHistory->total_vat = $purchaseHistory->vat_amount_per_unit * $product['quantity'];
                    $purchaseHistory->total_amount = $product['unit_price'] * $product['quantity'];
                    $purchaseHistory->total_amount_with_vat = $product['total_price_with_vat'];
                    $purchaseHistory->remaining_product_quantity = $product['quantity'];
                    // if ($purchaseHistory->transferred_product_quantity > 0) {
                    //     // If product was transferred before, keep the transferred quantity
                    //     $purchaseHistory->remaining_product_quantity = 0;
                    //     $purchaseHistory->transferred_product_quantity = $product['quantity'];
                    // } else {
                    //     // If the product was not transferred, just update remaining quantity
                    //     $purchaseHistory->remaining_product_quantity = $product['quantity'];
                    //     $purchaseHistory->transferred_product_quantity = 0;
                    // }
                    $purchaseHistory->updated_by = Auth::user()->id;
                    $purchaseHistory->save();
                }
            } else {
                // New product, create new history entry
                $purchaseHistory = new PurchaseHistory();
                $purchaseHistory->purchase_id = $purchase->id;
                $purchaseHistory->product_id = $product['product_id'];
                $purchaseHistory->quantity = $product['quantity'];
                $purchaseHistory->product_size = $product['product_size'];
                $purchaseHistory->product_color = $product['product_color'];
                $purchaseHistory->zip = $product['zip'];
                $purchaseHistory->purchase_price = $product['unit_price'];
                $purchaseHistory->vat_percent = $product['vat_percent'];
                $purchaseHistory->vat_amount_per_unit = $product['vat_amount'] / $product['quantity'];
                $purchaseHistory->total_vat = $purchaseHistory->vat_amount_per_unit * $product['quantity'];
                $purchaseHistory->total_amount = $product['unit_price'] * $product['quantity'];
                $purchaseHistory->total_amount_with_vat = $product['total_price_with_vat'];
                $purchaseHistory->created_by = Auth::user()->id;
                $purchaseHistory->save();
            }
        }
    
        // Update Transaction for Purchase
        $transaction = Transaction::where('purchase_id', $purchase->id)->first();
        if ($transaction) {
            $transaction->amount = $request->total_amount;
            $transaction->vat_amount = $request->total_vat_amount;
            $transaction->discount = $request->discount;
            $transaction->at_amount = $request->net_amount;
            $transaction->save();
        }
    
        // Update Cash Payment
        if ($request->cash_payment) {
            $cashpayment = Transaction::where('purchase_id', $purchase->id)
                ->where('payment_type', 'Cash')->first();
    
            if ($cashpayment) {
                $cashpayment->amount = $request->cash_payment;
                $cashpayment->at_amount = $request->cash_payment;
                $cashpayment->save();
            } else {
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
        }
    
        // Update Bank Payment
        if ($request->bank_payment) {
            $bankpayment = Transaction::where('purchase_id', $purchase->id)
                ->where('payment_type', 'Bank')->first();
    
            if ($bankpayment) {
                $bankpayment->amount = $request->bank_payment;
                $bankpayment->at_amount = $request->bank_payment;
                $bankpayment->save();
            } else {
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
        }
    
        return response()->json([
            'status' => 'success',
            'message' => 'Purchase updated successfully.',
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

        $request->validate([
            'date' => 'required|date',
            'reason' => 'required|string|max:255',
            'supplierId' => 'required|exists:suppliers,id',
            'products' => 'required|array',
            'products.*.purchase_history_id' => 'required|exists:purchase_histories,id',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.return_quantity' => 'required|numeric|min:1',
        ]);

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
            'color' => 'nullable',
            'size' => 'nullable',
            'zip' => 'nullable',
            'type_id' => 'nullable',
        ]);

        $stock = Stock::where('product_id', $validatedData['productId'])->where('size',$request->size)->where('color',$request->color)->where('warehouse_id', $request->warehouse)->where('zip', $request->zip)->where('type_id', $request->type_id)->first();

        if (!$stock) {
            return response()->json(['message' => 'Stock record not found.'], 404);
        }
        if ($validatedData['lossQuantity'] > $stock->quantity) {
            return response()->json(['message' => 'Loss quantity cannot be more than current stock quantity.'], 422);
        }

        $newQuantity = $stock->quantity - $validatedData['lossQuantity'];
        $stock->update(['quantity' => $newQuantity]);

        $history = StockHistory::where('product_id', $validatedData['productId'])
            ->where('stock_id', $stock->id)
            ->orderBy('id', 'desc')
            ->first();

        if ($history) {
            $history->systemloss_qty = ($history->systemloss_qty ?? 0) + $validatedData['lossQuantity'];
            $history->available_qty = ($history->available_qty ?? 0) - $validatedData['lossQuantity'];
            $history->save();
        }

        $systemLoss = new SystemLose();
        $systemLoss->warehouse_id = $validatedData['warehouse'];
        $systemLoss->product_id = $validatedData['productId'];
        $systemLoss->quantity = $validatedData['lossQuantity'];
        $systemLoss->reason = $validatedData['lossReason'];
        $systemLoss->color = $validatedData['color'];
        $systemLoss->zip = $validatedData['zip'];
        $systemLoss->size = $validatedData['size'];
        $systemLoss->type_id = $validatedData['type_id'] ?? null;
        $systemLoss->created_by = Auth::user()->id;
        $systemLoss->save();

        return response()->json(['message' => 'System loss processed successfully.']);
    }

    public function systemLosses()
    {
        $systemLosses = SystemLose::with('product', 'type')->latest()->get();
        return view('admin.stock.system_losses', compact('systemLosses'));
    }

    public function sendToStock(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
            'order_details_id' => 'required|exists:order_details,id',
        ]);

        $order_details_id = $request->input('order_details_id');
        $product_id = $request->input('product_id');
        $quantity = $request->input('quantity');

        $orderDetails = OrderDetails::find($order_details_id);

        if (!$orderDetails) {
            return redirect()->back()->with('error', 'Order details not found.');
        }

        $size = $orderDetails->size;
        $color = $orderDetails->color;
        $warehouseId = $orderDetails->warehouse_id;

        $stock = Stock::where('product_id', $product_id)
            ->where('size', $size)
            ->where('color', $color)
            ->where('warehouse_id', $warehouseId)
            ->where('type_id', $orderDetails->type_id)
            ->first();

        if ($stock) {
            $stock->quantity += $quantity;
            $stock->updated_by = auth()->user()->id;
            $stock->save();
        }

        $remainingQty = $quantity; // Quantity to reverse
        $stockHistories = StockHistory::where('stock_id', $stock->id)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($stockHistories as $history) {
            if ($remainingQty > 0) {
                $history->available_qty += $remainingQty;
                $history->save();
                $remainingQty = 0;
            } else {
                break;
            }
        }

        $orderReturn = OrderReturn::where('product_id', $product_id)
            ->where('order_details_id', $request->order_details_id)
            ->first();

        if ($orderReturn) {
            $orderReturn->new_quantity -= $quantity;
            $orderReturn->return_stock += $quantity;
            $orderReturn->save();
        }


        return redirect()->back()->with('success', 'Stock updated successfully.');
    }

    public function sendToSystemLose(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
            'order_details_id' => 'required|exists:order_details,id',
        ]);

        $product_id = $request->input('product_id');
        $quantity = $request->input('quantity');
        $order_details_id = $request->input('order_details_id');

        $orderDetails = OrderDetails::find($order_details_id);

        if (!$orderDetails) {
            return redirect()->back()->with('error', 'Order details not found.');
        }

        $size = $orderDetails->size;
        $color = $orderDetails->color;
        $warehouseId = $orderDetails->warehouse_id;

        $systemLoss = new SystemLose();
        $systemLoss->warehouse_id = $warehouseId;
        $systemLoss->product_id = $product_id;
        $systemLoss->order_id = $request->order_id;
        $systemLoss->type_id = $orderDetails->type_id;
        $systemLoss->quantity = $quantity;
        $systemLoss->size = $size;
        $systemLoss->color = $color;
        $systemLoss->reason = $request->input('reason');
        $systemLoss->created_by = auth()->user()->id;
        $systemLoss->save();

        $orderReturn = OrderReturn::where('product_id', $product_id)
            ->where('order_details_id', $request->order_details_id)
            ->first();

        if ($orderReturn) {
            $orderReturn->new_quantity -= $quantity;
            $orderReturn->system_lose += $quantity;
            $orderReturn->save();
        }

        $stock = Stock::where('product_id', $product_id)
            ->where('size', $size)
            ->where('color', $color)
            ->where('warehouse_id', $warehouseId)
            ->first();

        // if ($stock) {
        //     $stock->quantity -= $quantity;
        //     $stock->save();

        //     $history = StockHistory::where('stock_id', $stock->id)
        //         ->where('available_qty', '!=', $quantity)
        //         ->orderBy('created_at', 'desc')
        //         ->first();

        //     if ($history) {
        //         $history->systemloss_qty = ($history->systemloss_qty ?? 0) + $quantity;
        //         $history->available_qty -= $quantity;
        //         $history->save();
        //     }
        // }

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

    public function checkInvoice(Request $request)
    {
        $season = $request->input('season');
        $invoice = $request->input('invoice');
    
        $invoicePrefix = "STL-{$season}-" . date('Y') . "-{$invoice}";
    
        $exists = Purchase::where('invoice', 'like', $invoicePrefix)->exists();
    
        return response()->json(['exists' => $exists]);
    } 

    public function getWarehouses(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $user = Auth::user();
        // $warehouseIds = json_decode($user->warehouse_ids, true);

        $query = Stock::where('product_id', $request->product_id)
        ->where('quantity', '>', 0);

        // if (!empty($warehouseIds)) {
        //     $query->whereIn('warehouse_id', $warehouseIds);
        // }

        $warehouses = $query->with('warehouse')
            ->groupBy('warehouse_id')
            ->get();

        return response()->json(['warehouses' => $warehouses]);
    }

    public function getColors(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists :warehouses,id',
        ]);

        $colors = Stock::where('product_id', $request->product_id)
            ->where('warehouse_id', $request->warehouse_id)
            ->where('quantity', '>', 0)
            ->select('color')
            ->distinct()
            ->get();

        return response()->json(['colors' => $colors]);
    }

    public function getSizes(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'color' => 'required|string',
        ]);
    
        $sizes = Stock::where('product_id', $request->product_id)
            ->where('warehouse_id', $request->warehouse_id)
            ->where('color', $request->color)
            ->where('quantity', '>', 0)
            ->select('size')
            ->distinct()
            ->get();
    
        return response()->json(['sizes' => $sizes]);
    }

    public function getTypes(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'color' => 'required|string',
            'size' => 'required|string',
        ]);

        try {
            $types = Stock::with('type') // Eager load the type relationship
                ->where('product_id', $request->product_id)
                ->where('warehouse_id', $request->warehouse_id)
                ->where('color', $request->color)
                ->where('size', $request->size)
                ->where('quantity', '>', 0)
                ->whereNotNull('type_id') // Only stocks with a type
                ->get()
                ->pluck('type') // Get the Type models
                ->unique('id') // Get unique types by ID
                ->map(function ($type) {
                    return [
                        'id' => $type->id,
                        'name' => $type->name // Assuming your Type model has a 'name' field
                    ];
                })
                ->values();

            return response()->json([
                'success' => true,
                'types' => $types
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch types: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getMaxQuantity(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'color' => 'required|string',
            'size' => 'required|string',
            'zip' => 'nullable|integer',
            'type_id' => 'nullable|integer',
        ]);

        $query = Stock::where('product_id', $request->product_id)
            ->where('warehouse_id', $request->warehouse_id)
            ->where('color', $request->color)
            ->where('size', $request->size)
            ->where('quantity', '>', 0);

        if ($request->filled('zip')) {
            $query->where('zip', $request->zip);
        }

        if ($request->filled('type_id')) {
            $query->where('type_id', $request->type_id);
        }

        $max_quantity = (int) $query->value('quantity');

        return response()->json(['max_quantity' => $max_quantity]);
    }

    public function generateInvoice($encoded_purchase_id)
    {
        $purchase_id = base64_decode($encoded_purchase_id);
        $purchase = Purchase::with(['supplier', 'purchaseHistory.product', 'purchaseHistory.type'])->findOrFail($purchase_id);
    
        $company = CompanyDetails::select(
            'company_name', 'company_logo', 'address1', 'email1', 'phone1', 'website', 
            'company_reg_number', 'vat_number'
        )->first();
    
        $pdf = PDF::loadView('admin.stock.purchase_invoice', compact('purchase', 'company'));
        
        return $pdf->stream('purchase_' . $purchase->id . '.pdf');
    }    

}
