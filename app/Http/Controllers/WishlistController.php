<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function store(Request $request)
    {
        $wishlist = session('wishlist', []);

        // Remove
        if ($request->remove) {
            unset($wishlist[$request->remove]);
            session()->put('wishlist', $wishlist);

            return response()->json([
                'wishlist' => $wishlist,
                'count' => count($wishlist)
            ]);
        }

        foreach ($request->sizes as $size) {
            $key = $request->product_id.'-'.$request->color_id.'-'.$size['size_id'];

            $wishlist[$key] = [
                'product_id' => $request->product_id,
                'name' => $request->product_name,
                'image' => asset('images/products/'.$request->product_image),
                'color' => $request->color_name,
                'size' => $size['size_name'],
                'size_id' => $size['size_id']
            ];
        }

        session()->put('wishlist', $wishlist);

        return response()->json([
            'wishlist' => $wishlist,
            'count' => count($wishlist)
        ]);
    }

    public function index()
    {
        $wishlist = session('wishlist', []);

        return response()->json([
            'wishlist' => $wishlist,
            'count' => count($wishlist),
        ]);
    }
}
