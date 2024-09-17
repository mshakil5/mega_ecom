@extends('frontend.layouts.app')

@section('content')
    <!-- Intro Slider Start-->
    @if($section_status->slider == 1)
    <div class="intro-section pt-3 pb-3 mb-2">
        <div class="container">
            <div class="row">
                <!-- First Column (Slider) -->
                <div class="col-lg-8">
                    <div class="intro-slider-container slider-container-ratio mb-2 mb-lg-0">
                        <div class="intro-slider owl-carousel owl-simple owl-dark owl-nav-inside" data-toggle="owl" 
                            data-owl-options='{
                                "dots": true,
                                "nav": false, 
                                "responsive": {
                                    "1200": {
                                        "nav": true,
                                        "dots": false
                                    }
                                }
                            }'>
                            @foreach($sliders as $slider)
                                <div class="intro-slide" style="background-image: url('{{ asset('images/slider/' . $slider->image) }}'); background-size: cover; background-position: center; height: 500px; display: flex; align-items: center; justify-content: flex-end;">
                                    <div class="container intro-content">
                                        <div class="row justify-content-end">
                                            <div class="col-auto col-sm-7 col-md-6 col-lg-5">
                                                <h3 class="intro-subtitle text-third">{{ $slider->sub_title }}</h3>
                                                <h1 class="intro-title">{{ $slider->title }}</h1>
                                                @if($slider->link)
                                                    <a href="{{ $slider->link }}" class="btn btn-primary btn-round">
                                                        <span>Shop More</span>
                                                        <i class="icon-long-arrow-right"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <span class="slider-loader"></span>
                    </div>
                </div>
                <!-- End of First Column -->

                <!-- Second Column (Categories) -->
                <div class="col-lg-4">
                    <div class="intro-banners">
                    @foreach($categories->take(3) as $category)
                        <div class="banner mb-lg-1 mb-xl-2">
                            <a href="{{ route('category.show', $category->slug) }}">
                            <img src="{{ asset('images/category/' . $category->image) }}" alt="{{ $category->name }}"style="width: 370px; height: 153px; object-fit: cover;">
                            </a>

                            <div class="banner-content">
                                <h4 class="banner-title d-lg-none d-xl-block">
                                    <a href="{{ route('category.show', $category->slug) }}">{{ $category->name }}</a>
                                </h4>
                                <a href="{{ route('category.show', $category->slug) }}" class="banner-link">
                                    Shop Now<i class="icon-long-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                    </div>
                </div>
                <!-- End of Second Column -->
            </div>
        </div>
    </div>
    @endif
    <!-- Intro Slider End -->

    {{--  
    <!-- Categories Start -->
    @if($section_status->categories == 1)
    <div class="container">
        <h2 class="title text-center mb-4">Explore Popular Categories</h2>
        <div class="cat-blocks-container">
            <div class="row justify-content-center">            
                @foreach($categories as $category)
                    <div class="col-6 col-sm-4 col-lg-2 mb-4 d-flex justify-content-center">
                        <a href="{{ route('category.show', $category->slug) }}" class="cat-block text-center">
                            <figure>
                                <span>
                                    <img src="{{ asset('images/category/' . $category->image) }}" alt="{{ $category->name }}" style="width: 200px;">
                                </span>
                            </figure>
                            <h3 class="cat-block-title">{{ $category->name }}</h3>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
    <!-- Categories End -->
    --}}
    
    <div class="mb-4"></div>

    <!-- Special Offer Start -->
    @if($section_status->special_offer == 1)
    <div class="row justify-content-center">
        @foreach($specialOffers as $specialOffer)
            <div class="col-md-6 col-lg-4">
                <div class="banner banner-overlay banner-overlay-light">
                    <a href="{{ route('special-offers.show', $specialOffer->slug) }}">
                        <img src="{{ asset('images/special_offer/' . $specialOffer->offer_image) }}" alt="Banner">
                    </a>
                    <div class="banner-content">
                        <h4 class="banner-subtitle">
                            <a href="{{ route('special-offers.show', $specialOffer->slug) }}">
                                {{ $specialOffer->offer_name }}
                            </a>
                        </h4>
                        <h3 class="banner-title">
                            <a href="{{ route('special-offers.show', $specialOffer->slug) }}">
                                <strong>{{ $specialOffer->offer_title }}</strong>
                            </a>
                        </h3>
                        <a href="{{ route('special-offers.show', $specialOffer->slug) }}" class="banner-link">
                            Shop Now<i class="icon-long-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @endif
    <!-- Special Offer End -->

    <div class="mb-3"></div>

    <!-- Category products slider Start-->
    @if ($section_status->category_products == 1)
    <div class="container new-arrivals">   
        <div class="heading heading-flex mb-3">
            <div class="heading-left" style="display:none;">
                <h2 class="title">Category Products</h2>
            </div>
            <div class="heading-right" style="width: 100%; text-align: center;">
                <ul class="nav nav-pills nav-border-anim nav-big justify-content-center" role="tablist">
                    @foreach($categories as $index => $category)
                        <li class="nav-item">
                            <a class="nav-link {{ $index == 0 ? 'active' : '' }}" 
                            id="category-{{ $category->id }}-link" 
                            data-toggle="tab" 
                            href="#category-{{ $category->id }}-tab" 
                            role="tab" 
                            aria-controls="category-{{ $category->id }}-tab" 
                            aria-selected="{{ $index == 0 ? 'true' : 'false' }}">
                                {{ $category->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="tab-content">
            @foreach($categories as $index => $category)
                <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}" id="category-{{ $category->id }}-tab" role="tabpanel" aria-labelledby="category-{{ $category->id }}-link">
                    <div class="owl-carousel owl-full carousel-equal-height carousel-with-shadow" data-toggle="owl" 
                        data-owl-options='{
                            "nav": true, 
                            "dots": true,
                            "margin": 20,
                            "loop": false,
                            "responsive": {
                                "0": {
                                    "items":2
                                },
                                "480": {
                                    "items":2
                                },
                                "768": {
                                    "items":3
                                },
                                "992": {
                                    "items":4
                                }
                            }
                        }'>
                        @foreach($category->products as $product)
                        <div class="product product-2">
                            <figure class="product-media">
                                <a href="{{ route('product.show', $product->slug) }}">
                                    <img src="{{ asset('images/products/' . $product->feature_image) }}" alt="{{ $product->name }}" class="product-image">
                                </a>
                                @if ($product->stock && $product->stock->quantity > 0)
                                    <div class="product-action-vertical">
                                        <a href="#" class="btn-product-icon btn-wishlist add-to-wishlist" 
                                        title="Add to wishlist" 
                                        data-product-id="{{ $product->id }}" 
                                        data-offer-id="0" 
                                        data-price="{{ $product->price }}">      
                                        </a>
                                    </div>
                                    <div class="product-action">
                                        <a href="#" class="btn-product btn-cart add-to-cart" title="Add to cart" data-product-id="{{ $product->id }}" data-offer-id="0" data-price="{{ $product->price }}"><span>add to cart</span></a>
                                    </div>
                                @else
                                    <span class="product-label label-out-stock">Out of stock</span>
                                @endif
                            </figure>
                            <div class="product-body">
                                <div class="product-cat">
                                    <a href="{{ route('category.show', $category->slug) }}">{{ $category->name }}</a>
                                </div>
                                <h3 class="product-title"><a href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a></h3>
                                <div class="product-price">
                                    ${{ number_format($product->price, 2) }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
    <!-- Category products slider End-->

    <div class="mb-6"></div>

    <!-- Recent advertisements start-->
    <div class="container">
        @foreach($advertisements as $advertisement)
            @if($advertisement->type == 'recent')
                <div class="cta cta-border mb-5" style="background-image: url('{{ asset('images/ads/' . $advertisement->image) }}');">
                    <div class="row justify-content-center">
                        <div class="col-md-12">
                            <div class="cta-content">
                                <div class="cta-text text-right text-white">
                                </div>
                                <a href="{{ $advertisement->link }}" class="btn btn-primary btn-round" target="_blank">
                                    <span>Shop Now</span><i class="icon-long-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
    <!-- Recent advertisements end-->

    <!-- Recent Products Start -->
    @if($section_status->recent_products == 1)
    <div class="pt-5 pb-6">
        <div class="container trending-products">
            <div class="heading heading-flex mb-3">
                <div class="heading-left">
                    <h2 class="title">Recent Products</h2>
                </div>
            </div>

            <div class="owl-carousel owl-full carousel-equal-height carousel-with-shadow" data-toggle="owl" 
                data-owl-options='{
                    "nav": true, 
                    "dots": true,
                    "margin": 20,
                    "loop": false,
                    "responsive": {
                        "0": {
                            "items":2
                        },
                        "480": {
                            "items":2
                        },
                        "768": {
                            "items":3
                        },
                        "992": {
                            "items":4
                        }
                    }
                }'>
                @if ($recentProducts->count() > 0)
                    @foreach($recentProducts as $product)
                    <div class="product product-2">
                        <figure class="product-media">
                            <a href="{{ route('product.show', $product->slug) }}">
                                <img src="{{ asset('images/products/' . $product->feature_image) }}" alt="{{ $product->name }}" class="product-image">
                            </a>
                            @if ($product->stock && $product->stock->quantity > 0)
                                <div class="product-action-vertical">
                                    <a href="#" class="btn-product-icon btn-wishlist add-to-wishlist" title="Add to wishlist" data-product-id="{{ $product->id }}" data-offer-id="0" data-price="{{ $product->price }}"></a>
                                </div>
                                <div class="product-action">
                                    <a href="#" class="btn-product btn-cart add-to-cart" title="Add to cart" data-product-id="{{ $product->id }}" data-offer-id="0" data-price="{{ $product->price }}"><span>add to cart</span></a>
                                </div>
                            @else
                                <span class="product-label label-out-stock">Out of stock</span>
                            @endif
                        </figure>

                        <div class="product-body">
                            <div class="product-cat">
                                <a href="{{ route('category.show', $category->slug) }}">{{ $category->name }}</a>
                            </div>
                            <h3 class="product-title"><a href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a></h3>
                            <div class="product-price">
                                ${{ number_format($product->price, 2) }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    @endif
    <!-- Recent Products End -->

    <!-- Campaigns Start -->
    @if($section_status->campaigns == 1)
    <div class="row justify-content-center mt-5">
        @foreach($campaigns as $campaign)
        <div class="col-md-6 col-lg-4">
            <div class="banner banner-overlay banner-overlay-light">
                <a href="{{ route('campaign.details.frontend', $campaign->slug) }}">
                    <img src="{{ asset('images/campaign_banner/' . $campaign->banner_image) }}" alt="{{ $campaign->title }}">
                </a>

                <div class="banner-content">
                    <h3 class="banner-title">
                        <a href="{{ route('campaign.details.frontend', $campaign->slug) }}">
                            <strong>{{ $campaign->title }}</strong>
                        </a>
                    </h3>
                    <a href="{{ route('campaign.details.frontend', $campaign->slug) }}" class="banner-link">
                        Shop Now<i class="icon-long-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
    <!-- Campaigns End -->

    <!-- Supplier advertisements start-->
    <div class="container">
        @foreach($advertisements as $advertisement)
            @if($advertisement->type == 'vendor')
                <div class="cta cta-border mb-5" style="background-image: url('{{ asset('images/ads/' . $advertisement->image) }}');">
                    <div class="row justify-content-center">
                        <div class="col-md-12">
                            <div class="cta-content">
                                <div class="cta-text text-right text-white">
                                </div>
                                <a href="{{ $advertisement->link }}" class="btn btn-primary btn-round" target="_blank">
                                    <span>Shop Now</span><i class="icon-long-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
    <!-- Supplier advertisements end-->

    <!-- Trending Products Start -->
    @if($section_status->trending_products == 1)
    <div class="pt-5 pb-6">
        <div class="container trending-products">
            <div class="heading heading-flex mb-3">
                <div class="heading-left">
                    <h2 class="title">Trending Products</h2>
                </div>
            </div>

            <div class="owl-carousel owl-full carousel-equal-height carousel-with-shadow" data-toggle="owl" 
                data-owl-options='{
                    "nav": true, 
                    "dots": true,
                    "margin": 20,
                    "loop": false,
                    "responsive": {
                        "0": {
                            "items":2
                        },
                        "480": {
                            "items":2
                        },
                        "768": {
                            "items":3
                        },
                        "992": {
                            "items":4
                        }
                    }
                }'>
                @if ($trendingProducts->count() > 0)
                    @foreach($trendingProducts as $product)
                    <div class="product product-2">
                        <figure class="product-media">
                            <a href="{{ route('product.show', $product->slug) }}">
                                <img src="{{ asset('images/products/' . $product->feature_image) }}" alt="{{ $product->name }}" class="product-image">
                            </a>
                            @if ($product->stock && $product->stock->quantity > 0)
                                <div class="product-action-vertical">
                                    <a href="#" class="btn-product-icon btn-wishlist add-to-wishlist" title="Add to wishlist" data-product-id="{{ $product->id }}" data-offer-id="0" data-price="{{ $product->price }}"></a>
                                </div>
                                <div class="product-action">
                                    <a href="#" class="btn-product btn-cart add-to-cart" title="Add to cart" data-product-id="{{ $product->id }}" data-offer-id="0" data-price="{{ $product->price }}"><span>add to cart</span></a>
                                </div>
                            @else
                                <span class="product-label label-out-stock">Out of stock</span>
                            @endif
                        </figure>

                        <div class="product-body">
                            <div class="product-cat">
                                <a href="{{ route('category.show', $category->slug) }}">{{ $category->name }}</a>
                            </div>
                            <h3 class="product-title"><a href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a></h3>
                            <div class="product-price">
                                ${{ number_format($product->price, 2) }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    @endif
    <!-- Trending Products End -->

    <!-- Flash Sell Start -->
    @if($section_status->flash_sell == 1)
    <div class="row justify-content-center">
        @foreach($flashSells as $flashSell)
            <div class="col-md-6 col-lg-4">
                <div class="banner banner-overlay banner-overlay-light">
                    <a href="{{ route('flash-sells.show', $flashSell->slug) }}">
                        <img src="{{ asset('images/flash_sell/' . $flashSell->flash_sell_image) }}" alt="Banner">
                    </a>
                    <div class="banner-content">
                        <h4 class="banner-subtitle">
                            <a href="{{ route('flash-sells.show', $flashSell->slug) }}">
                                {{ $flashSell->flash_sell_name }}
                            </a>
                        </h4>
                        <h3 class="banner-title">
                            <a href="{{ route('flash-sells.show', $flashSell->slug) }}">
                                <strong>{{ $flashSell->flash_sell_title }}</strong>
                            </a>
                        </h3>
                        <a href="{{ route('flash-sells.show', $flashSell->slug) }}" class="banner-link">
                            Shop Now<i class="icon-long-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @endif
    <!-- Flash Sell End -->

    <div class="mb-5"></div>

    <!-- Buy One Get One Start -->
    @if($section_status->buy_one_get_one == 1)
    <div class="container for-you">
        <h2 class="title text-center mb-4">Buy One Get One</h2>
        <div class="products">
            <div class="row justify-content-center">
                @foreach($buyOneGetOneProducts as $bogo)
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="product product-2">
                            <figure class="product-media">
                                <a href="{{ route('product.show.bogo', $bogo->product->slug) }}">
                                    <img src="{{ asset('images/buy_one_get_one/' . $bogo->feature_image) }}" alt="{{ $bogo->product->name }}" class="product-image">
                                </a>
                            </figure>
                            <div class="product-body">
                                <h3 class="product-title">
                                    <a href="{{ route('product.show.bogo', $bogo->product->slug) }}">{{ $bogo->product->name }}</a>
                                </h3>
                                <div class="product-price">
                                    <span class="new-price">{{ $currency }}{{ number_format($bogo->price, 2) }}</span>
                                </div>                               
                                <div class="product-info mt-2">
                                    <span class="badge badge-primary">Get {{ $bogo->get_products->count() }} extra products</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
    <!-- Buy One Get One End -->

    <div class="mb-4"></div>

    <!-- Bundle Products Start -->
    @if($section_status->bundle_products == 1)
    <div class="container for-you">
        <h2 class="title text-center mb-4">Bundle Products</h2>
        <div class="products">
            <div class="row justify-content-center">
                @foreach($bundleProducts as $bundle)
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="product product-2">
                            <figure class="product-media">
                                <a href="{{ route('bundle_product.show', $bundle->slug) }}">
                                    <img src="{{ asset('images/bundle_product/' . $bundle->feature_image) }}" alt="{{ $bundle->name }}" class="product-image">
                                </a>
                            </figure>
                            <div class="product-body">
                                <h3 class="product-title">
                                    <a href="{{ route('bundle_product.show', $bundle->slug) }}">{{ $bundle->name }}</a>
                                </h3>
                                <div class="product-price">
                                    <span class="new-price">{{ $currency }}{{ number_format($bundle->price, 2) }}</span>
                                </div>                               
                                <div class="product-info mt-2">
                                    <span class="badge badge-primary">Includes {{ count($bundle->product_ids) }} products</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
    <!-- Bundle Products End -->

    <!-- Suppliers start -->
    @if($section_status->vendors == 1)
    <div class="container">   
        <h2 class="title text-center mb-4 mt-4">Explore To Our Suppliers</h2>
        <div class="cat-blocks-container">
            <div class="row justify-content-center">
            @foreach($suppliers as $supplier)
                <div class="col-6 col-sm-4 col-lg-3 mb-4 d-flex justify-content-center">
                    <a href="{{ route('supplier.show', $supplier->slug) }}" class="cat-block text-center d-block">
                        <figure class="mb-3">
                            <img src="{{ asset('/images/supplier/' . $supplier->image) }}" alt="{{ $supplier->name }}" class="img-fluid rounded" style="max-width: 250px;">
                        </figure>
                        <h3 class="h5">{{ $supplier->name }}</h3>
                    </a>
                </div>
            @endforeach
            </div>
        </div>
    </div>
    @endif
    <!-- Suppliers end -->

    <div class="container">
        <hr class="mb-0">
    </div>

    <!-- Features Start -->
    @if($section_status->features == 1)
    <div class="icon-boxes-container bg-transparent">
        <div class="container">
            <div class="row">
                <div class="col-sm-6 col-lg-3">
                    <div class="icon-box icon-box-side">
                        <span class="icon-box-icon text-dark">
                            <i class="icon-rocket"></i>
                        </span>
                        <div class="icon-box-content">
                            <h3 class="icon-box-title">Free Shipping</h3>
                            <p>Orders $50 or more</p>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="icon-box icon-box-side">
                        <span class="icon-box-icon text-dark">
                            <i class="icon-rotate-left"></i>
                        </span>

                        <div class="icon-box-content">
                            <h3 class="icon-box-title">Free Returns</h3>
                            <p>Within 30 days</p>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="icon-box icon-box-side">
                        <span class="icon-box-icon text-dark">
                            <i class="icon-info-circle"></i>
                        </span>

                        <div class="icon-box-content">
                            <h3 class="icon-box-title">Get 20% Off 1 Item</h3>
                            <p>when you sign up</p>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="icon-box icon-box-side">
                        <span class="icon-box-icon text-dark">
                            <i class="icon-life-ring"></i>
                        </span>

                        <div class="icon-box-content">
                            <h3 class="icon-box-title">We Support</h3>
                            <p>24/7 amazing services</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    <!-- Features End -->

@endsection