<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\StockTransferRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class StockTransferRequestController extends Controller
{

    public function index()
    {
        $stockTransferRequests = StockTransferRequest::orderBy('id', 'desc')->get();
        return view('admin.stock.transfer_requests', compact('stockTransferRequests'));
    }
    public function storeTransferRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'productId' => 'required|exists:products,id',
            'warehouse' => 'required|exists:warehouses,id',
            'toWarehouse' => 'required|exists:warehouses,id',
            'color' => 'nullable|string',
            'size' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'max_quantity' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $stockTransferRequest = new StockTransferRequest();
        $stockTransferRequest->product_id = $request->productId;
        $stockTransferRequest->from_warehouse_id = $request->warehouse;
        $stockTransferRequest->to_warehouse_id = $request->toWarehouse;
        $stockTransferRequest->color = $request->color;
        $stockTransferRequest->size = $request->size;
        $stockTransferRequest->request_quantity = $request->quantity;
        $stockTransferRequest->max_quantity = $request->max_quantity;
        $stockTransferRequest->note = $request->note;
        $stockTransferRequest->created_by = Auth::id();
        $stockTransferRequest->status = 0;
        $stockTransferRequest->save();

        return response()->json(['message' => 'Stock transfer request created successfully!'], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $stockTransferRequest = StockTransferRequest::findOrFail($id);

        $stockTransferRequest->request_quantity = $request->input('quantity');
        $stockTransferRequest->save();

        return response()->json(['message' => 'Quantity updated successfully.']);
    }

    public function accept($id)
    {
        return DB::transaction(function () use ($id) {
            $stockTransferRequest = StockTransferRequest::find($id);
    
            if (!$stockTransferRequest) {
                return response()->json(['message' => 'Request not found.'], 404);
            }
    
            $stockFrom = Stock::where('product_id', $stockTransferRequest->product_id)
                ->where('warehouse_id', $stockTransferRequest->from_warehouse_id)
                ->first();
    
            $stockTo = Stock::where('product_id', $stockTransferRequest->product_id)
                ->where('warehouse_id', $stockTransferRequest->to_warehouse_id)
                ->first();
    
            if (!$stockFrom) {
                return response()->json(['message' => 'Stock not found in the from warehouse.'], 404);
            }

            if ($stockFrom->quantity < $stockTransferRequest->request_quantity) {
                return response()->json(['message' => 'Not enough stock in the from warehouse.'], 400);
            }
    
            if (!$stockTo) {
                $stockTo = new Stock();
                $stockTo->product_id = $stockTransferRequest->product_id;
                $stockTo->size = $stockFrom->size;
                $stockTo->color = $stockFrom->color;
                $stockTo->warehouse_id = $stockTransferRequest->to_warehouse_id;
                $stockTo->purchase_price = $stockFrom->purchase_price;
                $stockTo->ground_price_per_unit = $stockFrom->ground_price_per_unit; 
                $stockTo->profit_margin = $stockFrom->profit_margin;
                $stockTo->selling_price = $stockFrom->selling_price;
                $stockTo->quantity = $stockTransferRequest->request_quantity;
                $stockTo->created_by = Auth::id();
                $stockTo->save();
            }
    
            $stockFrom->quantity -= $stockTransferRequest->request_quantity;
            $stockFrom->save();
    
            if ($stockTo) {
                $stockTo->quantity += $stockTransferRequest->request_quantity;
                $stockTo->save();
            }
    
            $stockTransferRequest->status = 1;
            $stockTransferRequest->save();
    
            return response()->json(['message' => 'Request accepted successfully.'], 200);
        });
    }

    public function reject($id)
    {
        $stockTransferRequest = StockTransferRequest::find($id);

        if (!$stockTransferRequest) {
            return response()->json(['message' => 'Request not found.'], 404);
        }

        $stockTransferRequest->status = 2;

        if ($stockTransferRequest->save()) {
            return response()->json(['message' => 'Request rejected successfully.'], 200);
        } else {
            return response()->json(['message' => 'Failed to reject the request.'], 500);
        }
    }
}
