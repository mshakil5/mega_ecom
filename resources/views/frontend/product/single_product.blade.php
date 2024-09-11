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
                @if(isset($offerPrice) && $offerPrice !== null)
                    <h3 class="font-weight-semi-bold mb-4">
                        <del>{{ $currency }} {{ $oldOfferPrice }}</del>
                        {{ $currency }} {{ $offerPrice }}
                        @php
                            $discountPercentage = (($oldOfferPrice - $offerPrice) / $oldOfferPrice) * 100;
                        @endphp
                        <small>({{ round($discountPercentage, 0) }}% off)</small>
                    </h3>
                @elseif(isset($flashSellPrice) && $flashSellPrice !== null)
                    <h3 class="font-weight-semi-bold mb-4">
                        <del>{{ $currency }} {{ $OldFlashSellPrice }}</del>
                        {{ $currency }} {{ $flashSellPrice }}
                        @php
                            $discountPercentage = (($OldFlashSellPrice - $flashSellPrice) / $OldFlashSellPrice) * 100;
                        @endphp
                        <small>({{ round($discountPercentage, 0) }}% off)</small>
                    </h3>
                @else
                    <h3 class="font-weight-semi-bold mb-4">
                        {{ $currency }} {{ $regularPrice }}
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

                @if(!$product->stock || $product->stock->quantity <= 0)
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

                    <input type="hidden" id="maxQuantity" value="{{ $product->stock && $product->stock->quantity !== null ? $product->stock->quantity : '' }}">

                    <div class="input-group-btn">
                        <button class="btn btn-primary" id="incrementBtn">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
                    <button class="btn btn-primary px-3 add-to-cart"
                            data-product-id="{{ $product->id }}"
                            data-price="{{ isset($offerPrice) ? $offerPrice : (isset($flashSellPrice) ? $flashSellPrice : $regularPrice) }}" 
                            data-offer-id="{{ isset($offerId) ? $offerId : '0' }}" 
                            @if($product->stock && $product->stock->quantity > 0)
                            @else
                                disabled
                            @endif>
                        <i class="fa fa-shopping-cart mr-1"></i> Add To Cart
                    </button>
                </div>
                {{-- <div class="d-flex pt-2">
                    <strong class="text-dark mr-2">Share on:</strong>
                    <div class="d-inline-flex">
                        <a class="text-dark px-2" href="#">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a class="text-dark px-2" href="#">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a class="text-dark px-2" href="#">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a class="text-dark px-2" href="#">
                            <i class="fab fa-pinterest"></i>
                        </a>
                    </div>
                </div> --}}
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
                        {{-- <div class="row">
                            <div class="col-md-6">
                                <h4 class="mb-4">1 review for {{ $product->name }}</h4>
                                <div class="media mb-4">
                                    <x-image-with-loader src="{{ asset('assets/frontend/img/user.jpg') }}" alt="Image" class="img-fluid mr-3 mt-1" style="width: 45px;"/>
                                    <div class="media-body">
                                        <h6>John Doe<small> - <i>01 Jan 2045</i></small></h6>
                                        <div class="text-primary mb-2">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star-half-alt"></i>
                                            <i class="far fa-star"></i>
                                        </div>
                                        <p>Diam amet duo labore stet elitr ea clita ipsum, tempor labore accusam ipsum et no at. Kasd diam tempor rebum magna dolores sed sed eirmod ipsum.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h4 class="mb-4">Leave a review</h4>
                                <small>Your email address will not be published. Required fields are marked *</small>
                                <div class="d-flex my-3">
                                    <p class="mb-0 mr-2">Your Rating * :</p>
                                    <div class="text-primary">
                                        <i class="far fa-star"></i>
                                        <i class="far fa-star"></i>
                                        <i class="far fa-star"></i>
                                        <i class="far fa-star"></i>
                                        <i class="far fa-star"></i>
                                    </div>
                                </div>
                                <form>
                                    <div class="form-group">
                                        <label for="message">Your Review *</label>
                                        <textarea id="message" cols="30" rows="5" class="form-control"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="name">Your Name *</label>
                                        <input type="text" class="form-control" id="name">
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Your Email *</label>
                                        <input type="email" class="form-control" id="email">
                                    </div>
                                    <div class="form-group mb-0">
                                        <input type="submit" value="Leave Your Review" class="btn btn-primary px-3">
                                    </div>
                                </form>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid py-5">
    @if(isset($relatedProducts) && count($relatedProducts) > 0)
    <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4"><span class="bg-secondary pr-3">You May Also Like</span></h2>
    <div class="row px-xl-5">
        <div class="col">
            <div class="owl-carousel related-carousel">
                @foreach($relatedProducts as $relatedProduct)
                    <div class="product-item bg-light">
                        <div class="product-img position-relative overflow-hidden" style="height: 250px;">
                            <x-image-with-loader class="img-fluid w-100" src="{{ asset('/images/products/' . $relatedProduct->feature_image) }}" alt="{{ $relatedProduct->name }}"/>
                            
                            <div class="product-action">
                               @if ($relatedProduct->stock && $relatedProduct->stock->quantity > 0)
                                    <a class="btn btn-outline-dark btn-square add-to-cart" data-product-id="{{ $relatedProduct->id }}" data-offer-id="0" data-price="{{ $relatedProduct->price }}">
                                        <i class="fa fa-shopping-cart"></i>
                                    </a>
                                @else
                                    <a class="btn btn-outline-dark btn-square disabled" aria-disabled="true">
                                        <i class="fa fa-shopping-cart"></i>
                                    </a>
                                @endif
                                <a class="btn btn-outline-dark btn-square add-to-wishlist" 
                                data-product-id="{{ $relatedProduct->id }}" 
                                data-offer-id="0" 
                                data-price="{{ $relatedProduct->price }}">
                                    <i class="fa fa-heart"></i>
                                </a>
                            </div>
                        </div>
                        <div class="text-center py-4">
                            <a class="h6 text-decoration-none text-truncate" href="{{ route('product.show', $relatedProduct->slug) }}">{{ $relatedProduct->name }}</a>
                            <div class="d-flex align-items-center justify-content-center mt-2">
                                <h5>{{$currency}} {{ $relatedProduct->price }}</h5>
                            </div>
                            <div class="d-flex align-items-center justify-content-center mb-1">
                                @if($relatedProduct->stock && $relatedProduct->stock->quantity > 0)
                                    <p>Available: {{ $relatedProduct->stock->quantity }}</p>
                                @else
                                    <p>Out of Stock</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
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