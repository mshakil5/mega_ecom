@extends('frontend.layouts.app')

@section('content')

    <style>
        @media (min-width: 992px) {
            .modal-content {
                width: 700px;
                height: 400px;
            }
            .modal-content img {
                width: 700px;
                height: 400px;
            }
        }

        @media (max-width: 991.98px) {
            .modal-content {
                width: 100%;
                height: auto;
            }
            .modal-content img {
                width: 100%;
                height: auto;
            }
        }

        .custom-ad-image {
                margin: 0;
                padding: 0;
                overflow: hidden;
            }

        .custom-ad-image img {
            width: 100%;
            max-height: 180px;
            object-fit: cover;
        }

        @media (max-width: 768px) {
            .custom-ad-image img {
                max-height: 120px;
            }
        }

        @media (max-width: 576px) {
            .custom-ad-image img {
                max-height: 100px; 
            }
        }
    </style>

    <!-- Home Page Modal Ad Start -->
    @foreach($advertisements as $advertisement)
        @if($advertisement->type == 'homepage_modal')
        <div class="modal fade" id="advertisementModal{{ $advertisement->id }}" tabindex="-1" role="dialog" aria-labelledby="advertisementModalLabel{{ $advertisement->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document" style="display: flex; justify-content: center; align-items: center; height: 100vh;">
                <div class="modal-content p-0" style="overflow: hidden;">
                    <button type="button" class="close position-absolute p-2" style="top: 0; right: 0; z-index: 1050;" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-white">&times;</span>
                    </button>
                    <a href="{{ $advertisement->link }}" target="_blank">
                         <x-image-with-loader src="{{ asset('images/ads/' . $advertisement->image) }}" class="img-fluid" alt="Advertisement" style="object-fit: cover; width: 100%; height: auto;"/>
                    </a>
                </div>
            </div>
        </div>
        @endif
    @endforeach
    <!-- Home Page Modal Ad End -->

    <!-- Carousel Slider,  Category Start -->
    <div class="container-fluid mb-3">
        <div class="row px-xl-5">
            @if($section_status->slider == 1)
                <div class="col-lg-8">
                    <div id="header-carousel" class="carousel slide carousel-fade mb-30 mb-lg-0" data-ride="carousel">
                        <ol class="carousel-indicators">
                            @foreach($sliders as $key => $slider)
                                <li data-target="#header-carousel" data-slide-to="{{ $key }}" class="{{ $key == 0 ? 'active' : '' }}"></li>
                            @endforeach
                        </ol>
                        <div class="carousel-inner">
                            @foreach($sliders as $key => $slider)
                                <div class="carousel-item position-relative {{ $key == 0 ? 'active' : '' }}" style="height: 430px;">
                                     <x-image-with-loader class="position-absolute w-100 h-100" src="{{ asset('images/slider/' . $slider->image) }}" style="object-fit: cover;"/>

                                    <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                                        <div class="p-3" style="max-width: 700px;">
                                            <h1 class="display-4 text-white mb-3 animate__animated animate__fadeInDown">{{ $slider->title }}</h1>
                                            <p class="mx-md-5 px-5 animate__animated animate__bounceIn">{{ $slider->sub_title }}</p>
                                            <a class="btn btn-outline-light py-2 px-4 mt-3 animate__animated animate__fadeInUp" href="{{ $slider->link }}">Shop Now</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if($section_status->categories == 1)
                <div class="col-lg-4">
                    <!-- Categories Start -->
                            @foreach($categories as $category)
                                <div class="product-offer mb-30" style="height: 200px;">
                                    <x-image-with-loader class="img-fluid" src="{{ asset('images/category/' . $category->image) }}" alt=""/>
                                    <div class="offer-text">
                                        <h3 class="text-white mb-3">{{ $category->name }}</h3>
                                        <a href="{{ route('category.show', $category->slug) }}" class="btn btn-primary">Shop Now</a>
                                    </div>
                                </div>
                            @endforeach
                    <!-- Categories End -->
                </div>
            @endif
        </div>
    </div>
    <!-- Carousel Slider, Category End -->

    @if($section_status->features == 1)
    <!-- Features Start -->
        <div class="container-fluid pt-5">
            <div class="row px-xl-5 pb-3">
                <div class="col-lg-3 col-md-6 col-sm-12 pb-1">
                    <div class="d-flex align-items-center bg-light mb-4" style="padding: 30px;">
                        <h1 class="fa fa-check text-primary m-0 mr-3"></h1>
                        <h5 class="font-weight-semi-bold m-0">Quality Product</h5>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12 pb-1">
                    <div class="d-flex align-items-center bg-light mb-4" style="padding: 30px;">
                        <h1 class="fa fa-shipping-fast text-primary m-0 mr-2"></h1>
                        <h5 class="font-weight-semi-bold m-0">Free Shipping</h5>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12 pb-1">
                    <div class="d-flex align-items-center bg-light mb-4" style="padding: 30px;">
                        <h1 class="fas fa-exchange-alt text-primary m-0 mr-3"></h1>
                        <h5 class="font-weight-semi-bold m-0">14-Day Return</h5>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12 pb-1">
                    <div class="d-flex align-items-center bg-light mb-4" style="padding: 30px;">
                        <h1 class="fa fa-phone-volume text-primary m-0 mr-3"></h1>
                        <h5 class="font-weight-semi-bold m-0">24/7 Support</h5>
                    </div>
                </div>
            </div>
        </div>
    <!-- Features End -->
    @endif

    @if($section_status->special_offer == 1)
    <!-- Special Offer Start -->
        <div class="container-fluid pt-5">
            <div class="row px-xl-5">
                @foreach($specialOffers as $specialOffer)
                    <div class="col-md-6">
                        <div class="product-offer mb-30" style="height: 300px;">
                            <x-image-with-loader class="img-fluid" src="{{ asset('images/special_offer/' . $specialOffer->offer_image) }}" alt="{{ $specialOffer->offer_title }}"/>
                            <div class="offer-text">
                                <h6 class="text-white text-uppercase">{{ $specialOffer->offer_name }}</h6>
                                <h3 class="text-white mb-3">{{ $specialOffer->offer_title }}</h3>
                                <a href="{{ route('special-offers.show', $specialOffer->slug) }}" class="btn btn-primary">Shop Now</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    <!-- Special Offer End -->
    @endif

    @if($section_status->campaigns == 1)
    <!-- Campaigns Start -->
        <div class="container-fluid pt-5">
            <div class="row px-xl-5">
                @foreach($campaigns as $campaign)
                    <div class="col-md-6">
                        <div class="product-offer mb-30" style="height: 300px;">
                            <x-image-with-loader class="img-fluid" src="{{ asset('images/campaign_banner/' . $campaign->banner_image) }}" alt="{{ $campaign->title }}"/>
                            <div class="offer-text">
                                <h3 class="text-white mb-3">{{ $campaign->title }}</h3>
                                <a href="{{ route('campaign.details.frontend', $campaign->slug) }}" class="btn btn-primary">View Campaign</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    <!-- Campaigns End -->
    @endif

    <!-- Featured Ad Start -->
    <div class="container-fluid pt-5 pb-3">
        <div class="px-xl-5">
            @foreach($advertisements as $advertisement)
                @if($advertisement->type == 'featured')
                    <div class="advertisement-image custom-ad-image">
                        <a href="{{ $advertisement->link }}" target="_blank">
                            <x-image-with-loader src="{{ asset('images/ads/' . $advertisement->image) }}" class="img-fluid" alt="Advertisement"/>
                        </a>
                    </div>
                @endif
            @endforeach
        </div>
    </div>  
    <!-- Featured Ad End -->

    @if($section_status->feature_products == 1)
    <!-- Feature Products Start -->
        @if ($featuredProducts->count() > 0)
            <div class="container-fluid pt-5 pb-3">
                <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4">
                    <span class="bg-secondary pr-3">Feature Products</span>
                </h2>
                <div class="row px-xl-5">
                    @foreach($featuredProducts as $product)
                        <div class="col-lg-3 col-md-4 col-sm-6 pb-1 mb-4">
                            <div class="product-item bg-light d-flex flex-column h-100">
                                <div class="product-img position-relative overflow-hidden" style="height: 250px;">
                                    <x-image-with-loader class="img-fluid w-100 h-100" src="{{ asset('/images/products/' . $product->feature_image) }}" alt="{{ $product->name }}" style="object-fit: cover;"/>
                                    <div class="product-action">
                                        @if ($product->stock && $product->stock->quantity > 0)
                                            <a class="btn btn-outline-dark btn-square add-to-cart" data-product-id="{{ $product->id }}" data-offer-id="0" data-price="{{ $product->price }}">
                                                <i class="fa fa-shopping-cart"></i>
                                            </a>
                                        @else
                                            <a class="btn btn-outline-dark btn-square disabled" aria-disabled="true">
                                                <i class="fa fa-shopping-cart"></i>
                                            </a>
                                        @endif
                                        <a class="btn btn-outline-dark btn-square add-to-wishlist" data-product-id="{{ $product->id }}" data-offer-id="0" data-price="{{ $product->price }}">
                                            <i class="fa fa-heart"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="text-center py-4 mt-auto">
                                    <a class="h6 text-decoration-none text-truncate" href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a>
                                    <div class="d-flex align-items-center justify-content-center mt-2">
                                        <h5>{{ $currency }} {{ $product->price }}</h5>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center mt-2">
                                        @if ($product->stock && $product->stock->quantity > 0)
                                            <p>Available: {{ $product->stock->quantity }}</p>
                                        @else
                                            <p>Out of Stock</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    <!-- Feature Products End -->
    @endif

    @if($section_status->flash_sell == 1)
    <!-- Flash Sell Start -->
        @if ($flashSells->count() > 0)
            <div class="container-fluid pt-5 pb-3">
                <div class="row px-xl-5">
                    @foreach($flashSells as $flashSell)
                        <div class="col-md-6">
                            <div class="product-offer mb-30" style="height: 300px;">
                                <x-image-with-loader class="img-fluid" src="{{ asset('images/flash_sell/' . $flashSell->flash_sell_image) }}" alt="{{ $flashSell->flash_sell_title }}"/>
                                <div class="offer-text">
                                    <h6 class="text-white text-uppercase">{{ $flashSell->flash_sell_name }}</h6>
                                    <h3 class="text-white mb-3">{{ $flashSell->flash_sell_title }}</h3>
                                    <a href="{{ route('flash-sells.show', $flashSell->slug) }}" class="btn btn-primary">Shop Now</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    <!-- Flash Sell End -->
    @endif

    @if($section_status->trending_products == 1)
    <!-- Trending Products Start -->
        @if ($trendingProducts->count() > 0)
            <div class="container-fluid pt-5 pb-3">
                <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4">
                    <span class="bg-secondary pr-3">Trending Now</span>
                </h2>
                <div class="row px-xl-5">
                    @foreach($trendingProducts as $product)
                        <div class="col-lg-3 col-md-4 col-sm-6 pb-1 mb-4">
                            <div class="product-item bg-light d-flex flex-column h-100">
                                <div class="product-img position-relative overflow-hidden" style="height: 250px;">
                                    <x-image-with-loader src="{{ asset('/images/products/' . $product->feature_image) }}" alt="{{ $product->name }}" class="img-fluid w-100 h-100" style="object-fit: cover;" />

                                    <div class="product-action">
                                        @if ($product->stock && $product->stock->quantity > 0)
                                            <a class="btn btn-outline-dark btn-square add-to-cart" data-product-id="{{ $product->id }}" data-offer-id="0" data-price="{{ $product->price }}">
                                                <i class="fa fa-shopping-cart"></i>
                                            </a>
                                        @else
                                            <a class="btn btn-outline-dark btn-square disabled" aria-disabled="true">
                                                <i class="fa fa-shopping-cart"></i>
                                            </a>
                                        @endif
                                        <a class="btn btn-outline-dark btn-square add-to-wishlist" data-product-id="{{ $product->id }}" data-offer-id="0" data-price="{{ $product->price }}">
                                            <i class="fa fa-heart"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="text-center py-4 mt-auto">
                                    <a class="h6 text-decoration-none text-truncate" href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a>
                                    <div class="d-flex align-items-center justify-content-center mt-2">
                                        <h5>{{ $currency }} {{ $product->price }}</h5>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center mt-2">
                                        @if ($product->stock && $product->stock->quantity > 0)
                                            <p>Available: {{ $product->stock->quantity }}</p>
                                        @else
                                            <p>Out of Stock</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    <!-- Trending Products End -->
    @endif

    @if($section_status->buy_one_get_one == 1)
    <!-- Buy One Get One Start -->
        @if ($buyOneGetOneProducts->count() > 0)
            <div class="container-fluid pt-5 pb-3">
                <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4">
                    <span class="bg-secondary pr-3">Buy One Get One</span>
                </h2>
                <div class="row px-xl-5">
                    @foreach($buyOneGetOneProducts as $bogo)
                        <div class="col-lg-3 col-md-4 col-sm-6 pb-1 mb-4">
                            <div class="product-item bg-light d-flex flex-column h-100">
                                <div class="product-img position-relative overflow-hidden" style="height: 250px;">
                                    <x-image-with-loader src="{{ asset('/images/buy_one_get_one/' . $bogo->feature_image) }}" alt="{{ $bogo->product->name }}" class="img-fluid w-100 h-100" style="object-fit: cover;" />
                                    <div class="product-action">
                                        <a class="btn btn-outline-dark btn-square" href="{{ route('product.show.bogo', $bogo->product->slug) }}">
                                            <i class="fa fa-shopping-cart"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="text-center py-4 mt-auto">
                                    <a class="h6 text-decoration-none text-truncate" href="{{ route('product.show.bogo', $bogo->product->slug) }}">{{ $bogo->product->name }}</a>
                                    <div class="d-flex align-items-center justify-content-center mt-2">
                                        <h5>{{ $currency }} {{ $bogo->price }}</h5>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center mt-2">
                                        @if ($bogo->product->stock && $bogo->product->stock->quantity > 0)
                                            <span>&nbsp;</span>
                                        @else
                                            <p>Out of Stock</p>
                                        @endif
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center mt-2">
                                        <span class="badge badge-primary">Get {{ $bogo->get_products->count() }} extra products</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    <!-- Buy One Get One End -->
    @endif

    <!-- Recent Ad Start -->
    <div class="container-fluid pt-5 pb-3">
        <div class="px-xl-5">
            @foreach($advertisements as $advertisement)
                @if($advertisement->type == 'recent')
                    <div class="advertisement-image custom-ad-image">
                        <a href="{{ $advertisement->link }}" target="_blank">
                            <x-image-with-loader src="{{ asset('images/ads/' . $advertisement->image) }}" class="img-fluid" alt="Advertisement"/>
                        </a>
                    </div>
                @endif
            @endforeach
        </div>
    </div>  
    <!-- Recent Ad End -->

    @if($section_status->recent_products == 1)
    <!-- Recent Products Start -->
        @if($recentProducts->count() > 0)
            <div class="container-fluid pt-5 pb-3">
                <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4">
                    <span class="bg-secondary pr-3">Recent Products</span>
                </h2>
                <div class="row px-xl-5">
                    @foreach($recentProducts as $product)
                        <div class="col-lg-3 col-md-4 col-sm-6 pb-1 mb-4">
                            <div class="product-item bg-light d-flex flex-column h-100">
                                <div class="product-img position-relative overflow-hidden" style="height: 250px;">
                                    <x-image-with-loader src="{{ asset('/images/products/' . $product->feature_image) }}" alt="{{ $product->name }}" class="img-fluid w-100 h-100" style="object-fit: cover;" />

                                    <div class="product-action">
                                        @if ($product->stock && $product->stock->quantity > 0)
                                            <a class="btn btn-outline-dark btn-square add-to-cart" data-product-id="{{ $product->id }}" data-offer-id="0" data-price="{{ $product->price }}">
                                                <i class="fa fa-shopping-cart"></i>
                                            </a>
                                        @else
                                            <a class="btn btn-outline-dark btn-square disabled" aria-disabled="true">
                                                <i class="fa fa-shopping-cart"></i>
                                            </a>
                                        @endif
                                        <a class="btn btn-outline-dark btn-square add-to-wishlist" data-product-id="{{ $product->id }}" data-offer-id="0" data-price="{{ $product->price }}">
                                            <i class="fa fa-heart"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="text-center py-4 mt-auto">
                                    <a class="h6 text-decoration-none text-truncate" href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a>
                                    <div class="d-flex align-items-center justify-content-center mt-2">
                                        <h5>{{ $currency }} {{ $product->price }}</h5>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center mt-2">
                                        @if ($product->stock && $product->stock->quantity > 0)
                                            <p>Available: {{ $product->stock->quantity }}</p>
                                        @else
                                            <p>Out of Stock</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    <!-- Recent Products End -->
    @endif

    @if($section_status->bundle_products == 1)
    <!-- Bundle Products Start -->
        @if ($bundleProducts->count() > 0)
            <div class="container-fluid pt-5 pb-3">
                <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4">
                    <span class="bg-secondary pr-3">Bundle Products</span>
                </h2>
                <div class="row px-xl-5">
                    @foreach($bundleProducts as $bundle)
                        <div class="col-lg-3 col-md-4 col-sm-6 pb-1 mb-4">
                            <div class="product-item bg-light d-flex flex-column h-100">
                                <div class="product-img position-relative overflow-hidden" style="height: 250px;">
                                    <x-image-with-loader src="{{ asset('/images/bundle_product/' . $bundle->feature_image) }}" alt="{{ $bundle->name }}" class="img-fluid w-100 h-100" style="object-fit: cover;" />
                                    <div class="product-action">
                                        <a class="btn btn-outline-dark btn-square" href="{{ route('bundle_product.show', $bundle->slug) }}">
                                            <i class="fa fa-shopping-cart"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="text-center py-4 mt-auto">
                                    <a class="h6 text-decoration-none text-truncate" href="{{ route('bundle_product.show', $bundle->slug) }}">{{ $bundle->name }}</a>
                                    <div class="d-flex align-items-center justify-content-center mt-2">
                                        <h5>{{ $currency }} {{ $bundle->price }}</h5>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center mt-2">
                                        @if ($bundle->quantity > 0)
                                            <span>&nbsp;</span>
                                        @else
                                            <p>Out of Stock</p>
                                        @endif
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center mt-2">
                                        <span class="badge badge-primary">Includes {{ count($bundle->product_ids) }} Products</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    <!-- Bundle Products End -->
    @endif

    @if ($section_status->category_products == 1)
    <!-- Category Wise Products Start -->
        <div class="container-fluid pt-5 pb-3">
            <div class="row px-xl-5">
                @foreach($initialCategoryProducts as $category)
                    @if($category->products->isNotEmpty())
                        <div class="col-12 mb-4">
                                <h2 class="section-title position-relative text-uppercase mb-4">
                                    <span class="bg-secondary pr-3">{{ $category->name }}</span>
                                </h2>
                            <div class="owl-carousel related-carousel" data-category-id="{{ $category->id }}" data-page="2">
                                @foreach($category->products as $product)
                                    <div class="product-item bg-light">
                                        <div class="product-img position-relative overflow-hidden" style="height: 250px;">
                                            <x-image-with-loader class="img-fluid w-100 h-100" src="{{ asset('/images/products/' . $product->feature_image) }}" alt="{{ $product->name }}" style="object-fit: cover;"/>
                                            <div class="product-action">
                                                @if ($product->stock && $product->stock->quantity > 0)
                                                    <a class="btn btn-outline-dark btn-square add-to-cart" data-product-id="{{ $product->id }}" data-offer-id="0" data-price="{{ $product->price }}">
                                                        <i class="fa fa-shopping-cart"></i>
                                                    </a>
                                                @else
                                                    <a class="btn btn-outline-dark btn-square disabled" aria-disabled="true">
                                                        <i class="fa fa-shopping-cart"></i>
                                                    </a>
                                                @endif
                                                <a class="btn btn-outline-dark btn-square add-to-wishlist" data-product-id="{{ $product->id }}" data-offer-id="0" data-price="{{ $product->price }}">
                                                    <i class="fa fa-heart"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="text-center py-4">
                                            <a class="h6 text-decoration-none text-truncate" href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a>
                                            <div class="d-flex align-items-center justify-content-center mt-2">
                                                <h5>{{ $currency }} {{ $product->price }}</h5>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-center mt-2">
                                                @if ($product->stock && $product->stock->quantity > 0)
                                                    <p>Available: {{ $product->stock->quantity }}</p>
                                                @else
                                                    <p>Out of Stock</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    <!-- Category Wise Products End -->
    @endif

    @if ($section_status->popular_products == 1)
    <!-- Popular Products Start -->
        @if ($popularProducts->count() > 0)
            <div class="container-fluid pt-5 pb-3">
                <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4">
                    <span class="bg-secondary pr-3">Popular Products</span>
                </h2>
                <div class="row px-xl-5">
                    @foreach($popularProducts as $product)
                        <div class="col-lg-3 col-md-4 col-sm-6 pb-1 mb-4">
                            <div class="product-item bg-light d-flex flex-column h-100">
                                <div class="product-img position-relative overflow-hidden" style="height: 250px;">
                                    <x-image-with-loader class="img-fluid w-100 h-100" src="{{ asset('/images/products/' . $product->feature_image) }}" alt="{{ $product->name }}" style="object-fit: cover;"/>
                                    <div class="product-action">
                                        @if ($product->stock && $product->stock->quantity > 0)
                                            <a class="btn btn-outline-dark btn-square add-to-cart" data-product-id="{{ $product->id }}" data-offer-id="0" data-price="{{ $product->price }}">
                                                <i class="fa fa-shopping-cart"></i>
                                            </a>
                                        @else
                                            <a class="btn btn-outline-dark btn-square disabled" aria-disabled="true">
                                                <i class="fa fa-shopping-cart"></i>
                                            </a>
                                        @endif
                                        <a class="btn btn-outline-dark btn-square add-to-wishlist" data-product-id="{{ $product->id }}" data-offer-id="0" data-price="{{ $product->price }}">
                                            <i class="fa fa-heart"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="text-center py-4 mt-auto">
                                    <a class="h6 text-decoration-none text-truncate" href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a>
                                    <div class="d-flex align-items-center justify-content-center mt-2">
                                        <h5>{{ $currency }} {{ $product->price }}</h5>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center mt-2">
                                        @if ($product->stock && $product->stock->quantity > 0)
                                            <p>Available: {{ $product->stock->quantity }}</p>
                                        @else
                                            <p>Out of Stock</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    <!-- Popular Products End -->
    @endif

    <!-- Vendor ad Start -->
    <div class="container-fluid pt-5 pb-3">
        <div class="px-xl-5">
            @foreach($advertisements as $advertisement)
                @if($advertisement->type == 'vendor')
                    <div class="advertisement-image custom-ad-image">
                        <a href="{{ $advertisement->link }}" target="_blank">
                            <x-image-with-loader src="{{ asset('images/ads/' . $advertisement->image) }}" class="img-fluid" alt="Advertisement"/>
                        </a>
                    </div>
                @endif
            @endforeach
        </div>
    </div>  
    <!-- Vendor ad End -->

    @if($section_status->vendors == 1)
    <!-- Vendor Start -->
        <div class="container-fluid py-5">
            <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4">
                <span class="bg-secondary pr-3">Vendors</span>
            </h2>

            <div class="row px-xl-5">
                <div class="col">
                    <div class="owl-carousel vendor-carousel">
                        @foreach($suppliers as $supplier)
                            <div class="bg-light p-4">
                                <a href="{{ route('supplier.show', $supplier->slug) }}">
                                    <x-image-with-loader src="{{ asset('/images/supplier/' . $supplier->image) }}" alt="{{ $supplier->name }}"/>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    <!-- Vendor End -->
    @endif

