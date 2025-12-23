<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\Product;
use session;
use App\Models\CompanyDetails;
use App\Models\Contact;
use App\Models\SpecialOfferDetails;
use App\Models\FlashSell;
use App\Models\FlashSellDetails;
use App\Models\Coupon;
use App\Models\SubCategory;
use App\Models\Stock;
use App\Models\SpecialOffer;
use App\Models\SectionStatus;
use App\Models\Ad;
use App\Models\Supplier;
use App\Models\Slider;
use App\Models\SupplierStock;
use App\Models\RelatedProduct;
use App\Models\BuyOneGetOne;
use App\Models\BundleProduct;
use App\Models\Campaign;
use App\Models\CampaignRequest;
use App\Models\CampaignRequestProduct;
use App\Models\Brand;
use App\Models\Color;
use App\Models\CouponUsage;
use App\Models\StockHistory;
use App\Models\Size;
use App\Models\ProductReview;
use App\Models\FaqQuestion;
use App\Models\ProductPrice;
use Illuminate\Support\Facades\Cache;
use SebastianBergmann\Environment\Console;
use App\Models\ContactEmail;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;

class FrontendController extends Controller
{
    



    public function index()
    {
        $currency = CompanyDetails::value('currency');
        $section_status = SectionStatus::first();

        $specialOffers = Cache::remember('home_special_offers', 600, function () {
            return SpecialOffer::select('offer_image', 'offer_name', 'offer_title', 'slug')
                ->where('status', 1)
                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->latest()
                ->get();
        });

        $flashSells = Cache::remember('home_flash_sells', 600, function () {
            return FlashSell::select('flash_sell_image', 'flash_sell_name', 'flash_sell_title', 'slug')
                ->where('status', 1)
                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->latest()
                ->get();
        });

        $campaigns = Cache::remember('home_campaigns', 600, function () {
            return Campaign::select('banner_image', 'title', 'slug')
                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->latest()
                ->get();
        });

        $getProducts = function ($flagColumn, $orderByColumn = 'id') {
            return Product::active()
                ->where($flagColumn, 1)
                ->withoutOffers()
                ->commonSelect()
                ->orderByDesc($orderByColumn)
                ->take(11)
                ->get();
        };

        // $getProducts = function ($flagColumn, $orderByColumn = 'id') {
        //     return Product::active()
        //         ->where($flagColumn, 1)
        //         ->withoutOffers()
        //         ->commonSelect()
        //         ->whereHas('stockhistory')
        //         ->with(['stockhistory' => function($q) {
        //             $q->with('product');
        //         }])
        //         ->orderByDesc($orderByColumn)
        //         ->take(11)
        //         ->get();
        // };

        // $products = $getProducts('is_trending');
        // dd($products->toArray());

        $trendingProducts     = Cache::remember('home_trending_products', 600, fn() => $getProducts('is_trending'));
        $recentProducts       = Cache::remember('home_recent_products', 600, fn() => $getProducts('is_recent', 'id'));
        $mostViewedProducts   = Cache::remember('home_most_viewed_products', 600, fn() => $getProducts('is_recent', 'watch'));
        $newProducts          = Cache::remember('home_new_products', 600, fn() => $getProducts('is_new_arrival'));
        $popularProducts      = Cache::remember('home_popular_products', 600, fn() => $getProducts('is_popular'));
        $featuredProducts     = Cache::remember('home_featured_products', 600, fn() => $getProducts('is_featured'));

        $customizableProducts = Product::active()
            ->where('is_customizable', 1)
            ->withoutOffers()
            ->commonSelect()
            ->orderByDesc('id')
            ->take(11)
            ->get();

        $slider = Cache::remember('home_sliders', 600, function () {
            return Slider::where('status', 1)
                ->orderBy('id', 'asc')
                ->select('title', 'sub_title', 'image', 'link')
                ->first();
        });

        $advertisements = Cache::remember('home_ads', 600, function () {
            return Ad::where('status', 1)
                ->select('type', 'link', 'image')
                ->get();
        });

        $categories = Cache::remember('home_categories', 600, function () {
            return Category::where('status', 1)
                ->select('id', 'name', 'image', 'slug')
                ->orderBy('id')
                ->with(['products' => function ($query) {
                    $query->active()
                        ->withoutOffers()
                        ->select('id', 'category_id', 'name', 'price', 'slug', 'feature_image', 'watch')
                        ->orderByDesc('watch')
                        ->with('stock')
                        ->take(6);
                }])
                ->get();
        });


        return view('frontend.index', compact(
            'currency',
            'specialOffers',
            'flashSells',
            'campaigns',
            'trendingProducts',
            'recentProducts',
            'mostViewedProducts',
            'newProducts',
            'popularProducts',
            'featuredProducts',
            'section_status',
            'advertisements',
            'slider',
            'categories',
            'customizableProducts'
        ));
    }

