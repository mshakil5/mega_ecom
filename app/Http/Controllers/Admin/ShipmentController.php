<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Models\ShipmentDetails;
use App\Models\StockHistory;
use App\Models\Stock;
use App\Models\Shipping;
use App\Models\PurchaseHistory;
use App\Models\Purchase;
use App\Models\Warehouse;
use App\Models\SystemLose;
use App\Models\Transaction;
use Illuminate\Support\Facades\Validator;

class ShipmentController extends Controller
{
    public function createShipment($id)
    {
        $shipping = Shipping::with('shipment')->find($id);
        if (!$shipping) {
            return redirect()->back()->with('error', 'Shipping not found.');
        }

        $purchaseIds = json_decode($shipping->purchase_ids, true);
        $purchaseHistories = PurchaseHistory::whereIn('purchase_id', $purchaseIds)
            ->with('product', 'purchase.supplier', 'type')
            ->orderBy('purchase_id', 'asc')
            ->get();

        $warehouses = Warehouse::select('id', 'name','location')->where('status', 1)->get();

        $expenses = ChartOfAccount::where('account_head', 'Expenses')->where('sub_account_head', 'Cost Of Good Sold')->select('id', 'account_name')->get();
        return view('admin.shipment.create', compact('shipping', 'purchaseHistories', 'warehouses', 'expenses'));
    }

    public function storeShipment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shipping_id' => 'required|integer',
            'warehouse_id' => 'required',
            'total_quantity' => 'required|integer',
            'total_purchase_cost' => 'required|numeric',
            'total_additional_cost' => 'required|numeric',
            'target_budget' => 'required',
            'shipment_details' => 'required|array',
            'shipment_details.*.supplier_id' => 'required|integer',
            'shipment_details.*.product_id' => 'required|integer',
            'shipment_details.*.purchase_history_id' => 'required|integer',
            'shipment_details.*.size' => 'required|string',
            'shipment_details.*.color' => 'required|string',
            'shipment_details.*.shipped_quantity' => 'required|integer',
            'shipment_details.*.quantity' => 'required|integer',
            'shipment_details.*.missing_quantity' => 'nullable|integer',
            'shipment_details.*.price_per_unit' => 'required|numeric',
            'shipment_details.*.ground_cost' => 'nullable|numeric',
            'shipment_details.*.profit_margin' => 'nullable|numeric',
            'shipment_details.*.selling_price' => 'nullable|numeric',
            'expenses' => 'required|array|min:1',
            'expenses.*.amount' => 'required|numeric',
            'expenses.*.payment_type' => 'required|string',
            'expenses.*.chart_of_account_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $shipping = Shipping::where('id', $request->shipping_id)->first();
        $purchaseIds = $shipping->purchase_ids ?? [];
        $shipment = Shipment::create([
            'shipping_id' => $request->shipping_id,
            'total_product_quantity' => $request->total_quantity,
            'total_missing_quantity' => $request->total_missing_quantity,
            'total_purchase_cost' => $request->total_purchase_cost,
            'total_additional_cost' => $request->total_additional_cost,
            'total_profit' => $request->total_profit,
            'target_budget' => $request->target_budget,
            'budget_over' => $request->budget_over,
            'total_cost_of_shipment' => $request->total_cost_of_shipment,
            'purchase_ids' => $purchaseIds,
            'created_by' => auth()->id(),
        ]);

        $expenses = $request->input('expenses');

        foreach ($expenses as $expense) {
            $transaction = new Transaction();
            $transaction->date = $shipping->shipping_date;
            $transaction->table_type = 'Expenses';
            $transaction->shipment_id = $shipment->id;
            $transaction->amount = $expense['amount'];
            $transaction->at_amount = $expense['amount'];
            $transaction->payment_type = $expense['payment_type'];
            $transaction->chart_of_account_id = $expense['chart_of_account_id'];
            $transaction->description = $expense['description'];
            $transaction->note = $expense['note'];
            $transaction->transaction_type = 'Current';
            $transaction->created_by = auth()->id();
            $transaction->save();
            $transaction->tran_id = 'EX' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
            $transaction->save();
        }

