@extends('frontend.layouts.app')
@section('title', $title)

@section('content')
<link rel="stylesheet" href="{{ asset('frontend/v2/css/custom.css') }}">



<div class="container mt-4 product-container">

    <!-- PRICE TABLE -->
        @if($product->prices->count())
        <div class="price-table-wrapper">
            <h4 class="mb-3">Pricing Summary</h4>
            <table class="price-table">
                <thead>
                    <tr>
                        <th>Min Qty</th>
                        <th>Max Qty</th>
                        <th>Discount (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($product->prices as $price)
                    <tr>
                        <td>{{ $price->min_quantity }}</td>
                        <td>{{ $price->max_quantity }}</td>
                        <td>{{ $price->discount_percent ?? 0 }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <div class="product-section">

        <!-- LEFT SIDE = IMAGES -->
        <div>
            <div class="main-image-box">
                <img id="main-product-image"
                     src="{{ asset('/images/products/' . $product->feature_image) }}"
                     alt="{{ $product->name }}">
            </div>

            <div class="thumb-list mt-3">
                <div class="thumb-item active">
                    <img src="{{ asset('/images/products/' . $product->feature_image) }}"
                        data-large="{{ asset('/images/products/' . $product->feature_image) }}">
                    <small class="d-block text-center mt-1">Main</small>
                </div>

                @foreach($product->colors as $color)
                    @if($color->image)
                        <div class="thumb-item">
                            <img src="{{ asset($color->image) }}"
                                data-large="{{ asset($color->image) }}">
                            <small class="d-block text-center mt-1">
                                {{ $color->color->color ?? 'Color' }}
                            </small>
                        </div>
                    @endif
                @endforeach
            </div>

            @if($product->positionImages->count())
            <div class="position-images-section mt-4">
                <h5 class="mb-3 text-center">Position Views</h5>
                <div class="position-thumb-list">
                    @foreach($product->positionImages as $posImg)
                        <div class="thumb-item">
                            <img src="{{ asset($posImg->image) }}"
                                data-large="{{ asset($posImg->image) }}">
                            <small class="d-block text-center mt-1">
                                <strong>{{ ucfirst($posImg->position) }} View</strong>
                            </small>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- RIGHT SIDE = INFO -->
        <div class="product-info">

            <h1>{{ $product->name }}</h1>
            <input type="hidden" id="product-id" value="{{ $product->id }}">

            <!-- PRICE -->
            <div id="productPrice" class="product-price-box">
                {{ $currency }}{{ number_format($product->selling_price ?? $regularPrice, 2) }}
            </div>

            <!-- DESCRIPTION SHORT -->
            <p>{!! $product->short_description !!}</p>

            <!-- COLOR SELECT -->
            <div class="color-selector">
                <label class="section-title">Color:</label>

                <div class="color-options">
                    @foreach ($product->colors as $index => $c)
                        @php
                            // robust color id resolution: prefer related color model id, fallback to pivot id or index
                            $colorId = $c->color->id ?? $c->id ?? $index;
                            $colorName  = $c->color->color ?? 'N/A';
                            $colorCode  = $c->color->color_code ?? 'N/A';
                            $colorImage = $c->image ? asset($c->image) : null;
                        @endphp

                        {{-- radio input (name="color" — single choice) --}}
                        <input type="radio"
                            name="color"
                            id="color-{{ $colorId }}"
                            value="{{ $colorId }}"
                            class="d-none color-radio">

                            {{-- <input type="radio" name="color" class="d-none color-radio" value="{{ $c->id }}"> --}}


                        {{-- button controlling selection (keeps same look) --}}
                        <button type="button"
                                class="color-option"
                                data-color-id="{{ $colorId }}"
                                data-image="{{ $colorImage }}"
                                data-color-name="{{ $colorName }}"
                                onclick="selectColor(this, 'color-{{ $colorId }}')">

                            @if ($colorImage)
                                <img src="{{ $colorImage }}" alt="{{ $colorName }}">
                            @else
                                <span class="color-swatch" style="background-color: {{ $colorCode }}"></span>
                            @endif

                            <span class="color-label">{{ $colorName }}</span>
                        </button>
                    @endforeach

                </div>
            </div>


            @php
                $variants = $product->stock;
                $sizes = $variants->pluck('size')->filter()->unique();
            @endphp

            @if ($sizes->count() > 0)
                <div class="variable-single-item mb-3">
                    <span class="d-block mb-2 fw-semibold">Select Sizes:</span>

                    <div class="size-qty-container">

                        @foreach ($sizes as $size)
                            @php
                                $variant = $variants->where('size', $size)->first();
                            @endphp

                            <div class="size-box">
                                <div class="size-name">{{ $size }}</div>

                                <button type="button" class="plus-btn">+</button>

                                <input type="number"
                                    name="sizes[{{ $size }}]"
                                    value="0"
                                    min="0"
                                    class="qty-input"
                                    data-size="{{ $size }}"
                                    data-ean="{{ $variant->ean ?? '' }}"
                                    data-variant-id="{{ $variant->id }}"
                                >

                                <button type="button" class="minus-btn">−</button>
                            </div>
                        @endforeach

                    </div>
                </div>

            @endif

            <div class="d-flex align-items-center mt-3">

                <div class="">
                    <a href="#" class="btn btn-outline-danger add-to-wishlist"
                        data-product-id="{{ $product->id }}"
                        data-product-name="{{ $product->name }}"
                        data-image="{{ $product->feature_image }}">
                        <i class="fas fa-heart"></i> Add to Wishlist
                    </a>
                </div>

                <div class="ms-2">
                    <a href="#" class="btn btn-dark add-to-cart ml-3"
                        data-product-id="{{ $product->id }}"
                        data-product-name="{{ $product->name }}"
                        data-image="{{ $product->feature_image }}"
                        data-action="cart">
                        Add To Cart
                    </a>
                </div>

                @if($product->is_customizable == 1)
                <div class="ms-2">
                    <a href="#" class="btn btn-dark add-to-cart ml-3"
                        data-product-id="{{ $product->id }}"
                        data-product-name="{{ $product->name }}"
                        data-image="{{ $product->feature_image }}"
                        data-action="customize">
                        Customise
                    </a>
                </div>
                @endif
            </div>

        </div>
    </div>

    <!-- Description -->
    <div class="desc-box">
        <h4>Description</h4>
        {!! $product->long_description !!}
    </div>

</div>
@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>

<script>
$(document).ready(function () {

    /**
     * ----------------------------------------------------
     *  THUMBNAIL CLICK — FIXED & CLEAN (ONE VERSION ONLY)
     * ----------------------------------------------------
     */
    $(document).on('click', '.thumb-item', function () {

        let largeImage = $(this).find('img').data('large');

        if (largeImage) {
            $('#main-product-image').attr('src', largeImage);
        }

        console.log(largeImage);
        

        // Set active class
        $('.thumb-item').removeClass('active');
        $(this).addClass('active');
    });

    // Set first thumbnail active on load
    $('.thumb-item:first').addClass('active');


    /**
     * -------------------------------
     *  MOBILE SLIDER INIT
     * -------------------------------
     */
    if ($(window).width() < 768) {
        $('.thumb-list').slick({
            dots: false,
            arrows: true,
            infinite: false,
            speed: 300,
            slidesToShow: 4,
            slidesToScroll: 1,
            responsive: [
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 1
                    }
                }
            ]
        });
    }

    


    /**
     * -------------------------------------
     *  COLOR OPTION CLICK → CHANGE IMAGE
     * -------------------------------------
     */
    $(document).on('click', '.color-option', function () {
        let colorImage = $(this).data('image');

        if (colorImage) {
            $('#main-product-image').attr('src', colorImage);
        }

        // Manage active class for color buttons
        $('.color-option').removeClass('active');
        $(this).addClass('active');
    });





});
</script>
<script>
    function selectColor(element, radioId = null, hasImage = true) {

        document.querySelectorAll('.color-option').forEach(option => {
            option.classList.remove('active');
        });

        element.classList.add('active');

        if (hasImage) {
            const newImage = element.getAttribute('data-image');
            const mainImage = document.getElementById('main-product-image');

            if (mainImage && newImage) {
                mainImage.src = newImage;
            }
        }

        if (radioId) {
            const radioButton = document.getElementById(radioId);
            if (radioButton) radioButton.checked = true;
        }
    }
</script>


@endsection
