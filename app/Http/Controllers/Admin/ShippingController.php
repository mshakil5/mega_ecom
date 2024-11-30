<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShippingCost;
use App\Models\Purchase;
use App\Models\StockHistory;

class ShippingController extends Controller
{
    public function storeShipping(Request $request)
    {
        $request->validate([
            'purchase_ids' => 'required|array',
            'direct_cost' => 'nullable|numeric',
            'cnf_cost' => 'nullable|numeric',
            'cost_a' => 'nullable|numeric',
            'cost_b' => 'nullable|numeric',
            'other_cost' => 'nullable|numeric',
        ]);

        $additionalCost = ($request->direct_cost ?? 0) +
                        ($request->cnf_cost ?? 0) +
                        ($request->cost_a ?? 0) +
                        ($request->cost_b ?? 0) +
                        ($request->other_cost ?? 0);

        $shippingCost = new ShippingCost();
        $shippingCost->purchase_ids = json_encode($request->purchase_ids);
        $shippingCost->direct_cost = $request->direct_cost;
        $shippingCost->cnf_cost = $request->cnf_cost;
        $shippingCost->cost_a = $request->cost_a;
        $shippingCost->cost_b = $request->cost_b;
        $shippingCost->other_cost = $request->other_cost;
        $shippingCost->additional_cost = $additionalCost;
        $shippingCost->created_by = auth()->user()->id; 
        $shippingCost->updated_by = auth()->user()->id;
        $shippingCost->save();

        foreach ($request->purchase_ids as $purchaseId) {
            $purchase = Purchase::find($purchaseId);
            if ($purchase) {
                $purchase->is_selling_cost_added = 1;
                $purchase->save();
            }

            $purchaseHistoryItems = StockHistory::where('purchase_id', $purchaseId)->get();
            foreach ($purchaseHistoryItems as $purchaseHistory) {
                $qty = $purchaseHistory->quantity - $purchaseHistory->missing_product_quantity;
                $countItem = Purchase::withCount('purchaseHistory')->where('id', $purchaseHistory->purchase_id)->first();
                $additionalCostPerProduct = $additionalCost / $countItem->purchase_history_count;

                $additionalCostPerUnit = $additionalCostPerProduct / $qty;

                $purchaseHistory->purchase_price = $purchaseHistory->purchase_price + $additionalCostPerUnit;

                $purchaseHistory->selling_price = $purchaseHistory->purchase_price + ($purchaseHistory->purchase_price * 0.30);

                $purchaseHistory->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Shipping cost added successfully and selling prices updated.',
            'data' => $shippingCost
        ]);
    }


}
