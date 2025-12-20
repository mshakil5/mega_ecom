<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Discount;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;

class DiscountController extends Controller
{
    public function index()
    {
        $discounts = Discount::with(['product', 'category', 'subcategory'])->latest()->get();
        $products = Product::where('status', 1)->select('id', 'name')->get();
        $categories = Category::select('id', 'name')->get();
        $subcategories = Subcategory::select('id', 'name', 'category_id')->get();

        return view('admin.discount.index', compact(
            'discounts',
            'products',
            'categories',
            'subcategories'
        ));
    }

    public function store(Request $request)
    {
        // Validate that at least one field is selected
        if (empty($request->category_id) && empty($request->subcategory_id) && empty($request->product_id)) {
            $message = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please select at least one of Category, Sub Category, or Product.</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }

        // Validate discount percentage
        if (empty($request->discount_percent)) {
            $message = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please enter discount percentage.</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }

        if ($request->discount_percent < 0 || $request->discount_percent > 100) {
            $message = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Discount percentage must be between 0 and 100.</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }

        // Check for existing discount with same parameters
        $existingDiscount = Discount::where('category_id', $request->category_id)
            ->where('subcategory_id', $request->subcategory_id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($existingDiscount) {
            return response()->json([
                'status' => 303,
                'message' => "<div class='alert alert-warning'><b>A discount with the same parameters already exists.</b></div>"
            ]);
        }

        try {
            \DB::beginTransaction();

            $discount = new Discount();
            $discount->product_id = $request->product_id ?: null;
            $discount->category_id = $request->category_id ?: null;
            $discount->subcategory_id = $request->subcategory_id ?: null;
            $discount->discount_percent = $request->discount_percent;
            $discount->status = $request->status ? 1 : 0;

            if ($discount->save()) {
                \DB::commit();

                $message = "<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Discount created successfully.</b></div>";
                return response()->json(['status' => 300, 'message' => $message]);
            } else {
                \DB::rollBack();
                return response()->json(['status' => 303, 'message' => 'Failed to save discount.']);
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'status' => 303,
                'message' => "<div class='alert alert-danger'><b>Error: " . $e->getMessage() . "</b></div>"
            ]);
        }
    }

    public function edit($id)
    {
        $discount = Discount::find($id);

        if (!$discount) {
            return response()->json([
                'status' => 404,
                'message' => 'Discount not found.'
            ]);
        }

        return response()->json($discount);
    }

    public function update(Request $request)
    {
        // Validate that at least one field is selected
        if (empty($request->category_id) && empty($request->subcategory_id) && empty($request->product_id)) {
            $message = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please select at least one of Category, Sub Category, or Product.</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }

        // Validate discount percentage
        if (empty($request->discount_percent)) {
            $message = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please enter discount percentage.</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }

        if ($request->discount_percent < 0 || $request->discount_percent > 100) {
            $message = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Discount percentage must be between 0 and 100.</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }

        // Check if discount exists
        $discount = Discount::find($request->codeid);
        if (!$discount) {
            return response()->json([
                'status' => 303,
                'message' => "<div class='alert alert-warning'><b>Discount record not found.</b></div>"
            ]);
        }

        // Check for existing discount with same parameters (excluding current one)
        $existingDiscount = Discount::where('category_id', $request->category_id)
            ->where('subcategory_id', $request->subcategory_id)
            ->where('product_id', $request->product_id)
            ->where('id', '!=', $request->codeid)
            ->first();

        if ($existingDiscount) {
            return response()->json([
                'status' => 303,
                'message' => "<div class='alert alert-warning'><b>Another discount with the same parameters already exists.</b></div>"
            ]);
        }

        try {
            \DB::beginTransaction();

            // Update discount
            $discount->product_id = $request->product_id ?: null;
            $discount->category_id = $request->category_id ?: null;
            $discount->subcategory_id = $request->subcategory_id ?: null;
            $discount->discount_percent = $request->discount_percent;
            $discount->status = $request->status ? 1 : 0;

            if ($discount->save()) {
                \DB::commit();

                $message = "<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Discount updated successfully.</b></div>";
                return response()->json(['status' => 300, 'message' => $message]);
            } else {
                \DB::rollBack();
                return response()->json(['status' => 303, 'message' => 'Failed to update discount.']);
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'status' => 303,
                'message' => "<div class='alert alert-danger'><b>Error: " . $e->getMessage() . "</b></div>"
            ]);
        }
    }

    public function destroy($id)
    {
        $discount = Discount::find($id);

        if (!$discount) {
            return response()->json([
                'success' => false,
                'message' => 'Discount not found.'
            ], 404);
        }

        try {
            \DB::beginTransaction();

            if ($discount->delete()) {
                \DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Discount deleted successfully.'
                ]);
            } else {
                \DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete discount.'
                ], 500);
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // AJAX Routes
    public function getSubcategories($categoryId)
    {
        $subcategories = Subcategory::where('category_id', $categoryId)
            ->select('id', 'name')
            ->get();

        return response()->json($subcategories);
    }

    public function getProducts(Request $request)
    {
        $query = Product::where('status', 1)->select('id', 'name');

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->subcategory_id) {
            $query->where('sub_category_id', $request->subcategory_id);
        }

        $products = $query->get();

        return response()->json($products);
    }
}