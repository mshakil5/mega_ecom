@extends('frontend.layouts.app')

@section('content')
    


    <!-- HERO CAROUSEL -->
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>

        <div class="carousel-inner">
            <div class="carousel-item slide-2">
                <div class="carousel-caption d-block text-center position-relative">
                    <h2 class="display-3 fw-bolder">50% OFF FOR LIMITED TIME</h2>
                    <p class="lead">Check out our new season essentials before they run out.</p>
                    <a href="#" class="btn btn-light btn-lg mt-3">Shop Essentials</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom strip + promo text + new arrival -->
    <div class="bottom-strip border-top border-bottom">
        <div class="container-fluid">
            <div class="row text-center align-items-center">
                <div class="col-md-2 col-3"><a href="#">SHOP</a></div>
                <div class="col-md-2 col-3"><a href="#">MEN</a></div>
                <div class="col-md-2 col-3"><a href="#">WOMEN</a></div>
                <div class="col-md-2 col-3"><a href="#">KIDS</a></div>

                <div class="col-md-4 col-12 d-flex justify-content-center justify-content-md-end align-items-center p-2">
                    <p class="mb-0 me-3 fw-bold text-success d-none d-lg-block">GET 5% OFF ON APP</p>
                    <a href="#" class="me-2"><img src="https://placehold.co/120x35/000000/ffffff?text=GET+IT+ON+Google+Play" alt="Google Play" class="img-fluid rounded"></a>
                    <a href="#"><img src="https://placehold.co/120x35/000000/ffffff?text=Download+on+the+App+Store" alt="App Store" class="img-fluid rounded"></a>
                </div>
            </div>
        </div>
    </div>

    <div class="promo-text-row">
        <div class="container">
            <strong>Event T-shirt</strong> • T-shirt/Clothing with your brand logo or design? We are delivering worldwide at unbeatable prices. <a href="#">Click here <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>

    <div class="new-arrival-banner mb-5">
        <div class="container text-center">
            <h2 class="display-5 fw-bolder text-uppercase" style="letter-spacing: 5px;">NEW ARRIVAL</h2>
        </div>
    </div>

    <!-- Category grid (static) -->
    <!-- 7. Category Product Grid -->
    <div class="container mb-5">

        <div class="row justify-content-center gx-2 gx-md-3"> 
            

            <!-- Row 1: 6 Categories (Desktop) -->
            @foreach ($categories as $categoriesItem)
                
            <div class="col-6 col-sm-4 col-md-3 col-lg-2 category-col">
                <div class="category-card">
                    <div class="product-image-container">
                    <span class="category-name">{{$categoriesItem->name}}</span>
                        @if ($categoriesItem->image)
                            <img src="{{ asset('/images/category/' . $categoriesItem->image) }}" alt="{{$categoriesItem->name}}" class="img-fluid">
                        @else
                            <img src="https://fabrilife.com/image-gallery/638741f4b169a-square.jpg" alt="{{$categoriesItem->name}}" class="img-fluid">
                        @endif
                    </div>
                </div>
            </div>
            
            @endforeach

            
        </div>
    </div>
    <!-- END OF NEW CONTENT -->

    <!-- MAIN content sections for Men/Women product lists (JS injects product cards) -->
    <main class="py-4">
        <section class="container section-container">
            <h2 class="fs-2 fw-bold mb-4 text-dark">New Arrivals - Men's Polo</h2>

            <div class="row g-4">
                <div class="col-12 col-lg-4">
                    <div class="promo-overlay" style="background-image: url('https://placehold.co/800x1200/262626/ffffff?text=AURUM+POLO+%7C+DESIGNER+POLO');">
                        <div class="promo-text-box">
                            <h3 class="fs-3 fw-bold">AURUM POLO</h3>
                            <p class="mb-0 small">Designer Polo</p>
                        </div>
                        <div class="position-absolute top-0 end-0 bg-white text-dark fw-bold px-3 py-1 rounded-pill small premium-badge">PREMIUM</div>
                    </div>
                </div>

                <div class="col-12 col-lg-8">
                    <div class="row g-3">
                        @foreach ($recentProducts as $product)
                            <div class="col-6 col-md-4 col-lg-3 mb-4 product-item-card">
                                <a href="{{ route('product.show', $product->slug) }}" class="card bg-white product-card text-decoration-none text-dark">

                                    <div class="product-image-container">
                                        <img src="{{ asset('images/products/' . $product->feature_image) }}" alt="{{ $product->name }}" class="img-fluid">
                                        <span class="badge position-absolute top-0 start-0 product-tag">{{ strtoupper($product->category->name ?? '') }} - {{$product->id}}</span>
                                    </div>
                                    <div class="product-price">
                                        <div><strong>£{{ number_format($product->price, 2) }}</strong> <strike>£{{ number_format($product->price, 2) }}</strike></div>
                                    </div>

                                </a>
                            </div>


                        @endforeach


                        <div class="col-6 col-md-4 col-lg-3 mb-4">
                            <a href="#" class="card view-more-card" style="min-height: 220px;">
                                <div class="overlay"></div>
                                <div class="view-more-content fw-bold fs-4">
                                    <i data-lucide="eye" class="mb-2"></i>
                                    <div class="tracking-wider"></div>
                                </div>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <section class="container section-container">
            <h2 class="fs-2 fw-bold mb-4 text-dark">Trending - Kurti & Tops</h2>

            <div class="row g-4">
                <div class="col-12 col-lg-4">
                    <div class="promo-overlay" style="background-image: url('https://placehold.co/800x1200/c084fc/ffffff?text=KURTI%2C+TUNIC+%26+TOPS');">
                        <div class="promo-text-box">
                            <h3 class="fs-3 fw-bold">Kurti, Tunic & Tops</h3>
                            <p class="mb-0 small">Latest Collection</p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-8">
                    <div class="row g-3" id="women-products-container"></div>
                </div>
            </div>
        </section>

        <section class="container section-container">
            <div class="row g-4">
                <div class="col-12 col-md-4">
                    <a href="#" class="d-block text-decoration-none">
                        <div class="promo-overlay bg-white" style="background-image: url('https://placehold.co/800x600/475569/ffffff?text=MATTE+BLACK+POLO'); min-height: 400px;">
                            <div class="promo-text-box p-4">
                                <h3 class="fs-2 fw-bolder">MATTE BLACK</h3>
                                <p class="fs-5 mb-0">Classic Polo</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-12 col-md-4">
                    <a href="#" class="d-block text-decoration-none">
                        <div class="promo-overlay bg-white" style="background-image: url('https://placehold.co/800x600/4f46e5/ffffff?text=SYNCHRONIZER+POLO'); min-height: 400px;">
                            <div class="promo-text-box p-4">
                                <h3 class="fs-2 fw-bolder">SYNCHRONIZER</h3>
                                <p class="fs-5 mb-0">Designer Polo</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-12 col-md-4">
                    <a href="#" class="d-block text-decoration-none">
                        <div class="promo-overlay bg-white" style="background-image: url('https://placehold.co/800x600/f97316/ffffff?text=KIDS+POLO+COLLECTION'); min-height: 400px;">
                            <div class="promo-text-box p-4">
                                <h3 class="fs-2 fw-bolder">THE LITTLE ONE</h3>
                                <p class="fs-5 mb-0">Kids Polo</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </section>
    </main>