    public function index2() { 

        $currency = CompanyDetails::value('currency'); $specialOffers = SpecialOffer::select('offer_image', 'offer_name', 'offer_title', 'slug') ->where('status', 1) ->whereDate('start_date', '<=', now()) ->whereDate('end_date', '>=', now()) ->latest() ->get(); $flashSells = FlashSell::select('flash_sell_image', 'flash_sell_name', 'flash_sell_title', 'slug') ->where('status', 1) ->whereDate('start_date', '<=', now()) ->whereDate('end_date', '>=', now()) ->latest() ->get(); $campaigns = Campaign::select('banner_image', 'title', 'slug') ->whereDate('start_date', '<=', now()) ->whereDate('end_date', '>=', now()) ->latest() ->get(); $trendingProducts = Product::where('active_status', 1) ->where('is_trending', 1) ->orderByDesc('id') ->whereDoesntHave('specialOfferDetails') ->whereDoesntHave('flashSellDetails') ->with('stock', 'category') ->select('id', 'name', 'feature_image', 'slug', 'price', 'category_id') ->take(12) ->get(); $mostViewedProducts = Product::where('active_status', 1) ->where('is_recent', 1) ->orderByDesc('watch') ->whereDoesntHave('specialOfferDetails') ->whereDoesntHave('flashSellDetails') ->with('stock', 'category') ->select('id', 'name', 'feature_image', 'price', 'slug', 'category_id') ->take(12) ->get(); 

        $recentProducts = Product::where('active_status', 1) ->where('is_recent', 1) ->orderByDesc('id')->with('stock', 'category') ->select('id', 'name', 'feature_image', 'price', 'slug', 'category_id')->count(); 

        dd( $recentProducts );
        
        $newProducts = Product::where('active_status', 1) ->where('is_new_arrival', 1) ->orderByDesc('id') ->whereDoesntHave('specialOfferDetails') ->whereDoesntHave('flashSellDetails') ->with('stock', 'category') ->select('id', 'name', 'feature_image', 'price', 'slug', 'category_id') ->take(12) ->get(); $popularProducts = Product::where('active_status', 1) ->where('is_popular', 1) ->orderByDesc('id') ->whereDoesntHave('specialOfferDetails') ->whereDoesntHave('flashSellDetails') ->with('stock', 'category') ->select('id', 'name', 'feature_image', 'price', 'slug', 'category_id') ->take(12) ->get(); $featuredProducts = Product::where('active_status', 1) ->where('is_featured', 1) ->orderByDesc('id') ->whereDoesntHave('specialOfferDetails') ->whereDoesntHave('flashSellDetails') ->with('stock', 'category') ->select('id', 'name', 'feature_image', 'price', 'slug', 'category_id') ->take(12) ->get(); $section_status = SectionStatus::first(); $advertisements = Ad::where('status', 1)->select('type', 'link', 'image')->get(); $sliders = Slider::orderBy('id', 'asc') ->where('status', 1) ->select('title', 'sub_title', 'image', 'link') ->get(); $categories = Category::where('status', 1) ->with(['products' => function ($query) { $query->where('active_status', 1) ->select('id', 'category_id', 'name', 'price', 'slug', 'feature_image', 'watch') ->orderBy('watch', 'desc') ->with('stock'); }]) ->select('id', 'name', 'image', 'slug') ->orderBy('id', 'asc') ->get() ->each(function ($category) { $category->setRelation('products', $category->products->take(6)); }); return view('frontend.index', compact('specialOffers', 'flashSells', 'trendingProducts', 'currency', 'recentProducts',  'section_status', 'advertisements', 'sliders', 'categories', 'campaigns', 'mostViewedProducts', 'newProducts', 'popularProducts', 'featuredProducts')); 
    }




