@extends('frontend.layouts.app')
@section('title', $title)
@section('content')

<div class="page-content mt-3">
    <div class="container">
        <div class="product-details-top">
            <div class="row">
                {{-- <div class="col-md-6 d-none">
                    <div class="product-gallery product-gallery-vertical">
                        <div class="row">
                            <figure class="product-main-image">
                                <x-image-with-loader src="{{ asset('/images/products/' . $product->feature_image) }}" alt="{{ $product->name }}" class="product-image" />
                            </figure>

                            <div id="product-zoom-gallery" class="product-image-gallery d-none">
                                @foreach($product->colors as $index => $image)
                                    <a class="product-gallery-item {{ $index == 0 ? 'active' : '' }}" href="#" data-image="{{ asset($image->image) }}" data-zoom-image="{{ asset($image->image) }}">
                                        <img src="{{ asset($image->image) }}" alt="product image">
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div> --}}

                <div class="col-md-6">
                    <div class="product-gallery-area">
                        <!-- Main Image -->
                        <div class="product-main-image mb-3">
                            <img id="main-product-image" 
                                src="{{ asset('/images/products/' . $product->feature_image) }}" 
                                alt="{{ $product->name }}" 
                                class="img-fluid w-100">
                        </div>

                        <!-- Thumbnail Slider -->
                        <div class="product-thumbnail-slider">
                            <div class="thumbnail-item">
                                <img src="{{ asset('/images/products/' . $product->feature_image) }}" 
                                    alt="Thumbnail" 
                                    class="img-fluid"
                                    data-large="{{ asset('/images/products/' . $product->feature_image) }}">
                            </div>
                            
                            @foreach($product->colors as $color)
                                @if($color->image)
                                    <div class="thumbnail-item color-thumbnail" data-color="{{ $color->color }}">
                                        <img src="{{ asset($color->image) }}" 
                                            alt="{{ $color->color }}" 
                                            class="img-fluid"
                                            data-large="{{ asset($color->image) }}">
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="product-details">
                        <h1 class="product-title">{{ $product->name }}</h1>
                        <input type="hidden" id="product-id" value="{{ $product->id }}">

                        <div class="product-price" id="productPrice">

                                @php
                                    $filteredStock = $product->stock()
                                        ->where('quantity', '>', 0)
                                        ->latest()
                                        ->select('id', 'selling_price', 'color', 'size', 'quantity')
                                        ->get();

                                    $sellingPrice = $filteredStock->first()->selling_price ?? 0; 
                                    $availableColors = $filteredStock->pluck('color')->unique();
                                    $sizes = $filteredStock->pluck('size')->unique();
                                @endphp

                            @if(isset($offerPrice) && $offerPrice !== null)
                                {{ $currency }} <del>{{ $oldOfferPrice }}</del> {{ $offerPrice }}
                                @php
                                    $discountPercentage = (($oldOfferPrice - $offerPrice) / $oldOfferPrice) * 100;
                                @endphp
                                <small>({{ round($discountPercentage, 0) }}% off)</small>
                            @elseif(isset($flashSellPrice) && $flashSellPrice !== null)
                                {{ $currency }} <del>{{ $OldFlashSellPrice }}</del> {{ $flashSellPrice }}
                                @php
                                    $discountPercentage = (($OldFlashSellPrice - $flashSellPrice) / $OldFlashSellPrice) * 100;
                                @endphp
                                <small>({{ round($discountPercentage, 0) }}% off)</small>
                            @else
                                {{ $currency }}{{ number_format($sellingPrice ?? $regularPrice, 2) }}
                            @endif
                        </div>

                        <div class="product-content">
                            <p>{!! $product->short_description !!} </p>
                        </div>

                        <div class="details-filter-row details-row-size d-none">
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

                        <div class="row"> 
                            <div class="col-8">
                                <div class="text-center">
                                    <button type="button" class="btn btn-secondary btn-sm mb-2" data-toggle="modal" data-target="#sizeGuideModal">
                                        <i class="fas fa-ruler-vertical"></i> Size Guide
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="details-filter-row details-row-size">
                            <label>Color:</label>
                            <div class="product-nav product-nav-thumbs">
                                @foreach($availableColors as $index => $color)
                                    @php
                                        $colorId = \App\Models\Color::where('color', $color)->value('id'); 
                                        $colorImage = $product->colors->firstWhere('color_id', $colorId);
                                    @endphp
                                    @if($colorImage)
                                        <div class="color-option-wrapper">
                                            <span class="color-name">{{ $color }}</span>
                                            <input type="radio" class="custom-control-input" id="color-{{ $index }}" name="color" value="{{ $color }}" style="display: none;">
                                            <button type="button" 
                                                    class="color-option" 
                                                    data-color="{{ $color }}"
                                                    data-image="{{ asset($colorImage->image) }}" 
                                                    onclick="selectColor(this, 'color-{{ $index }}')">
                                                <img src="{{ asset($colorImage->image) }}" alt="Color Image">
                                            </button>
                                        </div>
                                    @else
                                        <div class="color-option-wrapper">
                                            <span class="color-name">{{ $color }}</span>
                                            <input type="radio" class="custom-control-input" id="color-{{ $index }}" name="color" value="{{ $color }}" style="display: none;">
                                            <button type="button" 
                                                class="color-option" 
                                                style="background-color: {{ $color }}; width: 66px; height: 45px;" 
                                                data-color="{{ $color }}"
                                                onclick="selectColor(this, 'color-{{ $index }}', false)">
                                            </button>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <div class="details-filter-row details-row-size">
                            <label for="size">Size:</label>
                            <form id="sizeForm">
                                
                            </form>
                        </div>

                        @if(!$product->stock || $product->stock->quantity <= 0)
                            <div class="text-danger mt-2 mb-2">
                                This product is currently out of stock.
                            </div>
                        @endif

                        <div class="details-filter-row details-row-size d-flex align-items-center">
                            <label for="qty" class="mr-2">Qty:</label>
                            <div class="product-details-quantity">
                                <input type="number" id="qty" class="form-control quantity-input" value="1" min="1" max="{{ $product->stock && $product->stock->quantity !== null ? $product->stock->sum('quantity') : '' }}" step="1" data-decimals="0" required>
                            </div>
                            <div class="product-details-action col-auto pt-3">
                                <a href="#" 
                                class="btn btn-product btn-cart add-to-cart" 
                                data-product-id="{{ $product->id }}" 
                                data-offer-id="0" 
                                data-price="{{ $sellingPrice ?? $product->price }}"
                                @if(!$product->stock || $product->stock->quantity <= 0)
                                style="pointer-events: none; opacity: 0.5;" 
                                title="Out of stock"
                                @endif>
                                <span>Add to cart</span>
                                </a>
                            </div>
                        </div>

                        <div class="product-details-footer d-none">
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
            <ul class="nav nav-pills justify-content-start" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="product-desc-link" data-toggle="tab" href="#product-desc-tab" role="tab" aria-controls="product-desc-tab" aria-selected="true">Description</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="product-review-link" data-toggle="tab" href="#product-review-tab" role="tab" aria-controls="product-review-tab" aria-selected="false">Reviews ({{ $product->reviews->count() }})</a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="product-desc-tab" role="tabpanel" aria-labelledby="product-desc-link">
                    <div class="product-desc-content">
                        <h3>Product Information</h3>
                        {!! $product->long_description !!}
                    </div>
                </div>
                <div class="tab-pane fade" id="product-review-tab" role="tabpanel" aria-labelledby="product-review-link">
                    <div class="reviews">
                        <h3>Reviews ({{ $product->reviews->count() }})</h3>

                        @foreach ($product->reviews as $review)
                            <div class="review">
                                <div class="row no-gutters">
                                    <div class="col-auto">
                                        <h4><a href="#">{{ $review->user->name }}</a></h4>
                                        <div class="ratings-container">
                                            <div class="ratings">
                                                <div class="ratings-val" style="width: {{ $review->rating * 20 }}%;"></div>
                                            </div>
                                        </div>
                                        <span class="review-date">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="col">
                                        <h4>{{ $review->title }}</h4>

                                        <div class="review-content">
                                            <p>{{ Str::limit($review->description, 200) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @auth
                    <div class="review-form">
                        <h4 class="mt-3">Submit a Review</h4>
                        <form id="reviewForm" method="POST">
                            <input type="hidden" name="product_id" id="product_id" value="{{ $product->id }}">
                            @csrf
                            <div class="row">
                                <div class="form-group col-6">
                                    <label for="reviewTitle">Title</label>
                                    <input type="text" id="reviewTitle" name="title" class="form-control" placeholder="Review Title" required>
                                </div>
                                <div class="form-group col-6">
                                    <label for="reviewRating">Rating</label>
                                    <select id="reviewRating" name="rating" class="form-control" required>
                                        <option value="5">5 Stars</option>
                                        <option value="4">4 Stars</option>
                                        <option value="3">3 Stars</option>
                                        <option value="2">2 Stars</option>
                                        <option value="1">1 Star</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="reviewDescription">Description</label>
                                <textarea id="reviewDescription" name="description" class="form-control" rows="3" placeholder="Your review" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Review</button>
                        </form>
                    </div>
                    @endauth

                    @guest
                    <p>You need to <a href="{{ route('login') }}">log in</a> to submit a review.</p>
                    @endguest
                </div>
            </div>
        </div>

        <h2 class="title text-center mb-4">You May Also Like</h2>

        <div class="owl-carousel owl-simple carousel-equal-height carousel-with-shadow" data-toggle="owl" 
            data-owl-options='{
                "nav": false, 
                "dots": true,
                "margin": 20,
                "loop": false,
                "responsive": {
                    "0": {
                        "items":1
                    },
                    "480": {
                        "items":2
                    },
                    "768": {
                        "items":3
                    },
                    "992": {
                        "items":4
                    },
                    "1200": {
                        "items":4,
                        "nav": true,
                        "dots": false
                    }
                }
            }'>
            @if ($relatedProducts->count() > 0)
                @foreach($relatedProducts as $product)
                <div class="product product-2">
                    <figure class="product-media">
                        <a href="{{ route('product.show', $product->slug) }}">
                            <img src="{{ asset('images/products/' . $product->feature_image) }}" alt="{{ $product->name }}" class="product-image">
                        </a>

                        @if ($product->stock && $product->stock->quantity > 0)
                            <div class="product-action-vertical">
                                <a href="#" class="btn-product-icon btn-wishlist add-to-wishlist" title="Add to wishlist" data-product-id="{{ $product->id }}" data-offer-id="0" data-price="{{ $product->price }}"></a>
                            </div>

                            @php
                                $filteredStock = $product->stock()
                                    ->where('quantity', '>', 0)
                                    ->latest()
                                    ->select('id', 'selling_price', 'color', 'size', 'quantity')
                                    ->get();

                                $sellingPrice = $filteredStock->first()->selling_price ?? 0; 
                                $colors = $filteredStock->pluck('color')->unique();
                                $sizes = $filteredStock->pluck('size')->unique();
                            @endphp

                            <div class="product-action">
                                <a href="#" class="btn-product btn-cart" title="Add to cart" data-product-id="{{ $product->id }}" data-offer-id="0" data-price="{{ $sellingPrice ?? $product->price }}"data-toggle="modal" data-target="#quickAddToCartModal" 
                                data-image ="{{ asset('images/products/' . $product->feature_image) }}" data-stock="{{ $product->stock->sum('quantity') }}"
                                data-colors="{{ $colors->toJson() }}" data-sizes="{{ $sizes->toJson() }}"><span>add to cart</span></a>
                            </div>
                        @else
                            <span class="product-label label-out-stock">Out of stock</span>
                        @endif
                    </figure>

                    <div class="product-body">
                        <h3 class="product-title"><a href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a></h3>
                        <div class="product-price">
                            {{ $currency }}{{ number_format($sellingPrice ?? $product->price, 2) }}
                        </div>
                    </div>
                </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

<!-- Size Guide Modal -->
<div class="modal fade" id="sizeGuideModal" tabindex="-1" role="dialog" aria-labelledby="sizeGuideModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sizeGuideModalLabel">Size Guide</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                {!! $sizeGuide !!}
            </div>
        </div>
    </div>
</div>

<style>
    .product-nav-thumbs {
        display: flex;
        gap: 10px;
    }

    .product-gallery-area {
        position: relative;
    }
    
    .product-main-image {
        border: 1px solid #eee;
        padding: 10px;
        margin-bottom: 15px;
        text-align: center;
    }
    
    .product-main-image img {
        max-height: 500px;
        object-fit: contain;
    }
    
    .product-thumbnail-slider {
        display: flex;
        gap: 10px;
        overflow-x: auto;
        padding-bottom: 10px;
    }
    
    .thumbnail-item {
        flex: 0 0 80px;
        height: 80px;
        border: 1px solid #ddd;
        padding: 5px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .thumbnail-item:hover {
        border-color: #333;
    }
    
    .thumbnail-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    @media (max-width: 767px) {
        .product-main-image img {
            max-height: 300px;
        }
        
        .thumbnail-item {
            flex: 0 0 60px;
            height: 60px;
        }
    }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css">

@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize thumbnail slider
        $('.thumbnail-item').on('click', function() {
            const largeImage = $(this).find('img').data('large');
            $('#main-product-image').attr('src', largeImage);
            
            // Update active state
            $('.thumbnail-item').removeClass('active');
            $(this).addClass('active');
        });
        
        // Set first thumbnail as active by default
        $('.thumbnail-item:first').addClass('active');
        
        // For color selection - update main image when color is selected
        $(document).on('click', '.color-option', function() {
            const colorImage = $(this).data('image');
            if (colorImage) {
                $('#main-product-image').attr('src', colorImage);
            }
        });
        
        // Initialize thumbnail slider scrolling for mobile
        if ($(window).width() < 768) {
            $('.product-thumbnail-slider').addClass('mobile-slider');
        }
    });
