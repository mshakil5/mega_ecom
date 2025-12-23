<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Color;
use App\Models\Purchase;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use App\Models\PurchaseHistory;
use App\Models\SampleProduct;
use Illuminate\Support\Facades\DB;
use App\Models\Size;
use App\Models\SystemLose;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Models\ShipmentDetails;

class DirectPurchaseController extends Controller
{
    public function purchase()
    {
        $products = Product::with(['stock', 'types:id,name'])->orderBy('id','DESC')->select('id', 'name', 'price', 'product_code')->where('active_status', 1)->get();
        $suppliers = Supplier::where('status', 1)->select('id', 'name')->orderby('id','DESC')->get();
        $colors = Color::where('status', 1)->select('id', 'color')->orderby('id','DESC')->get();
        $sizes = Size::where('status', 1)->select('id', 'size')->orderby('id','DESC')->get();
        $warehouses = Warehouse::select('id', 'name','location')->where('status', 1)->get();
        $expenses = ChartOfAccount::where('account_head', 'Expenses')->where('sub_account_head', 'Cost Of Good Sold')->select('id', 'account_name')->get();

        return view('admin.stock.direct_purchase', compact('products', 'suppliers', 'colors', 'sizes','warehouses','expenses'));
    }

    public function stockStore(Request $request)
    {
        $validated = $request->validate([
            'season'                   => 'required|string',
            'supplier_id'              => 'required|exists:suppliers,id',
            'purchase_date'            => 'required|date',
            'total_amount'             => 'required|numeric',
            'discount'                 => 'nullable|numeric',
            'total_vat_amount'         => 'required|numeric',
            'net_amount'               => 'required|numeric',
            'due_amount'               => 'required|numeric',
            'cash_payment'             => 'nullable|numeric',
            'bank_payment'             => 'nullable|numeric',
            'warehouse_id'             => 'required|exists:warehouses,id',

            'products'                             => 'required|array',
            'products.*.product_id'                => 'required|exists:products,id',
            'products.*.quantity'                  => 'required|numeric|min:1',

            'products.*.sample_quantity'           => 'nullable|numeric',
            'products.*.missing_quantity'          => 'nullable|numeric',
            'products.*.profit_margin'             => 'nullable|numeric',
            'products.*.selling_price_per_unit'    => 'nullable|numeric',

            'products.*.product_size'              => 'nullable|string',
            'products.*.product_color'             => 'nullable|string',
            'products.*.type_id'                   => 'nullable|exists:types,id',
            'products.*.unit_price'                => 'required|numeric',
            'products.*.vat_percent'               => 'nullable|numeric',
            'products.*.vat_amount'                => 'nullable|numeric',
            'products.*.total_price_with_vat'      => 'required|numeric',
            'products.*.zip'                       => 'nullable|string',

            'expenses.*.expense_id'           => 'required|numeric',
            'expenses.*.payment_type'         => 'nullable|string',
            'expenses.*.amount'               => 'required|numeric',
            'expenses.*.description'          => 'nullable|string',
            'expenses.*.note'                 => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {

            $prefix = 'INV-' . date('ym') . '-';

            $lastInvoice = Purchase::where('invoice', 'like', "%{$prefix}%")
                ->orderBy('id', 'desc')
                ->value('invoice');

            $lastSeq = 0;

            if ($lastInvoice) {
                if (preg_match("/{$prefix}(\d{4})/", $lastInvoice, $matches)) {
                    $lastSeq = (int) $matches[1];
                }
            }

            $nextSeq = $lastSeq + 1;
            $invoicePrefix = "STL-{$validated['season']}-" . $prefix . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);

            while (Purchase::where('invoice', $invoicePrefix)->exists()) {
                $nextSeq++;
                $invoicePrefix = "STL-{$validated['season']}-" . $prefix . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
            }

            $purchase = Purchase::create([
                'invoice'          => $invoicePrefix,
                'supplier_id'      => $validated['supplier_id'],
                'purchase_date'    => $validated['purchase_date'],
                'purchase_type'    => $request->purchase_type,
                'total_amount'     => $validated['total_amount'],
                'discount'         => $validated['discount'],
                'total_vat_amount' => $validated['total_vat_amount'],
                'net_amount'       => $validated['net_amount'],
                'paid_amount'      => ($request->cash_payment ?? 0) + ($request->bank_payment ?? 0),
                'other_cost'       => ($request->total_additional_cost ?? 0),
                'due_amount'       => $validated['net_amount'] - (($request->cash_payment ?? 0) + ($request->bank_payment ?? 0)),
                'status'           => 1,
                'direct_purchase'  => 1,
                'created_by'       => Auth::id(),
            ]);

            $total_quantity = 0;
            $total_missing_quantity = 0;
            $total_purchase_cost = 0;
            $total_additional_cost = $request->total_additional_cost ?? 0;
            $total_saleable_quantity = 0;

            $shipment = Shipment::create([
                'shipping_id' => null,
                'total_product_quantity' => 0,
                'total_missing_quantity' => 0,
                'total_purchase_cost' => 0,
                'total_additional_cost' => $total_additional_cost,
                'total_profit' => 0,
                'target_budget' => 0,
                'budget_over' => 0,
                'total_cost_of_shipment' => $validated['net_amount'],
                'purchase_ids' => json_encode([(string) $purchase->id]),
                'created_by' => auth()->id(),
            ]);

            foreach ($validated['products'] as $item) {

                $vatPerUnit = ($item['unit_price'] * ($item['vat_percent'] ?? 0)) / 100;
                $totalVat   = $vatPerUnit * $item['quantity'];
                $totalPrice = $item['unit_price'] * $item['quantity'];

                $missing_quantity = $item['missing_quantity'] ?? 0;
                $sample_quantity = $item['sample_quantity'] ?? 0;
                $saleable_quantity = $item['quantity'] - $missing_quantity - $sample_quantity;

                $purchaseHistory = PurchaseHistory::create([
                    'purchase_id'               => $purchase->id,
                    'product_id'                => $item['product_id'],
                    'type_id'                   => $item['type_id'] ?? null,
                    'quantity'                  => $item['quantity'],
                    'product_size'              => $item['product_size'],
                    'product_color'             => $item['product_color'],
                    'purchase_price'            => $item['unit_price'],
                    'vat_percent'               => $item['vat_percent'] ?? 0,
                    'vat_amount_per_unit'       => $vatPerUnit,
                    'total_vat'                 => $totalVat,
                    'total_amount'              => $totalPrice,
                    'total_amount_with_vat'     => $totalPrice + $totalVat,
                    'remaining_product_quantity'=> $saleable_quantity,
                    'transferred_product_quantity'=> 0,
                    'zip'                       => $item['zip'] ?? 0,
                    'created_by'                => Auth::id(),
                ]);

                $total_quantity += $saleable_quantity;
                $total_missing_quantity += $missing_quantity;
                $total_purchase_cost += ($item['unit_price'] * $saleable_quantity);
                $total_saleable_quantity += $saleable_quantity;

                $additional_cost_per_item = $total_additional_cost > 0 ? 
                    ($total_additional_cost / $total_saleable_quantity) : 0;
                $ground_cost_per_item = $item['unit_price'] + $additional_cost_per_item;

                $profit_margin = $item['profit_margin'] ?? 30;
                $selling_price = $ground_cost_per_item + ($ground_cost_per_item * $profit_margin / 100);

                $shipmentDetail = ShipmentDetails::create([
                    'shipment_id' => $shipment->id,
                    'product_id' => $item['product_id'],
                    'supplier_id' => $validated['supplier_id'],
                    'purchase_history_id' => $purchaseHistory->id,
                    'size' => $item['product_size'],
                    'color' => $item['product_color'],
                    'type_id' => $item['type_id'] ?? null,
                    'warehouse_id' => $validated['warehouse_id'],
                    'quantity' => $saleable_quantity,
                    'shipped_quantity' => 0,
                    'missing_quantity' => $missing_quantity,
                    'price_per_unit' => $item['unit_price'],
                    'ground_price_per_unit' => $ground_cost_per_item,
                    'profit_margin' => $profit_margin,
                    'selling_price' => $selling_price,
                    'considerable_margin' => 0,
                    'considerable_price' => 0,
                    'sample_quantity' => $sample_quantity,
                ]);

                Product::where('id', $item['product_id'])
                    ->update(['price' => $item['unit_price']]);

                if (!empty($missing_quantity) && $missing_quantity > 0) {
                    SystemLose::create([
                        'product_id' => $item['product_id'],
                        'shipment_detail_id' => $shipmentDetail->id,
                        'warehouse_id' => $validated['warehouse_id'],
                        'quantity' => $missing_quantity,
                        'size' => $item['product_size'],
                        'color' => $item['product_color'],
                        'type_id' => $item['type_id'] ?? null,
                        'reason' => 'Damaged from purchase',
                        'created_by' => auth()->id()
                    ]);
                }

                if (!empty($sample_quantity) && $sample_quantity > 0) {
                    SampleProduct::create([
                        'product_id' => $item['product_id'],
                        'warehouse_id' => $validated['warehouse_id'],
                        'shipment_detail_id' => $shipmentDetail->id,
                        'purchase_id' => $purchase->id,
                        'quantity' => $sample_quantity,
                        'size' => $item['product_size'],
                        'color' => $item['product_color'],
                        'reason' => 'Sample quantity create from purchase',
                        'created_by' => auth()->id()
                    ]);
                }
            }

            $shipment->update([
                'total_product_quantity' => $total_quantity,
                'total_missing_quantity' => $total_missing_quantity,
                'total_purchase_cost' => $total_purchase_cost,
            ]);

            $mainTransaction = Transaction::create([
                'date'           => $validated['purchase_date'],
                'supplier_id'    => $validated['supplier_id'],
                'purchase_id'    => $purchase->id,
                'table_type'     => 'Purchase',
                'description'    => 'Purchase',
                'payment_type'   => 'Credit',
                'transaction_type'=> 'Due',
                'amount'         => $validated['total_amount'],
                'vat_amount'     => $validated['total_vat_amount'],
                'discount'       => $validated['discount'] ?? 0,
                'at_amount'      => $validated['net_amount'],
                'created_by'      => auth()->id(),
            ]);

            $mainTransaction->update([
                'tran_id' => 'SL' . date('ymd') . str_pad($mainTransaction->id, 4, '0', STR_PAD_LEFT)
            ]);

            if ($request->cash_payment > 0) {
                $this->storePaymentTransaction(
                    $validated['purchase_date'],
                    $validated['supplier_id'],
                    $purchase->id,
                    'Cash',
                    $request->cash_payment
                );
            }

            if ($request->bank_payment > 0) {
                $this->storePaymentTransaction(
                    $validated['purchase_date'],
                    $validated['supplier_id'],
                    $purchase->id,
                    'Bank',
                    $request->bank_payment
                );
            }

            $expenses = $request->input('expenses');

            foreach ($expenses as $expense) {
                $transaction = new Transaction();
                $transaction->date = $validated['purchase_date'];
                $transaction->table_type = 'Expenses';
                $transaction->purchase_id = $purchase->id;
                $transaction->shipment_id = $shipment->id;
                $transaction->supplier_id = $validated['supplier_id'];
                $transaction->amount = $expense['amount'];
                $transaction->at_amount = $expense['amount'];
                $transaction->payment_type = $expense['payment_type'];
                $transaction->chart_of_account_id = $expense['expense_id'];
                $transaction->expense_id = $expense['expense_id'];
                $transaction->description = $expense['description'];
                $transaction->note = $expense['note'];
                $transaction->transaction_type = 'Current';
                $transaction->created_by = auth()->id();
                $transaction->save();

                $transaction->tran_id = 'EX' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
                $transaction->save();
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Purchase saved successfully!',
                'purchase_id' => $purchase->id,
                'shipment_id' => $shipment->id,
            ]);

        } catch (\Exception $e) {

            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function edit($id)
    {
        $purchase = Purchase::with([
            'purchaseHistory' => function($q) {
                $q->with('product');
            },
            'supplier'
        ])->findOrFail($id);

        // Get shipment separately since purchase_ids is JSON
        $shipment = Shipment::where('purchase_ids', 'like', "%{$purchase->id}%")
            ->with('shipmentDetails')
            ->first();

        // Only allow editing if status is not 4 (Received)
        if ($purchase->status == 4) {
            return redirect()->back()->with('error', 'Cannot edit a received purchase.');
        }

        // Get products, suppliers, colors, sizes, warehouses, and expenses
        $products = Product::with(['stock', 'types:id,name'])->orderBy('id','DESC')->select('id', 'name', 'price', 'product_code')->where('active_status', 1)->get();
        $suppliers = Supplier::where('status', 1)->select('id', 'name')->orderby('id','DESC')->get();
        $colors = Color::where('status', 1)->select('id', 'color')->orderby('id','DESC')->get();
        $sizes = Size::where('status', 1)->select('id', 'size')->orderby('id','DESC')->get();
        $warehouses = Warehouse::select('id', 'name','location')->where('status', 1)->get();
        $expenses = ChartOfAccount::where('account_head', 'Expenses')->where('sub_account_head', 'Cost Of Good Sold')->select('id', 'account_name')->get();

        // Get transactions for this purchase
        $purchaseTransactions = Transaction::where('purchase_id', $id)
            ->where('table_type', 'Purchase')
            ->get();

        $expenseTransactions = Transaction::where('purchase_id', $id)
            ->where('table_type', 'Expenses')
            ->get();

        $paymentTransactions = Transaction::where('purchase_id', $id)
            ->where('transaction_type', 'Current')
            ->where('table_type', 'Purchase')
            ->get();

        return view('admin.stock.direct_purchase_edit', compact(
            'purchase',
            'products',
            'suppliers',
            'colors',
            'sizes',
            'warehouses',
            'expenses',
            'purchaseTransactions',
            'expenseTransactions',
            'paymentTransactions',
            'shipment'
        ));
    }

    public function update(Request $request, $id)
    {
        $purchase = Purchase::findOrFail($id);

        if ($purchase->status == 4) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot update a received purchase.',
            ], 403);
        }