    public function getCategoryProducts(Request $request)
    {
        $categoryId = $request->input('category_id');
        $page = $request->input('page', 1);
        $perPage = 6;

        $query = Product::where('category_id', $categoryId)
                        ->where('active_status', 1)
                        ->whereDoesntHave('specialOfferDetails')
                        ->whereDoesntHave('flashSellDetails')
                        ->select('id', 'name', 'feature_image', 'price', 'slug')
                        ->orderBy('id', 'desc');

        $shownProducts = $request->input('shown_products', []);
        if (!empty($shownProducts)) {
            $query->whereNotIn('id', $shownProducts);
        }

        $products = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json($products);
    }

    public function showCategoryProducts($slug)
    {
        $currency = CompanyDetails::value('currency');

        $category = Category::where('slug', $slug)->firstOrFail();

        $products = Product::where('category_id', $category->id)
                            ->where('active_status', 1)
                            ->whereDoesntHave('specialOfferDetails')
                            ->whereDoesntHave('flashSellDetails')
                            ->with('stock')
                            ->select('id', 'category_id', 'name', 'feature_image', 'price', 'slug')
                            ->paginate(20);
        
        $company = CompanyDetails::select('company_name')->first();
        $title = $company->company_name . ' - ' . $category->name;
        
        return view('frontend.category_products', compact('category', 'products', 'title', 'currency'));
    }    

    public function showSubCategoryProducts($slug)
    {
        $currency = CompanyDetails::value('currency');

        $sub_category = SubCategory::where('slug', $slug)->firstOrFail();

        $products = Product::where('sub_category_id', $sub_category->id)
                            ->where('active_status', 1)
                            ->whereDoesntHave('specialOfferDetails')    
                            ->whereDoesntHave('flashSellDetails')
                            ->with('stock')
                            ->select('id', 'sub_category_id', 'name', 'feature_image', 'price', 'slug')
                            ->paginate(20);

        $company = CompanyDetails::select('company_name')->first();
        $title = $company->company_name . ' - ' . $sub_category->name;

        return view('frontend.sub_category_products', compact('sub_category', 'products', 'title', 'currency'));
    }



    public function showProduct($slug, $offerId = null)
    {
        $product = Product::where('slug', $slug)->with(['colors.color', 'stockhistory', 'stock', 'reviews', 'prices', 'positionImages'])->firstOrFail();

        $supplierPrice = null;

        $product->watch = $product->watch + 1;
        $product->save();
        $specialOffer = null;
        $flashSell = null;
        $offerPrice = null;
        $flashSellPrice = null;
        $oldOfferPrice = null;
        $OldFlashSellPrice = null;

        if ($offerId == 1) {
            $specialOffer = SpecialOfferDetails::where('product_id', $product->id)
                ->whereHas('specialOffer', function ($query) {
                    $query->whereDate('start_date', '<=', now())
                        ->whereDate('end_date', '>=', now());
                })
                ->first();
            $offerPrice = $specialOffer ? $specialOffer->offer_price : null;
            $oldOfferPrice = $specialOffer ? $specialOffer->old_price : null;
        } elseif ($offerId == 2) {
            $flashSell = FlashSellDetails::where('product_id', $product->id)
                ->whereHas('flashsell', function ($query) {
                    $query->whereDate('start_date', '<=', now())
                        ->whereDate('end_date', '>=', now());
                })
                ->first();
            
            $flashSellPrice = $flashSell ? $flashSell->flash_sell_price : null;
            $OldFlashSellPrice = $flashSell ? $flashSell->old_price : null;
        }

        $regularPrice = $product->price;

        $company_name = CompanyDetails::value('company_name');
        $title = $company_name . ' - ' . $product->name;
        $currency = CompanyDetails::value('currency');
        $sizeGuide = CompanyDetails::value('size_guide');


        $relatedProducts = RelatedProduct::where('product_id', $product->id)
            ->where('status', 1)
            ->first();

        if ($relatedProducts && $relatedProducts->related_product_ids) {
            $relatedProductIds = json_decode($relatedProducts->related_product_ids, true);

            $relatedProducts = Product::whereIn('id', $relatedProductIds)
                ->where('id', '!=', $product->id)
                ->select('id', 'name', 'feature_image', 'price', 'slug')
                ->orderByDesc('created_at')
                ->get();
        } else {
            $relatedProducts = Product::where('category_id', $product->category_id)
                ->whereDoesntHave('specialOfferDetails')
                ->whereDoesntHave('flashSellDetails')
                ->where('id', '!=', $product->id)
                ->select('id', 'name', 'feature_image', 'price', 'slug')
                ->orderByDesc('created_at')
                ->take(5)
                ->get();
        }

        return view('frontend.product.single_product', compact('product', 'relatedProducts', 'title', 'regularPrice', 'offerPrice', 'flashSellPrice', 'offerId', 'currency', 'oldOfferPrice', 'OldFlashSellPrice', 'sizeGuide'));
    }