</script>

<script>
    $(document).ready(function() {
        if ($(window).width() < 768) {
            $('.product-thumbnail-slider').slick({
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
        
        $('.thumbnail-item').on('click', function() {
            const largeImage = $(this).find('img').data('large');
            $('#main-product-image').attr('src', largeImage);
            
            $('.thumbnail-item').removeClass('active');
            $(this).addClass('active');
        });
        
        $('.thumbnail-item:first').addClass('active');
        
        $(document).on('click', '.color-option', function() {
            const colorImage = $(this).data('image');
            if (colorImage) {
                $('#main-product-image').attr('src', colorImage);
            }
        });
    });
</script>

<script>
    $(document).ready(function () {
        $(document).on('click', '.color-option', function () {
            var selectedColor = $(this).attr('data-color');
            if (!selectedColor) {
                console.error("Color not selected");
                return;
            }

            var productId = $('#product-id').val();
            var modal = $('#quickAddToCartModal');
            console.log(selectedColor, productId);

            $.ajax({
                url: '/get-sizes',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: productId,
                    color: selectedColor, 
                },
                success: function (response) {
                    console.log(response);
                    var sizeForm = $('#sizeForm');
                    sizeForm.empty(); 
                    response.sizes.forEach(function (size, index) {
                        sizeForm.append(`
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" class="custom-control-input largerRadiobox" id="size-${index}" name="size" value="${size}">
                                <label class="custom-control-label largerRadiobox-label" for="size-${index}">${size}</label>
                            </div>
                        `);
                    });

                    var price = response.selling_price;
                    $('#productPrice').html('{{ $currency }}' + parseFloat(price).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));

                    $('.add-to-cart').attr('data-price', price);

                    var maxQuantity = response.max_quantity;
                    $('#qty').attr('max', maxQuantity).val(1);

                    sizeForm.closest('.details-filter-row-size').show();
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching sizes:', xhr.responseText);
                },
            });
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

<script>
    $(document).ready(function() {
        $('#reviewForm').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                type: 'POST',
                url: '{{ route('reviews.store') }}',
                data: {
                    _token: $('input[name="_token"]').val(),
                    product_id: $('#product_id').val(),
                    title: $('#reviewTitle').val(),
                    description: $('#reviewDescription').val(),
                    rating: $('#reviewRating').val(),
                },
                success: function(response) {
                    toastr.success('Review submitted successfully!');
                    $('#reviewForm')[0].reset();
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    toastr.error('An error occurred while submitting your review.');
                }
            });
        });
    });
</script>

@endsection