        $validated = $request->validate([
            'season'                   => 'required|string',
            'supplier_id'              => 'required|exists:suppliers,id',
            'purchase_date'            => 'required|date',
            'total_amount'             => 'required|numeric',
            'discount'                 => 'nullable|numeric',
            'total_vat_amount'         => 'required|numeric',
            'net_amount'               => 'required|numeric',
            'due_amount'               => 'required|numeric',
            'cash_payment'             => 'nullable|numeric',
            'bank_payment'             => 'nullable|numeric',
            'warehouse_id'             => 'required|exists:warehouses,id',
            'total_additional_cost'    => 'nullable|numeric',

            'products'                             => 'required|array',
            'products.*.product_id'                => 'required|exists:products,id',
            'products.*.quantity'                  => 'required|numeric|min:1',
            'products.*.product_size'              => 'nullable|string',
            'products.*.product_color'             => 'nullable|string',
            'products.*.type_id'                   => 'nullable|exists:types,id',
            'products.*.unit_price'                => 'required|numeric',
            'products.*.vat_percent'               => 'nullable|numeric',
            'products.*.sample_quantity'           => 'nullable|numeric',
            'products.*.missing_quantity'          => 'nullable|numeric',
            'products.*.profit_margin'             => 'nullable|numeric',
            'products.*.selling_price_per_unit'    => 'nullable|numeric',
            'products.*.zip'                       => 'nullable|string',

            'expenses'                             => 'nullable|array',
            'expenses.*.expense_id'                => 'nullable|numeric',
            'expenses.*.payment_type'              => 'nullable|string',
            'expenses.*.amount'                    => 'nullable|numeric',
            'expenses.*.description'               => 'nullable|string',
            'expenses.*.note'                      => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $purchase->update([
                'supplier_id'    => $validated['supplier_id'],
                'purchase_date'  => $validated['purchase_date'],
                'total_amount'   => $validated['total_amount'],
                'discount'       => $validated['discount'] ?? 0,
                'total_vat_amount' => $validated['total_vat_amount'],
                'net_amount'     => $validated['net_amount'],
                'paid_amount'    => ($request->cash_payment ?? 0) + ($request->bank_payment ?? 0),
                'other_cost'     => ($request->total_additional_cost ?? 0),
                'due_amount'     => $validated['net_amount'] - (($request->cash_payment ?? 0) + ($request->bank_payment ?? 0)),
                'updated_by'     => Auth::id(),
            ]);

            $shipment = Shipment::where('purchase_ids', 'like', "%{$purchase->id}%")->first();
            
            if (!$shipment) {
                throw new \Exception('Shipment not found');
            }

            $existingPurchaseHistoryIds = PurchaseHistory::where('purchase_id', $id)->pluck('id')->toArray();
            $updatedHistoryIds = [];
            
            $total_quantity = 0;
            $total_missing_quantity = 0;
            $total_purchase_cost = 0;
            $total_additional_cost = $request->total_additional_cost ?? 0;
            $total_saleable_quantity = 0;

            // FIRST PASS: Calculate total saleable quantity
            foreach ($validated['products'] as $item) {
                $missing_quantity = $item['missing_quantity'] ?? 0;
                $sample_quantity = $item['sample_quantity'] ?? 0;
                $saleable_quantity = $item['quantity'] - $missing_quantity - $sample_quantity;
                $total_saleable_quantity += $saleable_quantity;
            }

            // SECOND PASS: Process each product
            foreach ($validated['products'] as $index => $item) {
                $vatPerUnit = ($item['unit_price'] * ($item['vat_percent'] ?? 0)) / 100;
                $totalVat   = $vatPerUnit * $item['quantity'];
                $totalPrice = $item['unit_price'] * $item['quantity'];

                $missing_quantity = $item['missing_quantity'] ?? 0;
                $sample_quantity = $item['sample_quantity'] ?? 0;
                $saleable_quantity = $item['quantity'] - $missing_quantity - $sample_quantity;

                // Calculate ground cost with additional cost distribution
                $additional_cost_per_item = $total_additional_cost > 0 ? 
                    ($total_additional_cost / $total_saleable_quantity) : 0;
                $ground_cost_per_item = $item['unit_price'] + $additional_cost_per_item;

                $profit_margin = $item['profit_margin'] ?? 30;
                $selling_price = $ground_cost_per_item + ($ground_cost_per_item * $profit_margin / 100);

                $existingHistory = null;
                if ($index < count($existingPurchaseHistoryIds)) {
                    $existingHistory = PurchaseHistory::find($existingPurchaseHistoryIds[$index]);
                }

                if ($existingHistory) {
                    $existingHistory->update([
                        'product_id'                => $item['product_id'],
                        'type_id'                   => $item['type_id'] ?? null,
                        'quantity'                  => $item['quantity'],
                        'product_size'              => $item['product_size'],
                        'product_color'             => $item['product_color'],
                        'purchase_price'            => $item['unit_price'],
                        'vat_percent'               => $item['vat_percent'] ?? 0,
                        'vat_amount_per_unit'       => $vatPerUnit,
                        'total_vat'                 => $totalVat,
                        'total_amount'              => $totalPrice,
                        'total_amount_with_vat'     => $totalPrice + $totalVat,
                        'remaining_product_quantity'=> $saleable_quantity,
                        'zip'                       => $item['zip'] ?? 0,
                        'updated_by'                => Auth::id(),
                    ]);
                    
                    $purchaseHistory = $existingHistory;
                    $updatedHistoryIds[] = $existingHistory->id;
                } else {
                    $purchaseHistory = PurchaseHistory::create([
                        'purchase_id'               => $purchase->id,
                        'product_id'                => $item['product_id'],
                        'type_id'                   => $item['type_id'] ?? null,
                        'quantity'                  => $item['quantity'],
                        'product_size'              => $item['product_size'],
                        'product_color'             => $item['product_color'],
                        'purchase_price'            => $item['unit_price'],
                        'vat_percent'               => $item['vat_percent'] ?? 0,
                        'vat_amount_per_unit'       => $vatPerUnit,
                        'total_vat'                 => $totalVat,
                        'total_amount'              => $totalPrice,
                        'total_amount_with_vat'     => $totalPrice + $totalVat,
                        'remaining_product_quantity'=> $saleable_quantity,
                        'transferred_product_quantity'=> 0,
                        'zip'                       => $item['zip'] ?? 0,
                        'created_by'                => Auth::id(),
                    ]);
                    
                    $updatedHistoryIds[] = $purchaseHistory->id;
                }

                $total_quantity += $saleable_quantity;
                $total_missing_quantity += $missing_quantity;
                $total_purchase_cost += ($item['unit_price'] * $saleable_quantity);

                $shipmentDetail = ShipmentDetails::where('purchase_history_id', $purchaseHistory->id)->first();
                
                if ($shipmentDetail) {
                    $shipmentDetail->update([
                        'product_id' => $item['product_id'],
                        'supplier_id' => $validated['supplier_id'],
                        'size' => $item['product_size'],
                        'color' => $item['product_color'],
                        'type_id' => $item['type_id'] ?? null,
                        'warehouse_id' => $validated['warehouse_id'],
                        'quantity' => $saleable_quantity,
                        'missing_quantity' => $missing_quantity,
                        'price_per_unit' => $item['unit_price'],
                        'ground_price_per_unit' => $ground_cost_per_item,
                        'profit_margin' => $profit_margin,
                        'selling_price' => $selling_price,
                        'sample_quantity' => $sample_quantity,
                    ]);
                } else {
                    $shipmentDetail = ShipmentDetails::create([
                        'shipment_id' => $shipment->id,
                        'product_id' => $item['product_id'],
                        'supplier_id' => $validated['supplier_id'],
                        'purchase_history_id' => $purchaseHistory->id,
                        'size' => $item['product_size'],
                        'color' => $item['product_color'],
                        'type_id' => $item['type_id'] ?? null,
                        'warehouse_id' => $validated['warehouse_id'],
                        'quantity' => $saleable_quantity,
                        'shipped_quantity' => 0,
                        'missing_quantity' => $missing_quantity,
                        'price_per_unit' => $item['unit_price'],
                        'ground_price_per_unit' => $ground_cost_per_item,
                        'profit_margin' => $profit_margin,
                        'selling_price' => $selling_price,
                        'considerable_margin' => 0,
                        'considerable_price' => 0,
                        'sample_quantity' => $sample_quantity,
                    ]);
                }

                Product::where('id', $item['product_id'])
                    ->update(['price' => $item['unit_price']]);

                if (!empty($missing_quantity) && $missing_quantity > 0) {
                    $systemLose = SystemLose::where('shipment_detail_id', $shipmentDetail->id)->first();
                    
                    if ($systemLose) {
                        $systemLose->update([
                            'product_id' => $item['product_id'],
                            'warehouse_id' => $validated['warehouse_id'],
                            'quantity' => $missing_quantity,
                            'size' => $item['product_size'],
                            'color' => $item['product_color'],
                            'type_id' => $item['type_id'] ?? null,
                            'reason' => 'Damaged from purchase',
                            'updated_by' => auth()->id()
                        ]);
                    } else {
                        SystemLose::create([
                            'product_id' => $item['product_id'],
                            'shipment_detail_id' => $shipmentDetail->id,
                            'warehouse_id' => $validated['warehouse_id'],
                            'quantity' => $missing_quantity,
                            'size' => $item['product_size'],
                            'color' => $item['product_color'],
                            'type_id' => $item['type_id'] ?? null,
                            'reason' => 'Damaged from purchase',
                            'created_by' => auth()->id()
                        ]);
                    }
                } else {
                    SystemLose::where('shipment_detail_id', $shipmentDetail->id)->delete();
                }

                if (!empty($sample_quantity) && $sample_quantity > 0) {
                    $sampleProduct = SampleProduct::where('shipment_detail_id', $shipmentDetail->id)->first();
                    
                    if ($sampleProduct) {
                        $sampleProduct->update([
                            'product_id' => $item['product_id'],
                            'warehouse_id' => $validated['warehouse_id'],
                            'purchase_id' => $purchase->id,
                            'quantity' => $sample_quantity,
                            'size' => $item['product_size'],
                            'color' => $item['product_color'],
                            'reason' => 'Sample quantity create from purchase',
                            'updated_by' => auth()->id()
                        ]);
                    } else {
                        SampleProduct::create([
                            'product_id' => $item['product_id'],
                            'warehouse_id' => $validated['warehouse_id'],
                            'shipment_detail_id' => $shipmentDetail->id,
                            'purchase_id' => $purchase->id,
                            'quantity' => $sample_quantity,
                            'size' => $item['product_size'],
                            'color' => $item['product_color'],
                            'reason' => 'Sample quantity create from purchase',
                            'created_by' => auth()->id()
                        ]);
                    }
                } else {
                    SampleProduct::where('shipment_detail_id', $shipmentDetail->id)->delete();
                }
            }

            $deletedHistoryIds = array_diff($existingPurchaseHistoryIds, $updatedHistoryIds);
            if (!empty($deletedHistoryIds)) {
                PurchaseHistory::whereIn('id', $deletedHistoryIds)->delete();
                ShipmentDetails::whereIn('purchase_history_id', $deletedHistoryIds)->delete();
                SystemLose::whereIn('shipment_detail_id', 
                    ShipmentDetails::whereIn('purchase_history_id', $deletedHistoryIds)->pluck('id')
                )->delete();
                SampleProduct::whereIn('shipment_detail_id',
                    ShipmentDetails::whereIn('purchase_history_id', $deletedHistoryIds)->pluck('id')
                )->delete();
            }

            $shipment->update([
                'total_product_quantity' => $total_quantity,
                'total_missing_quantity' => $total_missing_quantity,
                'total_purchase_cost' => $total_purchase_cost,
                'total_additional_cost' => $total_additional_cost,
                'total_cost_of_shipment' => $validated['net_amount'],
            ]);

            $mainTransaction = Transaction::where('purchase_id', $id)
                ->where('table_type', 'Purchase')
                ->where('transaction_type', 'Due')
                ->first();

            if ($mainTransaction) {
                $mainTransaction->update([
                    'date'           => $validated['purchase_date'],
                    'supplier_id'    => $validated['supplier_id'],
                    'amount'         => $validated['total_amount'],
                    'vat_amount'     => $validated['total_vat_amount'],
                    'discount'       => $validated['discount'] ?? 0,
                    'at_amount'      => $validated['net_amount'],
                    'updated_by'     => auth()->id(),
                ]);
            } else {
                $mainTransaction = Transaction::create([
                    'date'           => $validated['purchase_date'],
                    'supplier_id'    => $validated['supplier_id'],
                    'purchase_id'    => $purchase->id,
                    'table_type'     => 'Purchase',
                    'description'    => 'Purchase',
                    'payment_type'   => 'Credit',
                    'transaction_type'=> 'Due',
                    'amount'         => $validated['total_amount'],
                    'vat_amount'     => $validated['total_vat_amount'],
                    'discount'       => $validated['discount'] ?? 0,
                    'at_amount'      => $validated['net_amount'],
                    'created_by'     => auth()->id(),
                ]);
                
                $mainTransaction->update([
                    'tran_id' => 'SL' . date('ymd') . str_pad($mainTransaction->id, 4, '0', STR_PAD_LEFT)
                ]);
            }

            $cashPayment = $request->cash_payment ?? 0;
            $bankPayment = $request->bank_payment ?? 0;
            
            if ($cashPayment > 0) {
                $cashTransaction = Transaction::where('purchase_id', $id)
                    ->where('transaction_type', 'Current')
                    ->where('table_type', 'Purchase')
                    ->where('payment_type', 'Cash')
                    ->first();
                    
                if ($cashTransaction) {
                    $cashTransaction->update([
                        'date'        => $validated['purchase_date'],
                        'supplier_id' => $validated['supplier_id'],
                        'amount'      => $cashPayment,
                        'at_amount'   => $cashPayment,
                        'updated_by'  => auth()->id(),
                    ]);
                } else {
                    $this->storePaymentTransaction(
                        $validated['purchase_date'],
                        $validated['supplier_id'],
                        $purchase->id,
                        'Cash',
                        $cashPayment
                    );
                }
            } else {
                Transaction::where('purchase_id', $id)
                    ->where('transaction_type', 'Current')
                    ->where('table_type', 'Purchase')
                    ->where('payment_type', 'Cash')
                    ->delete();
            }
            
            if ($bankPayment > 0) {
                $bankTransaction = Transaction::where('purchase_id', $id)
                    ->where('transaction_type', 'Current')
                    ->where('table_type', 'Purchase')
                    ->where('payment_type', 'Bank')
                    ->first();
                    
                if ($bankTransaction) {
                    $bankTransaction->update([
                        'date'        => $validated['purchase_date'],
                        'supplier_id' => $validated['supplier_id'],
                        'amount'      => $bankPayment,
                        'at_amount'   => $bankPayment,
                        'updated_by'  => auth()->id(),
                    ]);
                } else {
                    $this->storePaymentTransaction(
                        $validated['purchase_date'],
                        $validated['supplier_id'],
                        $purchase->id,
                        'Bank',
                        $bankPayment
                    );
                }
            } else {
                Transaction::where('purchase_id', $id)
                    ->where('transaction_type', 'Current')
                    ->where('table_type', 'Purchase')
                    ->where('payment_type', 'Bank')
                    ->delete();
            }

            $expenses = $request->input('expenses', []);
            $existingExpenseIds = Transaction::where('purchase_id', $id)
                ->where('table_type', 'Expenses')
                ->pluck('id')
                ->toArray();
            $updatedExpenseIds = [];
            
            foreach ($expenses as $index => $expense) {
                if (!empty($expense['expense_id'])) {
                    $existingExpense = null;
                    if ($index < count($existingExpenseIds)) {
                        $existingExpense = Transaction::find($existingExpenseIds[$index]);
                    }
                    
                    if ($existingExpense) {
                        $existingExpense->update([
                            'date' => $validated['purchase_date'],
                            'supplier_id' => $validated['supplier_id'],
                            'amount' => $expense['amount'],
                            'at_amount' => $expense['amount'],
                            'payment_type' => $expense['payment_type'] ?? 'Cash',
                            'chart_of_account_id' => $expense['expense_id'],
                            'expense_id' => $expense['expense_id'],
                            'description' => $expense['description'] ?? '',
                            'note' => $expense['note'] ?? '',
                            'updated_by' => auth()->id(),
                        ]);
                        
                        $updatedExpenseIds[] = $existingExpense->id;
                    } else {
                        $transaction = new Transaction();
                        $transaction->date = $validated['purchase_date'];
                        $transaction->table_type = 'Expenses';
                        $transaction->purchase_id = $purchase->id;
                        $transaction->shipment_id = $shipment->id;
                        $transaction->supplier_id = $validated['supplier_id'];
                        $transaction->amount = $expense['amount'];
                        $transaction->at_amount = $expense['amount'];
                        $transaction->payment_type = $expense['payment_type'] ?? 'Cash';
                        $transaction->chart_of_account_id = $expense['expense_id'];
                        $transaction->expense_id = $expense['expense_id'];
                        $transaction->description = $expense['description'] ?? '';
                        $transaction->note = $expense['note'] ?? '';
                        $transaction->transaction_type = 'Current';
                        $transaction->created_by = auth()->id();
                        $transaction->save();

                        $transaction->tran_id = 'EX' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
                        $transaction->save();
                        
                        $updatedExpenseIds[] = $transaction->id;
                    }
                }
            }
            
            $deletedExpenseIds = array_diff($existingExpenseIds, $updatedExpenseIds);
            if (!empty($deletedExpenseIds)) {
                Transaction::whereIn('id', $deletedExpenseIds)->delete();
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Purchase updated successfully!',
                'purchase_id' => $purchase->id,
                'shipment_id' => $shipment->id,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    private function storePaymentTransaction($date, $supplierId, $purchaseId, $paymentType, $amount)
    {
        $trx = Transaction::create([
            'date'            => $date,
            'supplier_id'     => $supplierId,
            'purchase_id'     => $purchaseId,
            'table_type'      => 'Purchase',
            'description'     => 'Purchase Payment',
            'payment_type'    => $paymentType,
            'transaction_type'=> 'Current',
            'amount'          => $amount,
            'at_amount'       => $amount,
            'created_by'      => auth()->id(),
        ]);

        $trx->update([
            'tran_id' => 'SL' . date('ymd') . str_pad($trx->id, 4, '0', STR_PAD_LEFT)
        ]);
    }
}