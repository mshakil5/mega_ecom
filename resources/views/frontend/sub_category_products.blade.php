@extends('frontend.layouts.app')
@section('title', $title)
@section('content')

<div class="container-fluid pt-5 pb-3">
    <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4">
        <span class="bg-secondary pr-3">Explore Products in {{ $sub_category->name }}</span>
    </h2>
    <div class="row px-xl-5">
        @foreach($sub_category->products as $product)
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

@endsection