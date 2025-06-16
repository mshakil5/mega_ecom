<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ (request()->is('admin/dashboard*')) ? 'active' : '' }}">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
            </a>
        </li>

        <a href="{{ route('toggle.sidebar') }}" class="btn btn-info my-2">
            Switch to Accounting <i class="fas fa-arrow-right"></i>
        </a>

        <!-- Inventory -->
        <li class="nav-item dropdown {{ (request()->is('admin/category*') || request()->is('admin/brand*') || request()->is('admin/model*') || request()->is('admin/unit*') || request()->is('admin/group*') || request()->is('admin/size*') || request()->is('admin/color*') || request()->is('admin/type*') || request()->is('admin/product*') || request()->is('admin/bundle-product*') || request()->is('admin/sub-category*') || request()->is('admin/related-product*') || request()->is('admin/create-product*') || request()->is('admin/bogo-product*')) ? 'menu-open' : '' }}">
            <a href="#" class="nav-link dropdown-toggle {{ (request()->is('admin/category*') || request()->is('admin/brand*') || request()->is('admin/model*') || request()->is('admin/unit*') || request()->is('admin/group*') || request()->is('admin/size*') || request()->is('admin/color*') || request()->is('admin/type*') || request()->is('admin/product*') || request()->is('admin/bundle-product*') || request()->is('admin/sub-category*') || request()->is('admin/create-product*') || request()->is('admin/related-product*') || request()->is('admin/bogo-product*')) ? 'active' : '' }}">
                <i class="nav-icon fas fa-clipboard-list"></i>
                <p>
                    Head of Products<i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('createproduct') }}" class="nav-link {{ (request()->is('admin/create-product')) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-plus-circle"></i>
                        <p>Create Product</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('allproduct') }}" class="nav-link {{ (request()->is('admin/product*')) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-box-open"></i>
                        <p>Manage Products</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('allcategory') }}" class="nav-link {{ (request()->is('admin/category*')) ? 'active' : '' }}">
                        <i class="far fa-list-alt nav-icon"></i>
                        <p>Categories</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('allsubcategory') }}" class="nav-link {{ (request()->is('admin/sub-category*')) ? 'active' : '' }}">
                        <i class="far fa-folder nav-icon"></i>
                        <p>Sub Categories</p>
                    </a>
                </li>
                <li class="nav-item d-none">
                    <a href="{{ route('allbrand') }}" class="nav-link {{ (request()->is('admin/brand*')) ? 'active' : '' }}">
                        <i class="fas fa-tags nav-icon"></i>
                        <p>Brands</p>
                    </a>
                </li>
                <li class="nav-item d-none">
                    <a href="{{ route('allmodel') }}" class="nav-link {{ (request()->is('admin/model*')) ? 'active' : '' }}">
                        <i class="fas fa-cogs nav-icon"></i>
                        <p>Models</p>
                    </a>
                </li>
                <li class="nav-item d-none">
                    <a href="{{ route('allunit') }}" class="nav-link {{ (request()->is('admin/unit*')) ? 'active' : '' }}">
                        <i class="fas fa-ruler nav-icon"></i>
                        <p>Units</p>
                    </a>
                </li>
                <li class="nav-item d-none">
                    <a href="{{ route('allgroup') }}" class="nav-link {{ (request()->is('admin/group*')) ? 'active' : '' }}">
                        <i class="fas fa-object-group nav-icon"></i>
                        <p>Groups</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('allsize') }}" class="nav-link {{ (request()->is('admin/size*')) ? 'active' : '' }}">
                        <i class="fas fa-expand-arrows-alt nav-icon"></i> <!-- Size icon -->
                        <p>Size</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('allcolor') }}" class="nav-link {{ (request()->is('admin/color*')) ? 'active' : '' }}">
                        <i class="fas fa-paint-brush nav-icon"></i> <!-- Color icon -->
                        <p>Color</p>
                    </a>
                </li>
               <li class="nav-item">
                    <a href="{{ route('alltype') }}" class="nav-link {{ (request()->is('admin/type*')) ? 'active' : '' }}">
                        <i class="fas fa-font nav-icon"></i>
                        <p>Type</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('allrelatedproduct') }}" class="nav-link {{ (request()->is('admin/related-product*')) ? 'active' : '' }}">
                        <i class="fas fa-tags nav-icon"></i>
                        <p>Related Product</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('allbundleproduct') }}" class="nav-link {{ (request()->is('admin/bundle-product*')) ? 'active' : '' }}">
                        <i class="fas fa-boxes nav-icon"></i>
                        <p>Bundle Product</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('allbogoproduct') }}" class="nav-link {{ (request()->is('admin/bogo-product*')) ? 'active' : '' }}">
                        <i class="fas fa-gift nav-icon"></i>
                        <p>Buy One Get One</p>
                    </a>
                </li>
            </ul>
        </li>

        <!-- purchase -->
        <li class="nav-item dropdown {{( request()->is('admin/purchase-history*') || request()->is('admin/add-stock*') || request()->is('admin/purchase-return-history*') || request()->routeIs('purchase.edit') || request()->routeIs('stockReturnHistory') || request()->routeIs('admin.shipping') || request()->routeIs('admin.shipment.edit') || request()->routeIs('admin.shipment.create') || request()->routeIs('returnProduct') || request()->routeIs('admin.newPurchaseHistory') || request()->routeIs('admin.livePurchaseHistory') || request()->routeIs('allsupplier') || request()->routeIs('admin.completedPurchaseHistory') || request()->routeIs('shipment.sample_products') || request()->routeIs('admin.shipment.print')) ? 'menu-open' : '' }}">
            <a href="#" class="nav-link dropdown-toggle {{ (request()->is('admin/purchase-history*') || request()->is('admin/add-stock*') || request()->is('admin/purchase-return-history*') || request()->routeIs('purchase.edit') || request()->routeIs('stockReturnHistory') || request()->routeIs('admin.shipping') || request()->routeIs('admin.shipment.create') || request()->routeIs('returnProduct') || request()->routeIs('admin.livePurchaseHistory') || request()->routeIs('admin.newPurchaseHistory') || request()->routeIs('allsupplier') || request()->routeIs('admin.shipment.edit') || request()->routeIs('admin.completedPurchaseHistory') || request()->routeIs('admin.shipment.print') || request()->routeIs('shipment.sample_products') ) ? 'active' : '' }}">
                <i class="nav-icon fas fa-warehouse"></i>
                <p>
                    Purchase <i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('addStock') }}" class="nav-link {{ (request()->is('admin/add-stock*')) ? 'active' : '' }}">
                        <i class="fas fa-shopping-cart nav-icon"></i>
                        <p>Purchase</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.shipping') }}" class="nav-link {{ (request()->is('admin/shipping*') || request()->routeIs('admin.shipment.create') || request()->routeIs('admin.shipment.edit') || request()->routeIs('admin.shipment.print')) ? 'active' : '' }}">
                        <i class="fas fa-box nav-icon"></i>
                        <p>Shipping List</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.livePurchaseHistory') }}" class="nav-link {{ (request()->is('admin/live-purchase-history*')) ? 'active' : '' }}">
                        <i class="fas fa-shopping-cart nav-icon"></i>
                        <p>Live Purchases</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.newPurchaseHistory') }}" class="nav-link {{ (request()->is('admin/new-purchase-history*')) ? 'active' : '' }}">
                        <i class="fas fa-cart-plus nav-icon"></i>
                        <p>New Purchases</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.completedPurchaseHistory') }}" class="nav-link {{ (request()->is('admin/completed-purchase-history*')) ? 'active' : '' }}">
                        <i class="fas fa-check-circle nav-icon"></i>
                        <p>Completed Purchases</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('productPurchaseHistory') }}" class="nav-link {{ (request()->is('admin/purchase-history*') || request()->routeIs('purchase.edit') || request()->routeIs('purchase.edit')) ? 'active' : '' }}">
                        <i class="fas fa-list-alt nav-icon"></i>
                        <p>All Purchases</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('shipment.sample_products') }}" class="nav-link {{ (request()->is('admin/shipment/sample-products*') || request()->routeIs('shipment.sample_products')) ? 'active' : '' }}">
                        <i class="fas fa-cube nav-icon"></i>
                        <p>Sample Products</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('stockReturnHistory') }}" class="nav-link {{ (request()->is('admin/purchase-return-history') || request()->routeIs('stockReturnHistory')) ? 'active' : '' }}">
                        <i class="fas fa-undo nav-icon"></i>
                        <p>Return History</p>
                    </a>
                </li>
                <!-- supplier -->
                <li class="nav-item">
                    <a href="{{ route('allsupplier') }}" class="nav-link {{ (request()->is('admin/supplier*')) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-truck"></i>
                        <p>Supplier</p>
                    </a>
                </li>
            </ul>
        </li>



        
        <!-- Sales -->
        <li class="nav-item dropdown {{ request()->is('admin/*order*') || request()->routeIs('allcustomer') || request()->routeIs('customer.transactions') || request()->is('admin/quotation*') || request()->is('admin/in-house-sell*') && !request()->is('admin/all-inhouse-orders') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link dropdown-toggle {{ request()->is('admin/*order*') || request()->routeIs('allcustomer') || request()->routeIs('customer.transactions') || request()->is('admin/in-house-sell*') || request()->is('admin/quotation*') && !request()->is('admin/all-inhouse-orders') ? 'active' : '' }}">
                <i class="nav-icon fas fa-truck"></i>
                <p>
                    Sales <i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                
                <li class="nav-item">
                    <a href="{{ route('pendingorders') }}" class="nav-link {{ request()->is('admin/pending-orders*') ? 'active' : '' }}">
                        <i class="fas fa-box-open nav-icon"></i>
                        <p>Customer Orders</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('getallorder') }}" class="nav-link {{ request()->is('admin/all-order*') || request()->routeIs('order-edit') || request()->routeIs('admin.orders.details') ? 'active' : '' }}">
                        <i class="fas fa-list nav-icon"></i>
                        <p>All Orders</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('inhousesell') }}" class="nav-link {{ (request()->is('admin/in-house-sell*')) ? 'active' : '' }}">
                        <i class="fas fa-store nav-icon"></i>
                        <p>In House Sale</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('getinhouseorder') }}" class="nav-link {{ (request()->is('admin/all-inhouse-orders*')) ? 'active' : '' }}">
                        <i class="fas fa-industry nav-icon"></i>
                        <p>In House Sales List</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('allquotations') }}" class="nav-link {{ (request()->is('admin/quotations*')) ? 'active' : '' }}">
                        <i class="fas fa-box nav-icon"></i>
                        <p>Quotation list</p>
                    </a>
                </li>

                <!-- customer -->
                <li class="nav-item">
                    <a href="{{ route('allcustomer') }}" class="nav-link {{ (request()->is('admin/whole-saler*') || request()->routeIs('admin.due.list') ) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Whole Saler</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('processingorders') }}" class="nav-link {{ request()->is('admin/processing-orders*') ? 'active' : '' }}">
                        <i class="fas fa-cogs nav-icon"></i>
                        <p>Processing Orders</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('packedorders') }}" class="nav-link {{ request()->is('admin/packed-orders*') ? 'active' : '' }}">
                        <i class="fas fa-boxes nav-icon"></i>
                        <p>Packed Orders</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('shippedorders') }}" class="nav-link {{ request()->is('admin/shipped-orders*') ? 'active' : '' }}">
                        <i class="fas fa-shipping-fast nav-icon"></i>
                        <p>Shipped Orders</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('deliveredorders') }}" class="nav-link {{ request()->is('admin/delivered-orders*') ? 'active' : '' }}">
                        <i class="fas fa-check-circle nav-icon"></i>
                        <p>Delivered Orders</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('returnedorders') }}" class="nav-link {{ request()->is('admin/returned-orders*') ? 'active' : '' }}">
                        <i class="fas fa-undo nav-icon"></i>
                        <p>Returned Orders</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('cancelledorders') }}" class="nav-link {{ request()->is('admin/cancelled-orders*') ? 'active' : '' }}">
                        <i class="fas fa-ban nav-icon"></i>
                        <p>Cancelled Orders</p>
                    </a>
                </li>
            </ul>
        </li>
   

        <!-- stock -->
        <li class="nav-item dropdown {{ (request()->is('admin/stock*') || request()->is('admin/system-losses*') || request()->is('admin/stock-transfer-requests*')) ? 'menu-open' : '' }}">
            <a href="#" class="nav-link dropdown-toggle {{ (request()->is('admin/stock*') || request()->is('admin/system-losses*') || request()->is('admin/stock-transfer-requests*') || request()->routeIs('stockhistory')) ? 'active' : '' }}">
                <i class="nav-icon fas fa-warehouse"></i>
                <p>
                    Stocks <i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('allstock') }}" class="nav-link {{ (request()->is('admin/stock')) ? 'active' : '' }}">
                        <i class="fas fa-list nav-icon"></i>
                        <p>Stock List</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('stockLedger') }}" class="nav-link {{ (request()->is('admin/stock-ledger') && request()->is('admin/stock-ledger')) ? 'active' : '' }}">
                        <i class="fas fa-list nav-icon"></i>
                        <p>Stock Ledger</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('stockhistory') }}" class="nav-link {{ (request()->is('admin/stocking-history') && !request()->is('admin/add-stock*')) ? 'active' : '' }}">
                        <i class="fas fa-list nav-icon"></i>
                        <p>Stocking History</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('stock-transfer-requests.index') }}" class="nav-link {{ (request()->is('admin/stock-transfer-requests')) ? 'active' : '' }}">
                        <i class="fas fa-box nav-icon"></i>
                        <p>Stock Transfer Requests</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('system-losses.index') }}" class="nav-link {{ (request()->is('admin/system-losses')) ? 'active' : '' }}">
                        <i class="fas fa-undo nav-icon"></i>
                        <p>System Loses</p>
                    </a>
                </li>
            </ul>
        </li>


        <!-- Warehouse -->
        <li class="nav-item">
            <a href="{{ route('allwarehouse') }}" class="nav-link {{ (request()->is('admin/warehouse*')) ? 'active' : '' }}">
                <i class="nav-icon fas fa-truck"></i>
                <p>Warehouse</p>
            </a>
        </li>

        

        <!-- Promotion & Advertisement -->
        <li class="nav-item dropdown {{ request()->is('admin/campaign*') ? 'menu-open' : '' }} {{ request()->is('admin/create-special-offer*') || request()->is('admin/special-offers*') || request()->is('admin/special-offer-history*') ? 'menu-open' : '' }} {{ request()->is('admin/create-flash-sell*') || request()->is('admin/flash-sells') || request()->is('admin/flash-sell-history*') || request()->routeIs('flash-sell.edit') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link dropdown-toggle {{ request()->is('admin/campaign*') ? 'active' : '' }} {{ request()->is('admin/create-special-offer*') || request()->is('admin/special-offers*') || request()->is('admin/special-offer-history*') ? 'active' : '' }} {{ request()->is('admin/create-flash-sell*') || request()->is('admin/flash-sells') || request()->is('admin/flash-sell-history*') || request()->routeIs('flash-sell.edit') ? 'active' : '' }}">
                <i class="nav-icon fas fa-bolt"></i>
                <p>
                    Promotion & Advertisement<i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">

                <li class="nav-item">
                    <a href="{{ route('createspecialoffer') }}" class="nav-link {{ request()->routeIs('createspecialoffer') ? 'active' : '' }}">
                        <i class="fas fa-plus nav-icon"></i>
                        <p>Special Offer Create</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('specialoffers') }}" class="nav-link {{ request()->routeIs('specialoffers') ? 'active' : '' }}">
                        <i class="fas fa-list nav-icon"></i>
                        <p>Special Offers</p>
                    </a>
                </li>


                <li class="nav-item">
                    <a href="{{ route('createflashsell') }}" class="nav-link {{ request()->routeIs('createflashsell') ? 'active' : '' }}">
                        <i class="fas fa-plus nav-icon"></i>
                        <p>Flash Sale Create</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('flashsells') }}" class="nav-link {{ request()->routeIs('flashsells') ? 'active' : '' }}">
                        <i class="fas fa-list nav-icon"></i>
                        <p>Flash Sales</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('allcampaign') }}" class="nav-link {{ request()->routeIs('allcampaign') ? 'active' : '' }}">
                        <i class="fas fa-plus nav-icon"></i>
                        <p>Campaigns</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('allcampaignRequests') }}" class="nav-link {{ request()->routeIs('allcampaignRequests') ? 'active' : '' }}">
                        <i class="fas fa-list nav-icon"></i>
                        <p>Campaign Requests</p>
                    </a>
                </li>

            </ul>
        </li>

        

        <li class="nav-item">
            <a href="{{ route('reports.index') }}" class="nav-link {{ 
                request()->is('admin/reports') ||
                request()->is('admin/daily-sale') ||
                request()->is('admin/weekly-sale') ||
                request()->is('admin/monthly-sale') ||
                request()->is('admin/date-to-date-sale') ||
                request()->is('admin/daily-purchase') ||
                request()->is('admin/weekly-purchase') ||
                request()->is('admin/monthly-purchase') ||
                request()->is('admin/date-to-date-purchase') ||
                request()->routeIs('reports.shipments') ||
                request()->routeIs('reports.dateToDateShipments') ? 'active' : '' }}">
                <i class="nav-icon fas fa-chart-bar"></i>
                <p>Reports</p>
            </a>
        </li>


        
        
        <!-- Settings -->
        <li class="nav-item mb-5 {{ 
                request()->is('admin/new-admin') ||
                request()->is('admin/role') ||
                request()->is('admin/slider') ||
                request()->is('admin/company-details') ||
                request()->is('admin/contact-email') ||
                request()->is('admin/mail-content') ||
                request()->is('admin/contact-message') ||
                request()->is('admin/section-status') ||
                request()->is('admin/ads') ||
                request()->is('admin/deliveryman') ||
                request()->is('admin/payment-gateway') ||
                request()->is('admin/delivery-charge') ||
                request()->is('admin/faq-questions') ||
                request()->is('admin/coupon')
                ? 'menu-open' : '' }}">
            <a href="#" class="nav-link dropdown-toggle {{ 
                (request()->is('admin/new-admin*')|| 
                request()->is('admin/role*')||
                request()->is('admin/slider*')||
                request()->is('admin/company-details*')||
                request()->is('admin/contact-email*')||
                request()->is('admin/mail-content*')||
                request()->is('admin/contact-message*')||
                request()->is('admin/section-status*')||
                request()->is('admin/ads*')||
                request()->is('admin/deliveryman*')||
                request()->is('admin/payment-gateway*')||
                request()->is('admin/delivery-charge') ||
                request()->is('admin/faq-questions') ||
                request()->is('admin/coupon*'))
                 ? 'active' : '' }}">
                <i class="nav-icon fas fa-cogs"></i>
                <p>
                    Settings<i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('alladmin') }}" class="nav-link {{ (request()->is('admin/new-admin*')) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-shield"></i>
                        <p>Admin</p>
                    </a>
                </li>
                
                <!-- Roles & Permissions -->
                <li class="nav-item">
                    <a href="{{ route('admin.role') }}" class="nav-link {{ (request()->is('admin/role*')) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-shield"></i>
                        <p>Roles & Permissions</p>
                    </a>
                </li>
        
                <!-- sidebar -->
                <li class="nav-item">
                    <a href="{{ route('allslider') }}" class="nav-link {{ (request()->is('admin/slider*')) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-sliders-h"></i>
                        <p>Slider</p>
                    </a>
                </li>
                <!-- company details -->
                <li class="nav-item">
                    <a href="{{ route('admin.companyDetail') }}" class="nav-link {{ (request()->is('admin/company-details*')) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-building"></i>
                        <p>Company Details</p>
                    </a>
                </li>

                <!-- contact email -->
                <li class="nav-item">
                    <a href="{{ route('allcontactemail') }}" class="nav-link {{ (request()->is('admin/contact-email')) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-envelope"></i>
                        <p>Contact Email</p>
                    </a>
                </li>

                <!-- mail content -->
                <li class="nav-item">
                    <a href="{{ route('admin.mail-content') }}" class="nav-link {{ (request()->is('admin/mail-content*')) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-paper-plane"></i>
                        <p>Mail Content</p>
                    </a>
                </li>
                <!-- contact message -->
                <li class="nav-item">
                    <a href="{{ route('allcontactmessae') }}" class="nav-link {{ (request()->is('admin/contact-message')) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-phone"></i>
                        <p>Contact Message</p>
                    </a>
                </li>

                <!-- section status -->
                <li class="nav-item">
                    <a href="{{ route('sectionstatus') }}" class="nav-link {{ (request()->is('admin/section-status')) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>Section Status</p>
                    </a>
                </li>
                
                <!-- ads -->
                <li class="nav-item">
                    <a href="{{ route('alladds') }}" class="nav-link {{ (request()->is('admin/ads')) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-ad"></i>
                        <p>Ads</p>
                    </a>
                </li>

                <!-- coupon -->
                <li class="nav-item">
                    <a href="{{ route('allcoupon') }}" class="nav-link {{ (request()->is('admin/coupon')) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-ticket-alt"></i>
                        <p>Coupons</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('allFaq') }}" class="nav-link {{ (request()->is('admin/faq-questions')) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-question-circle"></i>
                        <p>FAQ Questions</p>
                    </a>
                </li>
                <!-- delivery man -->
                <li class="nav-item">
                    <a href="{{ route('alldeliverymen') }}" class="nav-link {{ (request()->is('admin/deliveryman*')) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Delivery Men</p>
                    </a>
                </li>

                <!-- delivery charge -->
                <li class="nav-item">
                    <a href="{{ route('alldeliverycharge') }}" class="nav-link {{ (request()->is('admin/delivery-charge*')) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-truck"></i>
                        <p>Delivery Charge</p>
                    </a>
                </li>

                <!-- payment gateway -->
                <li class="nav-item">
                    <a href="{{ route('allpaymentgateways') }}" class="nav-link {{ (request()->is('admin/payment-gateway*')) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-credit-card"></i>
                        <p>Payment Getways</p>
                    </a>
                </li>

            </ul>
        </li>

        
        <li class="mb-5"></li>
        <li class="mb-5"></li>
        <li class="mb-5"></li>
        
    </ul>
  </nav>