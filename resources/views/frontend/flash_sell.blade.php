@extends('frontend.layouts.app')
@section('title', $title)
@section('content')

<div class="container for-you">
    <h2 class="title text-center mb-5 mt-4">Explore Products in {{ $flashSell->flash_sell_name }}</h2>
    <div class="products">
        @php
            $currency = \App\Models\CompanyDetails::value('currency');
        @endphp

        <div class="row justify-content-center">
        @foreach($flashSell->FlashSellDetails as $detail)
            @if($detail->product)
                @php
                    $discount = 100 * (($detail->old_price - $detail->flash_sell_price) / $detail->old_price);
                @endphp
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="product product-2">
                        <figure class="product-media">
                            <a href="{{ route('product.show.offer', ['slug' => $detail->product->slug, 'offerId' => 2]) }}">
                                <x-image-with-loader src="{{ asset('/images/products/' . $detail->product->feature_image) }}" alt="{{ $detail->product->name }}" class="product-image" />
                            </a>

                            @if ($detail->product->stock && $detail->product->stock->quantity > 0)
                                <div class="product-action-vertical">
                                    <a href="#" class="btn-product-icon btn-wishlist add-to-wishlist btn-expandable" title="Add to wishlist" data-product-id="{{ $detail->product->id }}" data-offer-id="2" data-price="{{ $detail->flash_sell_price }}"><span>Add to wishlist</span></a>
                                </div>

                                <!-- <div class="product-action">
                                    <a href="#" class="btn-product btn-cart add-to-cart" title="Add to cart" data-product-id="{{ $detail->product->id }}" data-offer-id="2" data-price="{{ $detail->flash_sell_price }}"><span>add to cart</span></a>
                                </div> -->
                                <div class="product-action">
                                    <a href="#" class="btn-product btn-cart" title="Add to cart"
                                    data-product-id="{{ $detail->product->id }}" 
                                    data-offer-id="2" 
                                    data-price="{{ $detail->flash_sell_price }}" 
                                    data-toggle="modal" data-target="#quickAddToCartModal" 
                                    data-image ="{{ asset('images/products/' . $detail->product->feature_image) }}" data-stock="{{ $detail->product->stock->quantity }}">
                                        <span>add to cart</span>
                                    </a>
                                </div>
                            @else
                                <span class="product-label label-out-stock">Out of stock</span>
                            @endif
                        </figure>

                        <div class="product-body">
                            <h3 class="product-title"><a href="{{ route('product.show', ['slug' => $detail->product->slug, 'offerId' => 2]) }}">{{ $detail->product->name }}</a></h3>
                            <div class="product-price">
                                {{ $currency }} {{ number_format($detail->flash_sell_price, 2) }}
                                <del>{{ $currency }} {{ number_format($detail->old_price, 2) }}</del>
                                <small>({{ round($discount, 0) }}% off)</small>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
        </div>
    </div>
</div>

@endsection