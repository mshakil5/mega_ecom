@extends('frontend.layouts.app')

@section('content')


<!-- Intro Slider Start-->
@if($section_status->slider == 1)    
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-9 col-xxl-8 offset-lg-3 offset-xxl-2">
            <div class="intro-slider-container slider-container-ratio mb-2">
                <div class="intro-slider owl-carousel owl-simple owl-nav-inside" data-toggle="owl" data-owl-options='{
                        "nav": false, 
                        "dots": true
                    }'>
                    @foreach($sliders as $slider)
                        <div class="intro-slide">
                            <figure class="slide-image">
                                <picture>
                                    <source media="(max-width: 480px)" srcset="{{ asset('images/slider/' . $slider->image) }}-480w.jpg">
                                    <img src="{{ asset('images/slider/' . $slider->image) }}" alt="{{ $slider->title }} image description">
                                </picture>
                            </figure>

                            <div class="intro-content">
                                <h3 class="intro-subtitle">{{ $slider->sub_title }}</h3>
                                <h1 class="intro-title">{{ $slider->title }}</h1>
                                @if($slider->link)
                                    <a href="{{ $slider->link }}" class="btn btn-primary btn-round">
                                        <span>Discover Now</span>
                                        <i class="icon-long-arrow-right"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <span class="slider-loader"></span>
            </div>
        </div>
    </div>
</div>
@endif
<!-- Intro Slider End -->

