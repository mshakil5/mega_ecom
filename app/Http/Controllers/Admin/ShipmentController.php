<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Models\ShipmentDetails;
use App\Models\StockHistory;
use App\Models\Stock;
use App\Models\Shipping;
use App\Models\PurchaseHistory;
use App\Models\Purchase;
use App\Models\Warehouse;

class ShipmentController extends Controller
{

    public function shipmentHistory()
    {
        $shipments = Shipment::with(['shipping', 'shipmentDetails.product', 'shipmentDetails.supplier'])
                         ->latest()
                         ->get();
        return view('admin.shipment.history', compact('shipments'));
    }

    public function createShipment($id)
    {
        $shipping = Shipping::with('shipment')->find($id);
        if (!$shipping) {
            return redirect()->back()->with('error', 'Shipping not found.');
        }

        $purchaseIds = json_decode($shipping->purchase_ids, true);
        $purchaseHistories = PurchaseHistory::whereIn('purchase_id', $purchaseIds)
            ->with('product', 'purchase.supplier')
            ->orderBy('purchase_id', 'asc')
            ->get();

        $warehouses = Warehouse::select('id', 'name','location')->where('status', 1)->get();
        return view('admin.shipment.create', compact('shipping', 'purchaseHistories', 'warehouses'));
    }

    public function storeShipment(Request $request)
    {
        $request->validate([
            'shipping_id' => 'required|integer',
            'warehouse_id' => 'required',
            'total_quantity' => 'required|integer',
            'total_purchase_cost' => 'required|numeric',
            'cnf_cost' => 'required|numeric',
            'import_taxes' => 'required|numeric',
            'warehouse_cost' => 'required|numeric',
            'other_cost' => 'required|numeric',
            'total_additional_cost' => 'required|numeric',
            'shipment_details' => 'required|array',
            'shipment_details.*.supplier_id' => 'required|integer',
            'shipment_details.*.product_id' => 'required|integer',
            'shipment_details.*.purchase_history_id' => 'required|integer',
            'shipment_details.*.size' => 'required|string',
            'shipment_details.*.color' => 'required|string',
            'shipment_details.*.shipped_quantity' => 'required|integer',
            'shipment_details.*.missing_quantity' => 'nullable|integer',
            'shipment_details.*.price_per_unit' => 'required|numeric',
            'shipment_details.*.ground_cost' => 'nullable|numeric',
            'shipment_details.*.profit_margin' => 'nullable|numeric',
            'shipment_details.*.selling_price' => 'nullable|numeric',
        ]);
    
        $shipping = Shipping::where('id', $request->shipping_id)->first();
        $purchaseIds = $shipping->purchase_ids ?? [];
        $shipment = Shipment::create([
            'shipping_id' => $request->shipping_id,
            'total_product_quantity' => $request->total_quantity,
            'total_missing_quantity' => $request->total_missing_quantity,
            'total_purchase_cost' => $request->total_purchase_cost,
            'cnf_cost' => $request->cnf_cost,
            'import_duties_tax' => $request->import_taxes,
            'warehouse_and_handling_cost' => $request->warehouse_cost,
            'other_cost' => $request->other_cost,
            'total_additional_cost' => $request->total_additional_cost,
            'purchase_ids' => $purchaseIds,
            'created_by' => auth()->id(),
        ]);
    
        foreach ($request->shipment_details as $detail) {
            ShipmentDetails::create([
                'shipment_id' => $shipment->id,
                'product_id' => $detail['product_id'],
                'supplier_id' => $detail['supplier_id'],
                'purchase_history_id' => $detail['purchase_history_id'],
                'size' => $detail['size'],
                'color' => $detail['color'],
                'quantity' => $detail['shipped_quantity'],
                'missing_quantity' => $detail['missing_quantity'],
                'price_per_unit' => $detail['price_per_unit'],
                'ground_price_per_unit' => $detail['ground_cost'],
                'profit_margin' => $detail['profit_margin'],
                'selling_price' => $detail['selling_price'],
            ]);

            $stock = Stock::where('product_id', $detail['product_id'])
                ->where('size', $detail['size'])
                ->where('color', $detail['color'])
                ->where('warehouse_id', $request->warehouse_id)
                ->first();
    
            if ($stock) {
                $stock->quantity += $detail['shipped_quantity'];
                $stock->updated_by = auth()->id();
                $stock->save();
            } else {
                $stock = Stock::create([
                    'product_id' => $detail['product_id'],
                    'quantity' => $detail['shipped_quantity'],
                    'size' => $detail['size'],
                    'color' => $detail['color'],
                    'warehouse_id' => $request->warehouse_id,
                    'created_by' => auth()->id(),
                ]);
            }
    
            $stockHistory = StockHistory::where('product_id', $detail['product_id'])
                ->where('size', $detail['size'])
                ->where('color', $detail['color'])
                ->where('warehouse_id', $request->warehouse_id)
                ->first();
    
            if ($stockHistory) {
                $stockHistory->quantity += $detail['shipped_quantity'];
                $stockHistory->available_qty += $detail['shipped_quantity'];
                $stockHistory->missing_product_quantity += $detail['missing_quantity'];
                $stockHistory->purchase_price = $detail['price_per_unit'];
                $stockHistory->ground_price_per_unit = $detail['ground_cost'];
                $stockHistory->profit_margin = $detail['profit_margin'];
                $stockHistory->selling_price = $detail['selling_price'];
                $stockHistory->updated_by = auth()->id();
                $stockHistory->save();
            } else {
                $stockid = date('mds') . str_pad($detail['product_id'], 4, '0', STR_PAD_LEFT);
                StockHistory::create([
                    'stock_id' => $stock->id,
                    'date' => date('Y-m-d'),
                    'product_id' => $detail['product_id'],
                    'quantity' => $detail['shipped_quantity'],
                    'size' => $detail['size'],
                    'color' => $detail['color'],
                    'warehouse_id' => $request->warehouse_id,
                    'available_qty' => $detail['shipped_quantity'],
                    'ground_price_per_unit' => $detail['ground_cost'],
                    'profit_margin' => $detail['profit_margin'],
                    'purchase_price' => $detail['price_per_unit'],
                    'selling_price' => $detail['selling_price'],
                    'created_by' => auth()->id(),
                    'stockid' => $stockid,
                ]);
            }
    
            $purchaseHistory = PurchaseHistory::find($detail['purchase_history_id']);
            if ($purchaseHistory) {
                $purchaseHistory->shipped_quantity = $detail['shipped_quantity'];
                $purchaseHistory->save();
            }
        }
    
        return response()->json(['message' => 'Shipment created successfully!', 'shipment_id' => $shipment->id], 201);
    }    

