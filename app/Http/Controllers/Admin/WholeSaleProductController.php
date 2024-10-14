<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Models\Size;
use Illuminate\Http\Request;
use App\Models\WholeSaleProduct;
use App\Models\Product;

class WholeSaleProductController extends Controller
{
    public function getWholeSaleProduct()
    {
        $wholeSaleProducts = WholeSaleProduct::all();
        $products = Product::select('id', 'name', 'price')->get();
        $sizes = Size::all();
        $colors = Color::all();
        return view('admin.whole_sale_products.index', compact('wholeSaleProducts', 'products', 'sizes', 'colors'));
    }
}
