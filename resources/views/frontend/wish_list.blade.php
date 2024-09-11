@extends('frontend.layouts.app')

@section('content')
<div class="container-fluid pt-5 pb-3">

    @php
        $currency = \App\Models\CompanyDetails::value('currency');
    @endphp

    @if($products->isEmpty())
        <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4">
            <span class="bg-secondary pr-3">Wishlist is empty</span>
        </h2>
    @else
        <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4">
            <span class="bg-secondary pr-3">Wishlist</span>
        </h2>
        <div class="row px-xl-5">
            @foreach($products as $product)
            <div class="col-lg-3 col-md-4 col-sm-6 pb-1">
                <div class="product-item bg-light mb-4 d-flex flex-column h-100">
                    <div class="product-img position-relative overflow-hidden" style="height: 250px;">
                        <img class="img-fluid w-100 h-100" src="{{ asset('/images/products/' . $product->feature_image) }}" alt="{{ $product->name }}" style="object-fit: cover;">
                        <div class="product-action">

                        @if(!$product->campaign_id)
                            @if ($product->stock && $product->stock->quantity > 0)
                                <a class="btn btn-outline-dark btn-square add-to-cart"
                                data-product-id="{{ $product->id }}"
                                data-offer-id="{{ $product->offer_id }}"
                                data-price="{{ $product->offer_price ?? $product->flash_sell_price ?? $product->price }}" data-campaign-id="{{ $product->campaign_id }}">
                                    <i class="fa fa-shopping-cart"></i>
                                </a>
                            @else
                                <a class="btn btn-outline-dark btn-square disabled" aria-disabled="true">
                                    <i class="fa fa-shopping-cart"></i>
                                </a>
                            @endif
                        @else
                        @if ($product->quantity && $product->quantity > 0)
                                <a class="btn btn-outline-dark btn-square add-to-cart"
                                data-product-id="{{ $product->id }}"
                                data-offer-id="{{ $product->offer_id }}"
                                data-price="{{ $product->offer_price ?? $product->flash_sell_price ?? $product->price }}" data-campaign-id="{{ $product->campaign_id }}">
                                    <i class="fa fa-shopping-cart"></i>
                                </a>
                            @else
                                <a class="btn btn-outline-dark btn-square disabled" aria-disabled="true">
                                    <i class="fa fa-shopping-cart"></i>
                                </a>
                            @endif
                        @endif
                            <a class="btn btn-outline-dark btn-square add-to-wishlist"
                            data-product-id="{{ $product->id }}"
                            data-offer-id="{{ $product->offer_id }}"
                            data-price="{{ $product->offer_price ?? $product->flash_sell_price ?? $product->price }}"
                            data-campaign-id="{{ $product->campaign_id ?? '' }}">
                                <i class="far fa-heart"></i>
                            </a>
                        </div>
                    </div>
                    <div class="text-center py-4 mt-auto">
                        <a class="h6 text-decoration-none text-truncate" href="{{ route('product.show', ['slug' => $product->slug, 'offerId' => $product->offer_id]) }}">{{ $product->name }}</a>
                        <div class="d-flex align-items-center justify-content-center mt-2">
                            <h5>
                                @if(isset($product->offer_price))
                                    <del>{{ $currency }} {{ $product->price }}</del>
                                    {{ $currency }} {{ $product->offer_price }}
                                    @php
                                        $discountPercentage = (($product->price - $product->offer_price) / $product->price) * 100;
                                    @endphp
                                    <small>({{ round($discountPercentage, 0) }}% off)</small>
                                @elseif(isset($product->flash_sell_price))
                                    <del>{{ $currency }} {{ $product->price }}</del>
                                    {{ $currency }} {{ $product->flash_sell_price }}
                                    @php
                                        $discountPercentage = (($product->price - $product->flash_sell_price) / $product->price) * 100;
                                    @endphp
                                    <small>({{ round($discountPercentage, 0) }}% off)</small>
                                @else
                                    {{ $currency }} {{ $product->price }}
                                @endif
                            </h5>
                        </div>

                        <div class="d-flex align-items-center justify-content-center mt-2">
                            @if(!$product->campaign_id)
                                @if ($product->stock && $product->stock->quantity > 0)
                                    <!-- <p>Available: {{ $product->stock->quantity }}</p> -->
                                @else
                                    <p>Out of Stock</p>
                                @endif
                            @else
                                @if ($product->quantity && $product->quantity > 0)
                                @else
                                    <p>Out of Stock</p>
                                @endif  
                            @endif 
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif

</div>
@endsection