<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CustomiserController extends Controller
{
    public function addToSession(Request $request)
    {
        $data = $request->all();

        $productId = (string) ($data['product_id'] ?? '');
        $productName = $data['product_name'] ?? '';
        $colorId = (string) ($data['color_id'] ?? '0');

        
        $product = Product::with('images')->findOrFail($productId);

        $productImage = $product->images()
            ->where([
                ['image_type', '=', 'front'],
                ['color_id', '=', $colorId],
            ])
            ->latest('id')
            ->value('image_path') ?? $data['product_image'];


        $sizes = $data['sizes'] ?? [];
        $sizeIdsFromPayload = $data['size_ids'] ?? [];
        if (!is_array($sizeIdsFromPayload)) {
            $sizeIdsFromPayload = json_decode($sizeIdsFromPayload, true) ?: [];
        }

        $customizationData = $data['customization_data'] ?? [];

        $cart = session()->get('cart', []);
        if (!is_array($cart)) $cart = [];

        foreach ($sizes as $size) {
            $sizeId = (string) ($size['size_id'] ?? '0');

            if ($sizeId === '0' || $sizeId === '' || $sizeId === null) {
                continue;
            }

            $quantity = isset($size['quantity']) ? (int)$size['quantity'] : 1;
            $ean = $size['ean'] ?? null;

            $key = "{$productId}_{$colorId}_{$sizeId}";

            if (isset($cart[$key])) {
                $cart[$key]['quantity'] = (int)$cart[$key]['quantity'] + $quantity;
            } else {
                $cart[$key] = [
                    'product_id' => $productId,
                    'color_id'   => $colorId,
                    'size_id'    => $sizeId,
                    'quantity'   => $quantity,
                    'product_name' => $productName,
                    'product_image' => $productImage,
                    'ean' => $ean,
                    'customization' => [],
                ];
            }
        }

        $existingKeysForProductColor = array_filter(array_keys($cart), function($k) use ($productId, $colorId) {
            return strpos($k, "{$productId}_{$colorId}_") === 0;
        });

        foreach ($customizationData as $custom) {
            $targetSizeIds = [];

            if (isset($custom['sizeId']) && $custom['sizeId'] !== null && $custom['sizeId'] !== '') {
                $targetSizeIds[] = (string)$custom['sizeId'];
            } elseif (!empty($sizeIdsFromPayload)) {
                foreach ($sizeIdsFromPayload as $s) {
                    $targetSizeIds[] = (string)$s;
                }
            } else {
                if (!empty($existingKeysForProductColor)) {
                    $firstKey = reset($existingKeysForProductColor);
                    $parts = explode('_', $firstKey);
                    $targetSizeIds[] = isset($parts[2]) ? (string)$parts[2] : '0';
                }
            }

            $normalized = [
                'productId' => isset($custom['productId']) ? $custom['productId'] : $productId,
                'sizeId'    => null,
                'colorId'   => isset($custom['colorId']) ? $custom['colorId'] : $colorId,
                'method'    => $custom['method'] ?? null,
                'position'  => $custom['position'] ?? null,
                'type'      => $custom['type'] ?? null,
                'data'      => $custom['data'] ?? [],
                'zIndex'    => $custom['zIndex'] ?? null,
                'layerId'   => $custom['layerId'] ?? null,
            ];

            foreach ($targetSizeIds as $ts) {
                $key = "{$productId}_{$colorId}_{$ts}";

                if (isset($cart[$key])) {
                    $norm = $normalized;
                    $norm['sizeId'] = $ts;
                    $cart[$key]['customization'][] = $norm;
                } else {
                    if (!empty($existingKeysForProductColor)) {
                        $fallbackKey = reset($existingKeysForProductColor);
                        $norm = $normalized;
                        $parts = explode('_', $fallbackKey);
                        $norm['sizeId'] = isset($parts[2]) ? $parts[2] : null;
                        $cart[$fallbackKey]['customization'][] = $norm;
                    }
                }
            }
        }

        session(['cart' => $cart]);

        return response()->json([
            'success' => true,
            'cart' => $cart,
            'data' => $data,
        ]);
    }
}
