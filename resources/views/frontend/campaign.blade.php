@extends('frontend.layouts.app')

@section('title', $title)

@section('content')
<div class="container for-you">
    <h2 class="title text-center mb-5 mt-4">Explore Products in {{ $title }}</h2>

    <div class="products">
        <div class="row justify-content-center">
            @foreach($campaignRequests as $request)
                @foreach($request->campaignRequestProducts as $detail)
                @if($detail->product)
                @php
                    $originalPrice = $detail->product->price;
                    $campaignPrice = $detail->campaign_price;
                    $discount = 100 * (($originalPrice - $campaignPrice) / $originalPrice);
                @endphp
                <div class="col-6 col-md-4 col-lg-3">
                <div class="product product-2">
                    <figure class="product-media">
                        <a href="{{ route('product.show.campaign', ['slug' => $detail->product->slug, 'supplierId' => $request->supplier_id]) }}">
                            <x-image-with-loader src="{{ asset('/images/products/' . $detail->product->feature_image) }}" alt="{{ $detail->product->name }}" class="product-image" />
                        </a>

                        @if ($detail->quantity > 0)
                            <div class="product-action-vertical">
                                <a href="#" class="btn-product-icon btn-wishlist add-to-wishlist btn-expandable" title="Add to wishlist" data-product-id="{{ $detail->product->id }}" data-offer-id="0" data-price="{{ $campaignPrice }}"
                                data-campaign-id="{{ $detail->id }}"><span>Add to wishlist</span></a>
                            </div>

                            <!-- <div class="product-action">
                                <a href="#" class="btn-product btn-cart add-to-cart" title="Add to cart" data-product-id="{{ $detail->product->id }}" data-price="{{ $campaignPrice }}" data-offer-id="0" data-campaign-id="{{ $detail->id }}"><span>add to cart</span></a>
                            </div> -->
                            <div class="product-action">
                                <a href="#" class="btn-product btn-cart" title="Add to cart"
                                data-product-id="{{ $detail->product->id }}" 
                                data-offer-id="0"
                                data-campaign-id="{{ $detail->id }}" 
                                data-price="{{ $campaignPrice }}" 
                                data-toggle="modal" data-target="#quickAddToCartModal" 
                                data-image ="{{ asset('images/products/' . $detail->product->feature_image) }}" data-stock="{{ $detail->quantity }}">
                                    <span>add to cart</span>
                                </a>
                            </div>
                        @else
                            <span class="product-label label-out-stock">Out of stock</span>
                        @endif
                    </figure>

                    <div class="product-body">
                        <h3 class="product-title"><a href="{{ route('product.show.campaign', ['slug' => $detail->product->slug, 'supplierId' => $request->supplier_id]) }}">{{ $detail->product->name }}</a></h3>
                        <div class="product-price">
                        <del>{{ $currency }} {{ number_format($originalPrice, 2) }}</del>
                            {{ $currency }} {{ number_format($campaignPrice, 2) }} 
                            <small>({{ round($discount, 0) }}% off)</small>
                        </div>
                    </div>
                </div>
                </div>
                @endif
                @endforeach
            @endforeach
        </div>
    </div>

</div>
@endsection
