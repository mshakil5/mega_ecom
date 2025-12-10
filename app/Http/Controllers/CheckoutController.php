<?php

namespace App\Http\Controllers;

use App\Models\Color;
use App\Models\Product;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
        public function checkout(Request $request)
    {
        $sessionCart = $request->session()->get('cart', []);


        if (!is_array($sessionCart) || empty($sessionCart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $productIds = collect($sessionCart)->pluck('product_id')->unique()->filter()->values()->all();
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        // dd($products);


        $cartItems = [];
        $total = 0;

        foreach ($sessionCart as $key => $item) {

            $pid = (int)($item['product_id'] ?? 0);
            $product = $products->get($pid);

            $price = $product ? ($product->price ?? 0) : 0;
            $quantity = isset($item['quantity']) ? (int)$item['quantity'] : 1;
            $subtotal = $price * $quantity;
            $total += $subtotal;

            $frontImage = null;
            if ($product) {
                $colorId = $item['color_id'] ?? null;
            

                $frontImageRow = $product->images()
                    ->where('image_type', 'front')
                    ->when($colorId, fn($q) => $q->where('color_id', $colorId))
                    ->latest()
                    ->first();
                $frontImage = $frontImageRow->image_path ?? null;
            }
                $colorName = Color::where('id', $item['color_id'])->first()->color;

            $cartItems[] = [
                'key' => $key,
                'product_id' => $pid,
                'product' => $product,
                'product_name' => $item['product_name'] ?? ($product->name ?? 'Unknown Product'),
                'product_image' => $frontImage ?? ($item['product_image'] ?? $product->feature_image ?? null),
                'ean' => $item['ean'] ?? null,
                'size_id' => $item['size_id'] ?? null,
                'color_id' => $item['color_id'] ?? null,
                'colorName' => $colorName,
                'quantity' => $quantity,
                'price' => $price,
                'subtotal' => $subtotal,
                'customization' => $item['customization'] ?? [],
            ];
        }

        return view('frontend.checkout', [
            'cartItems' => $cartItems,
            'total' => $total,
            'currency' => '£',
        ]);
    }

}
