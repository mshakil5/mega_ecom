<?php
  
use Illuminate\Support\Facades\Route;
  
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\SpecialOfferController;
use App\Http\Controllers\Admin\FlashSellController;
use App\Http\Controllers\Supplier\SupplierAuthController;
use App\Http\Controllers\Supplier\SupplierController;
use App\Http\Controllers\Supplier\StockController;
use App\Http\Controllers\Supplier\CampaignController;
  

// cache clear
Route::get('/clear', function() {
    Auth::logout();
    session()->flush();
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    return "Cleared!";
 });
//  cache clear
  
  
Auth::routes();

Route::get('login', function () {
    if (Auth::guard('supplier')->check()) {
        Auth::guard('supplier')->logout();
        return redirect()->route('login')->with('status', 'You were logged out from your supplier account to access the login page.');
    }
    return view('auth.login');
})->name('login');

Route::get('register', function () {
    if (Auth::guard('supplier')->check()) {
        Auth::guard('supplier')->logout();
        return redirect()->route('register')->with('status', 'You were logged out from your supplier account to access the registration page.');
    }
    return view('auth.register');
})->name('register');

Route::fallback(function () {
    return redirect('/');
});
  
  
// Frontend
Route::get('/', [FrontendController::class, 'index'])->name('frontend.homepage');
Route::get('/category/{slug}', [FrontendController::class, 'showCategoryProducts'])->name('category.show');
Route::get('/sub-category/{slug}', [FrontendController::class, 'showSubCategoryProducts'])->name('subcategory.show');
Route::get('/product/{slug}', [FrontendController::class, 'showProduct'])->name('product.show');
Route::get('/product/{slug}/{offerId?}', [FrontendController::class, 'showProduct'])->name('product.show');
Route::get('/sup-product/{slug}/{supplierId?}', [FrontendController::class, 'showSupplierProduct'])->name('product.show.supplier');
Route::get('/campaign/product/{slug}/{supplierId?}', [FrontendController::class, 'showCampaignProduct'])->name('product.show.campaign');

// Buy One Get One Single Product
Route::get('/bogo/{slug}', [FrontendController::class, 'bogoShowProduct'])->name('product.show.bogo');

// Bundle Details
Route::get('/bundle/{slug}', [FrontendController::class, 'bundleSingleProduct'])->name('bundle_product.show');

Route::get('/category-products', [FrontendController::class, 'getCategoryProducts'])->name('getCategoryProducts');

//Check Coupon
Route::get('/check-coupon', [FrontendController::class, 'checkCoupon']);


Route::get('/special-offers/{slug}', [SpecialOfferController::class, 'show'])->name('special-offers.show');

Route::get('flash-sells/{slug}', [FlashSellController::class, 'show'])->name('flash-sells.show');

Route::get('/campaign/{slug}', [FrontendController::class, 'showCampaignDetails'])->name('campaign.details.frontend');

Route::get('/shop', [FrontendController::class, 'shop'])->name('frontend.shop');

Route::get('/about-us', [FrontendController::class, 'aboutUs'])->name('frontend.shopdetail');

Route::get('/contact', [FrontendController::class, 'contact'])->name('frontend.contact');
Route::post('/contact-us', [FrontendController::class, 'storeContact'])->name('contact.store');

// Wish list
Route::put('/wishlist/store', [FrontendController::class, 'storeWishlist'])->name('wishlist.store');
Route::get('/wishlist', [FrontendController::class, 'showWishlist'])->name('wishlist.index');

// Search products
Route::get('/search/products', [FrontendController::class, 'search'])->name('search.products');

// Cart list
Route::put('/cart/store', [FrontendController::class, 'storeCart'])->name('cart.store');
Route::get('/cart', [FrontendController::class, 'showCart'])->name('cart.index');

Route::post('/checkout', [FrontendController::class, 'checkout'])->name('checkout.store');

Route::post('/place-order', [OrderController::class, 'placeOrder'])->name('place.order');

Route::get('payment/success', [OrderController::class, 'paymentSuccess'])->name('payment.success');
Route::get('payment/cancel', [OrderController::class, 'paymentCancel'])->name('payment.cancel');

