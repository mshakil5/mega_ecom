<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Color;
use App\Models\Size;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class DirectPurchaseController extends Controller
{
    public function purchase()
    {
        $products = Product::with(['stock', 'types:id,name'])->orderBy('id','DESC')->select('id', 'name', 'price', 'product_code')->where('active_status', 1)->get();
        $suppliers = Supplier::where('status', 1)->select('id', 'name')->orderby('id','DESC')->get();
        $colors = Color::where('status', 1)->select('id', 'color')->orderby('id','DESC')->get();
        $sizes = Size::where('status', 1)->select('id', 'size')->orderby('id','DESC')->get();
        $warehouses = Warehouse::select('id', 'name','location')->where('status', 1)->get();
        $expenses = ChartOfAccount::where('account_head', 'Expenses')->where('sub_account_head', 'Cost Of Good Sold')->select('id', 'account_name')->get();
        return view('admin.stock.direct_purchase', compact('products', 'suppliers', 'colors', 'sizes','warehouses','expenses'));
    }
}
