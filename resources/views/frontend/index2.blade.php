@extends('frontend.layouts.app')

@section('content')

    <!-- Intro Slider Start-->
    @if($section_status->slider == 1 && count($sliders) > 0)
    <div class="intro-section mt-1">
        <div class="container mobile-margin">
            <!-- Full-width Slider Row -->
            <div class="row">
                <div class="col-12">
                    <div class="intro-slider-container slider-container-ratio mb-2">
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
                                <div class="intro-slide" style="background-image: url('{{ asset('images/slider/' . $slider->image) }}'); background-size: cover; background-position: center;">
                                    <div class="intro-content" style="padding: 20px; display: flex; align-items: center; justify-content: center; height: 100%;">
                                        <div class="row justify-content-left">
                                          @if ($slider->sub_title || $slider->title || $slider->link)                  
                                            <div class="col-auto" style="background: rgba(0, 0, 0, 0.5); padding: 20px; border-radius: 10px;">
                                                @if($slider->title)
                                                    <h1 class="intro-title" style="color: #fff;">{{ $slider->title }}</h1>
                                                @endif
                                                                                                                                                                                  @if($slider->sub_title)
                                                    <h2 class="intro-subtitle" style="color: #fff;">{{ $slider->sub_title }}</h2>
                                                @endif
                                                @if($slider->link)
                                                    <a href="{{ $slider->link }}" class="btn btn-primary btn-round">
                                                        <span>Shop Now</span>
                                                        <i class="icon-long-arrow-right"></i>
                                                    </a>
                                                @endif
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <span class="slider-loader"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    <!-- Intro Slider End -->

    <!-- New Products Start -->
    @if($newProducts->count() > 0)
    <div class="container trending-products mt-1">
        <div class="heading heading-flex mb-3">
            <div class="heading-left">
                <h2 class="title">New Arrivals</h2>
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
            @if ($newProducts->count() > 0)
                @foreach($newProducts as $product)
                <div class="product product-2">
                    <figure class="product-media">
                        <a href="{{ route('product.show', $product->slug) }}">
                            <x-image-with-loader src="{{ asset('images/products/' . $product->feature_image) }}" alt="{{ $product->name }}" class="product-image" />
                        </a>
                        @php
                            $sellingPrice = $product->selling_price;
                            $colors = $product->available_colors;
                            $sizes = $product->available_sizes;
                        @endphp
                        @if ($product->is_in_stock)
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
                            <a href="{{ route('category.show', $product->category->slug) }}">{{ $product->category->name }}</a>
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
    <!-- New Products End -->

    <!-- Categories Start -->
    @if ($section_status->category_products == 1 && count($categories) > 0)
    <div class="intro-section">
      <div class="container mobile-margin">
        <div class="row">
            @foreach($categories->take(4) as $category)
            <div class="col-12 col-md-6">
                <div class="banner">
                    <a href="{{ route('category.show', $category->slug) }}">
                        <x-image-with-loader src="{{ asset('images/category/' . $category->image) }}" alt="{{ $category->name }}" class="category-img" />
                    </a>
                    <div class="banner-content" style="background: rgba(0, 0, 0, 0.5); padding: 10px; border-radius: 10px;">
                        <h4 class="banner-title text-center">
                            <a href="{{ route('category.show', $category->slug) }}" style="color: #fff;">{{ $category->name }}</a>
                        </h4>
                    </div>
                </div>
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
        <div class="row justify-content-center pt-2">
            @foreach($specialOffers->take(3) as $specialOffer)
                <div class="col-md-6 col-lg-4">
                    <div class="banner banner-overlay banner-overlay-light">
                        <a href="{{ route('special-offers.show', $specialOffer->slug) }}">
                            <x-image-with-loader src="{{ asset('images/special_offer/' . $specialOffer->offer_image) }}" alt="Banner" style="height: 300px; object-fit: cover;" />
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
    <div class="container">   
        <div class="heading heading-flex">
            <div class="heading-right" style="width: 100%; text-align: center;">
                <ul class="nav nav-pills nav-border-anim nav-big justify-content-center" role="tablist">
                     @foreach($categories->take(3) as $index => $category)
                        <li class="nav-item">
                            <a class="nav-link {{ $index == 0 ? 'active' : '' }}" 
                            id="category-{{ $category->id }}-link" 
                            data-toggle="tab" 
                            href="#category-{{ $category->id }}-tab" 
                            role="tab" 
                            aria-controls="category-{{ $category->id }}-tab" 
                            aria-selected="{{ $index == 0 ? 'true' : 'false' }}"
                            index="{{ $index }}" style="font-size: 20px; font-weight: bold;">
                                {{ $category->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="tab-content pt-1">
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
                                    <x-image-with-loader src="{{ asset('images/products/' . $product->feature_image) }}" alt="{{ $product->name }}" class="product-image" />
                                </a>
                                @php
                                    $sellingPrice = $product->selling_price;
                                    $colors = $product->available_colors;
                                    $sizes = $product->available_sizes;
                                @endphp
                                @if ($product->is_in_stock)
                                    <div class="product-action-vertical">
                                        <a href="#" class="btn-product-icon btn-wishlist add-to-wishlist btn-expandable" 
                                        title="Add to wishlist" 
                                        data-product-id="{{ $product->id }}" 
                                        data-offer-id="0" 
                                        data-price="{{ $sellingPrice ?? $product->price }}"><span>Add to wishlist</span>      
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
    @if($advertisements->contains('type', 'recent'))
    <div class="container">
        @foreach($advertisements as $advertisement)
            @if($advertisement->type == 'recent')
                <div class="cta cta-border" style="background-image: url('{{ asset('images/ads/' . $advertisement->image) }}');">
                    <div class="row justify-content-center">
                        <div class="col-md-12">
                            <div class="cta-content">
                                <div class="cta-text text-right text-white">
                                </div>
                                @if($advertisement->link)<a href="{{ $advertisement->link }}" class="btn btn-primary btn-round" target="_blank">
                                    <span>Shop Now</span><i class="icon-long-arrow-right"></i>
                                </a> @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
    @endif
    <!-- Recent advertisements end-->

    <!-- Recent Products Start -->
    @if($section_status->recent_products == 1 && $recentProducts->count() > 0)
    <div class="container trending-products mt-3">
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
                          <x-image-with-loader src="{{ asset('images/products/' . $product->feature_image) }}" alt="{{ $product->name }}" class="product-image" />
                        </a>
                          @php
                            $sellingPrice = $product->selling_price;
                            $colors = $product->available_colors;
                            $sizes = $product->available_sizes;
                        @endphp
                        @if ($product->is_in_stock)

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
                            <a href="{{ route('category.show', $product->category->slug) }}">{{ $product->category->name }}</a>
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
    @if($section_status->campaigns == 1 && $campaigns->count() > 0)
    <div class="container mt-2 mb-2">
        <div class="row justify-content-center">
            @foreach($campaigns->take(3) as $campaign)
            <div class="col-md-6 col-lg-4">
                <div class="banner banner-overlay banner-overlay-light">
                    <a href="{{ route('campaign.details.frontend', $campaign->slug) }}">
                        <x-image-with-loader src="{{ asset('images/campaign_banner/' . $campaign->banner_image) }}" alt="{{ $campaign->title }}" style="height: 300px; object-fit: cover;" />
                    </a>

                    <div class="banner-content" style="background: rgba(0, 0, 0, 0.5); padding: 20px; border-radius: 10px;">
                        <h3 class="banner-title">
                           @if($campaign->title) <a href="{{ route('campaign.details.frontend', $campaign->slug) }}">
                                <strong style="color: #fff;">{{ $campaign->title }}</strong>
                            </a>@endif
                        </h3>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    <!-- Campaigns End -->

    <!-- Trending Products Start -->
    @if($section_status->trending_products == 1 && $trendingProducts->count() > 0)
    <div class="container trending-products mt-1">
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
                            <x-image-with-loader src="{{ asset('images/products/' . $product->feature_image) }}" alt="{{ $product->name }}" class="product-image" />
                        </a>
                          @php
                            $sellingPrice = $product->selling_price;
                            $colors = $product->available_colors;
                            $sizes = $product->available_sizes;
                        @endphp

                        @if ($product->is_in_stock)
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
                            <a href="{{ route('category.show', $product->category->slug) }}">{{  $product->category->name }}</a>
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
    @if($advertisements->contains('type', 'featured'))
    <div class="container">
        @foreach($advertisements as $advertisement)
            @if($advertisement->type == 'featured')
                <div class="cta cta-border" style="background-image: url('{{ asset('images/ads/' . $advertisement->image) }}');">
                    <div class="row justify-content-center">
                        <div class="col-md-12">
                            <div class="cta-content">
                                <div class="cta-text text-right text-white">
                                </div>
                                @if($advertisement->link)<a href="{{ $advertisement->link }}" class="btn btn-primary btn-round" target="_blank">
                                    <span>Shop Now</span><i class="icon-long-arrow-right"></i>
                                </a> @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
    @endif
    <!-- Featured advertisements end-->

    <!-- Featured Products Start -->
    @if($featuredProducts->count() > 0)
    <div class="container trending-products mt-3">
        <div class="heading heading-flex mb-3">
            <div class="heading-left">
                <h2 class="title">Featured Products</h2>
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
            @if ($featuredProducts->count() > 0)
                @foreach($featuredProducts as $product)
                <div class="product product-2">
                    <figure class="product-media">
                        <a href="{{ route('product.show', $product->slug) }}">
                            <x-image-with-loader src="{{ asset('images/products/' . $product->feature_image) }}" alt="{{ $product->name }}" class="product-image" />
                        </a>
                        @php
                            $sellingPrice = $product->selling_price;
                            $colors = $product->available_colors;
                            $sizes = $product->available_sizes;
                        @endphp
                        @if ($product->is_in_stock)

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
                            <a href="{{ route('category.show', $product->category->slug) }}">{{ $product->category->name }}</a>
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
    <!-- Featured Products End -->

    <!-- Most Viewed Products Start -->
    @if($section_status->most_viewed_products == 1 && $mostViewedProducts->count() > 0)
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
                            <x-image-with-loader src="{{ asset('images/products/' . $product->feature_image) }}" alt="{{ $product->name }}" class="product-image" />
                        </a>
                        @php
                            $sellingPrice = $product->selling_price;
                            $colors = $product->available_colors;
                            $sizes = $product->available_sizes;
                        @endphp

                        @if ($product->is_in_stock)
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
                            <a href="{{ route('category.show', $product->category->slug) }}">{{ $product->category->name }}</a>
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
    
    <!-- Popular Products Start -->
    @if ($popularProducts->count() > 0)
    <div class="container trending-products">
        <div class="heading heading-flex mb-3">
            <div class="heading-left">
                <h2 class="title">Popular Products</h2>
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
            @if ($popularProducts->count() > 0)
                @foreach($popularProducts as $product)
                <div class="product product-2">
                    <figure class="product-media">
                        <a href="{{ route('product.show', $product->slug) }}">
                            <x-image-with-loader src="{{ asset('images/products/' . $product->feature_image) }}" alt="{{ $product->name }}" class="product-image" />
                        </a>
                        
                        @php
                            $sellingPrice = $product->selling_price;
                            $colors = $product->available_colors;
                            $sizes = $product->available_sizes;
                        @endphp

                        @if ($product->is_in_stock)
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
                            <a href="{{ route('category.show', $product->category->slug) }}">{{ $product->category->name }}</a>
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
    <!-- Popular Products End -->

    <!-- Flash Sell Start -->
    @if($section_status->flash_sell == 1)
    <div class="container mt-2 mb-2">
        <div class="row justify-content-center">
            @foreach($flashSells->take(3) as $flashSell)
                <div class="col-md-6 col-lg-4">
                    <div class="banner banner-overlay banner-overlay-light">
                        <a href="{{ route('flash-sells.show', $flashSell->slug) }}">
                            <x-image-with-loader src="{{ asset('images/flash_sell/' . $flashSell->flash_sell_image) }}" alt="Banner" style="height: 300px; object-fit: cover;" />
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
    {{-- <div class="container for-you">
        <h2 class="title text-center mb-4">Buy One Get One</h2>
        <div class="products">
            <div class="row justify-content-center">
                @foreach($buyOneGetOneProducts as $bogo)
                    <div class="col-6 col-md-4 col-lg-3 mx-2">
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
    <div class="mb-2"></div> --}}
    @endif
    <!-- Buy One Get One End -->

    <!-- Bundle Products Start -->
    @if($section_status->bundle_products == 1 && count($bundleProducts) > 0)
    <div class="container for-you">
        <h2 class="title text-center mb-4">Bundle Products</h2>
        <div class="products">
            <div class="row justify-content-center">
                @foreach($bundleProducts as $bundle)
                    <div class="col-6 col-md-4 col-lg-3 mx-2">
                        <div class="product product-2">
                            <figure class="product-media">
                                <a href="{{ route('bundle_product.show', $bundle->slug) }}">
                                    <x-image-with-loader src="{{ asset('images/bundle_product/' . $bundle->feature_image) }}" alt="{{ $bundle->name }}" class="product-image" />
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

    <!-- Supplier advertisements start-->
    @foreach($advertisements as $advertisement)
        @if($advertisement->type == 'vendor')
          <div class="container">
            <div class="cta cta-border" style="background-image: url('{{ asset('images/ads/' . $advertisement->image) }}');">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="cta-content">
                            <div class="cta-text text-right text-white">
                            </div>
                            @if($advertisement->link)<a href="{{ $advertisement->link }}" class="btn btn-primary btn-round" target="_blank">
                                <span>Shop Now</span><i class="icon-long-arrow-right"></i>
                            </a>@endif
                        </div>
                    </div>
                </div>
            </div>
          </div>
        @endif
    @endforeach
    <!-- Supplier advertisements end-->

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
                                <x-image-with-loader src="{{ asset('/images/supplier/' . $supplier->image) }}" alt="{{ $supplier->name }}" class="img-fluid rounded" style="max-width: 100%; height: auto; object-fit: cover;" />
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
                <div class="col-6 col-lg-3">
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

                <div class="col-6 col-lg-3">
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

                <div class="col-6 col-lg-3">
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

                <div class="col-6 col-lg-3">
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