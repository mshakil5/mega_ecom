<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShippingCost;
use App\Models\Purchase;
use App\Models\StockHistory;
use App\Models\Shipping;
use App\Models\PurchaseHistory;
use Carbon\Carbon;

class ShippingController extends Controller
{

    public function shipping()
    {
        $data = Shipping::with(['shipment.shipmentDetails.supplier', 'shipment.shipmentDetails.product'])->orderBy('id', 'DESC')->get();
        $purchases = Purchase::select('id', 'invoice')->latest()->get();
        foreach ($data as $shipment) {
            $purchaseIds = json_decode($shipment->purchase_ids);
    
            if ($purchaseIds) {
                $invoices = Purchase::whereIn('id', $purchaseIds)
                    ->pluck('invoice')
                    ->toArray();
                $shipment->invoice_numbers = implode(', ', $invoices);
            } else {
                $shipment->invoice_numbers = 'No Invoices Found';
            }
        }

        return view('admin.shipping.index', compact('data', 'purchases'));
    }

    public function searchPurchases(Request $request)
    {
        $invoice = $request->input('invoice');
        $purchase = Purchase::where('invoice', $invoice)->first(['id', 'invoice']);
    
        if ($purchase) {
            $allShipped = Purchase::with('purchaseHistory')
                ->where('invoice', $invoice)
                ->whereHas('purchaseHistory', function ($query) {
                    $query->havingRaw('SUM(quantity) = SUM(shipped_quantity)')
                        ->where('quantity', '>', 0)
                        ->where('shipped_quantity', '>', 0);
                })
                ->exists();
    
            if ($allShipped) {
                return response()->json(['message' => 'This invoice has already been completed (all products shipped)'], 400);
            }
    
            return response()->json($purchase);
        } else {
            return response()->json(['message' => 'Purchase not found'], 404);
        }
    }

    public function checkShippingId(Request $request)
    {
        $request->validate([
            'shipping_id' => 'required|string',
        ]);

        $query = Shipping::where('shipping_id', $request->shipping_id);
    
        if ($request->filled('shipment_id')) {
            $query->where('id', '!=', $request->shipment_id);
        }
    
        $exists = $query->exists();
    
        return response()->json(['exists' => $exists]);
    }

    public function storeShipment(Request $request)
    {
        $request->validate([
            'shipping_id' => 'required|string',
            'shipping_name' => 'required|string',
            'shipping_date' => 'required|date',
            'purchase_ids' => 'required|array',
            'purchase_ids.*' => 'exists:purchases,id',
        ]);

        $shipment = Shipping::create([
            'shipping_id' => $request->shipping_id,
            'shipping_name' => $request->shipping_name,
            'shipping_date' => $request->shipping_date,
            'purchase_ids' => json_encode($request->purchase_ids),
        ]);

        return response()->json(['message' => 'Shipment created successfully', 'shipment' => $shipment], 201);
    }

    public function updateShipment($id, Request $request)
    {
        $request->validate([
            'id' => 'required',
            'shipping_id' => 'required|string',
            'shipping_name' => 'required|string',
            'shipping_date' => 'required|date',
            'purchase_ids' => 'required|array',
            'purchase_ids.*' => 'exists:purchases,id',
        ]);

        $shipment = Shipping::findOrFail($id);

        $shipment->shipping_id = $request->shipping_id;
        $shipment->shipping_name = $request->shipping_name;
        $shipment->shipping_date = $request->shipping_date;
        $shipment->purchase_ids = json_encode($request->purchase_ids);
        $shipment->save();

        return response()->json(['message' => 'Shipment updated successfully', 'shipment' => $shipment]);
    }

}
