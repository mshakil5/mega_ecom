@extends('frontend.layouts.app')
@section('title', $title)
@section('content')

<div class="container for-you">
    <h2 class="title text-center mb-5 mt-4">Explore Products in {{ $category->name }}</h2>
    <div class="products">
        <div class="row justify-content-center">
            @foreach($products as $product)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="product product-2">
                        <figure class="product-media">
                            <a href="{{ route('product.show', $product->slug) }}">
                                <x-image-with-loader src="{{ asset('/images/products/' . $product->feature_image) }}" alt="{{ $product->name }}" class="product-image" />
                            </a>

                            @php
                                $sellingPrice = $product->selling_price;
                                $colors = $product->available_colors;
                                $sizes = $product->available_sizes;
                            @endphp

                            @if ($product->is_in_stock)

                                <div class="product-action-vertical">
                                    <a href="#" class="btn-product-icon btn-wishlist add-to-wishlist btn-expandable" title="Add to wishlist" data-product-id="{{ $product->id }}" data-offer-id="0" data-price="{{ $sellingPrice ?? $product->price }}"> <span>Add to wishlist</span> </a>
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
                            <h3 class="product-title"><a href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a></h3>
                            <div class="product-price">
                                {{ $currency }} {{ number_format($sellingPrice ?? $product->price, 2) }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="col-12">
        <div class="pagination-wrapper d-flex justify-content-center">
            {{ $products->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>

@endsection