    public function editShipment($id)
    {
        $shipment = Shipment::with('shipmentDetails.supplier', 'shipmentDetails.product')->findOrFail($id);
        return view('admin.shipment.edit', compact('shipment'));
    }

    public function updateShipment(Request $request, $id)
    {
        $validated = $request->validate([
            'total_quantity' => 'required',
            'total_purchase_cost' => 'required',
            'cnf_cost' => 'required',
            'import_taxes' => 'required',
            'warehouse_cost' => 'required',
            'other_cost' => 'required',
            'total_additional_cost' => 'required',
            'shipment_details' => 'required|array',
            'shipment_details.*.id' => 'required',
            'shipment_details.*.quantity' => 'required',
            'shipment_details.*.price_per_unit' => 'required',
            'shipment_details.*.ground_cost' => 'required',
            'shipment_details.*.profit_margin' => 'required',
            'shipment_details.*.selling_price' => 'required',
        ]);

        $shipment = Shipment::findOrFail($id);

        $shipment->total_product_quantity = $request->total_quantity;
        $shipment->total_missing_quantity = $request->total_missing_quantity;
        $shipment->total_purchase_cost = $request->total_purchase_cost;
        $shipment->cnf_cost = $request->cnf_cost;
        $shipment->import_duties_tax = $request->import_taxes;
        $shipment->warehouse_and_handling_cost = $request->warehouse_cost;
        $shipment->other_cost = $request->other_cost;
        $shipment->total_additional_cost = $request->total_additional_cost;
        $shipment->save();

        foreach ($request->shipment_details as $detail) {
            $shipmentDetail = ShipmentDetails::findOrFail($detail['id']);
            
            $oldQuantity = $shipmentDetail->quantity;
            $oldMissingQuantity = $shipmentDetail->missing_quantity;

            $shipmentDetail->quantity = $detail['quantity'];
            $shipmentDetail->missing_quantity = $detail['missing_quantity'];
            $shipmentDetail->price_per_unit = $detail['price_per_unit'];
            $shipmentDetail->ground_price_per_unit = $detail['ground_cost'];
            $shipmentDetail->profit_margin = $detail['profit_margin'];
            $shipmentDetail->selling_price = $detail['selling_price'];
            $shipmentDetail->save();
    
            $quantityDifference = $detail['quantity'] - $oldQuantity;
            $missingDifference = $detail['missing_quantity'] - $oldMissingQuantity;
    
            $stock = Stock::where('product_id', $shipmentDetail->product_id)
                ->where('size', $shipmentDetail->size)
                ->where('color', $shipmentDetail->color)
                ->first();
    
            if ($stock) {
                $stock->quantity += $quantityDifference;
                $stock->updated_by = auth()->id();
                $stock->save();
            } else {
                $stock = Stock::create([
                    'product_id' => $shipmentDetail->product_id,
                    'quantity' => $detail['quantity'],
                    'size' => $shipmentDetail->size,
                    'color' => $shipmentDetail->color,
                    'created_by' => auth()->id(),
                ]);
            }
    
            $stockHistory = StockHistory::where('product_id', $shipmentDetail->product_id)
                ->where('size', $shipmentDetail->size)
                ->where('color', $shipmentDetail->color)
                ->first();
    
            if ($stockHistory) {
                $stockHistory->quantity += $quantityDifference;
                $stockHistory->available_qty += $quantityDifference;
                $stockHistory->missing_product_quantity += $missingDifference;
                $stockHistory->purchase_price = $detail['price_per_unit'];
                $stockHistory->ground_price_per_unit = $detail['ground_cost'];
                $stockHistory->profit_margin = $detail['profit_margin'];
                $stockHistory->selling_price = $detail['selling_price'];
                $stockHistory->updated_by = auth()->user()->id;
                $stockHistory->save();
            } else {
                $stockHistoryId = date('mds') . str_pad($shipmentDetail->product_id, 4, '0', STR_PAD_LEFT);
    
                StockHistory::create([
                    'stock_id' => $stock->id,
                    'date' => date('Y-m-d'),
                    'product_id' => $shipmentDetail->product_id,
                    'quantity' => $detail['quantity'],
                    'size' => $shipmentDetail->size,
                    'color' => $shipmentDetail->color,
                    'available_qty' => $detail['quantity'],
                    'ground_price_per_unit' => $detail['ground_cost'],
                    'profit_margin' => $detail['profit_margin'],
                    'purchase_price' => $detail['price_per_unit'],
                    'selling_price' => $detail['selling_price'],
                    'created_by' => auth()->id(),
                    'stockid' => $stockHistoryId,
                ]);
            }
        }
    
        if (!empty($request->removed_ids)) {
            $removedDetails = ShipmentDetails::whereIn('id', $request->removed_ids)->get();
            foreach ($removedDetails as $removedDetail) {
                $stock = Stock::where('product_id', $removedDetail->product_id)
                    ->where('size', $removedDetail->size)
                    ->where('color', $removedDetail->color)
                    ->first();
        
                if ($stock) {
                    $stock->quantity -= $removedDetail->quantity;
                    $stock->updated_by = auth()->id();
                    $stock->save();
                }
        
                $stockHistory = StockHistory::where('product_id', $removedDetail->product_id)
                    ->where('size', $removedDetail->size)
                    ->where('color', $removedDetail->color)
                    ->first();
        
                if ($stockHistory) {
                    $stockHistory->quantity -= $removedDetail->quantity;
                    $stockHistory->available_qty -= $removedDetail->quantity;
        
                    $stockHistory->quantity = max($stockHistory->quantity, 0);
                    $stockHistory->available_qty = max($stockHistory->available_qty, 0);
        
                    $stockHistory->updated_by = auth()->id();
                    $stockHistory->save();
                }
        
                $removedDetail->delete();
            }
        }        
    
        return response()->json(['message' => 'Shipment updated successfully!']);
    }    

    public function updateStatus(Request $request)
    {
        $request->validate([
            'shipment_id' => 'required|exists:shipments,id',
            'status' => 'required',
        ]);

        $shipment = Shipment::findOrFail($request->shipment_id);
        $shipment->status = $request->status;
        $shipment->save();
        
        return response()->json(['message' => 'Status updated successfully'], 200);
    }
}