@endsection

@section('script')

    <script>
        $(document).ready(function() {
            $('.related-carousel').each(function() {
                var $carousel = $(this);
                var categoryId = $carousel.data('category-id');
                var page = $carousel.data('page');
                var isLoading = false;

                $carousel.on('changed.owl.carousel', function(event) {
                    if (event.item.index + event.page.size >= event.item.count && !isLoading) {
                        isLoading = true;
                        $.ajax({
                            url: '{{ route('getCategoryProducts') }}',
                            method: 'GET',
                            data: {
                                category_id: categoryId,
                                page: page
                            },
                            success: function(response) {
                                page++;
                                $carousel.data('page', page);
                                $.each(response.data, function(index, product) {
                                    var productHtml = `
                                        <div class="product-item bg-light">
                                            <div class="product-img position-relative overflow-hidden" style="height: 250px;">
                                                <img class="img-fluid w-100 h-100" src="/images/products/${product.feature_image}" alt="${product.name}" style="object-fit: cover;"/>
                                                <div class="product-action">
                                                    ${product.stock && product.stock.quantity > 0 ? 
                                                        `<a class="btn btn-outline-dark btn-square add-to-cart" data-product-id="${product.id}" data-offer-id="0" data-price="${product.price}">
                                                            <i class="fa fa-shopping-cart"></i>
                                                        </a>` :
                                                        `<a class="btn btn-outline-dark btn-square disabled" aria-disabled="true">
                                                            <i class="fa fa-shopping-cart"></i>
                                                        </a>`
                                                    }
                                                    <a class="btn btn-outline-dark btn-square add-to-wishlist" data-product-id="${product.id}" data-offer-id="0" data-price="${product.price}">
                                                        <i class="fa fa-heart"></i>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="text-center py-4">
                                                <a class="h6 text-decoration-none text-truncate" href="/product/${product.slug}">${product.name}</a>
                                                <div class="d-flex align-items-center justify-content-center mt-2">
                                                    <h5>${product.price}</h5>
                                                </div>
                                                <div class="d-flex align-items-center justify-content-center mt-2">
                                                    ${product.stock && product.stock.quantity > 0 ? 
                                                        `<p>Available: ${product.stock.quantity}</p>` : 
                                                        `<p>Out of Stock</p>`
                                                    }
                                                </div>
                                            </div>
                                        </div>`;
                                    $carousel.trigger('add.owl.carousel', [$(productHtml)]).trigger('refresh.owl.carousel');
                                });
                                isLoading = false;
                            },
                            error: function(xhr, status, error) {
                                console.error('Error fetching products:', error);
                                isLoading = false;
                            }
                        });
                    }
                });
            });
        });
    </script>

    <!-- <script>
        $(document).ready(function() {

            @foreach($advertisements as $advertisement)
                @if($advertisement->type == 'homepage_modal')
                    $('#advertisementModal{{ $advertisement->id }}').modal('show');
                @endif
            @endforeach
        });
    </script> -->

@endsection