<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SampleProductAssignment;
use App\Models\User;
use App\Models\SampleProduct;

class SampleProductController extends Controller
{
    public function storeAssignment(Request $request)
    {
        $validated = $request->validate([
            'sample_product_id' => 'required|exists:sample_products,id',
            'wholesaler_id' => 'required|exists:users,id',
            'quantity' => 'required|numeric|min:1',
            'assignment_date' => 'required|date',
        ]);

        $sampleProduct = SampleProduct::findOrFail($validated['sample_product_id']);
        
        $wholesaler = User::where('id', $validated['wholesaler_id'])
            ->where('is_type', 0)
            ->firstOrFail();

        if ($validated['quantity'] > $sampleProduct->available_quantity) {
            return response()->json([
                'success' => false,
                'message' => "Only {$sampleProduct->available_quantity} units available."
            ], 422);
        }

        try {
            SampleProductAssignment::create([
                'sample_product_id' => $validated['sample_product_id'],
                'wholesaler_id' => $validated['wholesaler_id'],
                'quantity' => $validated['quantity'],
                'assignment_date' => $validated['assignment_date'],
                'created_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Distribution created successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAssignmentList($sampleProductId)
    {
        $assignments = SampleProductAssignment::with(['wholesaler', 'createdBy'])
            ->where('sample_product_id', $sampleProductId)
            ->latest('assignment_date')
            ->get();

        return response()->json([
            'assignments' => $assignments,
            'total' => $assignments->sum('quantity'),
        ]);
    }

    public function getWholesalers()
    {
        $wholesalers = User::where('is_type', 0)
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        return response()->json($wholesalers);
    }
}
