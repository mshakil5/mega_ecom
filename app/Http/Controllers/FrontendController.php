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

class FrontendController extends Controller
{
    public function index()
    {
        $currency = CompanyDetails::value('currency');
        $specialOffers = SpecialOffer::select('offer_image', 'offer_name', 'offer_title', 'slug')
            ->where('status', 1)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->latest()
            ->get();
        $flashSells = FlashSell::select('flash_sell_image', 'flash_sell_name', 'flash_sell_title', 'slug')
            ->where('status', 1)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->latest()
            ->get();

        $campaigns = Campaign::select('banner_image', 'title', 'slug')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->latest()
            ->get();
        $trendingProducts = Product::where('status', 1)
            ->where('is_trending', 1)
            ->orderByDesc('id')
            ->whereDoesntHave('specialOfferDetails')
            ->whereDoesntHave('flashSellDetails')
            ->with('stock')
            ->select('id', 'name', 'feature_image', 'slug', 'price')
            ->take(12)
            ->get();

        $recentProducts = Product::where('status', 1)
            ->where('is_recent', 1)
            ->orderByDesc('id')
            ->whereDoesntHave('specialOfferDetails')
            ->whereDoesntHave('flashSellDetails')
            ->with('stock')
            ->select('id', 'name', 'feature_image', 'price', 'slug')
            ->take(12)
            ->get();
        $buyOneGetOneProducts = BuyOneGetOne::where('status', 1)
            ->with(['product' => function($query) {
                $query->select('id', 'name', 'feature_image', 'price', 'slug');
            }])
            ->get()
            ->map(function($bogo) {
                $bogo->get_products_count = Product::whereIn('id', json_decode($bogo->get_product_ids))->count();
                return $bogo;
            });

        $bundleProducts = BundleProduct::select('id', 'name', 'feature_image', 'price', 'slug', 'product_ids')
            ->get()
            ->map(function($bundle) {
                $bundle->product_ids_count = json_decode($bundle->product_ids, true) ? count(json_decode($bundle->product_ids, true)) : 0;
                return $bundle;
            });

        $section_status = SectionStatus::first();
        $advertisements = Ad::where('status', 1)->select('type', 'link', 'image')->get();

        $suppliers = Supplier::where('status', 1)
                        ->orderBy('id', 'desc')
                        ->select('id', 'name', 'image', 'slug')
                        ->get();

        $sliders = Slider::orderBy('id', 'asc')
                 ->select('title', 'sub_title', 'image', 'link')
                 ->get();

        $categories = Category::where('status', 1)
            ->with(['products' => function ($query) {
                $query->select('id', 'category_id', 'name', 'price', 'slug', 'feature_image', 'watch')
                    ->orderBy('watch', 'desc');
            }])
            ->select('id', 'name', 'image', 'slug')
            ->orderBy('id', 'asc')
            ->get()
            ->each(function ($category) {
                $category->setRelation('products', $category->products->take(6));
            });

        $companyDesign = CompanyDetails::value('design');

        $wholeSaleProducts = Product::where('status', 1)
                        ->orderBy('id', 'desc')
                        ->has('prices')
                        ->get();

        if (in_array($companyDesign, ['2', '3', '4'])) {
            return view('frontend.index2', compact('specialOffers', 'flashSells', 'trendingProducts', 'currency', 'recentProducts', 'buyOneGetOneProducts', 'bundleProducts', 'section_status', 'advertisements', 'suppliers', 'sliders', 'categories', 'campaigns', 'wholeSaleProducts'));
        } elseif ($companyDesign == '5') {
            return view('frontend.index5', compact('specialOffers', 'flashSells', 'trendingProducts', 'currency', 'recentProducts', 'buyOneGetOneProducts', 'bundleProducts', 'section_status', 'advertisements', 'suppliers', 'sliders', 'categories', 'campaigns', 'wholeSaleProducts'));
        } else {
            return view('frontend.index', compact('specialOffers', 'flashSells', 'trendingProducts', 'currency', 'recentProducts', 'buyOneGetOneProducts', 'bundleProducts', 'section_status', 'advertisements', 'suppliers', 'sliders', 'categories', 'campaigns', 'wholeSaleProducts'));
        }

    }

    public function getCategoryProducts(Request $request)
    {
        $categoryId = $request->input('category_id');
        $page = $request->input('page', 1);
        $perPage = 6;

        $query = Product::where('category_id', $categoryId)
                        ->where('status', 1)
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
                            ->select('id', 'sub_category_id', 'name', 'feature_image', 'price', 'slug')
                            ->paginate(20);

        $company = CompanyDetails::select('company_name')->first();
        $title = $company->company_name . ' - ' . $sub_category->name;

        return view('frontend.sub_category_products', compact('sub_category', 'products', 'title', 'currency'));
    }

