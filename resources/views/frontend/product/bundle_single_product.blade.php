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
                                        <x-image-with-loader src="{{ asset('/images/bundle_product/' . $bundle->feature_image) }}" alt="{{ $bundle->name }}" class="product-image" />
                                    </figure>

                                    <div id="product-zoom-gallery" class="product-image-gallery">
                                        @foreach($bundle->images as $index => $image)
                                            <a class="product-gallery-item {{ $index == 0 ? 'active' : '' }}" href="#" data-image="{{ asset('/images/bundle_product_images/' . $image->image) }}" data-zoom-image="{{ asset('/images/bundle_product_images/' . $image->image) }}">
                                                <img src="{{ asset('/images/bundle_product_images/' . $image->image) }}" alt="product image">
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="product-details">
                                <h1 class="product-title">{{ $bundle->name }}</h1>

                                <div class="product-price">
                                    {{ $currency }} {{ $bundle->price }}
                                </div>

                                <div class="product-content">
                                    <p>{!! $bundle->short_description !!} </p>
                                </div>

                                @if($bundle->quantity <= 0)
                                    <div class="text-danger mt-2 mb-2">
                                        This bundle is currently out of stock.
                                    </div>
                                @endif

                                <div class="details-filter-row details-row-size">
                                    <label for="qty">Qty:</label>
                                    <div class="product-details-quantity">
                                        <input type="number" id="qty" class="form-control quantity-input" value="1" min="1" max="{{ $bundle->quantity }}" step="1" data-decimals="0" required>
                                    </div>
                                </div>

                                <div class="product-details-action">
                                    <a href="#" 
                                    class="btn-product btn-cart add-to-cart" 
                                    data-product-id="{{ $bundle->id }}" 
                                    data-price="{{ $bundle->price }}"
                                    data-bundle-id="{{ $bundle->id }}"
                                    @if($bundle->quantity <= 0)
                                        style="pointer-events: none; opacity: 0.5;" 
                                        title="Out of stock"
                                    @endif>
                                    <span>Add to cart</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <aside class="col-xl-2 d-md-none d-xl-block">
                <div class="sidebar sidebar-product">
                    <div class="widget widget-products">
                        <h4 class="widget-title">Get {{ $bundleProducts->count() }} extra products</h4>

                        <div class="products">
                            @foreach($bundleProducts as $bundleProduct)
                                <div class="product product-sm">
                                    <figure class="product-media">
                                        <a href="{{ route('product.show', $bundleProduct->slug) }}">
                                            <img src="{{ asset('/images/products/' . $bundleProduct->feature_image) }}" alt="{{ $bundleProduct->name }}" class="product-image">
                                        </a>
                                    </figure>

                                    <div class="product-body">
                                        <h5 class="product-title">
                                            <a href="{{ route('product.show', $bundleProduct->slug) }}">
                                                {{ $bundleProduct->name }}
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
                            {!! $bundle->long_description !!}
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