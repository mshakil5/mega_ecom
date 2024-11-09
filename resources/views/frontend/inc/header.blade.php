<header class="header header-intro-clearance header-4">
    <div class="header-top">
        <div class="container">
            <div class="header-left">
                <a href="tel:{{ $company->phone1 }}"><i class="icon-phone"></i>{{ $company->phone1 }}</a>
            </div>

            <div class="header-right">

                <ul class="top-menu">
                    <li>
                        <a href="#">
                            @if(Auth::check())
                            {{ auth()->user()->name }}
                            @elseif(Auth::guard('supplier')->check())
                            {{ Auth::guard('supplier')->user()->name }}
                            @else
                            Log In / Register
                            @endif
                        </a>
                        <ul>
                            <li>
                                @if(Auth::check())
                                    <a href="
                                        @if(auth()->user()->is_type == '1')
                                            {{ route('admin.dashboard') }}
                                        @elseif(auth()->user()->is_type == '0')
                                            {{ route('user.dashboard') }}
                                        @endif
                                    ">
                                        {{ auth()->user()->name }}
                                    </a>
                                @elseif(Auth::guard('supplier')->check())
                                    <a href="{{ route('supplier.dashboard') }}">
                                        {{ Auth::guard('supplier')->user()->name }}
                                    </a>
                                @else
                                    <div class="header-dropdown">
                                        <a>
                                            Log In / Register
                                        </a>
                                        <div class="header-menu">
                                            <ul>
                                                <li><a href="{{ route('login') }}">Log In</a></li>
                                                <li><a href="{{ route('register') }}">Register</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>

        </div>
    </div>

    <div class="header-middle">
        <div class="container">
            <div class="header-left">
                <button class="mobile-menu-toggler">
                    <span class="sr-only">Toggle mobile menu</span>
                    <i class="icon-bars"></i>
                </button>
                
                <a href="{{ route('frontend.homepage') }}" class="logo">
                    <img src="{{ asset('images/company/' . $company->company_logo) }}" alt="{{ $company->company_name }}" width="105" height="25">
                </a>
            </div>

            <div class="header-center">
                <div class="header-search header-search-extended header-search-visible d-none d-lg-block">
                    <a href="#" class="search-toggle" role="button"><i class="icon-search"></i></a>
                    <form id="search-form" class="position-relative">
                        <div class="header-search-wrapper search-wrapper-wide">
                            <label for="search-input" class="sr-only">Search</label>
                            <button class="btn btn-primary search-icon" type="button" id="search-icon"><i class="icon-search"></i></button>
                            <input type="search" class="form-control search-input" id="search-input" placeholder="Search product ..." required>
                        </div>
                    </form>
                </div>
            </div>

            <div class="header-right">

                <div class="wishlist">
                    <a href="{{ route('wishlist.index') }}" class="wishlistBtn" title="Wishlist">
                        <div class="icon">
                            <i class="icon-heart-o"></i>
                            <span class="wishlist-count badge wishlistCount">0</span>
                        </div>
                    </a>
                </div>

                <div class="dropdown cart-dropdown">
                    <a href="{{ route('cart.index') }}" class="dropdown-toggle cartBtn" title="Cart">
                        <div class="icon">
                            <i class="icon-shopping-cart"></i>
                            <span class="cart-count cartCount">0</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="header-bottom sticky-header">
        <div class="container">
            <div class="header-left">
                <div class="dropdown category-dropdown">
                <a class="dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-display="static" title="Browse Categories">
                    Browse Categories <i class="icon-angle-down"></i>
                </a>

                <div class="dropdown-menu">
                    <nav class="side-nav">
                        <ul class="menu-vertical sf-arrows">
                            @foreach($categories as $category)
                                <li>
                                    <a href="{{ route('category.show', $category->slug) }}">{{ $category->name }}</a>
                                    @if(count($category->subcategories) > 0)
                                        <ul>
                                            @foreach($category->subcategories as $subcategory)
                                                <li><a href="{{ route('subcategory.show', $subcategory->slug) }}">{{ $subcategory->name }}</a></li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

        <div class="header-center">
            <nav class="main-nav">
                <ul class="menu sf-arrows">
                    <li class="megamenu-container {{ request()->is('/') ? 'active' : '' }}">
                        <a href="{{ route('frontend.homepage') }}">Home</a>
                    </li>

                    <li class="dropdown">
                        <a class="sf-with-ul">Products</a>

                        <ul>
                            @foreach($categories as $category)
                                @if($category->products->count() > 0)
                                    <li>
                                        <a href="{{ route('category.show', $category->slug) }}" class="sf-with-ul">{{ $category->name }}</a>

                                        <ul>
                                            @foreach($category->products as $product)
                                                <li><a href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a></li>
                                            @endforeach
                                        </ul>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </li>

                    <li class="{{ request()->routeIs('frontend.shop') ? 'active' : '' }}">
                        <a href="{{ route('frontend.shop') }}">Shop</a>
                    </li>
                    <li class="{{ request()->routeIs('frontend.about') ? 'active' : '' }}">
                        <a href="{{ route('frontend.about') }}">About Us</a>
                    </li>
                    <li class="{{ request()->routeIs('frontend.contact') ? 'active' : '' }}">
                        <a href="{{ route('frontend.contact') }}">Contact Us</a>
                    </li>
                </ul>
            </nav>
        </div>

        <div class="header-right d-none">
            <i class="la la-lightbulb-o"></i><p>Clearance<span class="highlight">&nbsp;Up to 30% Off</span></p>
        </div>
    </div>
    
</header>

<div class="mt-1" id="searchSection">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="row search-products align-items-center py-3 px-xl-5">
            </div>
        </div>
    </div>
</div>