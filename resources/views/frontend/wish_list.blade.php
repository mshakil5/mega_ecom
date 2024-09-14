@extends('frontend.layouts.app')

@section('content')
<div class="container for-you">

    @php
        $currency = \App\Models\CompanyDetails::value('currency');
    @endphp

    @if($products->isEmpty())
    <h1 class="title text-center mb-5 mt-4">Wishlist is empty</h1>
    @else
    <h1 class="title text-center mb-5 mt-4">Wishlist</h1>
    <div class="products">
        <div class="row justify-content-center">
        @foreach($products as $product)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="product product-2">
                    <figure class="product-media">
                        <a href="{{ route('product.show', $product->slug) }}">
                            <x-image-with-loader src="{{ asset('/images/products/' . $product->feature_image) }}" alt="{{ $product->name }}" class="product-image" />
                        </a>

                        @if ($product->stock && $product->stock->quantity > 0)
                            <div class="product-action-vertical">
                                <a href="#" class="btn-product-icon btn-wishlist add-to-wishlist" title="Add to wishlist" 
                                data-product-id="{{ $product->id }}"
                                data-offer-id="{{ $product->offer_id }}"
                                data-price="{{ $product->offer_price ?? $product->flash_sell_price ?? $product->price }}"
                                data-campaign-id="{{ $product->campaign_id ?? '' }}"></a>
                            </div>

                            <div class="product-action">
                                <a href="#" class="btn-product btn-cart add-to-cart" title="Add to cart" data-product-id="{{ $product->id }}"
                                data-offer-id="{{ $product->offer_id }}"
                                data-price="{{ $product->offer_price ?? $product->flash_sell_price ?? $product->price }}" data-campaign-id="{{ $product->campaign_id }}"><span>Add to Cart</span></a>
                            </div>
                        @else
                            <span class="product-label label-out-stock">Out of Stock</span>
                        @endif
                    </figure>

                    <div class="product-body">
                        <h3 class="product-title"><a href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a></h3>
                        <div class="product-price">
                            @if(isset($product->offer_price))
                                <del>{{ $currency }} {{ number_format($product->price, 2) }}</del>
                                {{ $currency }} {{ number_format($product->offer_price, 2) }}
                                @php
                                    $discountPercentage = (($product->price - $product->offer_price) / $product->price) * 100;
                                @endphp
                                <small>({{ round($discountPercentage, 0) }}% off)</small>
                            @elseif(isset($product->flash_sell_price))
                                <del>{{ $currency }} {{ number_format($product->price, 2) }}</del>
                                {{ $currency }} {{ number_format($product->flash_sell_price, 2) }}
                                @php
                                    $discountPercentage = (($product->price - $product->flash_sell_price) / $product->price) * 100;
                                @endphp
                                <small>({{ round($discountPercentage, 0) }}% off)</small>
                            @else
                                {{ $currency }} {{ number_format($product->price, 2) }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        </div>
    </div>
    @endif

</div>
@endsection