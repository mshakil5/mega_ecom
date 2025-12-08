<?php

namespace App\Http\Controllers;

use App\Models\Color;
use App\Models\Guideline;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\Size;
use App\Models\Stock;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function getCount()
    {
        $cart = session()->get('cart', []);

        if (!is_array($cart)) {
            $cart = json_decode($cart, true) ?: [];
        }
        $count = count($cart);

        return response()->json(['count' => $count]);
    }

    public function addToSession(Request $request)
    {
        $cart = session()->get('cart', []);
        if (!is_array($cart)) $cart = [];

        foreach ($request->sizes as $size) {
            $sizeName = Stock::where('id', $size['stock_id'])->first();
            $key = $request->product_id . '_' . ($request->color_id ?? 0) . '_' . $sizeName->size . '_' . $size['stock_id'];

            // $product = Product::findOrFail($request->color_id);
            $product = Product::findOrFail($request->product_id);


            if(isset($cart[$key]) && is_array($cart[$key])) {
                $cart[$key]['quantity'] += $size['quantity'];
            } else {
                $cart[$key] = [
                    'product_id' => $request->product_id,
                    'color_id' => $request->color_id,
                    'sizeName' => $sizeName->size,
                    'stock_id' => $size['stock_id'],
                    'quantity' => $size['quantity'],
                    'product_name' => $request->product_name,
                    'product_image' => $request->product_image ?? null,
                    'ean' => $size['ean'] ?? null
                ];
            }
        }

        session(['cart' => $cart]);

        return response()->json([
            'success' => true,
            'cart' => $cart
        ]);
    }


    public function customize(Request $request)
    {
        $encodedId = $request->query('product');
        $productId = intval(base64_decode($encodedId));
        $cart = session()->get('cart', []);


        // Group by size_id for this product
        $sizeWiseData = collect($cart)
            ->where('product_id', (string)$productId)
            ->groupBy('stock_id')
            ->map(function ($items, $sizeId) {
                $sizeName = Stock::find($sizeId)?->size ?? 'N/A';
                $qty = collect($items)->sum('quantity');
                return [
                    'stock_id' => $sizeId,
                    'size_name' => $sizeName,
                    'quantity' => $qty,
                ];
            })
            ->values()
            ->toArray();


        // Total qty
        $totalQty = collect($sizeWiseData)->sum('quantity');

        $product = Product::with(['colors'])->findOrFail($productId);



        $colors = collect($cart)
            ->where('product_id', (string)$productId)
            ->pluck('color_id')
            ->unique()
            ->values()
            ->toArray();


        // get first color id
        $firstColorId = $colors[0] ?? null;

        $colorName = null;

        if ($firstColorId) {
            $colorName = Color::where('id', $firstColorId)->value('color');
        }



        $guidelines = Guideline::latest()->get();


        $images = [];
        foreach (['front', 'back', 'left', 'right'] as $type) {
            $img = $product->images()
                ->where('image_type', $type)
                ->where('color_id', $firstColorId)
                ->orderByDesc('id')
                ->first();

            $images[$type] = $img
                ? $img->image_path
                : 'https://placehold.co/400x300?bg=ccc&color=000&text=' . ucfirst($type);
        }

        
        $dataProduct = [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'image' => $product->feature_image,
            'img' => $images,
            'baseWidthCm' => 30,
            'quantity' => $totalQty,
            'sizes' => $sizeWiseData, // now contains name + qty per size
            'colorID' => $firstColorId, // now contains name + qty per size
            'colorName' => $colorName, // now contains name + qty per size
        ];




        return view('frontend.product.customize', compact('dataProduct', 'guidelines', 'cart'));
    }


}