    public function showProduct($slug, $offerId = null)
    {
        $product = Product::where('slug', $slug)->with('colors.color')->firstOrFail();
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

        return view('frontend.product.single_product', compact('product', 'relatedProducts', 'title', 'regularPrice', 'offerPrice', 'flashSellPrice', 'offerId', 'currency', 'oldOfferPrice', 'OldFlashSellPrice'));
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

        return view('frontend.product.supplier_single_product', compact('product', 'title', 'regularPrice', 'currency', 'stockQuantity', 'stockDescription'));
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
        $products = Product::whereIn('id', $productIds)->get();

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
                        $product->price = $item['price'];
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
        $cartJson = $request->session()->get('cart', '[]');
        $cart = json_decode($cartJson, true);
        return view('frontend.cart', compact('cart'));
    }

    public function checkout(Request $request)
    {
        $cart = json_decode($request->input('cart'), true);
        return view('frontend.checkout', compact('cart'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $products = Product::where('name', 'LIKE', "%$query%")
                            ->where('status', 1)
                            ->orderBy('id', 'desc')
                            ->whereDoesntHave('specialOfferDetails')
                            ->whereDoesntHave('flashSellDetails')
                            ->take(15)
                            ->get();

        if ($products->isEmpty()) {
            return response()->json('<div class="p-2">No products found</div>');
        }

        $output = '<li class="dropdown">
                        <a class="sf-with-ul">Search Results</a>
                        <ul>';
        foreach ($products as $product) {
            $output .= '<li>
                            <a href="'.route('product.show', $product->slug).'">
                                '.$product->name.'
                            </a>
                        </li>';
        }
        $output .= '</ul>
                    </li>';
    
        return response()->json($output);
    }

    public function shop(Request $request)
    {
        $currency = CompanyDetails::value('currency');
        $categories = Category::where('status', 1)
            ->with('products')
            ->orderBy('id', 'desc')
            ->select('id', 'name')
            ->get();

        $brands = Brand::where('status', 1)
            ->with('products')
            ->orderBy('id', 'desc')
            ->select('id', 'name')
            ->get();

        $perPage = $request->input('per_page', 10);

        $minPrice = Product::where('status', 1)->min('price'); 
        $maxPrice = Product::where('status', 1)->max('price');

        $products = Product::where('status', 1)
            ->orderBy('id', 'desc')
            ->whereDoesntHave('specialOfferDetails')
            ->whereDoesntHave('flashSellDetails')
            ->with('stock')
            ->select('id', 'name', 'feature_image', 'price', 'slug')
            ->paginate($perPage);

        return view('frontend.shop', compact('currency', 'products', 'categories', 'brands', 'minPrice', 'maxPrice'));
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

        return back()->with('success', 'Your message has been sent successfully!');
    }

    public function aboutUs()
    {
        $companyDetails = CompanyDetails::select('about_us')->first();
        return view('frontend.about', compact('companyDetails'));
    }

    public function checkCoupon(Request $request)
    {
        $coupon = Coupon::where('coupon_name', $request->coupon_name)->first();

        if ($coupon && $coupon->status == 1) {
            return response()->json([
                'success' => true,
                'coupon_type' => $coupon->coupon_type,
                'coupon_value' => $coupon->coupon_value
            ]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function filter(Request $request)
    {
        $startPrice = $request->input('start_price');
        $endPrice = $request->input('end_price');
        $categoryId = $request->input('category');
        // $brandId = $request->input('brand');
        // $selectedSize = $request->input('size');
        $selectedColor = $request->input('color');

        $productsQuery = Product::select('products.id', 'products.name', 'products.price', 'products.slug', 'products.feature_image')
                                ->where('products.status', 1)
                                ->leftJoin('stocks', 'products.id', '=', 'stocks.product_id')
                                ->whereDoesntHave('specialOfferDetails')
                                ->whereDoesntHave('flashSellDetails')
                                ->orderByRaw('COALESCE(stocks.quantity, 0) DESC')  //treating NULL stock values as 0
                                ->with('stock');
    
        if ($startPrice !== null && $endPrice !== null) {
            $productsQuery->whereBetween('products.price', [$startPrice, $endPrice]);
        }
    
        if (!empty($categoryId)) {
            $productsQuery->where('products.category_id', $categoryId);
        }

        if (!empty($brandId)) {
            $productsQuery->where('products.brand_id', $brandId);
        }

        // if (!empty($selectedSize)) {
        //     $productsQuery->where('stocks.size', $selectedSize);
        // }

        // if (!empty($selectedColor)) {
        //     $productsQuery->where('stocks.color', $selectedColor);
        // }

        $products = $productsQuery->get();

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

}
