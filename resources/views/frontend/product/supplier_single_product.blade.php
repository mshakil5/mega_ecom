@extends('frontend.layouts.app')
@section('title', $title)
@section('content')

<div class="page-content mt-3">
    <div class="container">
        <div class="product-details-top">
            <div class="row">
                <div class="col-md-6">
                    <div class="product-gallery product-gallery-vertical">
                        <div class="row">
                            <figure class="product-main-image">
                                <x-image-with-loader src="{{ asset('/images/products/' . $product->feature_image) }}" alt="{{ $product->name }}" class="product-image" />
                            </figure>

                            <div id="product-zoom-gallery" class="product-image-gallery">
                                @foreach($product->images as $index => $image)
                                    <a class="product-gallery-item {{ $index == 0 ? 'active' : '' }}" href="#" data-image="{{ asset('/images/products/' . $image->image) }}" data-zoom-image="{{ asset('/images/products/' . $image->image) }}">
                                        <img src="{{ asset('/images/products/' . $image->image) }}" alt="product image">
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="product-details">
                        <h1 class="product-title">{{ $product->name }}</h1>

                        <div class="product-price">
                            {{ $currency }} {{ $regularPrice }}
                        </div>

                        <div class="product-content">
                            <p>{!! $stockDescription !!} </p>
                        </div>

                        <div class="details-filter-row details-row-size">
                            <label>Color:</label>
                            <div class="product-nav product-nav-thumbs">
                                <form id="colorForm">
                                @php
                                    $colors = SupplierStock::where('supplier_id', $supplierId)
                                        ->where('product_id', $product->id)
                                        ->where('quantity', '>', 0)
                                        ->distinct()
                                        ->pluck('color');
                                @endphp

                                    @foreach($colors as $index => $color)
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" class="custom-control-input" id="color-{{ $index }}" name="color" value="{{ $color }}">
                                            <label class="custom-control-label" for="color-{{ $index }}">{{ $color }}</label>
                                        </div>
                                    @endforeach
                                </form>
                            </div>
                        </div>

                        <div class="details-filter-row details-row-size">
                            <label for="size">Size:</label>
                            <form id="sizeForm">
                                @php
                                    $sizes = SupplierStock::where('supplier_id', $supplierId)
                                        ->where('product_id', $product->id)
                                        ->where('quantity', '>', 0)
                                        ->distinct()
                                        ->pluck('color');
                                @endphp
                                @foreach($sizes as $index => $size)
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" class="custom-control-input" id="size-{{ $index }}" name="size" value="{{ $size }}">
                                        <label class="custom-control-label" for="size-{{ $index }}">{{ $size }}</label>
                                    </div>
                                @endforeach
                            </form>
                        </div>

                        @if($stockQuantity <= 0)
                            <div class="text-danger mt-2 mb-2">
                                This product is currently out of stock.
                            </div>
                        @endif

                        <div class="details-filter-row details-row-size">
                            <label for="qty">Qty:</label>
                            <div class="product-details-quantity">
                                <input type="number" id="qty" class="form-control quantity-input" value="1" min="1" max="{{ $stockQuantity  }}" step="1" data-decimals="0" required>
                            </div>
                        </div>

                        <div class="product-details-action">
                            <a href="#" 
                            class="btn-product btn-cart add-to-cart" 
                            data-product-id="{{ $product->id }}"
                            data-price="{{ $regularPrice }}" 
                            data-offer-id="0" 
                            @if($stockQuantity <= 0)
                            style="pointer-events: none; opacity: 0.5;" 
                            title="Out of stock"
                            @endif>
                            <span>add to cart</span>
                            </a>
                        </div>

                        <div class="product-details-footer">
                            <div class="product-cat" style="display: flex; align-items: center;">
                                <span style="margin-right: 5px;">Category:</span>
                                <a href="{{ route('category.show', $product->category->slug) }}">
                                    {{ $product->category->name }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="product-details-tab">
            <ul class="nav nav-pills justify-content-center" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="product-desc-link" data-toggle="tab" href="#product-desc-tab" role="tab" aria-controls="product-desc-tab" aria-selected="true">Description</a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="product-desc-tab" role="tabpanel" aria-labelledby="product-desc-link">
                    <div class="product-desc-content">
                        <h3>Product Information</h3>
                        {!! $product->description !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection