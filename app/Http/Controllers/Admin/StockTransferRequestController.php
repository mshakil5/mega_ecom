<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\StockTransferRequest;
use Illuminate\Support\Facades\Auth;

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