    public function showProduct2($slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        $product->increment('watch');
        $prices = ProductPrice::where('product_id', $product->id)
          ->where('status', 1)
          ->get()
          ->groupBy('category');
        $relatedProducts = Product::where('category_id', $product->category_id)->where('id', '!=', $product->id)->take(5)->get();
        return view('frontend.product.single_product', compact('product', 'relatedProducts', 'prices'));
    }

    public function bogoShowProduct($slug)
    {
        $product = Product::where('slug', $slug)->with('images')->firstOrFail();
        $product->watch = $product->watch + 1;
        $product->save();
        $regularPrice = $product->price;

        $bogo = BuyOneGetOne::with('images')->where('product_id', $product->id)
            ->where('status', 1)
            ->first();

        if ($bogo) {
            $regularPrice = $bogo->price;

            $getProductIds = json_decode($bogo->get_product_ids, true);
            $bogoProducts = Product::whereIn('id', $getProductIds)
                ->select('id', 'name', 'feature_image', 'price', 'slug')
                ->get();
        } else {
            $bogoProducts = collect();
        }

        $company_name = CompanyDetails::value('company_name');
        $title = $company_name . ' - ' . $product->name;
        $currency = CompanyDetails::value('currency');
        $quantity = $bogo ? $bogo->quantity : null;

        return view('frontend.product.bogo_single_product', compact('product', 'title', 'regularPrice', 'currency', 'quantity', 'bogo', 'bogoProducts'));
    }

    public function bundleSingleProduct($slug)
    {

        $bundle = BundleProduct::with('images')
            ->where('slug', $slug)
            ->where('status', 1)
            ->firstOrFail();

        $productIds = json_decode($bundle->product_ids, true);
        $bundleProducts = Product::whereIn('id', $productIds)
            ->select('id', 'name', 'feature_image', 'price', 'slug')
            ->get();

        $company_name = CompanyDetails::value('company_name');
        $title = $company_name . ' - ' . $bundle->name;
        $currency = CompanyDetails::value('currency');

        return view('frontend.product.bundle_single_product', compact('bundle', 'title', 'currency', 'bundleProducts'));
    }

    public function showSupplierProduct($slug, $supplierId = null)
    {
        $product = Product::where('slug', $slug)->with('images')->firstOrFail();
        $regularPrice = null;

            if ($supplierId) {
                
            $stock = SupplierStock::where('product_id', $product->id)
                ->where('supplier_id', $supplierId)
                ->first();

            if ($stock) {
                $regularPrice = $stock->price;
                $stockQuantity = $stock->quantity;
                $stockDescription = $stock->description;
            }
        }

        $product->watch = $product->watch + 1;
        $product->save();

        $company_name = CompanyDetails::value('company_name');
        $title = $company_name . ' - ' . $product->name;
        $currency = CompanyDetails::value('currency');

        return view('frontend.product.supplier_single_product', compact('product', 'title', 'regularPrice', 'currency', 'stockQuantity', 'stockDescription', 'supplierId'));
    }

    public function storeWishlist(Request $request)
    {
        $request->session()->put('wishlist', $request->input('wishlist'));
        return response()->json(['success' => true]);
    }

