@extends('frontend.layouts.app')
@section('title', $title)
@section('content')

<div class="page-content mt-3">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-10">
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
                                    <p>{!! $product->short_description !!} </p>
                                </div>

                                <div class="details-filter-row details-row-size">
                                    <label>Color:</label>

                                    <div class="product-nav product-nav-thumbs">
                                        <form id="colorForm">
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" class="custom-control-input" id="color-1" name="color" value="Black">
                                                <label class="custom-control-label" for="color-1">Black</label>
                                            </div>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" class="custom-control-input" id="color-2" name="color" value="White">
                                                <label class="custom-control-label" for="color-2">White</label>
                                            </div>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" class="custom-control-input" id="color-3" name="color" value="Red">
                                                <label class="custom-control-label" for="color-3">Red</label>
                                            </div>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" class="custom-control-input" id="color-4" name="color" value="Blue">
                                                <label class="custom-control-label" for="color-4">Blue</label>
                                            </div>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" class="custom-control-input" id="color-5" name="color" value="Green">
                                                <label class="custom-control-label" for="color-5">Green</label>
                                            </div>
                                        </form>    
                                    </div>
                                </div>

                                <div class="details-filter-row details-row-size">
                                    <label for="size">Size:</label>
                                    <form id="sizeForm">
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" class="custom-control-input" id="size-1" name="size" value="XS">
                                            <label class="custom-control-label" for="size-1">XS</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" class="custom-control-input" id="size-2" name="size" value="S">
                                            <label class="custom-control-label" for="size-2">S</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" class="custom-control-input" id="size-3" name="size" value="M">
                                            <label class="custom-control-label" for="size-3">M</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" class="custom-control-input" id="size-4" name="size" value="L">
                                            <label class="custom-control-label" for="size-4">L</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" class="custom-control-input" id="size-5" name="size" value="XL">
                                            <label class="custom-control-label" for="size-5">XL</label>
                                        </div>
                                    </form>
                                </div>

                                @if($quantity <= 0)
                                    <div class="text-danger mt-2 mb-2">
                                        This product is currently out of stock.
                                    </div>
                                @endif

                                <div class="details-filter-row details-row-size">
                                    <label for="qty">Qty:</label>
                                    <div class="product-details-quantity">
                                        <input type="number" id="qty" class="form-control quantity-input" value="1" min="1" max="{{ $quantity }}" step="1" data-decimals="0" required>
                                    </div>
                                </div>

                                <div class="product-details-action">
                                    <a href="#" 
                                    class="btn-product btn-cart add-to-cart" 
                                    data-product-id="{{ $product->id }}" 
                                    data-offer-id="0" 
                                    data-price="{{ $regularPrice }}"
                                    data-bogo-id="{{ $bogo->id }}"
                                    @if($quantity <= 0)
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
            </div>
            <aside class="col-xl-2 d-md-none d-xl-block">
                <div class="sidebar sidebar-product">
                    <div class="widget widget-products">
                        <h4 class="widget-title">Get {{ $bogoProducts->count() }} extra products</h4>

                        <div class="products">
                            @foreach($bogoProducts as $bogoProduct)
                                <div class="product product-sm">
                                    <figure class="product-media">
                                        <a href="{{ route('product.show', $bogoProduct->slug) }}">
                                            <img src="{{ asset('/images/products/' . $bogoProduct->feature_image) }}" alt="{{ $bogoProduct->name }}" class="product-image">
                                        </a>
                                    </figure>

                                    <div class="product-body">
                                        <h5 class="product-title">
                                            <a href="{{ route('product.show', $bogoProduct->slug) }}">
                                                {{ $bogoProduct->name }}
                                            </a>
                                        </h5>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>
            </aside>

            <div class="product-details-tab col-lg-12">
                <ul class="nav nav-pills justify-content-center" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="product-desc-link" data-toggle="tab" href="#product-desc-tab" role="tab" aria-controls="product-desc-tab" aria-selected="true">Description</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="product-desc-tab" role="tabpanel" aria-labelledby="product-desc-link">
                        <div class="product-desc-content">
                            <h3>Product Information</h3>
                            {!! $bogo->long_description !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')

<script>
    $(document).ready(function() {
        var currentValue = 1;
        $('#qty').on('input', function() {
            var maxValue = parseInt($('#qty').attr('max'));
            if ($(this).val() > maxValue) {
                $(this).val(maxValue);
            } else if ($(this).val() < 1) {
                $(this).val(1);
            }
        });
    });
</script>

@endsection