<style>
    #search-results ul {
        list-style-type: none;
        padding-left: 0;
        margin: 0;
    }

    #search-results li {
        padding: 10px;
        border-bottom: 1px solid #ddd;
    }

    #search-results li a {
        text-decoration: none;
        color: #333;
    }

    #search-results li:hover {
        background-color: #f8f8f8;
    }
</style>

<header class="header header-14">
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
                                                <li><a href="{{ route('supplier.login') }}">Log In As Supplier</a></li>
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
        <div class="container-fluid">
            <div class="row">

                <div class="col-auto col-lg-3 col-xl-3 col-xxl-2">
                    <button class="mobile-menu-toggler">
                        <span class="sr-only">Toggle mobile menu</span>
                        <i class="icon-bars"></i>
                    </button>
            
                    <a href="{{ route('frontend.homepage') }}" class="logo">
                        <img src="{{ asset('images/company/' . $company->company_logo) }}" alt="Molla Logo" width="105" height="25">
                    </a>
                </div>

                <div class="col col-lg-9 col-xl-9 col-xxl-10 header-middle-right">
                    <div class="row">
                        <div class="col-lg-8 col-xxl-4-5col d-none d-lg-block">
                            <div class="header-search header-search-extended header-search-visible d-none d-lg-block">
                                <a href="#" class="search-toggle" role="button"><i class="icon-search"></i></a>
                                <form id="search-form" class="position-relative">
                                    <div class="header-search-wrapper search-wrapper-wide">
                                        <label for="search-input" class="sr-only">Search</label>
                                        <input type="search" class="form-control" id="search-input" placeholder="Search product ..." required>
                                        <button class="btn btn-primary" type="submit"><i class="icon-search"></i></button>
                                    </div>
                                </form>
                                <div id="search-results" class="bg-light position-absolute w-100" style="z-index: 1000;"></div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-xxl-5col d-flex justify-content-end align-items-center">
                            <div class="header-dropdown-link">
                                <a href="{{ route('wishlist.index') }}" class="wishlistBtn wishlist-link" title="Wishlist">
                                    <i class="icon-heart-o"></i>
                                    <span class="wishlist-count wishlistCount">0</span>
                                    <span class="wishlist-txt">Wishlist</span>
                                </a>
                                <div class="dropdown cart-dropdown">
                                    <a href="{{ route('cart.index') }}" class="dropdown-toggle cartBtn">
                                        <div class="icon">
                                            <i class="icon-shopping-cart"></i>
                                            <span class="cart-count cartCount">0</span>
                                        </div>
                                        <p>Cart</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>

    <div class="header-bottom sticky-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-auto col-lg-3 col-xl-3 col-xxl-2 header-left">
                    <div class="dropdown category-dropdown show is-on" data-visible="true">
                        <a href="#" class="dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-display="static" title="Browse Categories">
                            Browse Categories
                        </a>
                        <div class="dropdown-menu show">
                            <nav class="side-nav">
                                <ul class="menu-vertical sf-arrows">
                                @foreach($categories->slice(0, 13) as $category)
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

                <div class="col col-lg-6 col-xl-6 col-xxl-8 header-center">
                    <nav class="main-nav">
                    </nav>
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
                        <li class="{{ request()->routeIs('frontend.shopdetail') ? 'active' : '' }}">
                            <a href="{{ route('frontend.shopdetail') }}">About Us</a>
                        </li>
                        <li class="{{ request()->routeIs('frontend.contact') ? 'active' : '' }}">
                            <a href="{{ route('frontend.contact') }}">Contact Us</a>
                        </li>
                    </ul>
                </div>

                <div class="col col-lg-3 col-xl-3 col-xxl-2 header-right">
                    <i class="la la-lightbulb-o"></i><p>Clearance Up to 30% Off</span></p>
                </div>
                
            </div>
        </div>
    </div>

</header>