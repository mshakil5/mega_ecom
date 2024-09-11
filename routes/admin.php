<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\ProductModelController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\Admin\CompanyDetailsController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\Admin\ContactMailController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\AdController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\SpecialOfferController;
use App\Http\Controllers\Admin\FlashSellController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\InHouseSellController;
use App\Http\Controllers\Admin\DeliveryManController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\BundleProductController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\RelatedProductController;
use App\Http\Controllers\Admin\BuyOneGetOneController;
use App\Http\Controllers\Admin\PaymentGatewayController;
use App\Http\Controllers\Admin\MailContentController;
use App\Http\Controllers\Admin\CampaignController;


Route::group(['prefix' =>'admin/', 'middleware' => ['auth', 'is_admin']], function(){
  
    Route::get('/dashboard', [HomeController::class, 'adminHome'])->name('admin.dashboard');
    //profile
    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::put('profile/{id}', [AdminController::class, 'adminProfileUpdate']);
    Route::post('changepassword', [AdminController::class, 'changeAdminPassword']);
    Route::put('image/{id}', [AdminController::class, 'adminImageUpload']);

    //Admin crud
    Route::get('/new-admin', [AdminController::class, 'getAdmin'])->name('alladmin');
    Route::post('/new-admin', [AdminController::class, 'adminStore']);
    Route::get('/new-admin/{id}/edit', [AdminController::class, 'adminEdit']);
    Route::post('/new-admin-update', [AdminController::class, 'adminUpdate']);
    Route::get('/new-admin/{id}', [AdminController::class, 'adminDelete']);

    //Customer crud
    Route::get('/new-customer', [CustomerController::class, 'getCustomer'])->name('allcustomer');
    Route::post('/new-customer', [CustomerController::class, 'customerStore']);
    Route::get('/new-customer/{id}/edit', [CustomerController::class, 'customerEdit']);
    Route::post('/new-customer-update', [CustomerController::class, 'customerUpdate']);
    Route::get('/new-customer/{id}', [CustomerController::class, 'customerDelete']);


    //Contact Email crud
    Route::get('/contact-email', [ContactMailController::class, 'getContactEmail'])->name('allcontactemail');
    Route::post('/contact-email', [ContactMailController::class, 'contactEmailStore']);
    Route::get('/contact-email/{id}/edit', [ContactMailController::class, 'contactEmailEdit']);
    Route::post('/contact-email-update', [ContactMailController::class, 'contactEmailUpdate']);
    Route::get('/contact-email/{id}', [ContactMailController::class, 'contactEmailDelete']);

    Route::get('/section-status', [SectionController::class, 'sectionStatus'])->name('sectionstatus');
    Route::post('/section-status/update', [SectionController::class, 'updateSectionStatus'])->name('updateSectionStatus');

    Route::get('/contact-message', [ContactMessageController::class, 'getMessaege'])->name('allcontactmessae');


    // Brand crud
    Route::get('/brand', [BrandController::class, 'getBrand'])->name('allbrand');
    Route::post('/brand', [BrandController::class, 'brandStore']);
    Route::get('/brand/{id}/edit', [BrandController::class, 'brandEdit']);
    Route::post('/brand-update', [BrandController::class, 'brandUpdate']);
    Route::get('/brand/{id}', [BrandController::class, 'brandDelete']);

    Route::post('/brand-status', [BrandController::class, 'toggleStatus']);

    // Model crud
    Route::get('/model', [ProductModelController::class, 'getModel'])->name('allmodel');
    Route::post('/model', [ProductModelController::class, 'modelStore']);
    Route::get('/model/{id}/edit', [ProductModelController::class, 'modelEdit']);
    Route::post('/model-update', [ProductModelController::class, 'modelUpdate']);
    Route::get('/model/{id}', [ProductModelController::class, 'modelDelete']);

    // Unit crud
    Route::get('/unit', [UnitController::class, 'getUnit'])->name('allunit');
    Route::post('/unit', [UnitController::class, 'unitStore']);
    Route::get('/unit/{id}/edit', [UnitController::class, 'unitEdit']);
    Route::post('/unit-update', [UnitController::class, 'unitUpdate']);
    Route::get('/unit/{id}', [UnitController::class, 'unitDelete']);

    // Group crud
    Route::get('/group', [GroupController::class, 'getGroup'])->name('allgroup');
    Route::post('/group', [GroupController::class, 'groupStore']);
    Route::get('/group/{id}/edit', [GroupController::class, 'groupEdit']);
    Route::post('/group-update', [GroupController::class, 'groupUpdate']);
    Route::get('/group/{id}', [GroupController::class, 'groupDelete']);

    // company information
    Route::get('/company-details', [CompanyDetailsController::class, 'index'])->name('admin.companyDetail');
    Route::post('/company-details', [CompanyDetailsController::class, 'update'])->name('admin.companyDetails');

    // Category crud
    Route::get('/category', [CategoryController::class, 'getCategory'])->name('allcategory');
    Route::post('/category', [CategoryController::class, 'categoryStore']);
    Route::get('/category/{id}/edit', [CategoryController::class, 'categoryEdit']);
    Route::post('/category-update', [CategoryController::class, 'categoryUpdate']);
    Route::get('/category/{id}', [CategoryController::class, 'categoryDelete']);

    Route::post('/category-status', [CategoryController::class, 'toggleStatus']);

    // Sub-Category crud
    Route::get('/sub-category', [SubCategoryController::class, 'getSubCategory'])->name('allsubcategory');
    Route::post('/sub-category', [SubCategoryController::class, 'subCategoryStore']);
    Route::get('/sub-category/{id}/edit', [SubCategoryController::class, 'subCategoryEdit']);
    Route::post('/sub-category-update', [SubCategoryController::class, 'subCategoryUpdate']);
    Route::get('/sub-category/{id}', [SubCategoryController::class, 'subCategoryDelete']);

    Route::post('/sub-category-status', [SubCategoryController::class, 'toggleStatus']);

    // Product crud
    Route::get('/product', [ProductController::class, 'getProduct'])->name('allproduct');
    Route::post('/product', [ProductController::class, 'productStore']);
    Route::get('/product/{id}/edit', [ProductController::class, 'productEdit']);
    Route::post('/product-update', [ProductController::class, 'productUpdate']);
    Route::get('/product/{id}', [ProductController::class, 'productDelete']);
    Route::get('/product-details/{id}', [ProductController::class, 'showProductDetails'])->name('product.show.admin');

    Route::post('/toggle-featured', [ProductController::class, 'toggleFeatured'])->name('toggleFeatured');
    Route::post('/toggle-recent', [ProductController::class, 'toggleRecent'])->name('toggle-recent');
    Route::post('/toggle-popular', [ProductController::class, 'togglePopular'])->name('togglePopular');
    Route::post('/toggle-trending', [ProductController::class, 'toggleTrending'])->name('toggleTrending');


    // Slider crud
    Route::get('/slider', [SliderController::class, 'getSlider'])->name('allslider');
    Route::post('/slider', [SliderController::class, 'sliderStore']);
    Route::get('/slider/{id}/edit', [SliderController::class, 'sliderEdit']);
    Route::post('/slider-update', [SliderController::class, 'sliderUpdate']);
    Route::get('/slider/{id}', [SliderController::class, 'sliderDelete']);

    // Supplier crud
    Route::get('/supplier', [SupplierController::class, 'getSupplier'])->name('allsupplier');
    Route::post('/supplier', [SupplierController::class, 'supplierStore']);
    Route::get('/supplier/{id}/edit', [SupplierController::class, 'supplierEdit']);
    Route::post('/supplier-update', [SupplierController::class, 'supplierUpdate']);
    Route::get('/supplier/{id}', [SupplierController::class, 'supplierDelete']);

    Route::get('/supplier/transactions/{supplierId}', [SupplierController::class, 'supplierTransactions'])->name('supplier.transactions');

    Route::get('/suppliers/{supplierId}/orders', [SupplierController::class, 'showOrders'])->name('supplier.orders');

    Route::get('/supplier/stocks/{id}', [SupplierController::class, 'showStocks'])->name('supplier.stocks');
    Route::post('/approve-supplier-products', [SupplierController::class, 'approveItem'])->name('approve-item');

    Route::post('/toggle-supplier-status', [SupplierController::class, 'toggleStatus'])->name('supplier.toggleStatus');


    // Stock
    Route::get('/stocks', [StockController::class, 'getStocks'])->name('allstocks');
    Route::get('/stock', [StockController::class, 'getStock'])->name('allstock');
    Route::get('/add-stock', [StockController::class, 'addstock'])->name('addStock');

    Route::post('/process/system-loss', [StockController::class, 'processSystemLoss'])->name('process.system.loss');

    Route::get('/system-losses', [StockController::class, 'systemLosses'])->name('system-losses.index');

    Route::post('/add-stock', [StockController::class, 'stockStore']);

    Route::get('/product-purchase-history', [StockController::class, 'productPurchaseHistory'])->name('productPurchaseHistory');
    Route::get('/purchase/{purchase}/history', [StockController::class, 'getPurchaseHistory'])->name('purchase.history');
    
    Route::get('/stock-return-history', [StockController::class, 'stockReturnHistory'])->name('stockReturnHistory');

    Route::get('/purchase/edit/{purchase}', [StockController::class, 'editPurchaseHistory'])->name('purchase.edit');

    Route::get('/admin/purchase-return/{purchase}', [StockController::class, 'returnProduct'])->name('returnProduct');

    Route::post('/update-stock', [StockController::class, 'stockUpdate']);

    Route::post('/submit-return', [StockController::class, 'returnStore']);

    Route::post('/pay', [TransactionController::class,'pay'])->name('pay');

    Route::get('/ads', [AdController::class, 'getAds'])->name('alladds');
    Route::post('/ads', [AdController::class, 'adStore']);
    Route::get('/ads/{id}/edit', [AdController::class, 'adEdit']);
    Route::post('/ads-update', [AdController::class, 'adUpdate']);
    Route::get('/ads/{id}', [AdController::class, 'adDelete']);

    Route::post('/ads-status', [AdController::class, 'toggleStatus']);

    //Orders
    Route::get('/all-order', [OrderController::class, 'allOrder'])->name('allorder');
    Route::get('/all-orders', [OrderController::class, 'allOrders'])->name('allorders');
    Route::get('/pending-orders', [OrderController::class, 'pendingOrders'])->name('pendingorders');
    Route::get('/processing-orders', [OrderController::class, 'processingOrders'])->name('processingorders');
    Route::get('/packed-orders', [OrderController::class, 'packedOrders'])->name('packedorders');
    Route::get('/shipped-orders', [OrderController::class, 'shippedOrders'])->name('shippedorders');
    Route::get('/delivered-orders', [OrderController::class, 'deliveredOrders'])->name('deliveredorders');
    Route::get('/returned-orders', [OrderController::class, 'returnedOrders'])->name('returnedorders');
    Route::get('/cancelled-orders', [OrderController::class, 'cancelledOrders'])->name('cancelledorders');

    Route::post('/send-to-stock', [StockController::class, 'sendToStock'])->name('send.to.stock');
    Route::post('/send-to-systemlose', [StockController::class, 'sendToSystemLose'])->name('send.to.systemlose');

    Route::get('/orders/{orderId}/details', [OrderController::class, 'showOrder'])->name('admin.orders.details');

    Route::post('/orders/notify', [OrderController::class, 'markAsNotified'])->name('orders.notify');

    Route::post('/orders/update-status', [OrderController::class, 'updateStatus']);

    Route::post('/orders/update-delivery-man', [OrderController::class, 'updateDeliveryMan']);

    // Coupon crud
    Route::get('/coupon', [CouponController::class, 'getCoupon'])->name('allcoupon');
    Route::post('/coupon', [CouponController::class, 'couponStore']);
    Route::get('/coupon/{id}/edit', [CouponController::class, 'couponEdit']);
    Route::post('/coupon-update', [CouponController::class, 'couponUpdate']);
    Route::get('/coupon/{id}', [CouponController::class, 'couponDelete']);

    Route::post('/coupon-status', [CouponController::class, 'toggleStatus']);

    // Spceial Offer
    Route::get('/create-special-offer', [SpecialOfferController::class, 'createSpecialOffer'])->name('createspecialoffer');
    Route::post('/store-special-offer', [SpecialOfferController::class, 'specialOfferStore']); 
    Route::get('/special-offers', [SpecialOfferController::class, 'specialOffers'])->name('specialoffers');
    Route::get('/special-offer/{id}/details', [SpecialOfferController::class, 'getOfferDetails'])->name('special-offer.details');
    Route::get('/special-offers/{id}/edit', [SpecialOfferController::class, 'edit'])->name('special-offer.edit');
    Route::post('/update-offer', [SpecialOfferController::class, 'update'])->name('update.offer');
    Route::delete('/special-offer/{id}', [SpecialOfferController::class, 'destroy'])->name('special-offer.delete');

    // Flash Sells
    Route::get('/create-flash-sell', [FlashSellController::class, 'createFlashSell'])->name('createflashsell');
    Route::post('/store-flash-sell', [FlashSellController::class, 'flashSellStore']); 
    Route::get('/flash-sells', [FlashSellController::class, 'flashSells'])->name('flashsells');
    Route::get('/flash-sell/{id}/details', [FlashSellController::class, 'getFlashSellDetails'])->name('flash-sell.details');
    Route::get('/flash-sell/{id}/edit', [FlashSellController::class, 'edit'])->name('flash-sell.edit');
    Route::post('/update-flash-sell', [FlashSellController::class, 'update'])->name('flash-sell.update');
    Route::delete('/flash-sell/{id}', [FlashSellController::class, 'destroy'])->name('flash-sell.delete');

    //Campaign
    Route::get('/campaigns', [CampaignController::class, 'getCampaigns'])->name('allcampaign');
    Route::post('/campaign', [CampaignController::class, 'campaignStore'])->name('campaign.store');
    Route::get('/campaign/{id}/edit', [CampaignController::class, 'campaignEdit']);
    Route::post('/campaign-update', [CampaignController::class, 'campaignUpdate']);
    Route::get('/campaign/{id}', [CampaignController::class, 'campaignDelete']);

    Route::get('/campaign-requests', [CampaignController::class, 'getCampaignRequests'])->name('allcampaignRequests');
    Route::get('/campaign-request/{id}', [CampaignController::class, 'getCampaignRequestDetails'])->name('campaign.request.details.admin');
    Route::post('/campaign-request/status/update', [CampaignController::class, 'updateStatus'])->name('campaign.request.status.update');
    Route::post('/campaign-request', [CampaignController::class, 'campaignRequestStore'])->name('admin.campaign.request.store');
    Route::get('/campaign/{id}/details', [CampaignController::class, 'showDetails'])->name('campaign.details');

    //In House sell
    Route::get('/in-house-sell', [InHouseSellController::class, 'inHouseSell'])->name('inhousesell');
    Route::post('/in-house-sell', [InHouseSellController::class, 'inHouseSellStore'])->name('inhousesell');
    Route::get('/in-house-order', [InHouseSellController::class, 'index'])->name('inhouseorders');
    Route::get('/in-house-sell/order/{encoded_order_id}', [InHouseSellController::class, 'generatePDF'])->name('in-house-sell.generate-pdf');

    //Delivery Man crud
    Route::get('/deliveryman', [DeliveryManController::class, 'getDeliveryMan'])->name('alldeliverymen');
    Route::post('/deliveryman', [DeliveryManController::class, 'deliveryManStore']);
    Route::get('/deliveryman/{id}/edit', [DeliveryManController::class, 'deliveryManEdit']);
    Route::post('/deliveryman-update', [DeliveryManController::class, 'deliveryManUpdate']);
    Route::get('/deliveryman/{id}', [DeliveryManController::class, 'deliveryManDelete']);

    //Reports 
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');

    Route::get('daily-sale', [ReportController::class, 'dailySale'])->name('reports.dailySale');
    Route::get('daily-sales', [ReportController::class, 'dailySalesDataTable'])->name('reports.dailySalesDataTable');

    Route::get('weekly-sale', [ReportController::class, 'weeklySale'])->name('reports.weeklySale');
    Route::get('weekly-sales', [ReportController::class, 'weeklySalesDataTable'])->name('reports.weeklySalesDataTable');

    Route::get('monthly-sale', [ReportController::class, 'monthlySale'])->name('reports.monthlySale');
    Route::get('monthly-sales', [ReportController::class, 'monthlySalesDataTable'])->name('reports.monthlySalesDataTable');

    Route::get('date-to-date-sale', [ReportController::class, 'dateToDateSale'])->name('reports.dateToDateSale');
    Route::get('date-to-date-sales-data', [ReportController::class, 'dateToDateSalesDataTable'])->name('reports.dateToDateSalesDataTable');

    Route::get('daily-purchase', [ReportController::class, 'dailyPurchase'])->name('reports.dailyPurchase');
    Route::get('daily-purchases', [ReportController::class, 'dailyPurchasesDataTable'])->name('reports.dailyPurchasesDataTable');

    Route::get('weekly-purchase', [ReportController::class, 'weeklyPurchase'])->name('reports.weeklyPurchase');
    Route::get('weekly-purchases', [ReportController::class, 'weeklyPurchasesDataTable'])->name('reports.weeklyPurchasesDataTable');

    Route::get('monthly-purchase', [ReportController::class, 'monthlyPurchase'])->name('reports.monthlyPurchase');
    Route::get('monthly-purchases', [ReportController::class, 'monthlyPurchasesDataTable'])->name('reports.monthlyPurchasesDataTable');

    Route::get('date-to-date-purchase', [ReportController::class, 'dateToDatePurchase'])->name('reports.dateToDatePurchase');
    Route::get('date-to-date-purchases-date', [ReportController::class, 'dateToDatePurchasesDataTable'])->name('reports.dateToDatePurchasesDataTable');

    //Related Product
    Route::get('/related-product', [RelatedProductController::class, 'getrelatedProduct'])->name('allrelatedproduct');
    Route::post('/related-product', [RelatedProductController::class, 'relatedProductStore'])->name('relatedproduct.store');
    Route::get('/related-product/{id}/edit', [RelatedProductController::class, 'relatedProductEdit']);
    Route::post('/related-product-update', [RelatedProductController::class, 'relatedProductUpdate']);
    Route::get('/related-product/{id}', [RelatedProductController::class, 'relatedProductDelete']);

    //Buy One Get One Product
    Route::get('/bogo-product', [BuyOneGetOneController::class, 'get'])->name('allbogoproduct');
    Route::post('/bogo-product', [BuyOneGetOneController::class, 'store'])->name('bogoproduct.store');
    Route::get('/bogo-product/{id}/edit', [BuyOneGetOneController::class, 'edit']);
    Route::post('/bogo-product-update', [BuyOneGetOneController::class, 'update']);
    Route::get('/bogo-product/{id}', [BuyOneGetOneController::class, 'delete']);

    //Bundle Product
    Route::get('/bundle-product', [BundleProductController::class, 'getBundleProduct'])->name('allbundleproduct');
    Route::post('/bundle-product', [BundleProductController::class, 'bundleProductStore'])->name('bundleproduct.store');
    Route::get('/bundle-product/{id}/edit', [BundleProductController::class, 'bundleProductEdit']);
    Route::post('/bundle-product-update', [BundleProductController::class, 'bundleProductUpdate']);
    Route::get('/bundle-product/{id}', [BundleProductController::class, 'bundleProductDelete']);

    // roles and permission
    Route::get('role', [RoleController::class, 'index'])->name('admin.role');
    Route::post('role', [RoleController::class, 'store'])->name('admin.rolestore');
    Route::get('role/{id}', [RoleController::class, 'edit'])->name('admin.roleedit');
    Route::post('role-update', [RoleController::class, 'update'])->name('admin.roleupdate');

    // Payment gateway crud
    Route::get('/payment-gateway', [PaymentGatewayController::class, 'index'])->name('allpaymentgateways');
    Route::post('/payment-gateway', [PaymentGatewayController::class, 'store']);
    Route::get('/payment-gateway/{id}/edit', [PaymentGatewayController::class, 'edit']);
    Route::post('/payment-gateway-update', [PaymentGatewayController::class, 'update']);

    // mail content
    Route::get('/mail-content', [MailContentController::class, 'index'])->name('admin.mail-content');
    Route::post('/mail-content', [MailContentController::class, 'store']);
    Route::get('/mail-content/{id}/edit', [MailContentController::class, 'edit']);
    Route::post('/mail-content-update', [MailContentController::class, 'update']);

});
  