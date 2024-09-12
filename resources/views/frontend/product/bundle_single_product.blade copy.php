@extends('frontend.layouts.app')
@section('title', $title)
@section('content')

<div class="container-fluid pb-5">
    <div class="row px-xl-5">
        <div class="col-lg-5 mb-30">
            <div id="product-carousel" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner bg-light">
                    @foreach($bundle->images as $index => $image)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <x-image-with-loader class="d-block w-100" src="{{ asset('/images/bundle_product_images/' . $image->image) }}" alt="Image" style="height: 400px; object-fit: cover;"/>
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
                <h3>{{ $bundle->name }}</h3>
                <div class="d-flex mb-3">
                    <div class="text-primary mr-2"></div>
                </div>
                <h3 class="font-weight-semi-bold mb-4">
                    {{ $currency }} {{ $bundle->price }}
                </h3>

                <p class="mb-4">{!! $bundle->short_description !!}</p>

                <div class="d-flex flex-row flex-wrap">
                    @foreach($bundleProducts as $bundleProduct)
                        <div class="p-3">
                            <x-image-with-loader src="{{ asset('/images/products/' . $bundleProduct->feature_image) }}" alt="{{ $bundleProduct->name }}" class="img-fluid rounded" style="width: 100px; height: auto;" />
                            <div class="mt-2">
                                <h5>{{ $bundleProduct->name }}</h5>
                                <a href="{{ route('product.show', $bundleProduct->slug) }}" class="btn btn-primary btn-sm">View Product</a>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($bundle->quantity <= 0)
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
                        <input type="hidden" id="maxQuantity" value="{{ $bundle->quantity !== null ? $bundle->quantity : '' }}">

                        <div class="input-group-btn">
                            <button class="btn btn-primary" id="incrementBtn">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <button class="btn btn-primary px-3 add-to-cart"
                            data-price="{{ $bundle->price }}" 
                            data-offer-id="0"
                            data-bundle-id="{{ $bundle->id }}" 
                            @if($bundle->quantity > 0)
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
                        <p>{!! $bundle->long_description !!}</p>
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
        $('#incrementBtn').click(function() {
            if (currentValue < maxQuantity) {
                currentValue++; 
                $('#quantityInput').val(currentValue);
            }
            $('#quantityInput').val(currentValue);
        });
    });
</script>

@endsection