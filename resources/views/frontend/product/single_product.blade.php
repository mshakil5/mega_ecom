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
                                @foreach($product->colors as $index => $image)
                                    <a class="product-gallery-item {{ $index == 0 ? 'active' : '' }}" href="#" data-image="{{ asset($image->image) }}" data-zoom-image="{{ asset($image->image) }}">
                                        <img src="{{ asset($image->image) }}" alt="product image">
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

                                @php
                                    $sellingPrice = $product->stockhistory()
                                        ->where('available_qty', '>', 0)
                                        ->orderBy('id', 'asc')
                                        ->value('selling_price');
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
                                {{ $currency }} {{ $sellingPrice ?? $regularPrice }}
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

                        @if(!$product->stock || $product->stock->quantity <= 0)
                            <div class="text-danger mt-2 mb-2">
                                This product is currently out of stock.
                            </div>
                        @endif

                        <div class="details-filter-row details-row-size">
                            <label for="qty">Qty:</label>
                            <div class="product-details-quantity">
                                <input type="number" id="qty" class="form-control quantity-input" value="1" min="1" max="{{ $product->stock && $product->stock->quantity !== null ? $product->stock->quantity : '' }}" step="1" data-decimals="0" required>
                            </div>
                        </div>

                        <div class="product-details-action">

                                @php
                                    $sellingPrice = $product->stockhistory()
                                        ->where('available_qty', '>', 0)
                                        ->orderBy('id', 'asc')
                                        ->value('selling_price');
                                @endphp

                            <a href="#" 
                            class="btn-product btn-cart add-to-cart" 
                            data-product-id="{{ $product->id }}" 
                            data-offer-id="0" 
                            data-price="{{ $sellingPrice ?? $product->price }}"
                            @if(!$product->stock || $product->stock->quantity <= 0)
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
                <li class="nav-item">
                    <a class="nav-link" id="product-review-link" data-toggle="tab" href="#product-review-tab" role="tab" aria-controls="product-review-tab" aria-selected="false">Reviews ({{ $product->reviews->count() }})</a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="product-desc-tab" role="tabpanel" aria-labelledby="product-desc-link">
                    <div class="product-desc-content">
                        <h3>Product Information</h3>
                        {!! $product->description !!}
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
                                    $sellingPrice = $product->stockhistory()
                                        ->where('available_qty', '>', 0)
                                        ->orderBy('id', 'asc')
                                        ->value('selling_price');
                                    $colors = $product->stock()
                                    ->where('quantity', '>', 0)
                                    ->distinct('color')
                                    ->pluck('color');

                                    $sizes = $product->stock()
                                        ->where('quantity', '>', 0)
                                        ->distinct('size')
                                        ->pluck('size');  
                                @endphp

                                <div class="product-action">
                                    <a href="#" class="btn-product btn-cart" title="Add to cart" data-product-id="{{ $product->id }}" data-offer-id="0" data-price="{{ $sellingPrice ?? $product->price }}"data-toggle="modal" data-target="#quickAddToCartModal" 
                                    data-image ="{{ asset('images/products/' . $product->feature_image) }}" data-stock="{{ $product->stock->quantity }}"
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

@endsection

@section('script')

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