Route::get('/order/{encoded_order_id}', [OrderController::class, 'generatePDF'])->name('generate-pdf');

Route::post('/products/filter', [FrontendController::class, 'filter']);

// Supplier shop
Route::get('/{slug}', [FrontendController::class, 'supplierPage'])->name('supplier.show');

// Search supplier products
Route::get('/search/supplier-products', [FrontendController::class, 'searchSupplierProducts'])->name('search.supplier.products');

Route::group(['prefix' =>'user/', 'middleware' => ['auth', 'is_user']], function(){
  
    Route::get('/dashboard', [HomeController::class, 'userHome'])->name('user.dashboard');

    Route::get('/profile', [UserController::class, 'userProfile'])->name('user.profile');

    Route::post('/update-profile', [UserController::class, 'updateProfile'])->name('user.profile.update');

    Route::get('/orders', [OrderController::class, 'getOrders'])->name('orders.index');

    Route::get('/orders/{orderId}/details', [OrderController::class, 'showOrderUser'])->name('orders.details');

    Route::post('{orderId}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    Route::get('/orders/details', [OrderController::class, 'getOrderDetailsModal'])->name('orders.details.modal');

    Route::post('/order-return', [OrderController::class, 'returnStore'])->name('orders.return');
});
  

Route::group(['prefix' =>'manager/', 'middleware' => ['auth', 'is_manager']], function(){
  
    Route::get('/dashboard', [HomeController::class, 'managerHome'])->name('manager.dashboard');
});

Route::get('/supplier/login', [SupplierAuthController::class, 'showLoginForm'])->name('supplier.login');
Route::post('/supplier/login', [SupplierAuthController::class, 'login'])->name('supplier.login');
Route::get('/supplier/register', [SupplierAuthController::class, 'showRegisterForm'])->name('supplier.register');
Route::post('/supplier/register', [SupplierAuthController::class, 'register'])->name('supplier.register');
Route::get('/email/verify/{token}', [SupplierAuthController::class, 'verify'])->name('supplier.verify');

Route::prefix('supplier')->middleware(['auth.supplier'])->group(function () {
    Route::get('dashboard', [SupplierController::class, 'dashboard'])->name('supplier.dashboard');
    Route::get('profile', [SupplierController::class, 'getSupplierProfile'])->name('supplier.profile');
    Route::post('profile', [SupplierController::class, 'updateSupplierProfile'])->name('supplier.profile');

    Route::get('sold-history', [SupplierController::class, 'productPurchaseHistory'])->name('productPurchaseHistory.supplier');
    Route::get('purchase/{purchase}/history', [SupplierController::class, 'getPurchaseHistory'])->name('purchase.history.supplier');

    Route::get('orders-history', [SupplierController::class, 'getSupplierOrders'])->name('order.supplier');
    Route::get('supplier-orders/{hashedOrderId}', [SupplierController::class, 'showOrderDetails'])->name('supplier.orders.details');
    Route::get('stocks', [StockController::class, 'index'])->name('stock.supplier');
    Route::post('stock', [StockController::class, 'store']);
    Route::get('stock/{id}/edit', [StockController::class, 'edit']);
    Route::post('stock-update', [StockController::class, 'update']);
    Route::get('stock/{id}', [StockController::class, 'delete']);

    Route::get('/order/{encoded_order_id}', [OrderController::class, 'generatePDFForSupplier'])->name('generate-pdf.supplier');

    Route::get('/supplier/transaction', [SupplierController::class, 'supplierTransaction'])->name('supplier.transaction');

    Route::get('/return-history', [SupplierController::class, 'stockReturnHistory'])->name('returnHistory.supplier');

    //Campaign Request
    Route::get('/campaign-request', [CampaignController::class, 'campaignRequest'])->name('supplier.campaignRequest');
    Route::post('/campaign-request', [CampaignController::class, 'campaignRequestStore'])->name('supplier.campaign.request.store');
    Route::get('/campaign-requests', [CampaignController::class, 'campaignRequests'])->name('supplier.campaignRequests');
    Route::get('/campaign-request/{id}', [CampaignController::class, 'getCampaignRequestDetails'])->name('campaign.request.details');


});

 