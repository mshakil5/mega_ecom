<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\StockTransferRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;
use App\Models\StockHistory;

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
            'toWarehouse' => 'required|exists:warehouses,id|different:warehouse',
            'color' => 'nullable|string',
            'size' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'max_quantity' => 'required|numeric|min:0',
        ], [
            'toWarehouse.different' => 'The destination warehouse must be different from the source warehouse.',
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
                ->where('size', $stockTransferRequest->size)
                ->where('color', $stockTransferRequest->color)
                ->first();
        
            $stockTo = Stock::where('product_id', $stockTransferRequest->product_id)
                ->where('warehouse_id', $stockTransferRequest->to_warehouse_id)
                ->where('size', $stockTransferRequest->size)
                ->where('color', $stockTransferRequest->color)
                ->first();
        
            if (!$stockFrom) {
                return response()->json(['message' => 'Stock not found in the from warehouse.'], 404);
            }
        
            if ($stockFrom->quantity < $stockTransferRequest->request_quantity) {
                return response()->json(['message' => 'Not enough stock in the from warehouse.'], 400);
            }
        
            $requestQuantity = $stockTransferRequest->request_quantity;
            $fromStockHistories = StockHistory::where('product_id', $stockTransferRequest->product_id)
                ->where('stock_id', $stockFrom->id)
                ->where('warehouse_id', $stockTransferRequest->from_warehouse_id)
                ->where('color', $stockFrom->color)
                ->where('size', $stockFrom->size)
                ->orderBy('id')
                ->get();
        
            $latestUsedHistory = null; 
        
            // First, check for any single history record that can fulfill the request
            foreach ($fromStockHistories as $history) {
                if ($history->available_qty >= $requestQuantity) {
                    // If found, update this record
                    $history->available_qty -= $requestQuantity;
                    $history->transferred_product_quantity += $requestQuantity;
                    $history->save();
                    $requestQuantity = 0; // Request fulfilled
                    $latestUsedHistory = $history;
                    break; // Exit the loop since the request is fulfilled
                }
            }
        
            if ($requestQuantity > 0) {
                foreach ($fromStockHistories as $history) {
                    if ($requestQuantity <= 0) {
                        break; // Stop if we've fulfilled the request
                    }
        
                    if ($history->available_qty > 0) {
                        if ($history->available_qty >= $requestQuantity) {
                            // If this history can fulfill the remaining request
                            $history->available_qty -= $requestQuantity;
                            $history->transferred_product_quantity += $requestQuantity;
                            $requestQuantity = 0; // Request fulfilled
                            $latestUsedHistory = $history;
                        } else {
                            // Take all available from this history
                            $requestQuantity -= $history->available_qty;
                            $history->transferred_product_quantity += $history->available_qty;
                            $history->available_qty = 0; // Mark as fully used
                            $latestUsedHistory = $history;
                        }
        
                        $history->save();
                    }
                }
            }
        
            // Update the 'from' stock quantity
            $stockFrom->quantity -= $stockTransferRequest->request_quantity;
            $stockFrom->save();
        
            // Handle the 'to' stock
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
        
                // Create a new stock history for the 'to' stock
                if ($latestUsedHistory) {
                    $newStockHistory = new StockHistory();
                    $newStockHistory->product_id = $stockTransferRequest->product_id;
                    $newStockHistory->stock_id = $stockTo->id;
                    $newStockHistory->warehouse_id = $stockTransferRequest->to_warehouse_id;
                    $newStockHistory->color = $stockTo->color;
                    $newStockHistory->size = $stockTo->size;
                    $newStockHistory->available_qty = $stockTransferRequest->request_quantity; 
                    $newStockHistory->received_quantity = $stockTransferRequest->request_quantity;
                    $newStockHistory->purchase_price = $latestUsedHistory->purchase_price;
                    $newStockHistory->ground_price_per_unit = $latestUsedHistory->ground_price_per_unit;
                    $newStockHistory->profit_margin = $latestUsedHistory->profit_margin;
                    $newStockHistory->considerable_margin = $latestUsedHistory->considerable_margin;
                    $newStockHistory->considerable_price = $latestUsedHistory->considerable_price;
                    $newStockHistory->selling_price = $latestUsedHistory->selling_price;
                    $newStockHistory->created_by = Auth::id();
                    $newStockHistory->save();
                }
            }
        
            // Update existing stock history for the 'to' stock
            if ($stockTo) {
                $stockTo->quantity += $stockTransferRequest->request_quantity;
                $stockTo->save();
        
                $toStockHistories = StockHistory::where('stock_id', $stockTo->id)
                    ->where('size', $stockTo->size)
                    ->where('color', $stockTo->color)
                    ->where('warehouse_id', $stockTransferRequest->to_warehouse_id)
                    ->orderBy('id', 'desc')
                    ->first();
        
                if ($toStockHistories) {
                    $toStockHistories->available_qty += $stockTransferRequest->request_quantity;
                    $toStockHistories->received_quantity += $stockTransferRequest->request_quantity;
                    $toStockHistories->save();
                } else {
                    // Optionally create a new stock history if none exists
                    $newStockHistory = new StockHistory();
                    $newStockHistory->product_id = $stockTransferRequest->product_id;
                    $newStockHistory->stock_id = $stockTo->id;
                    $newStockHistory->warehouse_id = $stockTransferRequest->to_warehouse_id;
                    $newStockHistory->color = $stockTo->color;
                    $newStockHistory->size = $stockTo->size;
                    $newStockHistory->available_qty = $stockTransferRequest->request_quantity; 
                    $newStockHistory->received_quantity = $stockTransferRequest->request_quantity;
                    $newStockHistory->purchase_price = $latestUsedHistory->purchase_price;
                    $newStockHistory->ground_price_per_unit = $latestUsedHistory->ground_price_per_unit;
                    $newStockHistory->profit_margin = $latestUsedHistory->profit_margin;
                    $newStockHistory->considerable_margin = $latestUsedHistory->considerable_margin;
                    $newStockHistory->considerable_price = $latestUsedHistory->considerable_price;
                    $newStockHistory->selling_price = $latestUsedHistory->selling_price;
                    $newStockHistory->created_by = Auth::id();
                    $newStockHistory->save();
                }
            }
        
            // Update the status of the stock transfer request
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
