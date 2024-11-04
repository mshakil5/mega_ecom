@extends('frontend.layouts.app')
@section('title', $title)
@section('content')

<div class="page-content mt-3">
    <div class="product-details-top">
        <div class="row">
            <div class="col-md-6">
                <div class="product-gallery product-gallery-vertical">
                    <div class="row">
                        <figure class="product-main-image">
                            <x-image-with-loader src="{{ asset('/images/products/' . $product->feature_image) }}" alt="{{ $product->name }}" class="product-image" />
                        </figure>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="product-details">
                    <h1 class="product-title">{{ $product->name }}</h1>

                    <div class="product-price">
                        @if(isset($campaignPrice) && $campaignPrice !== null)
                            @php
                                $discountPercentage = (($product->price - $campaignPrice) / $product->price) * 100;
                            @endphp
                            <del>{{ $currency }} {{ $product->price }}</del>
                                {{ $currency }} {{ $campaignPrice }}
                                <small>({{ round($discountPercentage, 0) }}% off)</small>
                            @else
                                {{ $currency }} {{ $product->price }}
                            @endif
                    </div>

                    <div class="product-content">
                        <p>{!! $product->short_description !!} </p>
                    </div>

                    <div class="details-filter-row details-row-size">
                        <label>Color:</label>
                        <div class="product-nav product-nav-thumbs">
                            <form id="colorForm">
                                @php
                                    $colors = $product->stock()
                                        ->where('quantity', '>', 0)
                                        ->distinct('color')
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
                                $sizes = $product->stock()
                                    ->where('quantity', '>', 0)
                                    ->distinct('size')
                                    ->pluck('size');
                            @endphp

                            @foreach($sizes as $index => $size)
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" class="custom-control-input" id="size-{{ $index }}" name="size" value="{{ $size }}">
                                    <label class="custom-control-label" for="size-{{ $index }}">{{ $size }}</label>
                                </div>
                            @endforeach
                        </form>
                    </div>

                    @if(!$campaignProduct->quantity || $campaignProduct->quantity <= 0)
                        <div class="text-danger mt-2 mb-2">
                            This product is currently out of stock.
                        </div>
                    @endif

                    <div class="details-filter-row details-row-size">
                        <label for="qty">Qty:</label>
                        <div class="product-details-quantity">
                            <input type="number" id="qty" class="form-control quantity-input" value="1" min="1" max="{{ $campaignProduct->quantity }}" step="1" data-decimals="0" required>
                        </div>
                    </div>

                    <div class="product-details-action">
                        <a href="#" 
                        class="btn-product btn-cart add-to-cart" 
                        data-product-id="{{ $product->id }}"
                        data-price="{{ $campaignPrice }}"
                        data-offer-id="{{ isset($offerId) ? $offerId : '0' }}" 
                        data-offer-id="0"
                        data-campaign-id="{{ $campaignProduct->id }}" 
                        @if($campaignProduct->quantity <= 0)
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
