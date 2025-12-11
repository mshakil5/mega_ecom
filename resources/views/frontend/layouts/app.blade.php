<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale())}}">
    @php

        $company = \App\Models\CompanyDetails::select('fav_icon', 'company_name', 'design', 'footer_content', 'address1', 'email1', 'phone1', 'company_logo', 'facebook', 'twitter', 'instagram', 'youtube', 'currency')->first();
        $currency = $company->currency;

        $topCategories = \App\Models\Category::with(['subcategories' => function($q){
                $q->where('status', 1)->orderBy('name');
            }])
            ->where('status', 1)
            ->withCount('subcategories')
            ->orderByDesc('subcategories_count')
            ->take(3)
            ->get();

    @endphp  

<head>
    <meta charset="UTF-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', $company->company_name)</title>

    <!-- 🔥🔥 SEO OPTIMIZATION START — Sapphire Trade Links 🔥🔥 -->

    <!-- Basic SEO -->
    <meta name="title" content="Sapphire Trade Links | Premium Clothing, Polo, Kurti, Hoodies & More">
    <meta name="description" content="Sapphire Trade Links UK — Premium fashion clothing including Polo shirts, T-shirts, Kurti, Hoodies, Sportswear & more. Fast delivery all over the UK. Exclusive new arrivals every week.">
    <meta name="keywords" content="sapphire trade links, clothing, polo shirt, kurti, hoodie, t shirt, fashion uk, online shopping uk, apparel wholesale, premium fashion">
    <meta name="author" content="Sapphire Trade Links UK">
    <meta name="robots" content="index, follow">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://www.sapphiretradelinks.co.uk/">

    <!-- Open Graph (Facebook/LinkedIn) -->
    <meta property="og:title" content="Sapphire Trade Links UK – Premium Fashion & Lifestyle">
    <meta property="og:description" content="Shop premium quality polo shirts, kurtis, hoodies, jackets and more. UK fast delivery. Trusted apparel brand.">
    <meta property="og:image" content="https://www.sapphiretradelinks.co.uk/images/logo.png">
    <meta property="og:url" content="https://www.sapphiretradelinks.co.uk/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Sapphire Trade Links">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Sapphire Trade Links UK – Premium Fashion & Lifestyle">
    <meta name="twitter:description" content="Premium polo shirts, hoodies, kurti, kids wear & more. New collections added weekly.">
    <meta name="twitter:image" content="https://www.sapphiretradelinks.co.uk/images/logo.png">

    <!-- Favicon & Icons -->
    <link rel="icon" type="image/png" href="https://www.sapphiretradelinks.co.uk/images/logo.png">
    <link rel="apple-touch-icon" href="https://www.sapphiretradelinks.co.uk/images/logo.png">

    <!-- Preload Fonts (optional but recommended for speed) -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" as="style">


    <!-- Bootstrap CSS (kept from your original) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Google Font (Inter) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('frontend/css/toastr.min.css') }}">

    <!-- Project CSS -->
    <link rel="stylesheet" href="{{ asset('frontend/v2/css/style.css') }}">

</head>
<body>


        @include('frontend.inc.header')
        @yield('content')
        @include('frontend.inc.footer')





    <!-- External vendor scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <!-- Project main JS -->
    <script src="{{ asset('frontend/v2/js/main.js') }}" defer></script>

    <script src="{{ asset('frontend/js/toastr.min.js')}}"></script>

    <script>
        toastr.options = {
            "closeButton": false,
            "debug": false,
            "newestOnTop": false,
            "progressBar": false,
            "positionClass": "toast-top-right",
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
