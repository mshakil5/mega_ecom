
@php
    $categories = \App\Models\Category::where('status', 1)
        ->whereHas('subcategories') 
        ->with('subcategories')  
        ->get();
@endphp

    <!-- Top small bar (hidden on small screens) -->
    <div class="top-bar bg-dark text-white d-none d-lg-block">
        <div class="container-fluid h-100">
            <div class="row h-100 align-items-center justify-content-between">
                <div class="col-auto p-0">
                    <button class="btn btn-link text-uppercase fw-bold">
                        <i class="fas fa-bolt me-2"></i>EXCLUSIVE FALL COLLECTION
                    </button>
                </div>

                <div class="col-auto p-0">
                    <a href="#" class="btn btn-link"><i class="fas fa-briefcase me-1"></i> Corporate Sales</a>
                    <a href="#" class="btn btn-link"><i class="fas fa-store me-1"></i> Store Locations</a>
                </div>

                <div class="col-auto p-0 d-flex align-items-center">
                    <a href="#" class="btn btn-link"><i class="fas fa-circle-info me-1"></i> About Us</a>
                    <div class="dropdown account-dropdown">
                        <button class="btn btn-link dropdown-toggle" type="button" id="accountDropdownMenu" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i> 
                            
                            @if(Auth::check())
                            {{ auth()->user()->name }}
                            @elseif(Auth::guard('supplier')->check())
                            {{ Auth::guard('supplier')->user()->name }}
                            @else
                            Account
                            @endif
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="accountDropdownMenu">

                            @if(Auth::check())
                                <li>
                                    <a class="dropdown-item"  href="
                                        @if(auth()->user()->is_type == '1')
                                            {{ route('admin.dashboard') }}
                                        @elseif(auth()->user()->is_type == '0')
                                            {{ route('user.dashboard') }}
                                        @endif
                                    ">
                                        {{ auth()->user()->name }}
                                    </a>
                                </li>
                                @elseif(Auth::guard('supplier')->check())
                                <li>
                                    <a class="dropdown-item"  href="{{ route('supplier.dashboard') }}">
                                        {{ Auth::guard('supplier')->user()->name }}
                                    </a>
                                </li>
                                @else

                                    <li><a class="dropdown-item" href="{{ route('login') }}"><i class="fas fa-right-to-bracket me-2"></i> Sign In</a></li>
                                    <li><a class="dropdown-item" href="{{ route('register') }}"><i class="fas fa-user-plus me-2"></i> Sign Up</a></li>

                                                
                                @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm" id="main-navbar">
        <div class="container">
            <a class="navbar-brand fw-bolder fs-3" href="{{ route('frontend.homepage') }}">
                <img src="{{ asset('images/company/' . $company->company_logo) }}" alt="{{ $company->company_name }}" width="105" height="25">
            </a>

            

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 align-items-center">
                    <li class="nav-item mega-dropdown dropdown me-5" id="shopDropdown">
                        <button class="btn btn-outline-dark dropdown-toggle text-dark fw-bold" type="button" id="shopMegaMenu" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-shop me-1"></i> SHOP
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="shopMegaMenu">
                            <div class="container">
                                <div class="row">
                                    <div class="col-lg-9 col-md-12">
                                        <div class="row">


                                            @foreach ($categories as $category)
                                                <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                                                    <h6 class="dropdown-header text-uppercase">  {{ $category->name }}    </h6>

                                                    @foreach ($category->subcategories as $subcategory)
                                                    <a class="dropdown-item" href="#">{{$subcategory->name}}</a>
                                                    @endforeach
                                                    
                                                </div>
                                            @endforeach


                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-3 col-md-12">
                                        <h6 class="dropdown-header">FEATURED</h6>
                                        <img src="https://placehold.co/300x200/212529/ffffff?text=NEW+ARRIVALS" class="img-fluid rounded shadow-sm" alt="New Arrivals Promo">
                                        <a href="#" class="btn btn-dark btn-sm mt-2 w-100">Shop New Collection</a>
                                    </div>
                                </div>
                            </div>
                        </ul>
                    </li>

                    <li class="nav-item search-container">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search Products ..." id="search-input">
                            <button class="btn btn-outline-secondary" id="search-btn"><i class="fas fa-search"></i></button>
                        </div>

                        <div class="search-results-dropdown rounded d-none" id="search-results"></div>
                    </li>
                </ul>


                <div class="d-flex align-items-center ms-auto">
                    <div class="dropdown me-3" id="wishlistDropdown">
                        <button class="btn btn-outline-dark position-relative" type="button" aria-expanded="false" data-bs-toggle="dropdown">
                            
                                <i class="fas fa-heart"></i>
                                <span class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle wishlistCount">0</span>
                                
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end mini-wishlist p-0" aria-labelledby="wishlistDropdown">
                            <li class="p-3"><h6 class="dropdown-header text-center mb-0">My Wishlist (5 Items)</h6></li>
                            <li class="cart-item d-flex justify-content-between align-items-center"><div><p class="mb-0 fw-bold">Denim Jacket</p><small class="text-muted">In Stock</small></div><button type="button" class="btn btn-sm btn-outline-success"><i class="fas fa-cart-plus"></i></button></li>
                            <li class="cart-item d-flex justify-content-between align-items-center"><div><p class="mb-0 fw-bold">Summer Dress</p><small class="text-muted">Low Stock</small></div><button type="button" class="btn btn-sm btn-outline-success"><i class="fas fa-cart-plus"></i></button></li>
                            <li><hr class="dropdown-divider my-0"></li>
                            <li class="p-3"><a href="{{ route('wishlist.index') }}" class="btn btn-outline-dark w-100 wishlistBtn">View Full Wishlist</a></li>
                        </ul>
                    </div>

                    {{-- <div class="dropdown" id="cartDropdown">
                        <button class="btn btn-outline-dark position-relative " type="button" aria-expanded="false" data-bs-toggle="dropdown">
                            
                            <i class="fas fa-cart-shopping"></i>
                            <span class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle cartCount">0</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end mini-cart p-0" aria-labelledby="cartDropdown">

                            <li class="cart-item d-flex justify-content-between align-items-center"><div><p class="mb-0 fw-bold">Denim Jacket</p><small class="text-muted">1 x £45.00</small></div><button type="button" class="btn btn-sm btn-outline-danger"><i class="fas fa-times"></i></button></li>

                            <li class="cart-item d-flex justify-content-between align-items-center"><div><p class="mb-0 fw-bold">Black Hoodie</p><small class="text-muted">2 x £30.00</small></div><button type="button" class="btn btn-sm btn-outline-danger"><i class="fas fa-times"></i></button></li>

                            <li><hr class="dropdown-divider my-0"></li>

                            <li class="p-3"><div class="d-flex justify-content-between fw-bold mb-2"><span>Total:</span><span>£105.00</span></div><a href="{{ route('cart.index') }}" class="btn btn-dark w-100 cartBtn">Checkout</a></li>

                        </ul>
                    </div> --}}

                    <div class="dropdown" id="cartDropdown">
                        <button 
                            class="btn btn-outline-dark position-relative"
                            type="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">

                            <i class="fas fa-cart-shopping"></i>

                            <!-- Cart Count -->
                            <span class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle cartCount">
                                0
                            </span>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end mini-cart p-0" aria-labelledby="cartDropdown">

                            <!-- Dynamic items wrapper -->
                            <div id="miniCartItems"></div>

                            <!-- Empty Message -->
                            <li class="p-3 text-center text-muted" id="emptyCartMsg">
                                Cart is empty.
                            </li>

                            <!-- Divider -->
                            <li><hr class="dropdown-divider my-0 d-none" id="miniCartDivider"></li>

                            <!-- Footer -->
                            <li class="p-3 d-none" id="miniCartFooter">
                                <div class="d-flex justify-content-between fw-bold mb-2">
                                    <span>Total:</span>
                                    <span id="miniCartTotal">£0.00</span>
                                </div>
                                <a href="{{ route('cart.index') }}" class="btn btn-dark w-100 cartBtn">Checkout</a>
                            </li>

                        </ul>





                    </div>

                </div>
            </div>
        </div>
    </nav>