        foreach ($request->shipment_details as $detail) {
            $detailShipment = ShipmentDetails::create([
                'shipment_id' => $shipment->id,
                'product_id' => $detail['product_id'],
                'supplier_id' => $detail['supplier_id'],
                'purchase_history_id' => $detail['purchase_history_id'],
                'size' => $detail['size'],
                'color' => $detail['color'],
                'type_id' => $detail['type_id'] ?? null,
                'warehouse_id' => $request->warehouse_id,
                'quantity' => $detail['quantity'],
                'shipped_quantity' => $detail['shipped_quantity'],
                'missing_quantity' => $detail['missing_quantity'],
                'price_per_unit' => $detail['price_per_unit'],
                'ground_price_per_unit' => $detail['ground_cost'],
                'profit_margin' => $detail['profit_margin'],
                'selling_price' => $detail['selling_price'],
                'considerable_margin' => $detail['considerable_margin'],
                'considerable_price' => $detail['considerable_price'],
                'sample_quantity' => $detail['sample_quantity'],
            ]);
    
            $purchaseHistory = PurchaseHistory::find($detail['purchase_history_id']);
            if ($purchaseHistory) {
                $purchaseHistory->shipped_quantity = $detail['shipped_quantity'] + $purchaseHistory->shipped_quantity; 
                $purchaseHistory->remaining_product_quantity -= $detail['shipped_quantity']; 
                $purchaseHistory->save();
            }

            if (!empty($detail['missing_quantity']) && $detail['missing_quantity'] > 0) {
                SystemLose::create([
                    'product_id' => $detail['product_id'],
                    'shipment_detail_id' => $detailShipment->id,
                    'warehouse_id' => $request->warehouse_id,
                    'quantity' => $detail['missing_quantity'],
                    'size' => $detail['size'],
                    'color' => $detail['color'],
                    'type_id' => $detail['type_id'] ?? null,
                    'reason' => 'Damaged from shipment',
                    'created_by' => auth()->id()
                ]);
            }

        }
    
