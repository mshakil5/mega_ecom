@extends('frontend.layouts.app')
@section('title', $title)
@section('content')

<div class="container-fluid pb-5">
    <div class="row px-xl-5">
        <div class="col-lg-5 mb-30">
            <div id="product-carousel" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner bg-light">
                    @foreach($product->images as $index => $image)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <x-image-with-loader class="d-block w-100" src="{{ asset('/images/products/' . $image->image) }}" alt="Image" style="height: 400px; object-fit: cover;"/>
                        </div>
                    @endforeach
                </div>
                <a class="carousel-control-prev" href="#product-carousel" data-slide="prev">
                    <i class="fa fa-2x fa-angle-left text-dark"></i>
                </a>
                <a class="carousel-control-next" href="#product-carousel" data-slide="next">
                    <i class="fa fa-2x fa-angle-right text-dark"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-7 h-auto mb-30">
            <div class="h-100 bg-light p-30">
                <h3>{{ $product->name }}</h3>
                <div class="d-flex mb-3">
                    <div class="text-primary mr-2">
                    </div>
                </div>
                @if(isset($campaignPrice) && $campaignPrice !== null)
                    @php
                        $discountPercentage = (($product->price - $campaignPrice) / $product->price) * 100;
                    @endphp
                    <h3 class="font-weight-semi-bold mb-4">
                        <del>{{ $currency }} {{ $product->price }}</del>
                        {{ $currency }} {{ $campaignPrice }}
                        <small>({{ round($discountPercentage, 0) }}% off)</small>
                    </h3>
                @else
                    <h3 class="font-weight-semi-bold mb-4">
                        {{ $currency }} {{ $product->price }}
                    </h3>
                @endif
                <p class="mb-4">{!! $product->short_description !!}</p>
                <div class="d-flex mb-3">
                    <strong class="text-dark mr-3">Sizes:</strong>
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
                <div class="d-flex mb-4">
                    <strong class="text-dark mr-3">Colors:</strong>
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

                @if(!$campaignProduct->quantity || $campaignProduct->quantity <= 0)
                    <div class="text-danger mt-2 mb-2">
                        This product is currently out of stock.
                    </div>
                @endif

                <div class="d-flex align-items-center mb-4 pt-2">
                    <div class="input-group quantity mr-3" style="width: 130px;">
                    <div class="input-group-btn">
                        <button class="btn btn-primary" id="decrementBtn">
                            <i class="fa fa-minus"></i>
                        </button>
                    </div>
                        <input type="text" class="form-control bg-secondary border-0 text-center" value="1" id="quantityInput" readonly min="1">

                        <input type="hidden" id="maxQuantity" value="{{ $campaignProduct->quantity }}">

                        <div class="input-group-btn">
                            <button class="btn btn-primary" id="incrementBtn1">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <button class="btn btn-primary px-3 add-to-cart"
                            data-product-id="{{ $product->id }}"
                            data-price="{{ $campaignPrice }}" 
                            data-offer-id="{{ isset($offerId) ? $offerId : '0' }}" 
                            data-campaign-id="{{ $campaignProduct->id }}"
                            @if($campaignProduct->quantity > 0)
                            @else
                                disabled
                            @endif>
                        <i class="fa fa-shopping-cart mr-1"></i> Add To Cart
                    </button>
                </div>

            </div>
        </div>
    </div>

    <div class="row px-xl-5">
        <div class="col">
            <div class="bg-light p-30">
                <div class="nav nav-tabs mb-4">
                    <a class="nav-item nav-link text-dark active" data-toggle="tab" href="#tab-pane-1">Description</a>
                    <a class="nav-item nav-link text-dark" data-toggle="tab" href="#tab-pane-2">Reviews (0)</a>
                </div>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tab-pane-1">
                        <h4 class="mb-3">Product Description</h4>
                        <p>{!! $product->description !!}</p>
                    </div>
                    <div class="tab-pane fade" id="tab-pane-2">
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
        $('#decrementBtn').click(function() {
            if (currentValue > 1) { 
                currentValue--;
                $('#quantityInput').val(currentValue);
            } else {
                currentValue = 1;
                $('#quantityInput').val(currentValue);
            }
        });

        const maxQuantity = parseInt($('#maxQuantity').val());
        $('#incrementBtn1').click(function() {
            if (currentValue < maxQuantity) {
                currentValue++; 
                $('#quantityInput').val(currentValue);
            }
            $('#quantityInput').val(currentValue);
        });
    });
</script>
@endsection