<div class="container-fluid">
    <div class="row">

        <div class="col-xl-9 col-xxl-10">

        <div class="row">
            
            <div class="col-lg-12 col-xxl-4-5col">
                <div class="row">
                    @foreach($specialOffers ->take(2) as $specialOffer)
                        <div class="col-md-6">
                            <div class="banner banner-overlay">
                                <a href="{{ route('special-offers.show', $specialOffer->slug) }}">
                                    <img src="{{ asset('images/special_offer/' . $specialOffer->offer_image) }}" alt="{{ $specialOffer->offer_name }} img desc" style="width: 540px; height: 250px; object-fit: cover;">
                                </a>

                                <div class="banner-content">
                                    <h4 class="banner-subtitle text-white d-none d-sm-block"><a href="{{ route('special-offers.show', $specialOffer->slug) }}">{{ $specialOffer->offer_title }}</a></h4>
                                    <h3 class="banner-title text-white"><a href="{{ route('special-offers.show', $specialOffer->slug) }}">{{ $specialOffer->offer_name }}</a></h3>
                                    <a href="{{ route('special-offers.show', $specialOffer->slug) }}" class="banner-link">Shop Now <i class="icon-long-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        
        </div>

        <div class="mb-3"></div>

        @if($section_status->vendors == 1)
        <div class="owl-carousel owl-simple brands-carousel" data-toggle="owl" data-owl-options='{
            "nav": false, 
            "dots": true,
            "margin": 20,
            "loop": false,
            "responsive": {
                "0": {
                    "items":2
                },
                "420": {
                    "items":3
                },
                "600": {
                    "items":4
                },
                "900": {
                    "items":5
                },
                "1600": {
                    "items":6,
                    "nav": true
                }
            }
        }'>
            @foreach($suppliers as $supplier)
                <a href="{{ route('supplier.show', $supplier->slug) }}" class="brand">
                    <img src="{{ asset('/images/supplier/' . $supplier->image) }}" alt="{{ $supplier->name }}">
                </a>
            @endforeach
        </div>

        @endif

        <!-- Category products slider Start-->
        @if ($section_status->category_products == 1 && count($categories) > 0)
        <div class="trending-products">   
            <div class="heading heading-flex mb-3">
                <div class="heading-left">
                    <h2 class="title">Category Productss</h2>
                </div>
                <div class="heading-right">
                    <ul class="nav nav-pills nav-border-anim justify-content-center" role="tablist">
                        @foreach($categories->sortBy('id')->take(3) as $index => $category)
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
                            <div class="product text-center">
                                <figure class="product-media">
                                    <a href="{{ route('product.show', $product->slug) }}">
                                        <img src="{{ asset('images/products/' . $product->feature_image) }}" alt="{{ $product->name }}" class="product-image">
                                    </a>
                                    @if ($product->is_in_stock)

                                    @php
                                        $sellingPrice = $product->selling_price;
                                        $colors = $product->available_colors;
                                        $sizes = $product->available_sizes;
                                    @endphp

                                        <div class="product-action-vertical">
                                            <a href="#" class="btn-product-icon btn-wishlist add-to-wishlist btn-expandable" 
                                            title="Add to wishlist" 
                                            data-product-id="{{ $product->id }}" 
                                            data-offer-id="0" 
                                            data-price="{{ $sellingPrice ?? $product->price }}">
                                                <span>Add to wishlist</span>      
                                            </a>
                                        </div>
                                        <div class="product-action">
                                            <a href="#" class="btn-product btn-cart" title="Add to cart"
                                            data-product-id="{{ $product->id }}" 
                                            data-offer-id="0" 
                                            data-price="{{ $sellingPrice ?? $product->price }}" 
                                            data-toggle="modal" data-target="#quickAddToCartModal" 
                                            data-image ="{{ asset('images/products/' . $product->feature_image) }}" 
                                            data-stock="{{ $product->stock->sum('quantity') }}"
                                            data-colors="{{ $colors->toJson() }}"
                                            data-sizes="{{ $sizes->toJson() }}"
                                            data-name="{{ $product->name }}">
                                                <span>add to cart</span>
                                            </a>
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
                                    {{ $currency }}{{ number_format($sellingPrice ?? $product->price, 2) }}
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

        <!-- Recent advertisements start-->
        <div class="trending-products">
            @foreach($advertisements as $advertisement)
                @if($advertisement->type == 'recent')
                    <div class="cta cta-border" style="background-image: url('{{ asset('images/ads/' . $advertisement->image) }}');">
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
        @if($section_status->recent_products == 1 && $recentProducts->count() > 0)
        <div class="trending-products">
            <div class="heading heading-flex mb-3">
                <div class="heading-left">
                    <h2 class="title">Recent Products</h2>
                </div>
            </div>

            <div class="owl-carousel owl-full carousel-equal-height carousel-with-shadow" data-toggle="owl" 
                data-owl-options='{
                    "nav": true, 
                    "dots": false,
                    "margin": 20,
                    "loop": true,
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
                            @if ($product->is_in_stock)

                                @php
                                    $sellingPrice = $product->selling_price;
                                    $colors = $product->available_colors;
                                    $sizes = $product->available_sizes;
                                @endphp
                                <div class="product-action-vertical">
                                    <a href="#" class="btn-product-icon btn-wishlist add-to-wishlist btn-expandable" title="Add to wishlist" data-product-id="{{ $product->id }}" data-offer-id="0" data-price="{{ $sellingPrice ?? $product->price }}">
                                        <span>Add to wishlist</span>
                                    </a>
                                </div>
                                <div class="product-action">
                                    <a href="#" class="btn-product btn-cart" title="Add to cart"
                                     data-product-id="{{ $product->id }}" 
                                     data-offer-id="0" 
                                     data-price="{{ $sellingPrice ?? $product->price }}" 
                                     data-toggle="modal" data-target="#quickAddToCartModal" 
                                     data-image ="{{ asset('images/products/' . $product->feature_image) }}" 
                                     data-stock="{{ $product->stock->sum('quantity') }}"
                                     data-colors="{{ $colors->toJson() }}"
                                     data-sizes="{{ $sizes->toJson() }}"
                                     data-name="{{ $product->name }}">
                                        <span>add to cart</span>
                                    </a>
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
                            {{ $currency }}{{ number_format($sellingPrice ?? $product->price, 2) }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
        @endif
        <!-- Recent Products End -->

        <!-- Campaigns Start -->
        @if($section_status->campaigns == 1)
        
        <div class="row">
            @foreach($campaigns as $campaign)
                <div class="col-md-6">
                    <div class="banner banner-overlay">
                        <a href="{{ route('campaign.details.frontend', $campaign->slug) }}">
                            <img src="{{ asset('images/campaign_banner/' . $campaign->banner_image) }}" alt="{{ $campaign->title }} image description" style="width: 100%; height: 300px; object-fit: cover;" >
                        </a>

                        <div class="banner-content">
                            <h3 class="banner-title text-white">
                                <a href="{{ route('campaign.details.frontend', $campaign->slug) }}">{{ $campaign->title }} </a>
                            </h3>
                            <a href="{{ route('campaign.details.frontend', $campaign->slug) }}" class="banner-link">Shop Now <i class="icon-long-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @endif
        <!-- Campaigns End -->

        <!-- supplier advertisements start-->
        <div class="trending-products">
            @foreach($advertisements as $advertisement)
                @if($advertisement->type == 'vendor')
                    <div class="cta cta-border" style="background-image: url('{{ asset('images/ads/' . $advertisement->image) }}');">
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
        <!-- supplier advertisements end-->

        <!-- Trending Products Start -->
        @if($section_status->trending_products == 1 && $trendingProducts->count() > 0)
        <div class="trending-products">
            <div class="heading heading-flex mb-3">
                <div class="heading-left">
                    <h2 class="title">Trending Products</h2>
                </div>
            </div>

            <div class="owl-carousel owl-full carousel-equal-height carousel-with-shadow" data-toggle="owl" 
                data-owl-options='{
                    "nav": true, 
                    "dots": false,
                    "margin": 20,
                    "loop": true,
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
                            @if ($product->is_in_stock)

                                @php
                                    $sellingPrice = $product->selling_price;
                                    $colors = $product->available_colors;
                                    $sizes = $product->available_sizes;
                                @endphp


                                <div class="product-action-vertical">
                                    <a href="#" class="btn-product-icon btn-wishlist add-to-wishlist btn-expandable" title="Add to wishlist" data-product-id="{{ $product->id }}" data-offer-id="0" data-price="{{ $sellingPrice ?? $product->price }}">
                                        <span>Add to wishlist</span>
                                    </a>
                                </div>
                                <div class="product-action">
                                    <a href="#" class="btn-product btn-cart" title="Add to cart"
                                     data-product-id="{{ $product->id }}" 
                                     data-offer-id="0" 
                                     data-price="{{ $sellingPrice ?? $product->price }}" 
                                     data-toggle="modal" data-target="#quickAddToCartModal" 
                                     data-image ="{{ asset('images/products/' . $product->feature_image) }}" 
                                     data-stock="{{ $product->stock->sum('quantity') }}"
                                     data-colors="{{ $colors->toJson() }}"
                                     data-sizes="{{ $sizes->toJson() }}"
                                     data-name="{{ $product->name }}">
                                        <span>add to cart</span>
                                    </a>
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
                            {{ $currency }}{{ number_format($sellingPrice ?? $product->price, 2) }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
        @endif
        <!-- Trending Products End -->

        <!-- Featured advertisements start-->
        <div class="trending-products">
            @foreach($advertisements as $advertisement)
                @if($advertisement->type == 'recent')
                    <div class="cta cta-border" style="background-image: url('{{ asset('images/ads/' . $advertisement->image) }}');">
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
        @if($section_status->recent_products == 1 && $recentProducts->count() > 0)
        <div class="trending-products">
            <div class="heading heading-flex mb-3">
                <div class="heading-left">
                    <h2 class="title">Recent Products</h2>
                </div>
            </div>

            <div class="owl-carousel owl-full carousel-equal-height carousel-with-shadow" data-toggle="owl" 
                data-owl-options='{
                    "nav": true, 
                    "dots": false,
                    "margin": 20,
                    "loop": true,
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
                            @if ($product->is_in_stock)

                                @php
                                    $sellingPrice = $product->selling_price;
                                    $colors = $product->available_colors;
                                    $sizes = $product->available_sizes;
                                @endphp
                                <div class="product-action-vertical">
                                    <a href="#" class="btn-product-icon btn-wishlist add-to-wishlist btn-expandable" title="Add to wishlist" data-product-id="{{ $product->id }}" data-offer-id="0" data-price="{{ $sellingPrice ?? $product->price }}">
                                        <span>Add to wishlist</span>
                                    </a>
                                </div>
                                <div class="product-action">
                                    <a href="#" class="btn-product btn-cart" title="Add to cart"
                                     data-product-id="{{ $product->id }}" 
                                     data-offer-id="0" 
                                     data-price="{{ $sellingPrice ?? $product->price }}" 
                                     data-toggle="modal" data-target="#quickAddToCartModal" 
                                     data-image ="{{ asset('images/products/' . $product->feature_image) }}" 
                                     data-stock="{{ $product->stock->sum('quantity') }}"
                                     data-colors="{{ $colors->toJson() }}"
                                     data-sizes="{{ $sizes->toJson() }}"
                                     data-name="{{ $product->name }}">
                                        <span>add to cart</span>
                                    </a>
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
                            {{ $currency }}{{ number_format($sellingPrice ?? $product->price, 2) }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
        @endif
        <!-- Recent Products End -->

        <!-- Most Viewed Products Start -->
        @if($section_status->most_viewed_products == 1 && $mostViewedProducts->count() > 0)
        <div class="trending-products">
            <div class="heading heading-flex mb-3">
                <div class="heading-left">
                    <h2 class="title">Most Viewed Products</h2>
                </div>
            </div>

            <div class="owl-carousel owl-full carousel-equal-height carousel-with-shadow" data-toggle="owl" 
                data-owl-options='{
                    "nav": true, 
                    "dots": false,
                    "margin": 20,
                    "loop": true,
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
                @if ($mostViewedProducts->count() > 0)
                    @foreach($mostViewedProducts as $product)
                    <div class="product product-2">
                        <figure class="product-media">
                            <a href="{{ route('product.show', $product->slug) }}">
                                <img src="{{ asset('images/products/' . $product->feature_image) }}" alt="{{ $product->name }}" class="product-image">
                            </a>
                            @if ($product->is_in_stock)
                                @php
                                    $sellingPrice = $product->selling_price;
                                    $colors = $product->available_colors;
                                    $sizes = $product->available_sizes;
                                @endphp
                                <div class="product-action-vertical">
                                    <a href="#" class="btn-product-icon btn-wishlist add-to-wishlist btn-expandable" title="Add to wishlist" data-product-id="{{ $product->id }}" data-offer-id="0" data-price="{{ $sellingPrice ?? $product->price }}">
                                        <span>Add to wishlist</span>
                                    </a>
                                </div>
                                <div class="product-action">
                                    <a href="#" class="btn-product btn-cart" title="Add to cart"
                                     data-product-id="{{ $product->id }}" 
                                     data-offer-id="0" 
                                     data-price="{{ $sellingPrice ?? $product->price }}" 
                                     data-toggle="modal" data-target="#quickAddToCartModal" 
                                     data-image ="{{ asset('images/products/' . $product->feature_image) }}" 
                                     data-stock="{{ $product->stock->sum('quantity') }}"
                                     data-colors="{{ $colors->toJson() }}"
                                     data-sizes="{{ $sizes->toJson() }}"
                                     data-name="{{ $product->name }}">
                                        <span>add to cart</span>
                                    </a>
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
                            {{ $currency }}{{ number_format($sellingPrice ?? $product->price, 2) }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
        @endif
        <!-- Most Viewed Products End -->

        <!-- Flash Sell Start -->
        @if($section_status->flash_sell == 1)
            <div class="row">
                @foreach($flashSells->take(2) as $flashSell)
                    <div class="col-md-6">
                        <div class="banner banner-overlay">
                            <a href="{{ route('flash-sells.show', $flashSell->slug) }}">
                                <img src="{{ asset('images/flash_sell/' . $flashSell->flash_sell_image) }}" alt="{{ $flashSell->flash_sell_title }} image description" style="width: 100%; height: 300px; object-fit: cover;">
                            </a>
                            <div class="banner-content">
                                <h3 class="banner-subtitle text-white">
                                    <a href="{{ route('flash-sells.show', $flashSell->slug) }}">
                                        {{ $flashSell->flash_sell_name }}
                                    </a>
                                </h3>
                                <h3 class="banner-title text-white">
                                    <a href="{{ route('flash-sells.show', $flashSell->slug) }}">
                                        <strong>{{ $flashSell->flash_sell_title }}</strong>
                                    </a>
                                </h3>
                                <a href="{{ route('flash-sells.show', $flashSell->slug) }}" class="banner-link">
                                    Shop Now <i class="icon-long-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        <!-- Flash Sell End -->
        </div>

        <aside class="col-xl-3 col-xxl-2 order-xl-first">
            <div class="sidebar sidebar-home">
                <div class="row">

                    <!-- Buy One Get One Start -->
                    {{-- @if($section_status->buy_one_get_one == 1 && count($buyOneGetOneProducts) > 0)
                    <div class="col-12">
                        <div class="widget widget-deals">
                            <h4 class="widget-title"><span>Buy One Get One</span></h4>

                            <div class="row">
                            @foreach($buyOneGetOneProducts as $bogo)
                                <div class="col-sm-6 col-xl-12">
                                    <div class="product text-center">
                                        <figure class="product-media">
                                            <a href="{{ route('product.show.bogo', $bogo->product->slug) }}">
                                                <img src="{{ asset('images/buy_one_get_one/' . $bogo->feature_image) }}" alt="{{ $bogo->product->name }}" class="product-image">
                                            </a>
                                        </figure>

                                        <div class="product-body">
                                            <h3 class="product-title"><a href="{{ route('product.show.bogo', $bogo->product->slug) }}">{{ $bogo->product->name }}</a></h3>
                                            <div class="product-price">
                                                <span class="new-price">{{ $currency }}{{ number_format($bogo->price, 2) }}</span>
                                            </div>

                                            <div class="product-info mt-2">
                                                <span class="badge badge-primary">Get {{ $bogo->get_products_count }} extra products</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            </div>
                        </div>
                    </div>
                    @endif --}}
                    <!-- Buy One Get One End -->

                    <!-- Bundle Products Start -->
                    @if($section_status->bundle_products == 1 && count($bundleProducts) > 0)
                    <div class="col-12">
                        <div class="widget widget-deals">
                            <h4 class="widget-title"><span>Bundle Products</span></h4>

                            <div class="row">
                                @foreach($bundleProducts as $bundle)
                                    <div class="col-sm-6 col-xl-12">
                                        <div class="product text-center">
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
                                                    <span class="badge badge-primary">Includes {{ $bundle->product_ids_count }} products</span>
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

                    <div class="col-sm-6 col-xl-12">
                        <div class="widget widget-banner">
                            <div class="banner banner-overlay">
                                <a>
                                    <img src="{{ asset('frontend/images/demo-14/banners/banner-11.jpg') }}" alt="Banner img desc">
                                </a>

                                <div class="banner-content banner-content-top banner-content-right text-right">
                                    <h3 class="banner-title text-white"><a href="#">Maximum Comfort <span>Sofas -20% Off</span></a></h3><!-- End .banner-title -->
                                    <a href="#" class="banner-link">Shop Now <i class="icon-long-arrow-right"></i></a>
                                </div><!-- End .banner-content -->
                            </div><!-- End .banner banner-overlay -->
                        </div><!-- End .widget widget-banner -->
                    </div>

                    <div class="col-sm-6 col-xl-12">
                        <div class="widget widget-banner">
                            <div class="banner banner-overlay">
                                <a>
                                    <img src="{{ asset('frontend/images/demo-14/banners/banner-12.jpg') }}" alt="Banner img desc">
                                </a>

                                <div class="banner-content banner-content-top banner-content-right text-right">
                                    <h3 class="banner-title text-white"><a href="#">Maximum Comfort <span>Sofas -20% Off</span></a></h3><!-- End .banner-title -->
                                    <a href="#" class="banner-link">Shop Now <i class="icon-long-arrow-right"></i></a>
                                </div><!-- End .banner-content -->
                            </div><!-- End .banner banner-overlay -->
                        </div><!-- End .widget widget-banner -->
                    </div>

                </div>
            </div>
        </aside>
    </div>
</div>

<style>
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
</div>

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