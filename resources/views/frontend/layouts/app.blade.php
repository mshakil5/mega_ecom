<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale())}}">

    @php
        $company = \App\Models\CompanyDetails::select('fav_icon', 'company_name')->first();
    @endphp  

<head>
    <meta charset="utf-8">
    <title>@yield('title', $company->company_name)</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/company/' . $company->fav_icon) }}">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">  

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/fontawesome-free/css/all.min.css')}}">

    <!-- Libraries Stylesheet -->
    <link rel="stylesheet" href="{{ asset('assets/frontend/lib/animate/animate.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/frontend/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">

    <!-- Customized Stylesheet -->
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/style.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/custom.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/frontend/lib/jquery/jquery.dataTables.min.css')}}" rel="stylesheet">
</head>

<body>
    <!-- Topbar Start -->
    @include('frontend.inc.topbar')
    <!-- Topbar End -->


    <!-- Navbar Start -->
    @include('frontend.inc.navbar')
    <!-- Navbar End -->


    <!-- Main Content Start -->
    @yield('content')
    <!-- Main Content End -->
   

    <!-- Footer Start -->
    @include('frontend.inc.footer')
    <!-- Footer End -->


    <!-- Back to Top -->
    <a class="btn btn-primary back-to-top"><i class="fa fa-angle-double-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="{{ asset('assets/admin/js/jquery.min.js')}}"></script>
    <script src="{{ asset('assets/admin/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{ asset('assets/frontend/lib/easing/easing.min.js')}}"></script>
    <script src="{{ asset('assets/frontend/lib/owlcarousel/owl.carousel.min.js')}}"></script>
    <script src="{{ asset('assets/frontend/lib/sweet-alert/sweetalert.min.js')}}"></script>
    <script src="{{ asset('assets/frontend/lib/jquery/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset('assets/frontend/lib/moment/moment.min.js')}}"></script>


    <!-- Main Javascript -->
    <script src="{{ asset('assets/frontend/js/main.js')}}"></script>

    @yield('script')

    @include('frontend.partials.wishlist_script')
    @include('frontend.partials.add_to_cart_script')
    @include('frontend.partials.search_script')
    
</body>

</html>