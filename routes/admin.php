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
use App\Http\Controllers\Admin\ChartOfAccountController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\IncomeController;
use App\Http\Controllers\Admin\LiabilityController;
use App\Http\Controllers\Admin\EquityController;
use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\EquityHolderController;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Admin\SizeController;
use App\Http\Controllers\Admin\WholeSaleProductController;
use App\Http\Controllers\Admin\IncomeStatementController;
use App\Http\Controllers\Admin\FinancialStatementController;
use App\Http\Controllers\Admin\LedgerController;
use App\Http\Controllers\Admin\WareHouseController;
use App\Http\Controllers\Admin\CashFlowController;
use App\Http\Controllers\Admin\DaybookController;
use App\Http\Controllers\Admin\DeliveryChargeController;
use App\Http\Controllers\Admin\ShippingController;
use App\Http\Controllers\Admin\ShipmentController;
use App\Http\Controllers\Admin\StockTransferRequestController;
use App\Http\Controllers\Admin\FAQController;

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

    //WholeSaler crud
    Route::get('/whole-saler', [CustomerController::class, 'getCustomer'])->name('allcustomer');
    Route::post('/whole-saler', [CustomerController::class, 'customerStore']);
    Route::get('/whole-saler/{id}/edit', [CustomerController::class, 'customerEdit']);
    Route::post('/whole-saler-update', [CustomerController::class, 'customerUpdate']);
    Route::get('/whole-saler/{id}', [CustomerController::class, 'customerDelete']);
    Route::post('/toggle-customer-status', [CustomerController::class, 'toggleStatus']);

    Route::post('/customer-store', [CustomerController::class, 'store'])->name('customer.store');
    Route::post('/customer-pay', [CustomerController::class,'pay']);    
    Route::get('/whole-saler/transactions/{wholeSalerId}', [CustomerController::class, 'customerTransactions'])->name('customer.transactions');
    Route::get('/whole-saler-email/{id}', [CustomerController::class, 'customerEmail'])->name('customer.email');
    Route::post('/customer/{id}/email/send', [CustomerController::class, 'sendCustomerEmail'])->name('customer.email.send');

    Route::post('/whole-saler/transactions/update', [CustomerController::class, 'updateTransaction'])->name('whole-saler.transactions.update');

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

    Route::post('/brands/store', [BrandController::class, 'store'])->name('brand.store');

    // Model crud
    Route::get('/model', [ProductModelController::class, 'getModel'])->name('allmodel');
    Route::post('/model', [ProductModelController::class, 'modelStore']);
    Route::get('/model/{id}/edit', [ProductModelController::class, 'modelEdit']);
    Route::post('/model-update', [ProductModelController::class, 'modelUpdate']);
    Route::get('/model/{id}', [ProductModelController::class, 'modelDelete']);

    Route::post('/product-models/store', [ProductModelController::class, 'store'])->name('product-model.store');

    // Unit crud
    Route::get('/unit', [UnitController::class, 'getUnit'])->name('allunit');
    Route::post('/unit', [UnitController::class, 'unitStore']);
    Route::get('/unit/{id}/edit', [UnitController::class, 'unitEdit']);
    Route::post('/unit-update', [UnitController::class, 'unitUpdate']);
    Route::get('/unit/{id}', [UnitController::class, 'unitDelete']);

    Route::post('/units/store', [UnitController::class, 'store'])->name('unit.store');

    // Group crud
    Route::get('/group', [GroupController::class, 'getGroup'])->name('allgroup');
    Route::post('/group', [GroupController::class, 'groupStore']);
    Route::get('/group/{id}/edit', [GroupController::class, 'groupEdit']);
    Route::post('/group-update', [GroupController::class, 'groupUpdate']);
    Route::get('/group/{id}', [GroupController::class, 'groupDelete']);

    Route::post('/groups/store', [GroupController::class, 'store'])->name('group.store');

    // Color crud
    Route::get('/color', [ColorController::class, 'index'])->name('allcolor');
    Route::post('/color', [ColorController::class, 'store']);
    Route::get('/color/{id}/edit', [ColorController::class, 'edit']);
    Route::post('/color-update', [ColorController::class, 'update']);
    Route::get('/color/{id}', [ColorController::class, 'delete']);

    Route::post('/color-status', [ColorController::class, 'toggleStatus']);

    Route::post('/color/store', [ColorController::class, 'storeColor'])->name('color.store');

    // Size crud
    Route::get('/size', [SizeController::class, 'index'])->name('allsize');
    Route::post('/size', [SizeController::class, 'store']);
    Route::get('/size/{id}/edit', [SizeController::class, 'edit']);
    Route::post('/size-update', [SizeController::class, 'update']);
    Route::get('/size/{id}', [SizeController::class, 'delete']);

    Route::post('/size-status', [SizeController::class, 'toggleStatus']);

    Route::post('/size/store', [SizeController::class, 'storeSize'])->name('size.store');

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

    Route::post('/categories/store', [CategoryController::class, 'store'])->name('category.store');

    // Sub-Category crud
    Route::get('/sub-category', [SubCategoryController::class, 'getSubCategory'])->name('allsubcategory');
    Route::post('/sub-category', [SubCategoryController::class, 'subCategoryStore']);
    Route::get('/sub-category/{id}/edit', [SubCategoryController::class, 'subCategoryEdit']);
    Route::post('/sub-category-update', [SubCategoryController::class, 'subCategoryUpdate']);
    Route::get('/sub-category/{id}', [SubCategoryController::class, 'subCategoryDelete']);

    Route::post('/sub-category-status', [SubCategoryController::class, 'toggleStatus']);

    Route::post('/sub-categories/store', [SubCategoryController::class, 'store'])->name('subcategory.store');

    // Product crud
    Route::get('/product', [ProductController::class, 'getProduct'])->name('allproduct');
    Route::get('/create-product', [ProductController::class, 'createProduct'])->name('createproduct');
    Route::post('/product', [ProductController::class, 'store'])->name('store.product');
    Route::get('/product/{id}/edit', [ProductController::class, 'productEdit'])->name('product.edit');
    Route::post('/product-update', [ProductController::class, 'update'])->name('update.product');
    Route::delete('/product', [ProductController::class, 'productDelete']);
    Route::get('/product-details/{id}', [ProductController::class, 'showProductDetails'])->name('product.show.admin');

    Route::get('/products/{product}/reviews', [ProductController::class, 'productReviews'])->name('product.reviews.show');

    Route::post('/review-status', [ProductController::class, 'changeReviewStatus'])->name('admin.review.status');


    Route::get('/check-product-code', [ProductController::class, 'checkProductCode'])->name('check.product.code');

    Route::post('/toggle-featured', [ProductController::class, 'toggleFeatured'])->name('toggleFeatured');
    Route::post('/toggle-recent', [ProductController::class, 'toggleRecent'])->name('toggle-recent');
    Route::post('/toggle-popular', [ProductController::class, 'togglePopular'])->name('togglePopular');
    Route::post('/toggle-trending', [ProductController::class, 'toggleTrending'])->name('toggleTrending');

    Route::get('product/{product}/prices', [ProductController::class, 'showProductPrices'])->name('product.prices.show');
    Route::post('/product-price', [ProductController::class, 'storePrice'])->name('product-price.store');
    Route::get('/product-price/{id}/edit', [ProductController::class, 'priceEdit']);
    Route::post('/product-price-update', [ProductController::class, 'updatePrice'])->name('product-price.update');
    Route::post('/product-price-status', [ProductController::class, 'updatePriceStatus'])->name('product-price.update-status');
    Route::get('/product-price/{id}', [ProductController::class, 'deletePrice']);

    // Slider crud
    Route::get('/slider', [SliderController::class, 'getSlider'])->name('allslider');
    Route::post('/slider', [SliderController::class, 'sliderStore']);
    Route::get('/slider/{id}/edit', [SliderController::class, 'sliderEdit']);
    Route::post('/slider-update', [SliderController::class, 'sliderUpdate']);
    Route::get('/slider/{id}', [SliderController::class, 'sliderDelete']);

    Route::post('/slider-status', [SliderController::class, 'toggleStatus']);

    // Supplier crud
    Route::get('/supplier', [SupplierController::class, 'getSupplier'])->name('allsupplier');
    Route::post('/supplier', [SupplierController::class, 'supplierStore']);
    Route::get('/supplier/{id}/edit', [SupplierController::class, 'supplierEdit']);
    Route::post('/supplier-update', [SupplierController::class, 'supplierUpdate']);
    Route::get('/supplier/{id}', [SupplierController::class, 'supplierDelete']);

    Route::post('/supplier/store', [SupplierController::class, 'store'])->name('supplier.store');

    Route::get('/supplier/transactions/{supplierId}', [SupplierController::class, 'supplierTransactions'])->name('supplier.transactions');

    Route::get('/suppliers/{supplierId}/orders', [SupplierController::class, 'showOrders'])->name('supplier.orders');
    Route::get('/suppliers/{supplierId}/purchase', [SupplierController::class, 'showPurchase'])->name('supplier.purchase');

    Route::get('/supplier/stocks/{id}', [SupplierController::class, 'showStocks'])->name('supplier.stocks');
    Route::post('/approve-supplier-products', [SupplierController::class, 'approveItem'])->name('approve-item');

    Route::post('/toggle-supplier-status', [SupplierController::class, 'toggleStatus'])->name('supplier.toggleStatus');

    Route::post('/supplier/transactions/update', [SupplierController::class, 'updateTransaction'])->name('supplier.transactions.update');

    Route::get('/supplier-email/{id}', [SupplierController::class, 'supplierEmail'])->name('supplier.email');
    Route::post('/supplier/{id}/email/send', [SupplierController::class, 'sendSupplierEmail'])->name('supplier.email.send');

    // Stock
    Route::get('/stocks', [StockController::class, 'getStocks'])->name('allstocks');
    Route::get('/stock', [StockController::class, 'getStock'])->name('allstock');
    Route::get('/stocking-history', [StockController::class, 'stockingHistory'])->name('stockhistory');
    Route::get('/get-stocking-history', [StockController::class, 'getStockingHistory'])->name('getstockhistory');

    Route::post('/get-warehouses', [StockController::class, 'getWarehouses']);
    Route::post('/get-colors', [StockController::class, 'getColors']);
    Route::post('/get-sizes', [StockController::class, 'getSizes']);
    Route::post('/get-max-quantity', [StockController::class, 'getMaxQuantity']);

    Route::get('/stock-transfer-requests', [StockTransferRequestController::class, 'index'])->name('stock-transfer-requests.index');
    
    Route::post('/stock-transfer-requests', [StockTransferRequestController::class, 'storeTransferRequest']);

    Route::put('/stock-transfer-requests/{id}', [StockTransferRequestController::class, 'update'])->name('stock-transfer-requests.update');

    Route::post('/stock-transfer-requests/reject/{id}', [StockTransferRequestController::class, 'reject'])->name('stock-transfer-requests.reject');
    Route::post('/stock-transfer-requests/accept/{id}', [StockTransferRequestController::class, 'accept'])->name('stock-transfer-requests.accept');
    
    // stock ledger
    Route::get('/stock-ledger', [StockController::class, 'getStockLedger'])->name('stockLedger');
    Route::get('/product-stock-history', [StockController::class, 'getproductHistory'])->name('productHistory');
    // stock ledger end
    Route::get('/add-stock', [StockController::class, 'addstock'])->name('addStock');
    Route::get('/product-history/{id}/{size?}/{color?}/{warehouse_id?}', [StockController::class, 'getsingleProductHistory'])->name('admin.product.purchasehistory');
    Route::post('/product-history/{id}/{size?}/{color?}/{warehouse_id?}', [StockController::class, 'getsingleProductHistory'])->name('admin.product.purchasehistorysearch');    
    Route::post('/process/system-loss', [StockController::class, 'processSystemLoss'])->name('process.system.loss');
    Route::get('/system-losses', [StockController::class, 'systemLosses'])->name('system-losses.index');
    Route::post('/add-stock', [StockController::class, 'stockStore']);

    Route::post('/check-invoice', [StockController::class, 'checkInvoice'])->name('admin.check.invoice');

    Route::get('/shipping', [ShippingController::class, 'shipping'])->name('admin.shipping');

    Route::post('/search-purchases', [ShippingController::class, 'searchPurchases']);

    Route::post('/create-shipment', [ShippingController::class, 'storeShipment']);

    Route::post('/check-shipping-id', [ShippingController::class, 'checkShippingId'])->name('check.shipping.id');

    Route::put('/update-shipment/{id}', [ShippingController::class, 'updateShipment'])->name('update.shipment');

    Route::post('/shipping/update-status', [ShippingController::class, 'updateStatus'])->name('admin.shipments.updateStatus');

    Route::get('/shipment-history/create/{id}', [ShipmentController::class, 'createShipment'])->name('admin.shipment.create');

    Route::post('/shipment-store', [ShipmentController::class, 'storeShipment']);

    Route::get('/shipment-history/edit/{id}', [ShipmentController::class, 'editShipment'])->name('admin.shipment.edit');

    Route::get('/shipment/print/{id}', [ShipmentController::class, 'printShipment'])->name('admin.shipment.print');

    Route::put('/shipment-update/{id}', [ShipmentController::class, 'updateShipment'])->name('admin.shipment.update');

    Route::get('/shipment/sample-products', [ShipmentController::class, 'showSampleProducts'])->name('shipment.sample_products');
    
    Route::get('/missing-purchase-product/{id}', [StockController::class, 'missingProduct'])->name('missingProduct');
    Route::get('/transfer-to-warehouse/{id}', [WarehouseController::class, 'transfer'])->name('transferToWarehouse');
    Route::get('/purchase-history', [StockController::class, 'productPurchaseHistory'])->name('productPurchaseHistory');
    Route::get('/new-purchase-history', [StockController::class, 'newPurchaseHistory'])->name('admin.newPurchaseHistory');
    Route::get('/live-purchase-history', [StockController::class, 'livePurchaseHistory'])->name('admin.livePurchaseHistory');
    Route::get('/completed-purchase-history', [StockController::class, 'completedPurchaseHistory'])->name('admin.completedPurchaseHistory');
    Route::get('/purchase/{purchase}/history', [StockController::class, 'getPurchaseHistory'])->name('purchase.history');
    Route::get('/purchase-return-history', [StockController::class, 'stockReturnHistory'])->name('stockReturnHistory');

    Route::post('/due-pay', [TransactionController::class,'duePay'])->name('due.pay');

    Route::get('/purchase/edit/{purchase}', [StockController::class, 'editPurchaseHistory'])->name('purchase.edit');

    Route::get('/admin/purchase-return/{purchase}', [StockController::class, 'returnProduct'])->name('returnProduct');

    Route::post('/purchases/update-status', [StockController::class, 'updateStatus'])->name('purchases.updateStatus');

    Route::post('/update-stock', [StockController::class, 'stockUpdate']);
    Route::post('/submit-return', [StockController::class, 'returnStore']);
    Route::post('/supplier-pay', [TransactionController::class,'pay'])->name('pay');

    Route::get('/ads', [AdController::class, 'getAds'])->name('alladds');
    Route::post('/ads', [AdController::class, 'adStore']);
    Route::get('/ads/{id}/edit', [AdController::class, 'adEdit']);
    Route::post('/ads-update', [AdController::class, 'adUpdate']);
    Route::get('/ads/{id}', [AdController::class, 'adDelete']);

    Route::post('/ads-status', [AdController::class, 'toggleStatus']);

    //Orders
    Route::get('/all-orders/{userId?}', [OrderController::class, 'getAllOrder'])->name('getallorder');
    Route::get('/coupon-all-orders/{couponId}', [OrderController::class, 'getAllOrderByCoupon'])->name('getallorderbycoupon');
    Route::get('/all-inhouse-orders/{userId?}', [OrderController::class, 'getInHouseOrder'])->name('getinhouseorder');
    Route::get('/pending-orders', [OrderController::class, 'pendingOrders'])->name('pendingorders');
    Route::get('/processing-orders', [OrderController::class, 'processingOrders'])->name('processingorders');
    Route::get('/packed-orders', [OrderController::class, 'packedOrders'])->name('packedorders');
    Route::get('/shipped-orders', [OrderController::class, 'shippedOrders'])->name('shippedorders');
    Route::get('/delivered-orders', [OrderController::class, 'deliveredOrders'])->name('deliveredorders');
    Route::get('/returned-orders', [OrderController::class, 'returnedOrders'])->name('returnedorders');
    Route::get('/cancelled-orders', [OrderController::class, 'cancelledOrders'])->name('cancelledorders');

    Route::get('/order-due/{user_id}', [OrderController::class, 'orderDueList'])->name('admin.due.list');
    Route::post('/payments/store', [CustomerController::class, 'orderDuePayment'])->name('due.payment.store');
    Route::post('/customer-order-pay', [CustomerController::class,'orderDuePayment']);   

    Route::get('/due-orders', [CustomerController::class, 'allDueOrders'])->name('admin.all.due.list');

    Route::post('/send-to-stock', [StockController::class, 'sendToStock'])->name('send.to.stock');
    Route::post('/send-to-systemlose', [StockController::class, 'sendToSystemLose'])->name('send.to.systemlose');

    Route::get('/orders/{orderId}/details', [OrderController::class, 'showOrder'])->name('admin.orders.details');

    Route::post('/orders/notify', [OrderController::class, 'markAsNotified'])->name('orders.notify');

    Route::post('/orders/update-status', [OrderController::class, 'updateStatus']);

    Route::post('/orders/update-delivery-man', [OrderController::class, 'updateDeliveryMan']);

    Route::post('/assign-warehouse', [OrderController::class, 'assignWarehouse'])->name('assign.warehouse');


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
    Route::post('/in-house-quotation-order', [InHouseSellController::class, 'inHouseQuotationSellStore'])->name('quotationToOrder');
    Route::get('/in-house-sell/order/{encoded_order_id}', [InHouseSellController::class, 'generatePDF'])->name('in-house-sell.generate-pdf');

    Route::get('/in-house-sell/delivery-note/{encoded_order_id}', [InHouseSellController::class, 'generateDeliveryNote'])->name('in-house-sell.generate-delivery-note');

    Route::get('order/{encoded_order_id}/download-pdf', [InHouseSellController::class, 'generateDownloadPDF'])->name('orders.download-pdf');

    Route::post('/send-email/{order}', [InHouseSellController::class, 'sendQuotationEmail'])->name('orders.send-email');
    Route::get('/quotation-send-email/{order}', [InHouseSellController::class, 'quotationEmailForm'])->name('quotation.emailform');

    Route::get('/order-edit/{orderId}', [InHouseSellController::class, 'editOrder'])->name('order-edit');

    Route::post('/order-update', [InHouseSellController::class, 'updateOrder']);

    Route::post('/check-product-stock', [InHouseSellController::class, 'checkStock']);
    Route::post('/get-product-stock', [InHouseSellController::class, 'getStock']);

    //Quotation
    Route::post('/make-quotation', [InHouseSellController::class, 'makeQuotationStore'])->name('make.quotation.store');
    Route::get('/quotations', [InHouseSellController::class, 'allquotations'])->name('allquotations');

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

    Route::get('shipments/{type}', [ReportController::class, 'shipments'])->name('reports.shipments');
    Route::get('date-to-date-shipments', [ReportController::class, 'dateToDateShipments'])->name('reports.dateToDateShipments');
    Route::post('date-to-date-shipments', [ReportController::class, 'dateToDateShipments'])->name('reports.filterDateToDateShipments');

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

    //Whole Sale Product
    Route::get('/whole-sale-product', [WholeSaleProductController::class, 'getWholeSaleProduct'])->name('allwholesaleproduct');
    Route::post('/whole-sale-product', [WholeSaleProductController::class, 'wholeSaleProductStore'])->name('whole_sale_product.store');
    Route::delete('/whole-sale-product/{id}', [WholeSaleProductController::class, 'destroyWholeSaleProduct'])->name('whole_sale_product.destroy');

    Route::get('/whole-sale-product/prices', [WholeSaleProductController::class, 'getWholeSalePrices']);
    Route::post('/whole-sale-product/prices', [WholeSaleProductController::class, 'saveWholeSalePrices']);

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

    // warehouse crud
    Route::get('/warehouse', [WareHouseController::class, 'index'])->name('allwarehouse');
    Route::post('/warehouse', [WareHouseController::class, 'store']);
    Route::get('/warehouse/{id}/edit', [WareHouseController::class, 'edit']);
    Route::post('/warehouse-update', [WareHouseController::class, 'update']);
    Route::get('/warehouse/{id}', [WareHouseController::class, 'delete']);
    Route::post('/warehouse-status', [WareHouseController::class, 'toggleStatus']);

    Route::post('/warehouse-store', [WareHouseController::class, 'storeWarehouse'])->name('warehouse.store');

    // warehouse crud
    Route::get('/delivery-charge', [DeliveryChargeController::class, 'index'])->name('alldeliverycharge');
    Route::post('/delivery-charge', [DeliveryChargeController::class, 'store']);
    Route::get('/delivery-charge/{id}/edit', [DeliveryChargeController::class, 'edit']);
    Route::post('/delivery-charge-update', [DeliveryChargeController::class, 'update']);
    Route::get('/delivery-charge/{id}', [DeliveryChargeController::class, 'delete']);

    Route::post('/transfer-to-warehouse/{id}', [WarehouseController::class, 'transferToWarehouse'])->name('transferToWarehouse');
    Route::post('/missing-purchase-product/{id}', [StockController::class, 'missingPurchaseProduct'])->name('missingPurchaseProduct');

    // mail content
    Route::get('/mail-content', [MailContentController::class, 'index'])->name('admin.mail-content');
    Route::post('/mail-content', [MailContentController::class, 'store']);
    Route::get('/mail-content/{id}/edit', [MailContentController::class, 'edit']);
    Route::post('/mail-content-update', [MailContentController::class, 'update']);

    //Toggle sidebar
    Route::get('/toggle-sidebar', [HomeController::class, 'toggleSidebar'])->name('toggle.sidebar');

    //Chart of account
    Route::get('chart-of-account', [ChartOfAccountController::class, 'index'])->name('admin.addchartofaccount');
    Route::post('chart-of-accounts', [ChartOfAccountController::class, 'index'])->name('admin.addchartofaccount.filter');
    Route::post('chart-of-account', [ChartOfAccountController::class, 'store']);
    Route::get('chart-of-account/{id}', [ChartOfAccountController::class, 'edit']);
    Route::put('chart-of-account/{id}', [ChartOfAccountController::class, 'update']);
    Route::get('chart-of-account/{id}/change-status', [ChartOfAccountController::class, 'changeStatus']);

    //Branch
    Route::get('/branch', [BranchController::class, 'view_branch'])->name('view_branch');
    Route::get('/branch-all', [BranchController::class, 'get_all_branch']);
    Route::post('/branch', [BranchController::class, 'save_branch']);
    Route::get('/published-branch/{id}', [BranchController::class, 'published_branch']);
    Route::get('/unpublished-branch/{id}', [BranchController::class, 'unpublished_branch']);
    Route::post('/edit-branch/{id}', [BranchController::class, 'edit_branch']);

    //Income
    Route::get('income', [IncomeController::class, 'index'])->name('admin.income');
    Route::post('incomes', [IncomeController::class, 'index'])->name('admin.income.filter');
    Route::post('income', [IncomeController::class, 'store']);
    Route::get('income/{id}', [IncomeController::class, 'edit']);
    Route::put('income/{id}', [IncomeController::class, 'update']); 

    //Liability
    Route::get('liabilities', [LiabilityController::class, 'index'])->name('admin.liabilities');
    Route::post('liability', [LiabilityController::class, 'index'])->name('admin.liability.filter');
    Route::post('liabilities', [LiabilityController::class, 'store']);
    Route::get('liabilities/{id}', [LiabilityController::class, 'edit']);
    Route::put('liabilities/{id}', [LiabilityController::class, 'update']);

    //Equity
    Route::get('equity', [EquityController::class, 'index'])->name('admin.equity');
    Route::post('equities', [EquityController::class, 'index'])->name('admin.equity.filter');
    Route::post('equity', [EquityController::class, 'store']);
    Route::get('equity/{id}', [EquityController::class, 'edit']);
    Route::put('equity/{id}', [EquityController::class, 'update']);
    
    //Asset
    Route::get('asset', [AssetController::class, 'index'])->name('admin.asset');
    Route::post('assets', [AssetController::class, 'index'])->name('admin.asset.filter');
    Route::post('asset', [AssetController::class, 'store']);
    Route::get('asset/{id}', [AssetController::class, 'edit']);
    Route::put('asset/{id}', [AssetController::class, 'update']); 

    //Expense
    Route::get('expense', [ExpenseController::class, 'index'])->name('admin.expense');
    Route::post('expenses', [ExpenseController::class, 'index'])->name('admin.expense.filter');
    Route::post('expense', [ExpenseController::class, 'store']);
    Route::get('expense/{id}', [ExpenseController::class, 'edit']);
    Route::put('expense/{id}', [ExpenseController::class, 'update']); 

    //Equity holders
    Route::get('share-holders', [EquityHolderController::class, 'index'])->name('admin.equityholders');
    Route::post('share-holders', [EquityHolderController::class, 'store']);
    Route::get('share-holders/{id}', [EquityHolderController::class, 'edit']);
    Route::put('share-holders/{id}', [EquityHolderController::class, 'update']);
    Route::get('share-holders/{id}/change-status', [EquityHolderController::class, 'changeStatus']);

    // income statement
    Route::get('income-statement', [IncomeStatementController::class, 'incomeStatement'])->name('admin.incomestatement');
    Route::post('income-statement', [IncomeStatementController::class, 'incomeStatementSearch'])->name('admin.incomestatement.report');


    // Balance Sheet
    Route::get('balance-sheet', [FinancialStatementController::class, 'balanceSheet'])->name('admin.balancesheet');
    Route::post('balance-sheet', [FinancialStatementController::class, 'balanceSheetReport'])->name('admin.balancesheet.report');

    // ledger
    Route::get('ledger-accounts', [LedgerController::class, 'showLedgerAccounts'])->name('admin.ledgeraccount');
    Route::get('ledger/asset-details/{id}', [LedgerController::class, 'asset']);
    Route::get('ledger/expense-details/{id}', [LedgerController::class, 'expense']);
    Route::get('ledger/income-details/{id}', [LedgerController::class, 'income']);
    Route::get('ledger/liability-details/{id}', [LedgerController::class, 'liability']);
    Route::get('ledger/equity-details/{id}', [LedgerController::class, 'equity']);

    Route::get('/ledger/{slug}/details', [LedgerController::class, 'shipmentCostLedger'])->name('ledger.expense-details');

    // purchase and sales ledger
    Route::get('ledger-sales', [LedgerController::class, 'salesLedger'])->name('ledger.sales');
    Route::get('ledger-purchase', [LedgerController::class, 'purchaseLedger'])->name('ledger.purchase');

    // cashflow
    Route::get('cashflow', [CashFlowController::class, 'cashflow'])->name('cashflow');
    // daybook
    Route::get('cash-book', [DaybookController::class, 'cashbook'])->name('cashbook');
    Route::get('bank-book', [DaybookController::class, 'bankbook'])->name('bankbook');

    //Faq Question crud
    Route::get('/faq-questions', [FAQController::class, 'index'])->name('allFaq');    
    Route::post('/faq-questions', [FAQController::class, 'store']);
    Route::get('/faq-questions/{id}/edit', [FAQController::class, 'edit']);
    Route::post('/faq-questions-update', [FAQController::class, 'update']);
    Route::get('/faq-questions/{id}', [FAQController::class, 'delete']);    


});
  