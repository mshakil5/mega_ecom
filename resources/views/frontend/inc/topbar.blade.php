@php
    $company = \App\Models\CompanyDetails::select('company_logo', 'address1', 'phone1')->first();
@endphp

<style>

    .custom-ad-image img {
        width: 100%;
        max-height: 180px;
        object-fit: cover;
    }

    @media (max-width: 768px) {
        .custom-ad-image img {
            max-height: 120px;
            margin-bottom: 10px; 
            padding-bottom: 10px;
        }
    }

    @media (max-width: 576px) {
        .custom-ad-image img {
            max-height: 100px;
            margin-bottom: 8px;
            padding-bottom: 8px;
        }
    }
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

<div class="container-fluid">
    <div class="row bg-secondary py-1 px-xl-5">
        <div class="col-lg-6 d-none d-lg-block">
            <div class="d-inline-flex align-items-center h-100">
                <div class="text-body mr-3">{{ $company->address1 }}</div>
            </div>
        </div>
        <div class="col-lg-6 text-center text-lg-right">
            <div class="d-inline-flex align-items-center">
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-light dropdown-toggle" data-toggle="dropdown">My Account</button>
                    <div class="dropdown-menu dropdown-menu-right">
                        @if(Auth::guard('supplier')->check())
                            <button class="dropdown-item" type="button" onclick="location.href='{{ route('supplier.dashboard') }}'">{{ Auth::guard('supplier')->user()->name }}</button>
                        @elseif(Auth::check())
                            @if(auth()->user()->is_type == '1')
                                <button class="dropdown-item" type="button" onclick="location.href='{{ route('admin.dashboard') }}'">{{ auth()->user()->name }}</button>
                            @elseif(auth()->user()->is_type == '0')
                                <button class="dropdown-item" type="button" onclick="location.href='{{ route('user.dashboard') }}'">{{ auth()->user()->name }}</button>
                            @endif
                        @else
                            <button class="dropdown-item" type="button" onclick="location.href='{{ route('login') }}'">Sign in</button>
                            <button class="dropdown-item" type="button" onclick="location.href='{{ route('register') }}'">Sign up</button>
                            <button class="dropdown-item" type="button" onclick="location.href='{{ route('supplier.login') }}'">Sign in As Supplier</button>
                        @endif
                    </div>
                </div>
            </div>
            <div class="d-inline-flex align-items-center d-block d-lg-none">
                <a href="{{ route('cart.index') }}" class="btn px-0 ml-2 cartBtn">
                    <i class="fas fa-shopping-cart text-dark"></i>
                    <span class="badge text-dark border border-dark rounded-circle cartCount" style="padding-bottom: 2px;">0</span>
                </a>
                <a href="{{ route('wishlist.index') }}" class="btn px-0 ml-2 wishlistBtn">
                    <i class="fas fa-heart text-dark"></i>
                    <span class="badge text-dark border border-dark rounded-circle wishlistCount" style="padding-bottom: 2px;">0</span>
                </a>       
            </div>
        </div>
    </div>

    @php
        $advertisements = \App\Models\Ad::where('status', 1)->select('type', 'link', 'image')->get();
    @endphp

        @foreach($advertisements as $advertisement)
            @if($advertisement->type == 'home_page_top_bar')
                <div class="advertisement-image custom-ad-image py-3 px-xl-5">
                    <a href="{{ $advertisement->link }}" target="_blank">
                        <img src="{{ asset('images/ads/' . $advertisement->image) }}" class="img-fluid" alt="Advertisement">
                    </a>
                </div>
            @endif
        @endforeach

    <div class="row align-items-center bg-light py-3 px-xl-5 d-none d-lg-flex">
        <div class="col-lg-4">
            <a href="{{ route('frontend.homepage') }}" class="text-decoration-none">
            @if (!empty($company) && !empty($company->company_logo))
                <img src="{{ asset('images/company/' . $company->company_logo) }}" alt="Company Logo" class="img-fluid" style="max-height: 75px;">
            @endif
            </a>
        </div>
        <div class="col-lg-4 col-6 text-left">
            <form id="search-form" class="position-relative">
                <div class="input-group">
                    <input type="text" id="search-input" class="form-control" placeholder="Search for products">
                    <div class="input-group-append">
                        <span class="input-group-text bg-transparent text-primary" id="search-icon" style="cursor: pointer;">
                            <i class="fa fa-search"></i>
                        </span>
                    </div>
                </div>
            </form>
            <div id="search-results" class="bg-light position-absolute w-100" style="z-index: 1000;"></div>
        </div>
        <div class="col-lg-4 col-6 text-right">
            <p class="m-0">Customer Service</p>
            <h5 class="m-0">
                @if (!empty($company) && !empty($company->phone1))
                    {{ $company->phone1 }}
                @endif
            </h5>
        </div>
    </div>
</div>