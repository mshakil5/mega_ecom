@extends('frontend.layouts.app')
@section('title', $title)

@section('content')
<link rel="stylesheet" href="{{ asset('frontend/v2/css/custom.css') }}">



<div class="container mt-4 product-container">

    <!-- PRICE TABLE -->
    <div class="price-table-wrapper">
        <h4 class="mb-3">Pricing Summary</h4>
        <table class="price-table">
            <thead>
                <tr>
                    <th>Feature</th>
                    <th>Value 1</th>
                    <th>Value 2</th>
                    <th>Value 3</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>Row 1</td><td>Data</td><td>Data</td><td>Data</td></tr>
                <tr><td>Row 2</td><td>Data</td><td>Data</td><td>Data</td></tr>
                <tr><td>Row 3</td><td>Data</td><td>Data</td><td>Data</td></tr>
                <tr><td>Row 4</td><td>Data</td><td>Data</td><td>Data</td></tr>
                <tr><td>Row 5</td><td>Data</td><td>Data</td><td>Data</td></tr>
            </tbody>
        </table>
    </div>

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
                </div>

                @foreach($product->colors as $color)
                    @if($color->image)
                        <div class="thumb-item">
                            <img src="{{ asset($color->image) }}"
                                 data-large="{{ asset($color->image) }}">
                        </div>
                    @endif
                @endforeach
            </div>
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
                            $colorName  = $c->color->color ?? 'N/A';
                            $colorCode  = $c->color->color_code ?? '#ccc';
                            $colorImage = $c->image ? asset($c->image) : null;
                        @endphp

                        <button type="button"
                                class="color-option"
                                data-color="{{ $colorName }}"
                                data-image="{{ $colorImage }}"
                                onclick="selectColor(this)"
                        >
                            {{-- If image exists, show image --}}
                            @if ($colorImage)
                                <img src="{{ $colorImage }}" alt="{{ $colorName }}">
                            @else
                                {{-- Otherwise show color swatch --}}
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
                                    data-slug="{{ $product->slug }}"
                                    data-size="{{ $size }}"
                                    data-ean="{{ $variant->ean ?? '' }}"
                                >

                                <button type="button" class="minus-btn">−</button>
                            </div>
                        @endforeach

                    </div>
                </div>

            @endif

            <div class="d-flex align-items-center mt-3">
                <div class="">
                    <a href="#" class="btn btn-dark add-to-cart ml-3"
                        data-product-id="{{ $product->id }}"
                        data-product-name="{{ $product->name }}"
                        data-image="{{ $product->feature_image }}"
                        data-action="cart">
                        Add To Cart
                    </a>
                </div>

                <div class="ms-2">
                    <a href="#" class="btn btn-dark add-to-cart ml-3"
                        data-product-id="{{ $product->id }}"
                        data-product-name="{{ $product->name }}"
                        data-image="{{ $product->feature_image }}"
                        data-action="customize">
                        Customise
                    </a>
                </div>
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
    document.querySelectorAll('.plus-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = btn.nextElementSibling;
            input.value = parseInt(input.value || 0) + 1;
        });
    });
    document.querySelectorAll('.minus-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = btn.previousElementSibling;
            if (parseInt(input.value) > 0) input.value = parseInt(input.value) - 1;
        });
    });
</script>
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
    function selectColor(element, radioId, hasImage = true) {
        document.querySelectorAll('.color-option').forEach(option => {
            option.classList.remove('active');
        });

        element.classList.add('active');

        if (hasImage) {
            const newImage = element.getAttribute('data-image');
            document.querySelector('.product-main-image img').src = newImage;
        }

        const radioButton = document.getElementById(radioId);
        if (radioButton) {
            radioButton.checked = true;
        }
    }
</script>

@endsection