        return response()->json(['message' => 'Shipment created successfully!', 'shipment_id' => $shipment->id], 201);
    }    

    public function editShipment($id, $status = null)
    {
        $shipment = Shipment::with('shipmentDetails.supplier', 'shipmentDetails.product', 'shipmentDetails.purchaseHistory', 'transactions', 'shipmentDetails.systemLose')->findOrFail($id);
        $warehouses = Warehouse::select('id', 'name','location')->where('status', 1)->get();
        $expenses = ChartOfAccount::where('account_head', 'Expenses')->where('sub_account_head', 'Cost Of Good Sold')->select('id', 'account_name')->get();
        return view('admin.shipment.edit', compact('shipment', 'warehouses', 'expenses', 'status'));
    }

    public function printShipment($id)
    {
        $shipment = Shipment::with('shipmentDetails.supplier', 'shipmentDetails.product', 'shipmentDetails.purchaseHistory', 'transactions', 'shipmentDetails.type')->findOrFail($id);
        return view('admin.shipment.print', compact('shipment'));
    }

    public function updateShipment(Request $request, $id)
    {
        $validated = $request->validate([
            'total_quantity' => 'required',
            'total_purchase_cost' => 'required',
            'total_additional_cost' => 'required',
            'shipment_details' => 'required|array',
            'shipment_details.*.id' => 'required',
            'shipment_details.*.shipped_quantity' => 'required',
            'shipment_details.*.price_per_unit' => 'required',
            'shipment_details.*.ground_cost' => 'required',
            'shipment_details.*.profit_margin' => 'required',
            'shipment_details.*.selling_price' => 'required',
        ]);

        $shipment = Shipment::findOrFail($id);

        $shipment->total_product_quantity = $request->total_quantity;
        $shipment->total_missing_quantity = $request->total_missing_quantity;
        $shipment->total_purchase_cost = $request->total_purchase_cost;
        $shipment->total_additional_cost = $request->total_additional_cost;
        $shipment->total_profit = $request->total_profit;
        $shipment->target_budget = $request->target_budget;
        $shipment->budget_over = $request->budget_over;
        $shipment->total_cost_of_shipment = $request->total_cost_of_the_shipment;
        $shipment->updated_by = auth()->id();
        $shipment->save();

        $expenses = $request->input('expenses');

        $existingTransactions = $shipment->transactions;

        $incomingTransactionIds = collect($expenses)->pluck('transaction_id')->filter();

        $existingTransactions->whereNotIn('id', $incomingTransactionIds)->each(function ($transaction) {
            $transaction->delete();
        });

        foreach ($expenses as $expense) {
            if (isset($expense['transaction_id']) && $expense['transaction_id']) {
                $transaction = Transaction::find($expense['transaction_id']);
                if ($transaction) {
                    $transaction->amount = $expense['amount'];
                    $transaction->at_amount = $expense['amount'];
                    $transaction->payment_type = $expense['payment_type'];
                    $transaction->updated_by = auth()->id();
                    $transaction->description = $expense['description'] ?? null;
                    $transaction->note = $expense['note'] ?? null;
                    $transaction->updated_by = auth()->id();
                    $transaction->save();
                }
            } else {
                $transaction = new Transaction();
                $transaction->date = $shipment->shipping->shipping_date;
                $transaction->table_type = 'Expenses';
                $transaction->shipment_id = $shipment->id;
                $transaction->amount = $expense['amount'];
                $transaction->at_amount = $expense['amount'];
                $transaction->payment_type = $expense['payment_type'];
                $transaction->chart_of_account_id = $expense['chart_of_account_id'];
                $transaction->description = $expense['description'];
                $transaction->note = $expense['note'];
                $transaction->transaction_type = 'Current';
                $transaction->created_by = auth()->id();
                $transaction->save();

                $transaction->tran_id = 'EX' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
                $transaction->save();

            }
        }

        foreach ($request->shipment_details as $detail) {
            $shipmentDetail = ShipmentDetails::findOrFail($detail['id']);
            
            $oldQuantity = $shipmentDetail->quantity;
            $oldMissingQuantity = $shipmentDetail->missing_quantity;
            $oldShippedQuantity = $shipmentDetail->shipped_quantity;

            $shipmentDetail->quantity = $detail['quantity'];
            $shipmentDetail->missing_quantity = $detail['missing_quantity'];
            $shipmentDetail->price_per_unit = $detail['price_per_unit'];
            $shipmentDetail->ground_price_per_unit = $detail['ground_cost'];
            $shipmentDetail->profit_margin = $detail['profit_margin'];
            $shipmentDetail->selling_price = $detail['selling_price'];
            $shipmentDetail->shipped_quantity = $detail['shipped_quantity']; 
            $shipmentDetail->sample_quantity = $detail['sample_quantity']; 
            $shipmentDetail->considerable_margin = $detail['considerable_margin']; 
            $shipmentDetail->considerable_price = $detail['considerable_price']; 
            $shipmentDetail->save();
    
            $quantityDifference = $detail['quantity'] - $oldQuantity;
            $missingDifference = $detail['missing_quantity'] - $oldMissingQuantity;
            $shippedDifference = $detail['shipped_quantity'] - $oldShippedQuantity;
    
            $purchaseHistory = PurchaseHistory::find($shipmentDetail->purchase_history_id);
            if ($purchaseHistory) {
                $purchaseHistory->shipped_quantity += $shippedDifference;
                $purchaseHistory->remaining_product_quantity -= $shippedDifference;
                $purchaseHistory->save();
            }

            $systemLose = SystemLose::where('shipment_detail_id', $shipmentDetail->id)->first();

            if (!empty($detail['missing_quantity']) && $detail['missing_quantity'] > 0) {
                if ($systemLose) {
                    $systemLose->quantity = $detail['missing_quantity'];
                    $systemLose->size = $detail['size'];
                    $systemLose->color = $detail['color'];
                    $systemLose->type_id = $detail['type_id'] ?? null;
                    $systemLose->updated_by = auth()->id();
                    $systemLose->save();
                } else {
                    SystemLose::create([
                        'product_id' => $detail['product_id'],
                        'shipment_detail_id' => $shipmentDetail->id,
                        'warehouse_id' => $shipmentDetail->warehouse_id,
                        'quantity' => $detail['missing_quantity'],
                        'size' => $detail['size'],
                        'color' => $detail['color'],
                        'type_id' => $detail['type_id'] ?? null,
                        'created_by' => auth()->id()
                    ]);
                }
            } elseif ($systemLose) {
                $systemLose->delete();
            }
        }
    
        return response()->json(['message' => 'Shipment updated successfully!']);
    }    

    public function shipmentReceived(Request $request, $id)
    {
        $validated = $request->validate([
            'total_quantity' => 'required',
            'total_purchase_cost' => 'required',
            'total_additional_cost' => 'required',
            'shipment_details' => 'required|array',
            'shipment_details.*.id' => 'required',
            'shipment_details.*.shipped_quantity' => 'required',
            'shipment_details.*.price_per_unit' => 'required',
            'shipment_details.*.ground_cost' => 'required',
            'shipment_details.*.profit_margin' => 'required',
            'shipment_details.*.selling_price' => 'required',
        ]);

        $shipment = Shipment::findOrFail($id);

        $shipment->total_product_quantity = $request->total_quantity;
        $shipment->total_missing_quantity = $request->total_missing_quantity;
        $shipment->total_purchase_cost = $request->total_purchase_cost;
        $shipment->total_additional_cost = $request->total_additional_cost;
        $shipment->total_profit = $request->total_profit;
        $shipment->target_budget = $request->target_budget;
        $shipment->budget_over = $request->budget_over;
        $shipment->total_cost_of_shipment = $request->total_cost_of_the_shipment;
        $shipment->updated_by = auth()->id();
        $shipment->save();

        $expenses = $request->input('expenses');

        $existingTransactions = $shipment->transactions;

        $incomingTransactionIds = collect($expenses)->pluck('transaction_id')->filter();

        $existingTransactions->whereNotIn('id', $incomingTransactionIds)->each(function ($transaction) {
            $transaction->delete();
        });

        foreach ($expenses as $expense) {
            if (isset($expense['transaction_id']) && $expense['transaction_id']) {
                $transaction = Transaction::find($expense['transaction_id']);
                if ($transaction) {
                    $transaction->amount = $expense['amount'];
                    $transaction->at_amount = $expense['amount'];
                    $transaction->payment_type = $expense['payment_type'];
                    $transaction->updated_by = auth()->id();
                    $transaction->description = $expense['description'] ?? null;
                    $transaction->note = $expense['note'] ?? null;
                    $transaction->updated_by = auth()->id();
                    $transaction->save();
                }
            } else {
                $transaction = new Transaction();
                $transaction->date = $shipment->shipping->shipping_date;
                $transaction->table_type = 'Expenses';
                $transaction->shipment_id = $shipment->id;
                $transaction->amount = $expense['amount'];
                $transaction->at_amount = $expense['amount'];
                $transaction->payment_type = $expense['payment_type'];
                $transaction->chart_of_account_id = $expense['chart_of_account_id'];
                $transaction->description = $expense['description'];
                $transaction->note = $expense['note'];
                $transaction->transaction_type = 'Current';
                $transaction->created_by = auth()->id();
                $transaction->save();

                $transaction->tran_id = 'EX' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
                $transaction->save();

            }
        }

        foreach ($request->shipment_details as $detail) {
            $shipmentDetail = ShipmentDetails::with('purchaseHistory')->findOrFail($detail['id']);
            $detail['zip'] = $shipmentDetail->purchaseHistory->zip ?? 0;
            $oldQuantity = $shipmentDetail->quantity;
            $oldMissingQuantity = $shipmentDetail->missing_quantity;
            $oldShippedQuantity = $shipmentDetail->shipped_quantity;

            $shipmentDetail->quantity = $detail['quantity'];
            $shipmentDetail->missing_quantity = $detail['missing_quantity'];
            $shipmentDetail->price_per_unit = $detail['price_per_unit'];
            $shipmentDetail->ground_price_per_unit = $detail['ground_cost'];
            $shipmentDetail->profit_margin = $detail['profit_margin'];
            $shipmentDetail->selling_price = $detail['selling_price'];
            $shipmentDetail->shipped_quantity = $detail['shipped_quantity']; 
            $shipmentDetail->sample_quantity = $detail['sample_quantity']; 
            $shipmentDetail->considerable_margin = $detail['considerable_margin']; 
            $shipmentDetail->considerable_price = $detail['considerable_price']; 
            $shipmentDetail->save();
    
            $quantityDifference = $detail['quantity'] - $oldQuantity;
            $missingDifference = $detail['missing_quantity'] - $oldMissingQuantity;
            $shippedDifference = $detail['shipped_quantity'] - $oldShippedQuantity;

            $stock = Stock::where('product_id', $shipmentDetail->product_id)
                ->where('size', $shipmentDetail->size)
                ->where('color', $shipmentDetail->color)
                ->where('zip', $shipmentDetail->purchaseHistory->zip)
                ->where('warehouse_id', $shipmentDetail->warehouse_id)
                ->where('type_id', $shipmentDetail->type_id)
                ->first();
    
            if ($stock) {
                $stock->quantity += $quantityDifference;
                $stock->purchase_price = $detail['price_per_unit'];
                $stock->ground_price_per_unit = $detail['ground_cost'];
                $stock->profit_margin = $detail['profit_margin'];
                $stock->selling_price = $detail['selling_price'];
                $stock->considerable_margin = $detail['considerable_margin'];
                $stock->considerable_price = $detail['considerable_price'];
                $stock->updated_by = auth()->id();
                $stock->save();
            } else {
                $stock = Stock::create([
                    'product_id' => $detail['product_id'],
                    'quantity' => $detail['quantity'],
                    'size' => $detail['size'],
                    'color' => $detail['color'],
                    'zip' => $detail['zip'],
                    'type_id' => $detail['type_id'] ?? null,
                    'purchase_price' => $detail['price_per_unit'],
                    'ground_price_per_unit' => $detail['ground_cost'],
                    'profit_margin' => $detail['profit_margin'],
                    'selling_price' => $detail['selling_price'],
                    'considerable_margin' => $detail['considerable_margin'],
                    'considerable_price' => $detail['considerable_price'],
                    'warehouse_id' => $request->warehouse_id,
                    'created_by' => auth()->id(),
                ]);
            }
    
            $stockid = date('mds') . str_pad($detail['product_id'], 4, '0', STR_PAD_LEFT);
            StockHistory::create([
                'stock_id' => $stock->id,
                'date' => date('Y-m-d'),
                'product_id' => $detail['product_id'],
                'quantity' => $detail['quantity'],
                'size' => $detail['size'],
                'color' => $detail['color'],
                'zip' => $detail['zip'],
                'type_id' => $detail['type_id'] ?? null,
                'warehouse_id' => $request->warehouse_id,
                'available_qty' => $detail['quantity'],
                'ground_price_per_unit' => $detail['ground_cost'],
                'profit_margin' => $detail['profit_margin'],
                'purchase_price' => $detail['price_per_unit'],
                'selling_price' => $detail['selling_price'],
                'considerable_margin' => $detail['considerable_margin'],
                'considerable_price' => $detail['considerable_price'],
                'sample_quantity' => $detail['sample_quantity'],
                'created_by' => auth()->id(),
                'stockid' => $stockid,
            ]);
    
            $purchaseHistory = PurchaseHistory::find($shipmentDetail->purchase_history_id);
            if ($purchaseHistory) {
                $purchaseHistory->shipped_quantity += $shippedDifference;
                $purchaseHistory->remaining_product_quantity -= $shippedDifference;
                $purchaseHistory->save();
            }

            $systemLose = SystemLose::where('shipment_detail_id', $shipmentDetail->id)->first();

            if (!empty($detail['missing_quantity']) && $detail['missing_quantity'] > 0) {
                if ($systemLose) {
                    $systemLose->quantity = $detail['missing_quantity'];
                    $systemLose->size = $detail['size'];
                    $systemLose->color = $detail['color'];
                    $systemLose->type_id = $detail['type_id'];
                    $systemLose->updated_by = auth()->id();
                    $systemLose->save();
                } else {
                    SystemLose::create([
                        'product_id' => $detail['product_id'],
                        'shipment_detail_id' => $shipmentDetail->id,
                        'warehouse_id' => $shipmentDetail->warehouse_id,
                        'quantity' => $detail['missing_quantity'],
                        'size' => $detail['size'],
                        'type_id' => $detail['type_id'],
                        'reason' => 'Damaged from shipment',
                        'color' => $detail['color'],
                        'created_by' => auth()->id()
                    ]);
                }
            } elseif ($systemLose) {
                $systemLose->delete();
            }
        }
        
        $shipping =  Shipping::find($shipment->shipping_id);
        $shipping->status = 3;
        $shipping->save();
    
        return response()->json(['message' => 'Shipment updated successfully!']);
    }  

    public function showSampleProducts()
    {
        $sampleProducts = ShipmentDetails::with(['product', 'shipment.shipping'])->where('sample_quantity', '>', 0)->latest()->get();
        return view('admin.shipment.sample_products', compact('sampleProducts'));
    }

}
