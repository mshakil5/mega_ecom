<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
           
        <li class="nav-item">
            <a href="{{ route('supplier.dashboard') }}" class="nav-link {{ (request()->is('supplier/dashboard')) ? 'active' : '' }}">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('supplier.profile') }}" class="nav-link {{ (request()->is('supplier/profile')) ? 'active' : '' }}">
                <i class="nav-icon fas fa-user"></i>
                <p>Profile</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('productPurchaseHistory.supplier') }}" class="nav-link {{ (request()->is('supplier/sold-history')) ? 'active' : '' }}">
                <i class="nav-icon fas fa-history"></i>
                <p>Sold To Shop</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('returnHistory.supplier') }}" class="nav-link {{ (request()->is('supplier/return-history')) ? 'active' : '' }}">
                <i class="nav-icon fas fa-undo-alt"></i>
                <p>Returned From Shop</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('stock.supplier') }}" class="nav-link {{ (request()->is('supplier/stocks')) ? 'active' : '' }}">
                <i class="fas fa-list nav-icon"></i>
                <p>Stock</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('order.supplier') }}" class="nav-link {{ request()->routeIs('order.supplier', 'supplier.orders.details') ? 'active' : '' }}">
                <i class="fas fa-shopping-cart nav-icon"></i>
                <p>Orders</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('supplier.transaction') }}" class="nav-link {{ request()->routeIs('supplier.transaction') ? 'active' : '' }}">
                <i class="fas fa-file-invoice-dollar nav-icon"></i>
                <p>Transactions</p>
            </a>
        </li>

        <li class="nav-item dropdown {{ request()->is('supplier/campaign*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link dropdown-toggle {{ request()->is('supplier/campaign*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-bullhorn"></i>
                <p>
                    Campaign Requests <i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('supplier.campaignRequest') }}" class="nav-link {{ request()->routeIs('supplier.campaignRequest') ? 'active' : '' }}">
                        <i class="fas fa-plus nav-icon"></i>
                        <p>Create New Request</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('supplier.campaignRequests') }}" class="nav-link {{ request()->routeIs('supplier.campaignRequests') ? 'active' : '' }}">
                        <i class="fas fa-list nav-icon"></i>
                        <p>My Campaign Requests</p>
                    </a>
                </li>
            </ul>
        </li>
    
    </ul>
  </nav>