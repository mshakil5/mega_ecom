<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale())}}">

    @php

        $company = \App\Models\CompanyDetails::select('fav_icon', 'company_name', 'design', 'footer_content', 'address1', 'email1', 'phone1', 'company_logo', 'facebook', 'twitter', 'instagram', 'youtube', 'currency')->first();

        $currency = $company->currency;

        $categories = \App\Models\Category::where('status', 1)
        ->select('id', 'name', 'slug')
        ->with(['products' => function($query) {
            $query->select('id', 'category_id', 'name', 'slug')
                ->orderBy('watch', 'desc')
                ->limit(20);
        }])
        ->get();

        $brands = \App\Models\Brand::where('status', 1)
        ->select('id', 'name')
        ->with(['products' => function($query) {
            $query->select('id', 'brand_id', 'name', 'slug')
                ->orderBy('watch', 'desc')
                ->limit(20);
        }])
        ->get();

        $brands = \App\Models\Brand::where('status', 1)
        ->select('id', 'name')
        ->with(['products' => function($query) {
            $query->select('id', 'brand_id', 'name', 'slug')
                ->orderBy('watch', 'desc')
                ->limit(20);
        }])
        ->get();

        $sub_categories = \App\Models\SubCategory::where('status', 1)
        ->select('id', 'name')
        ->with(['products' => function($query) {
            $query->select('id', 'sub_category_id', 'brand_id', 'name', 'slug')
                ->orderBy('watch', 'desc')
                ->limit(20);
        }])
        ->get();

        $advertisements = \App\Models\Ad::where('status', 1)->select('type', 'link', 'image')->get();

    @endphp  

<head>
    <meta charset="utf-8">
    <title>@yield('title', $company->company_name)</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">
    <meta name="theme-color" content="#ffffff">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/company/' . $company->fav_icon) }}">

    <link rel="stylesheet" href="{{ asset('frontend/css/line-awesome/css/line-awesome.min.css') }}">

    <link rel="stylesheet" href="{{ asset('frontend/css/bootstrap.min.css') }}">

    <link rel="stylesheet" href="{{ asset('frontend/css/owl-carousel/owl.carousel.css') }}">

    <link rel="stylesheet" href="{{ asset('frontend/css/magnific-popup/magnific-popup.css') }}">

    <link rel="stylesheet" href="{{ asset('frontend/css/toastr.min.css') }}">

    <style>
        .toast {
            font-size: 15px !important;
        }
    </style>

    <link rel="stylesheet" href="{{ asset('frontend/css/jquery.countdown.css') }}">

    <link rel="stylesheet" href="{{ asset('frontend/css/style.css') }}">

    @if($company->design == '1')

    <link rel="stylesheet" href="{{ asset('frontend/css/skin-demo-4.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/demo-4.css') }}">

    @elseif ($company->design == '2')

    <link rel="stylesheet" href="{{ asset('frontend/css/skin-demo-3.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/demo-3.css') }}">

    @elseif ($company->design == '3')

    <link rel="stylesheet" href="{{ asset('frontend/css/skin-demo-17.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/demo-17.css') }}">

    @elseif ($company->design == '4')

    <link rel="stylesheet" href="{{ asset('frontend/css/skin-demo-10.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/demo-10.css') }}">

    @elseif ($company->design == '5')

    <link rel="stylesheet" href="{{ asset('frontend/css/skin-demo-14.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/demo-14.css') }}">

    @endif

    <link rel="stylesheet" href="{{ asset('frontend/css/fontawesome/css/all.min.css')}}">

    <link rel="stylesheet" href="{{ asset('frontend/css/nouislider.css')}}">

    <!-- Data table css -->
    <link rel="stylesheet" href="{{ asset('assets/admin/datatables/dataTables.bootstrap4.min.css')}}">

</head>

<body>
    <div class="page-wrapper">

        <!-- Header Start -->
        @if($company->design == '5')

        @include('frontend.inc.header5')

        @else

        @include('frontend.inc.header')
        
        @endif
        <!-- Header End -->

        <!-- Main Content Start -->
        <main class="main">
        @yield('content')
        </main>
        <!-- Main Content End -->

        @include('frontend.modals.add_to_cart_modal')

        <!-- Footer Start -->
        @if($company->design == '5')

        @include('frontend.inc.footer5')

        @else

        @include('frontend.inc.footer')
        
        @endif
        <!-- Footer End -->
    </div>

        <!-- Mobile Menu Start -->

        @include('frontend.inc.mobile-menu')

        <!-- Mobile Menu End -->

    <script src="{{ asset('frontend/js/jquery.min.js') }}"></script>

    <script src="{{ asset('frontend/js/bootstrap.bundle.min.js') }}"></script>

    <script src="{{ asset('frontend/js/jquery.hoverIntent.min.js') }}"></script>

    <script src="{{ asset('frontend/js/jquery.waypoints.min.js') }}"></script>

    <script src="{{ asset('frontend/js/superfish.min.js') }}"></script>

    <script src="{{ asset('frontend/js/owl.carousel.min.js') }}"></script>

    <script src="{{ asset('frontend/js/bootstrap-input-spinner.js') }}"></script>

    <script src="{{ asset('frontend/js/jquery.plugin.min.js') }}"></script>

    <script src="{{ asset('frontend/js/jquery.magnific-popup.min.js') }}"></script>

    <script src="{{ asset('frontend/js/jquery.countdown.min.js') }}"></script>

    <script src="{{ asset('frontend/js/main.js') }}"></script>

    <script src="{{ asset('frontend/js/demo-4.js') }}"></script>

    <script src="{{ asset('frontend/js/moment.min.js')}}"></script>

    <script src="{{ asset('assets/admin/js/sweetalert.min.js')}}"></script>

    <script src="{{ asset('frontend/js/nouislider.min.js')}}"></script>

    <script src="{{ asset('frontend/js/wNumb.js')}}"></script>

    <script src="{{ asset('frontend/js/toastr.min.js')}}"></script>

    <script>
        toastr.options = {
            "closeButton": false,
            "debug": false,
            "newestOnTop": false,
            "progressBar": false,
            "positionClass": "toast-top-center",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
    </script>

    <!-- Data table js -->
    <script src="{{ asset('assets/admin/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset('assets/admin/datatables/dataTables.bootstrap4.min.js')}}"></script>

    @yield('script')

    @include('frontend.partials.wishlist_script')
    @include('frontend.partials.add_to_cart_script')
    @include('frontend.partials.search_script')
    @include('frontend.modals.add_to_cart_modal_script')
    
    @if(session('session_clear'))
        <script>
            localStorage.removeItem('wishlist');
            localStorage.removeItem('cart');
            @php
                session()->forget('session_clear');
            @endphp
        </script>
    @endif

</body>

</html>