    public function showWishlist(Request $request)
    {
        $wishlistJson = $request->session()->get('wishlist', '[]');
        $wishlist = json_decode($wishlistJson, true);
 
        $productIds = array_column($wishlist, 'productId');
        $products = Product::whereIn('id', $productIds)->with('stock')->get();

        foreach ($products as $product) {
            foreach ($wishlist as $item) {
                if ($item['productId'] == $product->id) {
                    if ($item['offerId'] == 1) {
                        $product->offer_price = $item['price'];
                        $product->offer_id = 1; 
                    } elseif ($item['offerId'] == 2) {
                        $product->flash_sell_price = $item['price'];
                        $product->offer_id = 2;
                    } else {
                        $product->price = $product->stock()
                          ->where('quantity', '>', 0)
                          ->orderBy('id', 'desc')
                          ->value('selling_price') ?? $item['price'];
                        $product->offer_id = 0;
                    }
                    if (isset($item['campaignId'])) {
                        $product->campaign_id = $item['campaignId'];
                        $campaignRequestProduct = CampaignRequestProduct::find($item['campaignId']);
                        $product->quantity = $campaignRequestProduct->quantity;
                    }
                }
            }
        }

        return view('frontend.wish_list', compact('products'));
    }

    public function storeCart(Request $request)
    {
        $request->session()->put('cart', $request->input('cart'));
        return response()->json(['success' => true]);
    }

    public function showCart(Request $request)
    {
        $cart = $request->session()->get('cart', '[]');
        return view('frontend.cart', compact('cart'));
    }

    // public function checkout(Request $request)
    // {
    //     $cart = json_decode($request->input('cart'), true);
    //     return view('frontend.checkout', compact('cart'));
    // }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $products = Product::where('name', 'LIKE', "%$query%")
                            ->where('active_status', 1)
                            ->whereDoesntHave('specialOfferDetails')
                            ->whereDoesntHave('flashSellDetails')
                            ->orderBy('id', 'desc')
                            ->take(15)
                            ->with('stock')
                            ->get();
    
        $products->each(function($product) {
            $product->price = $product->stockhistory()
                ->where('available_qty', '>', 0)
                ->orderBy('id', 'asc')
                ->value('selling_price') ?? $product->price;
    
            $product->colors = $product->stock()
                ->where('quantity', '>', 0)
                ->distinct('color')
                ->pluck('color');
    
            $product->sizes = $product->stock()
                ->where('quantity', '>', 0)
                ->distinct('size')
                ->pluck('size');
    
            return $product;
        });

