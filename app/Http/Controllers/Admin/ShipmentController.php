<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Models\ShipmentDetails;
use App\Models\StockHistory;

class ShipmentController extends Controller
{

    public function shipmentHistory()
    {
        $shipments = Shipment::with(['shipping', 'shipmentDetails.product', 'shipmentDetails.supplier'])
                         ->latest()
                         ->get();
        return view('admin.shipment.history', compact('shipments'));
    }

    public function storeShipment(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'total_quantity' => 'required|integer',
            'direct_cost' => 'required|numeric',
            'cnf_cost' => 'required|numeric',
            'import_taxes' => 'required|numeric',
            'warehouse_cost' => 'required|numeric',
            'other_cost' => 'required|numeric',
            'total_additional_cost' => 'required|numeric',
            'shipment_details' => 'required|array',
            'shipment_details.*.supplier_id' => 'required|integer',
            'shipment_details.*.product_id' => 'required|integer',
            'shipment_details.*.size' => 'nullable|string',
            'shipment_details.*.color' => 'nullable|string',
            'shipment_details.*.quantity' => 'required|integer',
            'shipment_details.*.price_per_unit' => 'required|numeric',
            'shipment_details.*.ground_cost' => 'nullable|numeric',
            'shipment_details.*.profit_margin' => 'nullable|numeric',
            'shipment_details.*.selling_price' => 'nullable|numeric',
        ]);

        $shipment = Shipment::create([
            'shipping_id' => $request->id,
            'total_product_quantity' => $request->total_quantity,
            'total_missing_quantity' => $request->total_missing_quantity,
            'total_purchase_cost' => $request->direct_cost,
            'cnf_cost' => $request->cnf_cost,
            'import_duties_tax' => $request->import_taxes,
            'warehouse_and_handling_cost' => $request->warehouse_cost,
            'other_cost' => $request->other_cost,
            'total_additional_cost' => $request->total_additional_cost,
            'created_by' => auth()->user()->id,
            'updated_by' => auth()->user()->id,
        ]);

        foreach ($request->shipment_details as $detail) {
            ShipmentDetails::create([
                'shipment_id' => $shipment->id,
                'product_id' => $detail['product_id'],
                'supplier_id' => $detail['supplier_id'],
                'size' => $detail['size'],
                'color' => $detail['color'],
                'quantity' => $detail['quantity'],
                'missing_quantity' => $detail['missing_quantity'],
                'price_per_unit' => $detail['price_per_unit'],
                'ground_price_per_unit' => $detail['ground_cost'],
                'profit_margin' => $detail['profit_margin'],
                'selling_price' => $detail['selling_price'],
            ]);

            $stockHistory = StockHistory::where('product_id', $detail['product_id'])
                ->where('size', $detail['size'])
                ->where('color', $detail['color'])
                ->first();

            if ($stockHistory) {
                $stockHistory->quantity += $detail['quantity'];
                $stockHistory->available_qty += $detail['quantity'];
                $stockHistory->missing_product_quantity += $detail['missing_quantity'];
                $stockHistory->quantity += $detail['quantity'];
                $stockHistory->purchase_price = $detail['price_per_unit'];
                $stockHistory->ground_price_per_unit = $detail['ground_cost'];
                $stockHistory->profit_margin = $detail['profit_margin'];
                $stockHistory->selling_price = $detail['selling_price'];
                $stockHistory->updated_by = auth()->user()->id;
                $stockHistory->save();
            } else {
                $newStockHistory = new StockHistory();
                $newStockHistory->date = date('Y-m-d');
                $newStockHistory->product_id = $detail['product_id'];
                $newStockHistory->quantity = $detail['quantity'];
                $newStockHistory->size = $detail['size'];
                $newStockHistory->color = $detail['color'];
                $newStockHistory->available_qty = $detail['quantity'];
                $newStockHistory->ground_price_per_unit = $detail['ground_cost'];
                $newStockHistory->profit_margin = $detail['profit_margin'];
                $newStockHistory->purchase_price = $detail['price_per_unit'];
                $newStockHistory->selling_price = $detail['selling_price'];
                $newStockHistory->created_by = auth()->user()->id;
                $newStockHistory->save();

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
        
            $stockHistory = StockHistory::where('product_id', $shipmentDetail->product_id)
                ->where('size', $shipmentDetail->size)
                ->where('color', $shipmentDetail->color)
                ->first();
        
            if ($stockHistory) {
                $quantityDifference = $detail['quantity'] - $oldQuantity;
                $missingDifference = $detail['missing_quantity'] - $oldMissingQuantity;
        
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
                $newStockHistory = new StockHistory();
                $newStockHistory->date = date('Y-m-d');
                $newStockHistory->product_id = $shipmentDetail->product_id;
                $newStockHistory->quantity = $detail['quantity'];
                $newStockHistory->size = $shipmentDetail->size;
                $newStockHistory->color = $shipmentDetail->color;
                $newStockHistory->available_qty = $detail['quantity'];
                $newStockHistory->ground_price_per_unit = $detail['ground_cost'];
                $newStockHistory->profit_margin = $detail['profit_margin'];
                $newStockHistory->purchase_price = $detail['price_per_unit'];
                $newStockHistory->selling_price = $detail['selling_price'];
                $newStockHistory->created_by = auth()->user()->id;
                $newStockHistory->save();
            }
        }                

        if (!empty($request->removed_ids)) {
            ShipmentDetails::whereIn('id', $request->removed_ids)->delete();
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