{{-- <style>
    #cookiebar {
            position: fixed;
            bottom: 0;
            left: 5px;
            right: 5px;
            display: none;
            z-index: 200;
        }

    #cookiebarBox {
        position: fixed;
        bottom: 0;
        left: 5px;
        right: 5px;
        // display: none;
        z-index: 200;
    }
    .containerrr {
        border-radius: 3px;
        background-color: white;
        color: #626262;
        margin-bottom: 10px;
        padding: 10px;
        overflow: hidden;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        position: fixed;
        padding: 20px;
        background-color: #fff;
        bottom: -10px;
        width: 100%;
        -webkit-box-shadow: 2px 2px 19px 6px #00000029;
        box-shadow: 2px 2px 19px 6px #00000029;
        border-top: 1px solid #356ffd1c;
    }
    .cookieok {
        -moz-border-radius: 3px;
        -webkit-border-radius: 3px;
        border-radius: 3px;
        background-color: #e8f0f3;
        color: #186782 !important;
        font-weight: 600;
        // float: right;
        line-height: 2.5em;
        height: 2.5em;
        display: block;
        padding-left: 30px;
        padding-right: 30px;
        border-bottom-width: 0 !important;
        cursor: pointer;
        max-width: 200px;
        margin: 0 auto;

    }
</style>

<div id="cookiebarBox" class="os-animation" data-os-animation="fadeIn" >
    <div class="containerrr risk-dismiss " style="display: flex;" >
          <div class="container">
            <div class="row">
                <div class="col-md-9">
                <p class="text-left">
              <h1 class="d-inline text-primary"><span class="iconify" data-icon="iconoir:half-cookie"></span> </h1>
              Like most websites, this site uses cookies to assist with navigation and your ability to provide feedback, analyse your use of products and services so that we can improve them, assist with our personal promotional and marketing efforts and provide consent from third parties.
            </p>
            </div>
                <div class="col-md-3 d-flex align-items-center justify-content-center">
                    <a id="cookieBoxok" class="btn btn-sm cookie-btn my-3 px-4 text-center" data-cookie="risk">Accept</a>
                </div>
            </div>
          </div>
    </div>
</div> --}}

@endsection

@section('script')

<script>
    // if you want to see a cookie, delete 'seen-cookiePopup' from cookies first.

    jQuery(document).ready(function($) {
    // Get CookieBox
    var cookieBox = document.getElementById('cookiebarBox');
        // Get the <span> element that closes the cookiebox
    var closeCookieBox = document.getElementById("cookieBoxok");
        closeCookieBox.onclick = function() {
            cookieBox.style.display = "none";
        };
    });

    (function () {

        /**
         * Set cookie
         *
         * @param string name
         * @param string value
         * @param int days
         * @param string path
         * @see http://www.quirksmode.org/js/cookies.html
         */
        function createCookie(name, value, days, path) {
            var expires = "";
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toGMTString();
            }
            else expires = "";
            document.cookie = name + "=" + value + expires + "; path=" + path;
        }

        function readCookie(name) {
            var nameEQ = name + "=";
            var ca = document.cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        }

        // Set/update cookie
        var cookieExpiry = 30;
        var cookiePath = "/";

        document.getElementById("cookieBoxok").addEventListener('click', function () {
            createCookie('seen-cookiePopup', 'yes', cookieExpiry, cookiePath);
        });

        var cookiePopup = readCookie('seen-cookiePopup');
        if (cookiePopup != null && cookiePopup == 'yes') {
            cookiebarBox.style.display = 'none';
        } else {
            cookiebarBox.style.display = 'block';
        }
    })();

</script>
@endsection