<?php
  
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
  
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
  
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function userHome(): View
    {
        return view('user.dashboard');
    } 
  
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function adminHome(): View
    {
        return view('admin.dashboard');
    }
  
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function managerHome(): View
    {
        return view('manager.dashboard');
    }

    public function admin()
    {
        if (Auth::check()) {
            $user = auth()->user();

            if ($user->is_type == '1') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->is_type == '2') {
                return redirect()->route('manager.dashboard');
            } else {
                return redirect()->route('user.dashboard');
            }
        } else {
            return redirect()->route('login');
        }
    }

    public function toggleSidebar(Request $request)
    {
        $user = Auth::user();

        if ($user->sidebar == 0) {
            $user->sidebar = 1;
        } else {
            $user->sidebar = 0;
        }
        // $user->sidebar = $request->input('sidebar');
        $user->save();

        return redirect()->route('admin.dashboard');
    }

    public function truncateTables()
    {
      $tables = [
        'ads',
        'branches',
        'bundle_products',
        'bundle_product_images',
        'buy_one_get_ones',
        'buy_one_get_one_images',
        'campaigns',
        'campaign_requests',
        'campaign_request_products',
        'cancelled_orders',
        'categories',
        'chart_of_accounts',
        'colors',
        'contacts',
        'coupons',
        'coupon_usages',
        'delivery_charges',
        'delivery_men',
        'equity_holders',
        'faq_questions',
        'flash_sells',
        'flash_sell_details',
        'groups',
        'orders',
        'order_details',
        'order_due_collections',
        'order_returns',
        'products',
        'product_colors',
        'product_models',
        'product_prices',
        'product_reviews',
        'product_sizes',
        'product_types',
        'purchases',
        'purchase_histories',
        'purchase_history_logs',
        'purchase_returns',
        'related_products',
        'shipments',
        'shipment_details',
        'shippings',
        'shipping_costs',
        'shipping_costs',
        'sizes',
        'sliders',
        'special_offers',
        'special_offer_details',
        'stocks',
        'stock_histories',
        'stock_transfer_requests',
        'sub_categories',
        'suppliers',
        'supplier_stocks',
        'supplier_transactions',
        'system_loses',
        'transactions',
        'types',
        'units',
        'warehouses',
      ];

      Schema::disableForeignKeyConstraints();

      foreach ($tables as $table) {
        DB::table($table)->truncate();
      }

      Schema::enableForeignKeyConstraints();

      return 'Tables truncated successfully.';
    }
}