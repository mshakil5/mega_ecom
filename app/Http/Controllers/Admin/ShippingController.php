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
        $data = Shipping::orderBy('id', 'DESC')->get();
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

        return view('admin.shipment.create', compact('data', 'purchases'));
    }

    public function searchPurchases(Request $request)
    {
        $invoice = $request->get('invoice');

        $purchase = Purchase::where('status', 4)->where('invoice', $invoice)->first(['id', 'invoice']);

        if ($purchase) {
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

    public function searchShipmentById(Request $request)
    {
        $shippingId = $request->input('shipping_id');
        $shipment = Shipping::where('shipping_id', $shippingId)->first();

        if ($shipment) {
            $purchaseIds = json_decode($shipment->purchase_ids, true);
            $purchaseHistories = PurchaseHistory::whereIn('purchase_id', $purchaseIds)
                ->with('product', 'purchase.supplier')
                ->orderBy('purchase_id', 'asc')
                ->get();
        
            $purchases = Purchase::with('supplier')->whereIn('id', $purchaseIds)->get();

            return response()->json([
                'success' => true,
                'id' => $shipment->id,
                'purchase_ids' => $purchaseIds,
                'shipping_id' => $shipment->shipping_id,
                'shipping_date' => Carbon::parse($shipment->shipping_date)->format('d-m-Y'),
                'shipping_name' => $shipment->shipping_name,
                'purchase_histories' => $purchaseHistories,
                'purchases' => $purchases
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Shipping ID not found.']);
    }

}
