@extends('frontend.layouts.app')

@section('content')
    <!-- Intro Slider Start-->
    @if($section_status->slider == 1)
    <div class="container">
        <div class="intro-slider-container mb-5">
            <div class="intro-slider owl-carousel owl-theme owl-nav-inside owl-light" data-toggle="owl" 
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
                            @if($slider->sub_title || $slider->title || $slider->link)
                            <div class="row justify-content-center">
                                <div class="col-auto col-sm-7 col-md-6 col-lg-5" style="background: rgba(0, 0, 0, 0.5); padding: 20px; border-radius: 10px;">
                                    <h3 class="intro-subtitle text-third" style="color: #fff;">{{ $slider->sub_title }}</h3>
                                    <h1 class="intro-title" style="color: #fff;">{{ $slider->title }}</h1>
                                    @if($slider->link)
                                        <a href="{{ $slider->link }}" class="btn btn-primary btn-round">
                                            <span>Shop More</span>
                                            <i class="icon-long-arrow-right"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <span class="slider-loader"></span>
        </div>
    </div>
    @endif
    <!-- Intro Slider End -->

    <div class="mb-15"></div>

    <!-- Categories Start -->
    @if($section_status->categories == 1 && count($categories) > 0)
    <div class="container">
        <h2 class="title text-center">Explore Popular Categories</h2>
        <div class="cat-blocks-container">
            <div class="row justify-content-center">            
                @foreach($categories->sortBy('id')->take(6) as $category)
                    <div class="col-6 col-sm-4 col-lg-2 mb-4 d-flex justify-content-center">
                        <a href="{{ route('category.show', $category->slug) }}" class="cat-block text-center">
                            <figure>
                                <span>
                                    <img src="{{ asset('images/category/' . $category->image) }}" alt="{{ $category->name }}" style="width: 200px;">
                                </span>
                            </figure>
                            <h1 class="cat-block-title">{{ $category->name }}</h1>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
    <!-- Categories End -->

    <!-- Special Offer Start -->
    @if($section_status->special_offer == 1)
    <div class="container">
        <div class="row justify-content-center">
            @foreach($specialOffers->take(3) as $specialOffer)
                <div class="col-md-6 col-lg-4">
                    <div class="banner banner-overlay banner-overlay-light">
                        <a href="{{ route('special-offers.show', $specialOffer->slug) }}">
                            <img src="{{ asset('images/special_offer/' . $specialOffer->offer_image) }}" alt="Banner" style="height: 300px; object-fit: cover;">
                        </a>
                        <div class="banner-content" style="background: rgba(0, 0, 0, 0.5); padding: 20px; border-radius: 10px;">
                            <h4 class="banner-subtitle">
                                <a href="{{ route('special-offers.show', $specialOffer->slug) }}" style="color: #fff;">
                                    {{ $specialOffer->offer_name }}
                                </a>
                            </h4>
                            <h3 class="banner-title">
                                <a href="{{ route('special-offers.show', $specialOffer->slug) }}">
                                    <strong style="color: #fff;">{{ $specialOffer->offer_title }}</strong>
                                </a>
                            </h3>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
    <!-- Special Offer End -->

    <!-- Category products slider Start-->
    @if ($section_status->category_products == 1 && count($categories) > 0)
    <div class="container new-arrivals">   
        <div class="heading heading-flex mb-3">
            <div class="heading-left">
                <h2 class="title">Category Products</h2>
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
                        @foreach($category->products as $product)
                        <div class="product product-2">
                            <figure class="product-media">
                                <a href="{{ route('product.show', $product->slug) }}">
                                    <img src="{{ asset('images/products/' . $product->feature_image) }}" alt="{{ $product->name }}" class="product-image">
                                </a>
                                @if ($product->stock && $product->stock->quantity > 0)

                                    @php
                                        $filteredStock = $product->stock()
                                            ->where('quantity', '>', 0)
                                            ->latest()
                                            ->select('id', 'selling_price', 'color', 'size', 'quantity')
                                            ->get();

                                        $sellingPrice = $filteredStock->first()->selling_price ?? 0; 
                                        $colors = $filteredStock->pluck('color')->unique();
                                        $sizes = $filteredStock->pluck('size')->unique();
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
                                        data-sizes="{{ $sizes->toJson() }}">
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
    <div class="container mt-5">
        @foreach($advertisements as $advertisement)
            @if($advertisement->type == 'recent')
                <div class="cta cta-border mb-5" style="background-image: url('{{ asset('images/ads/' . $advertisement->image) }}');">
                    <div class="row justify-content-center">
                        <div class="col-md-12">
                            <div class="cta-content">
                                <div class="cta-text text-lg-right text-white">
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
    <div class="container trending-products">
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
                        @if ($product->stock && $product->stock->quantity > 0)

                            @php
                                $filteredStock = $product->stock()
                                    ->where('quantity', '>', 0)
                                    ->latest()
                                    ->select('id', 'selling_price', 'color', 'size', 'quantity')
                                    ->get();

                                $sellingPrice = $filteredStock->first()->selling_price ?? 0; 
                                $colors = $filteredStock->pluck('color')->unique();
                                $sizes = $filteredStock->pluck('size')->unique();
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
                                    data-sizes="{{ $sizes->toJson() }}">
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
    <div class="container mb-2 mt-2">
        <div class="row justify-content-center">
            @foreach($campaigns as $campaign)
            <div class="col-md-6 col-lg-4">
                <div class="banner banner-overlay banner-overlay-light">
                    <a href="{{ route('campaign.details.frontend', $campaign->slug) }}">
                        <img src="{{ asset('images/campaign_banner/' . $campaign->banner_image) }}" alt="{{ $campaign->title }}" style="height: 300px; object-fit: cover;">
                    </a>

                    <div class="banner-content" style="background: rgba(0, 0, 0, 0.5); padding: 20px; border-radius: 10px;">
                        <h3 class="banner-title">
                            <a href="{{ route('campaign.details.frontend', $campaign->slug) }}">
                                <strong style="color: #fff;">{{ $campaign->title }}</strong>
                            </a>
                        </h3>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    <!-- Campaigns End -->

    <!-- Supplier advertisements start-->
    <div class="container">
        @foreach($advertisements as $advertisement)
            @if($advertisement->type == 'vendor')
                <div class="cta cta-border" style="background-image: url('{{ asset('images/ads/' . $advertisement->image) }}');">
                    <div class="row justify-content-center">
                        <div class="col-md-12">
                            <div class="cta-content d-lg-flex justify-content-lg-end">
                                <div class="cta-text text-white">
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
    @if($section_status->trending_products == 1 && $trendingProducts->count() > 0)
    <div class="pt-5">
        <div class="container trending-products">
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
                            @if ($product->stock && $product->stock->quantity > 0)

                                @php
                                    $filteredStock = $product->stock()
                                        ->where('quantity', '>', 0)
                                        ->latest()
                                        ->select('id', 'selling_price', 'color', 'size', 'quantity')
                                        ->get();

                                    $sellingPrice = $filteredStock->first()->selling_price ?? 0; 
                                    $colors = $filteredStock->pluck('color')->unique();
                                    $sizes = $filteredStock->pluck('size')->unique();
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
                                     data-sizes="{{ $sizes->toJson() }}">
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
    </div>
    @endif
    <!-- Trending Products End -->

    <!-- Most Viewed Products Start -->
    @if($section_status->most_viewed_products == 1 && $mostViewedProducts->count() > 0)
    <div class="pt-5">
        <div class="container trending-products">
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
                            @if ($product->stock && $product->stock->quantity > 0)

                                @php
                                    $filteredStock = $product->stock()
                                        ->where('quantity', '>', 0)
                                        ->latest()
                                        ->select('id', 'selling_price', 'color', 'size', 'quantity')
                                        ->get();

                                    $sellingPrice = $filteredStock->first()->selling_price ?? 0; 
                                    $colors = $filteredStock->pluck('color')->unique();
                                    $sizes = $filteredStock->pluck('size')->unique();
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
                                     data-sizes="{{ $sizes->toJson() }}">
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
    </div>
    @endif
    <!-- Most Viewed Products End -->

    <!-- Flash Sell Start -->
    @if($section_status->flash_sell == 1)
    <div class="container mt-2 mb-2">
        <div class="row justify-content-center">
            @foreach($flashSells as $flashSell)
                <div class="col-md-6 col-lg-4">
                    <div class="banner banner-overlay banner-overlay-light">
                        <a href="{{ route('flash-sells.show', $flashSell->slug) }}">
                            <img src="{{ asset('images/flash_sell/' . $flashSell->flash_sell_image) }}" alt="Banner" style="height: 300px; object-fit: cover;">
                        </a>
                        <div class="banner-content" style="background: rgba(0, 0, 0, 0.5); padding: 20px; border-radius: 10px;">
                            <h4 class="banner-subtitle">
                                <a href="{{ route('flash-sells.show', $flashSell->slug) }}" style="color: #fff;">
                                    {{ $flashSell->flash_sell_name }}
                                </a>
                            </h4>
                            <h3 class="banner-title">
                                <a href="{{ route('flash-sells.show', $flashSell->slug) }}">
                                    <strong style="color: #fff;">{{ $flashSell->flash_sell_title }}</strong>
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
    </div>
    @endif
    <!-- Flash Sell End -->

    <!-- Buy One Get One Start -->
    @if($section_status->buy_one_get_one == 1 && count($buyOneGetOneProducts) > 0)
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
                                    <span class="badge badge-primary">Get {{ $bogo->get_products_count }} extra products</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="mb-2"></div>
    @endif
    <!-- Buy One Get One End -->

    <!-- Bundle Products Start -->
    @if($section_status->bundle_products == 1 && count($bundleProducts) > 0)
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

    <!-- Suppliers start -->
    @if($section_status->vendors == 1 && count($suppliers) > 0)
    <div class="container">   
        <h2 class="title text-center mb-4 mt-4">Explore Our Suppliers</h2>
        <div class="cat-blocks-container">
            <div class="row justify-content-center">
                @foreach($suppliers as $supplier)
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-4 d-flex justify-content-center">
                        <a href="{{ route('supplier.show', $supplier->slug) }}" class="cat-block text-center d-block">
                            <figure class="mb-3">
                                <img src="{{ asset('/images/supplier/' . $supplier->image) }}" alt="{{ $supplier->name }}" class="img-fluid rounded" style="max-width: 100%; height: auto; object-fit: cover;">
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