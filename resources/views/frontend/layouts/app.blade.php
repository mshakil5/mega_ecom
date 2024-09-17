<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale())}}">

    @php

        $company = \App\Models\CompanyDetails::select('fav_icon', 'company_name', 'design', 'footer_content', 'address1', 'email1', 'phone1', 'company_logo')->first();

        $categories = \App\Models\Category::where('status', 1)
        ->with(['products' => function($query) {
            $query->orderBy('watch', 'desc')->limit(20);
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

    @endif

    <link rel="stylesheet" href="{{ asset('frontend/css/fontawesome/css/all.min.css')}}">

    <!-- Data table css -->
    <link rel="stylesheet" href="{{ asset('assets/admin/datatables/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/admin/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/admin/datatables-buttons/css/buttons.bootstrap4.min.css')}}">

</head>

<body>
    <div class="page-wrapper">

        <!-- Header Start -->
        @include('frontend.inc.header')
        <!-- Header End -->

        
        <!-- Main Content Start -->
        <main class="main">
        @yield('content')
        </main>
        <!-- Main Content End -->
    

        <!-- Footer Start -->
        @include('frontend.inc.footer')
        <!-- Footer End -->
    </div>

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

    <!-- Data table js -->
    <script src="{{ asset('assets/admin/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset('assets/admin/datatables/dataTables.bootstrap4.min.js')}}"></script>

    <script src="{{ asset('assets/admin/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{ asset('assets/admin/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>

    <script src="{{ asset('assets/admin/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{ asset('assets/admin/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>
    <script src="{{ asset('assets/admin/datatables/jszip/jszip.min.js')}}"></script>
    <script src="{{ asset('assets/admin/datatables/pdfmake/pdfmake.min.js')}}"></script>
    <script src="{{ asset('assets/admin/datatables/pdfmake/vfs_fonts.js')}}"></script>

    <script src="{{ asset('assets/admin/datatables-buttons/js/buttons.html5.min.js')}}"></script>
    <script src="{{ asset('assets/admin/datatables-buttons/js/buttons.print.min.js')}}"></script>
    <script src="{{ asset('assets/admin/datatables-buttons/js/buttons.colVis.min.js')}}"></script>

    @yield('script')

    @include('frontend.partials.wishlist_script')
    @include('frontend.partials.add_to_cart_script')
    @include('frontend.partials.search_script')
    
</body>

</html>