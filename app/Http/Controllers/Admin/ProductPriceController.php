<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Http\Request;
use DataTables;

class ProductPriceController extends Controller
{
    public function productPricesIndex(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::with(['prices'])
                ->select('id', 'name', 'product_code', 'feature_image')
                ->orderBy('id', 'DESC');

            return DataTables::of($products)
                ->addIndexColumn()
                ->addColumn('image', function($row) {
                    $imageUrl = asset('images/products/' . $row->feature_image);
                    return '<img src="' . $imageUrl . '" style="width: 50px; height: 50px; object-fit: cover;">';
                })
                ->addColumn('blank_pricing', function($row) {
                    $prices = $row->prices->where('category', 'Blank pricing')->sortBy('min_quantity');
                    return $this->renderPriceTable($prices, $row->id, 'Blank pricing');
                })
                ->addColumn('print_pricing', function($row) {
                    $prices = $row->prices->where('category', 'Print')->sortBy('min_quantity');
                    return $this->renderPriceTable($prices, $row->id, 'Print');
                })
                ->addColumn('embroidery_pricing', function($row) {
                    $prices = $row->prices->where('category', 'Embroidery')->sortBy('min_quantity');
                    return $this->renderPriceTable($prices, $row->id, 'Embroidery');
                })
                ->addColumn('high_stitch_pricing', function($row) {
                    $prices = $row->prices->where('category', 'High stitch count')->sortBy('min_quantity');
                    return $this->renderPriceTable($prices, $row->id, 'High stitch count');
                })
                ->addColumn('action', function($row) {
                    return '
                        <a class="deleteAllPricesBtn" rid="' . $row->id . '" style="cursor: pointer;">
                            <i class="fa fa-trash-o" style="color: red; font-size:16px;"></i>
                        </a>
                    ';
                })
                ->rawColumns(['image', 'blank_pricing', 'print_pricing', 'embroidery_pricing', 'high_stitch_pricing', 'action'])
                ->make(true);
        }

        return view('admin.product-prices.index');
    }

    private function renderPriceTable($prices, $productId, $category)
    {
        if ($prices->isEmpty()) {
            return '<div class="text-center text-muted">
                        <i class="fas fa-exclamation-circle"></i><br>
                        No prices set
                    </div>';
        }

        $html = '<div class="table-responsive">';
        $html .= '<table class="table table-sm table-bordered mb-0">';
        $html .= '<thead class="bg-light">';
        $html .= '<tr>';
        $html .= '<th class="text-center" style="width: 40%;">Quantity Range</th>';
        $html .= '<th class="text-center" style="width: 30%;">Discount %</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        
        foreach ($prices as $price) {
            $html .= '<tr>';
            $html .= '<td class="text-center">' . $price->min_quantity . ' - ' . ($price->max_quantity ?: '∞') . '</td>';
            $html .= '<td class="text-center">' . ($price->discount_percent ? $price->discount_percent . '%' : '0%') . '</td>';
            $html .= '</tr>';
        }
        
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';
        
        return $html;
    }

    public function getProducts()
    {
        $products = Product::select('id', 'name', 'product_code')
            ->orderBy('name')
            ->get();
        
        return response()->json($products);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id',
            'prices' => 'required|array',
            'prices.*.category' => 'required|in:Blank pricing,Print,Embroidery,High stitch count',
            'prices.*.min_quantity' => 'required|integer|min:1',
            'prices.*.max_quantity' => 'required|integer|min:1',
            'prices.*.discount_percent' => 'required|integer|min:0|max:100',
        ]);

        try {
            $productIds = $validated['product_ids'];
            $prices = $validated['prices'];

            foreach ($productIds as $productId) {
                // Delete existing prices for this product for these categories
                $categories = array_column($prices, 'category');
                ProductPrice::where('product_id', $productId)
                    ->whereIn('category', $categories)
                    ->delete();

                // Insert new prices
                foreach ($prices as $price) {
                    ProductPrice::create([
                        'product_id' => $productId,
                        'category' => $price['category'],
                        'min_quantity' => $price['min_quantity'],
                        'max_quantity' => $price['max_quantity'],
                        'discount_percent' => $price['discount_percent'],
                        'price' => 0,
                        'status' => 1,
                        'created_by' => auth()->id(),
                    ]);
                }
            }

            return response()->json([
                'status' => 300,
                'message' => 'Prices saved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 303,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteAllPrices($productId)
    {
        try {
            $product = Product::findOrFail($productId);
            
            // Delete all prices for this product across all categories
            ProductPrice::where('product_id', $productId)->delete();

            return response()->json([
                'success' => true,
                'message' => 'All prices deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}