        return response()->json(['products' => $products]);
    }
    
    // public function shop(Request $request)
    // {
    //     $currency = CompanyDetails::value('currency');

    //     $categories = Category::where('status', 1)
    //         ->whereHas('products.stock', function($query) {
    //             $query->where('quantity', '>', 0);
    //         })
    //         ->orderBy('id', 'desc')
    //         ->select('id', 'name')
    //         ->get();
            
    //     $brands = Brand::where('status', 1)
    //         ->whereHas('products.stock', function($query) {
    //             $query->where('quantity', '>', 0);
    //         })
    //         ->orderBy('id', 'desc')
    //         ->select('id', 'name')
    //         ->get();

    //     $colors = Stock::where('quantity', '>', 0)
    //         ->groupBy('color')
    //         ->select('color')
    //         ->get();

    //     $sizes = Stock::where('quantity', '>', 0)
    //         ->groupBy('size')
    //         ->select('size')
    //         ->get();

    //     $minPrice = Stock::where('status', 1)->min('selling_price'); 
    //     $maxPrice = Stock::where('status', 1)->max('selling_price');

    //     return view('frontend.shop', compact('currency', 'categories', 'brands', 'colors', 'sizes', 'minPrice', 'maxPrice'));
    // }

    public function shop2(Request $request)
    {
        $currency = CompanyDetails::value('currency');

        // $categories = Category::where('status', 1)
        //     ->whereHas('products.stock', function($query) {
        //         $query->where('quantity', '>', 0);
        //     })
        //     ->orderBy('id', 'desc')
        //     ->get();

            $products = Product::where('status', 1)->get();

            $categories = Category::with('products', 'subcategories')->where('status', 1)
            ->orderBy('id', 'desc')->limit(2)
            ->get();

            dd($categories);
            

            

        return view('frontend.shop', compact('currency', 'categories',));
    }

    public function shop(Request $request)
    {
        // 1. Fetch Categories with their Subcategories (Eager Loading)
        $categories = Category::with('subcategories')
                            ->where('status', 1) // Assuming status 1 means active
                            ->get();

        // 2. Fetch the initial list of Products (e.g., paginated)
        // Adjust pagination as needed, e.g., 8 products per page.
        $products = Product::where('status', 1)->with('stock') // Assuming status 1 means active
                           ->get();

        $selectedCategory = $request->query('category');
        $selectedSubcategory = $request->query('subcategory');
        // 3. Pass data to the view
        return view('frontend.shop', compact('categories', 'products', 'selectedCategory', 'selectedSubcategory'));
    }

    public function shopfilter(Request $request)
    {
        $query = Product::where('status', 1);

        // Check for Category IDs array
        if ($request->has('category_ids') && is_array($request->category_ids) && count($request->category_ids) > 0) {
            $query->whereIn('category_id', $request->category_ids);
        }

        // Check for Subcategory IDs array
        if ($request->has('subcategory_ids') && is_array($request->subcategory_ids) && count($request->subcategory_ids) > 0) {
            
            // If both Category AND Subcategory filters are active, you might need a complex OR condition.
            // For a simple hierarchy, if a subcategory is chosen, we filter by that.
            // You might want to decide if selecting a main category should include ALL its subproducts.
            
            // For now, let's combine the filters (AND logic for selected categories AND subcategories).
            $query->whereIn('sub_category_id', $request->subcategory_ids);
        }

        // Fetch ALL filtered products
        $products = $query->get();

        // Render the product grid part of the blade file and return it as HTML
        $productHtml = view('frontend.partials.product_grid', compact('products'))->render();

        return response()->json([
            'html' => $productHtml,
        ]);
    }

    public function supplierPage($slug)
    {
        $currency = CompanyDetails::value('currency');
        $supplier = Supplier::where('slug', $slug)->firstOrFail();
        $company = CompanyDetails::select('company_name')->first();
        $title = $company->company_name . ' - ' . $supplier->name;

        $approvedStocks = SupplierStock::where('supplier_id', $supplier->id)
                                    ->where('is_approved', 1)
                                    ->select('product_id', 'price', 'quantity')
                                    ->get();

        $productIds = $approvedStocks->pluck('product_id');

        $products = Product::whereIn('id', $productIds)
                            ->with(['supplierStocks' => function ($query) {
                                $query->select('product_id', 'price', 'quantity');
                            }])
                            ->select('id', 'name', 'slug', 'price', 'feature_image')
                            ->get();

        return view('frontend.supplier_products', compact('supplier', 'title', 'currency', 'products'));
    }

    public function searchSupplierProducts(Request $request)
    {
        $query = $request->input('query');
        $supplierId = $request->input('supplier_id');

        $products = Product::whereHas('supplierStocks', function ($q) use ($supplierId) {
                                    $q->where('supplier_id', $supplierId);
                                })
                                ->where('name', 'LIKE', "%$query%")
                                ->where('status', 1)
                                ->orderBy('id', 'desc')
                                ->with(['supplierStocks' => function ($q) {
                                    $q->select('product_id', 'price', 'quantity');
                                }])
                                ->select('id', 'name', 'slug', 'price', 'feature_image')
                                ->take(15)
                                ->get();

        if ($products->isEmpty()) {
            return response()->json('<div class="p-2">No products found</div>');
        }

        $output = '<ul class="list-group">';
        foreach ($products as $product) {
            $output .= '<li class="list-group-item">
                            <a href="'.route('product.show.supplier', [$product->slug, $supplierId]).'">
                                '.$product->name.'
                            </a>
                        </li>';
        }
        $output .= '</ul>';

        return response()->json($output);
    }

    public function contact()
    {
        $companyDetails = CompanyDetails::select('google_map', 'address1', 'email1', 'phone1')->first();
        return view('frontend.contact', compact('companyDetails'));
    }

    public function storeContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);

        $contact = new Contact();
        $contact->name = $request->input('name');
        $contact->email = $request->input('email');
        $contact->phone = $request->input('phone');
        $contact->subject = $request->input('subject');
        $contact->message = $request->input('message');
        $contact->save();

        $contactEmails = ContactEmail::where('status', 1)->pluck('email');

        foreach ($contactEmails as $contactEmail) {
            Mail::to($contactEmail)->send(new ContactMail($contact));
        }

        return back()->with('success', 'Your message has been sent successfully!');
    }

    public function aboutUs()
    {
        $companyDetails = CompanyDetails::select('about_us')->first();
        return view('frontend.about', compact('companyDetails'));
    }

    public function privacyPolicy()
    {
        $companyDetails = CompanyDetails::select('privacy_policy')->first();
        return view('frontend.privacy', compact('companyDetails'));
    }

    public function termsAndConditions()
    {
        $companyDetails = CompanyDetails::select('terms_and_conditions')->first();
        return view('frontend.terms', compact('companyDetails'));
    }

    public function faq()
    {
        $faqQuestions = FaqQuestion::orderBy('id', 'asc')->get();
        return view('frontend.faq', compact('faqQuestions'));
    }

    public function checkCoupon(Request $request)
    {
        $coupon = Coupon::where('coupon_name', $request->coupon_name)->first();
    
        // Check if the coupon exists
        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon not found.'
            ]);
        }

        // Check if the coupon is active
        if ($coupon->status != 1) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon is inactive.'
            ]);
        }
    
        // Check coupon usage
        $totalUsage = CouponUsage::where('coupon_id', $coupon->id)->count();
        if ($coupon->total_max_use > 0 && $totalUsage >= $coupon->total_max_use) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon has reached its maximum usage limit.'
            ]);
        }
    
        // Check max usage per user or guest
        if (auth()->check()) {
            $userId = auth()->user()->id;
            $userUsage = CouponUsage::where('coupon_id', $coupon->id)->where('user_id', $userId)->count();
    
            if ($coupon->max_use_per_user > 0 && $userUsage >= $coupon->max_use_per_user) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have exceeded the limit for using this coupon.'
                ]);
            }
        } else {
            // Check max usage per guest based on either email or phone
            $guestEmail = $request->input('guest_email');
            $guestPhone = $request->input('guest_phone');
            
            $guestUsage = CouponUsage::where('coupon_id', $coupon->id)
                ->where(function ($query) use ($guestEmail, $guestPhone) {
                    if ($guestEmail) {
                        $query->where('guest_email', $guestEmail);
                    }
                    if ($guestPhone) {
                        $query->orWhere('guest_phone', $guestPhone);
                    }
                })
                ->count();
    
            if ($coupon->max_use_per_user > 0 && $guestUsage >= $coupon->max_use_per_user) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have exceeded the limit for using this coupon.'
                ]);
            }
        }
    
        return response()->json([
            'success' => true,
            'coupon_id' => $coupon->id,
            'coupon_type' => $coupon->coupon_type,
            'coupon_value' => $coupon->coupon_value
        ]);
    }    
    

    public function filter(Request $request)
    {
        $startPrice = $request->input('start_price');
        $endPrice   = $request->input('end_price');
        $categoryId = $request->input('category');
        $brandId    = $request->input('brand');
        $size       = $request->input('size');
        $color      = $request->input('color');

        $productsQuery = Product::select(
            'products.id',
            'products.name',
            'products.slug',
            'products.feature_image',
            'products.price',
            'products.category_id',
            'products.brand_id',
            \DB::raw('COALESCE(SUM(stocks.quantity), 0) as total_stock')
        )
        ->leftJoin('stocks', 'products.id', '=', 'stocks.product_id')
        ->where('products.active_status', 1)
        ->where('stocks.quantity', '>', 0)
        ->whereDoesntHave('specialOfferDetails')
        ->whereDoesntHave('flashSellDetails')
        ->groupBy('products.id', 'products.name', 'products.slug', 'products.feature_image', 'products.price', 'products.category_id', 'products.brand_id')
        ->orderByDesc(\DB::raw('COALESCE(SUM(stocks.quantity), 0)'));

        // Filters
        if (!empty($startPrice) && !empty($endPrice)) {
            $productsQuery->whereBetween('stocks.selling_price', [$startPrice, $endPrice]);
        }
        if (!empty($categoryId)) {
            $productsQuery->where('products.category_id', $categoryId);
        }
        if (!empty($brandId)) {
            $productsQuery->where('products.brand_id', $brandId);
        }
        if (!empty($size)) {
            $productsQuery->where('stocks.size', $size);
        }
        if (!empty($color)) {
            $productsQuery->where('stocks.color', $color);
        }

        $products = $productsQuery->with([
            'stock' => function ($q) {
                $q->where('quantity', '>', 0)
                  ->select('id', 'product_id', 'selling_price', 'color', 'size', 'quantity')
                  ->orderByDesc('id');
            }
        ])->get();

        foreach ($products as $product) {
            $filteredStock = $product->stock;

            $product->price  = $filteredStock->first()->selling_price ?? $product->price;
            $product->colors = $filteredStock->pluck('color')->unique()->values();
            $product->sizes  = $filteredStock->pluck('size')->unique()->values();
        }

        return response()->json(['products' => $products]);
    }

    public function showCampaignDetails($slug)
    {
        $campaign = Campaign::where('slug', $slug)->firstOrFail();
        $campaignRequests = CampaignRequest::with(['supplier', 'campaignRequestProducts.product'])
            ->where('campaign_id', $campaign->id)
            ->where('status', 1)
            ->get();

        $company = CompanyDetails::select('company_name')
                    ->first();
        $title = $company->company_name . ' - ' . $campaign->title;
        $currency = CompanyDetails::value('currency');

        return view('frontend.campaign', compact('campaign', 'campaignRequests', 'title', 'currency'));
    }

    public function showCampaignProduct($slug, $supplierId = null)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
     
        $campaignPrice = null;
        $campaignQuantity = null;

        if ($supplierId) {
            $campaignRequest = CampaignRequest::where('supplier_id', $supplierId)
                ->where('status', 1)
                ->first();

            if ($campaignRequest) {
                $campaignProduct = CampaignRequestProduct::where('campaign_request_id', $campaignRequest->id)
                    ->where('product_id', $product->id)
                    ->first();

                if ($campaignProduct) {
                    $campaignPrice = $campaignProduct->campaign_price;
                    $campaignQuantity = $campaignProduct->quantity;
                }
            }
        } else{
            $campaignProduct = CampaignRequestProduct::where('product_id', $product->id)
            ->whereHas('campaignRequest', function ($query) {
                $query->where('status', 1);
                $query->whereNull('supplier_id');
            })->first();

            if ($campaignProduct) {
                $campaignPrice = $campaignProduct->campaign_price;
                $campaignQuantity = $campaignProduct->quantity;
            }

        }

        // dd($campaignProduct);

        $product->watch = $product->watch + 1;
        $product->save();

        $company_name = CompanyDetails::value('company_name');
        $title = $company_name . ' - ' . $product->name;
        $currency = CompanyDetails::value('currency');

        return view('frontend.product.campaign_single_product', compact('product', 'campaignProduct', 'title', 'campaignPrice', 'currency', 'campaignQuantity'));
    }

    public function wholesaleProductDetails($slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        $title = $product->name;
        $currency = CompanyDetails::value('currency');
        $wholeSaleProduct = WholeSaleProduct::with('prices')
            ->where('product_id', $product->id)
            ->first();

        return view('frontend.product.wh_single_product', compact('product', 'title', 'currency', 'wholeSaleProduct'));
    }

    public function clearAllSessionData()
    {
        session()->flush();
        session()->regenerate();
        session(['session_clear' => true]);
        return redirect()->route('login');
    }

    public function storeReview(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'rating' => 'required|integer|between:1,5',
        ]);

        $review = new ProductReview();
        $review->user_id = auth()->id();
        $review->product_id = $request->product_id;
        $review->title = $request->title;
        $review->description = $request->description;
        $review->rating = $request->rating;
        $review->created_by = auth()->id();
        $review->save();
    
        return response()->json(['success' => true]);
    }

    public function getSizes(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'color' => 'required|string',
        ]);

        $productStocks = Stock::where('product_id', $request->product_id)
            ->where('color', $request->color)
            ->where('quantity', '>', 0)
            ->latest()
            ->select(['size', 'quantity', 'selling_price'])
            ->get();

        $sizes = $productStocks->pluck('size')->toArray();
        $maxQuantity = $productStocks->sum('quantity');
        $latestSellingPrice = optional($productStocks->first())->selling_price;

        return response()->json([
            'sizes' => $sizes,
            'max_quantity' => $maxQuantity,
            'selling_price' => $latestSellingPrice,
        ]);
    }

    public function getTypes(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'color' => 'required|string',
            'size' => 'required|string',
        ]);

        $productStocks = Stock::with('type')
            ->where('product_id', $request->product_id)
            ->where('color', $request->color)
            ->where('size', $request->size)
            ->where('quantity', '>', 0)
            ->get();

        $types = $productStocks->filter(fn($stock) => $stock->type)
            ->unique('type_id')
            ->map(function ($stock) {
                return [
                    'id' => $stock->type->id,
                    'name' => $stock->type->name,
                    'price' => $stock->selling_price,
                ];
            })->values();

        return response()->json([
            'types' => $types,
        ]);
    }

}
