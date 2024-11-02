@extends('frontend.layouts.app')

@section('content')

<div class="page-content">
    <div class="container">
        <div class="row">
            <!-- Shop Sidebar Start -->
            <aside class="col-lg-3 mt-9">
                <div class="sidebar sidebar-shop">

                    <!-- Category Filter Start -->
                    <div class="widget widget-collapsible">
                        <h3 class="widget-title">
                            <a data-toggle="collapse" href="#widget-category" role="button" aria-expanded="true" aria-controls="widget-category">
                                Filter by Category
                            </a>
                        </h3>

                        <div class="collapse show" id="widget-category">
                            <div class="widget-body">
                                <form id="filterForm">
                                    <div class="filter-items">
                                        <div class="custom-control custom-radio d-flex align-items-center justify-content-between mb-3">
                                            <input type="radio" class="custom-control-input" id="category-all" name="category" value="">
                                            <label class="custom-control-label" for="category-all">All Categories</label>
                                        </div>
                                        @foreach($categories as $category)
                                        <div class="custom-control custom-radio d-flex align-items-center justify-content-between mb-3">
                                            <input type="radio" class="custom-control-input" id="category-{{ $category->id }}" name="category" value="{{ $category->id }}">
                                            <label class="custom-control-label d-flex justify-content-between w-100" for="category-{{ $category->id }}">
                                                <span>{{ $category->name }}</span>
                                                <span class="ml-auto">{{ $category->products->count() }}</span>
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Category Filter End -->

                    <!-- Size Filter Start -->
                    <div class="widget widget-collapsible d-none">
                        <h3 class="widget-title">
                            <a data-toggle="collapse" href="#widget-size" role="button" aria-expanded="true" aria-controls="widget-size">
                                Filter by Size
                            </a>
                        </h3>

                        <div class="collapse" id="widget-size">
                            <div class="widget-body">
                                <form id="sizeFilterForm">
                                    <div class="filter-items">
                                        <div class="custom-control custom-radio d-flex align-items-center justify-content-between mb-3">
                                            <input type="radio" class="custom-control-input" id="size-xs" name="size" value="XS">
                                            <label class="custom-control-label" for="size-xs">
                                                <span>XS</span>
                                            </label>
                                        </div>
                                        <div class="custom-control custom-radio d-flex align-items-center justify-content-between mb-3">
                                            <input type="radio" class="custom-control-input" id="size-s" name="size" value="S">
                                            <label class="custom-control-label" for="size-s">
                                                <span>S</span>
                                            </label>
                                        </div>
                                        <div class="custom-control custom-radio d-flex align-items-center justify-content-between mb-3">
                                            <input type="radio" class="custom-control-input" id="size-m" name="size" value="M">
                                            <label class="custom-control-label" for="size-m">
                                                <span>M</span>
                                            </label>
                                        </div>
                                        <div class="custom-control custom-radio d-flex align-items-center justify-content-between mb-3">
                                            <input type="radio" class="custom-control-input" id="size-l" name="size" value="L">
                                            <label class="custom-control-label" for="size-l">
                                                <span>L</span>
                                            </label>
                                        </div>
                                        <div class="custom-control custom-radio d-flex align-items-center justify-content-between mb-3">
                                            <input type="radio" class="custom-control-input" id="size-xl" name="size" value="XL">
                                            <label class="custom-control-label" for="size-xl">
                                                <span>XL</span>
                                            </label>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Size Filter End -->

                    <!-- Color Filter Start -->
                    <div class="widget widget-collapsible d-none">
                        <h3 class="widget-title">
                            <a data-toggle="collapse" href="#widget-color" role="button" aria-expanded="true" aria-controls="widget-color">
                                Filter by Color
                            </a>
                        </h3>

                        <div class="collapse" id="widget-color">
                            <div class="widget-body">
                                <form id="colorFilterForm">
                                    <div class="filter-items">
                                        <div class="custom-control custom-radio d-flex align-items-center justify-content-between mb-3">
                                            <input type="radio" class="custom-control-input" id="color-black" name="color" value="Black">
                                            <label class="custom-control-label" for="color-black">
                                                <span>Black</span>
                                            </label>
                                        </div>
                                        <div class="custom-control custom-radio d-flex align-items-center justify-content-between mb-3">
                                            <input type="radio" class="custom-control-input" id="color-white" name="color" value="White">
                                            <label class="custom-control-label" for="color-white">
                                                <span>White</span>
                                            </label>
                                        </div>
                                        <div class="custom-control custom-radio d-flex align-items-center justify-content-between mb-3">
                                            <input type="radio" class="custom-control-input" id="color-red" name="color" value="Red">
                                            <label class="custom-control-label" for="color-red">
                                                <span>Red</span>
                                            </label>
                                        </div>
                                        <div class="custom-control custom-radio d-flex align-items-center justify-content-between mb-3">
                                            <input type="radio" class="custom-control-input" id="color-blue" name="color" value="Blue">
                                            <label class="custom-control-label" for="color-blue">
                                                <span>Blue</span>
                                            </label>
                                        </div>
                                        <div class="custom-control custom-radio d-flex align-items-center justify-content-between mb-3">
                                            <input type="radio" class="custom-control-input" id="color-green" name="color" value="Green">
                                            <label class="custom-control-label" for="color-green">
                                                <span>Green</span>
                                            </label>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Color Filter End -->

                    <!-- Brand Filter Start -->
                    <div class="widget widget-collapsible">
                        <h3 class="widget-title">
                            <a data-toggle="collapse" href="#widget-brand" role="button" aria-expanded="false" aria-controls="widget-brand">
                                Filter by Brand
                            </a>
                        </h3>

                        <div class="collapse" id="widget-brand">
                            <div class="widget-body">
                                <form id="brandFilterForm">
                                    <div class="filter-items">
                                        @foreach($brands as $brand)
                                        <div class="custom-control custom-radio d-flex align-items-center justify-content-between mb-3">
                                            <input type="radio" class="custom-control-input" id="brand-{{ $brand->id }}" name="brand" value="{{ $brand->id }}">
                                            <label class="custom-control-label d-flex justify-content-between w-100" for="brand-{{ $brand->id }}">
                                                <span>{{ $brand->name }}</span>
                                                <span class="ml-auto">{{ $brand->products->count() }}</span>
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Brand Filter End -->

                    <!-- Price Filter Start -->
                    <div class="widget widget-collapsible">
                        <h3 class="widget-title">
                            <a data-toggle="collapse" href="#widget-price" role="button" aria-expanded="true" aria-controls="widget-price">
                                Filter by Price
                            </a>
                        </h3>

                        <div class="collapse" id="widget-price">
                            <div class="widget-body">
                                <div class="filter-price">
                                    <div class="filter-price-text">
                                        Price Range: <span id="filter-price-range">{{ $currency }}{{ $minPrice }} - ${{ $maxPrice }}</span>
                                    </div>
                                    <input type="hidden" id="price-min" name="price-min" value="{{ $minPrice }}">
                                    <input type="hidden" id="price-max" name="price-max" value="{{ $maxPrice }}">
                                    <div id="price-slider"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Price Filter End -->

                </div>
            </aside>
            <!-- Shop Sidebar End -->

            <!-- Shop Product Start -->
            <div class="col-lg-9 col-md-8">
                <div class="row pb-3">
                    <div class="col-12 pb-1">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div>
                            </div>
                            <div class="ml-2">
                                <div class="btn-group">
                                </div>
                                <div class="btn-group ml-2">
                                    <button type="button" class="btn btn-sm btn-light dropdown-toggle" data-toggle="dropdown">
                                        Showing {{ request()->input('per_page', 10) }}
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" style="font-size: 16px;" href="{{ route('frontend.shop', ['per_page' => 10]) }}">10</a>
                                        <a class="dropdown-item" style="font-size: 16px;" href="{{ route('frontend.shop', ['per_page' => 20]) }}">20</a>
                                        <a class="dropdown-item" style="font-size: 16px;" href="{{ route('frontend.shop', ['per_page' => 30]) }}">30</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="product-list">
                        @foreach($products as $product)
                            <div class="col-6 col-md-4 col-lg-4 mb-4">
                                <div class="product product-2" style="height: 100%; display: flex; flex-direction: column;">
                                    <figure class="product-media">
                                        <a href="{{ route('product.show', $product->slug) }}">
                                            <x-image-with-loader src="{{ asset('/images/products/' . $product->feature_image) }}" alt="{{ $product->name }}" class="product-image" style="height: 200px; object-fit: cover;" />
                                        </a>

                                        @if ($product->stock && $product->stock->quantity > 0)
                                            @php
                                                $sellingPrice = $product->stockhistory()
                                                    ->where('available_qty', '>', 0)
                                                    ->orderBy('id', 'asc')
                                                    ->value('selling_price');
                                            @endphp
                                            <div class="product-action-vertical">
                                                <a href="#" class="btn-product-icon btn-wishlist add-to-wishlist btn-expandable" title="Add to wishlist" data-product-id="{{ $product->id }}" data-offer-id="0" data-price="{{ $sellingPrice ?? $product->price }}"><span>Add to wishlist</span></a>
                                            </div>

                                            <!-- <div class="product-action">
                                                <a href="#" class="btn-product btn-cart add-to-cart" title="Add to cart" data-product-id="{{ $product->id }}" data-offer-id="0" data-price="{{ $sellingPrice ?? $product->price }}"><span>add to cart</span></a>
                                            </div> -->
                                            <div class="product-action">
                                                <a href="#" class="btn-product btn-cart" title="Add to cart"
                                                data-product-id="{{ $product->id }}" 
                                                data-offer-id="0" 
                                                data-price="{{ $sellingPrice ?? $product->price }}" 
                                                data-toggle="modal" data-target="#quickAddToCartModal" 
                                                data-image ="{{ asset('images/products/' . $product->feature_image) }}" data-stock="{{ $product->stock->quantity }}">
                                                    <span>add to cart</span>
                                                </a>
                                            </div>
                                        @else
                                            <span class="product-label label-out-stock">Out of stock</span>
                                        @endif
                                    </figure>

                                    <div class="product-body" style="flex-grow: 1;">
                                        <h3 class="product-title">
                                            <a href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a>
                                        </h3>
                                        <div class="product-price">
                                            {{ $currency }} {{ number_format($sellingPrice ?? $product->price, 2) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="col-12">
                        <nav>
                            {{ $products->appends(['per_page' => request()->input('per_page', 10)])->links('pagination::bootstrap-4') }}
                        </nav>
                    </div>
                </div>
            </div>
            <!-- Shop Product End -->
        </div>
    </div>
</div>

<style>
    .col-lg-4 {
        min-width: 300px;
    }
</style>

@endsection

@section('script')

<script>
    $(document).ready(function() {
        var priceSlider = document.getElementById('price-slider');
        var minPrice = {{ $minPrice }};
        var maxPrice = {{ $maxPrice }};
        var currencySymbol = "{{ $currency }}";

        if (priceSlider) {
            noUiSlider.create(priceSlider, {
                start: [minPrice, maxPrice],
                connect: true,
                step: 100,
                range: {
                    'min': minPrice,
                    'max': maxPrice
                },
                tooltips: true,
                format: wNumb({
                    decimals: 0,
                    prefix: currencySymbol
                })
            });

            priceSlider.noUiSlider.on('update', function(values, handle) {
                $('#filter-price-range').text(values.join(' - '));
                $('#price-min').val(values[0].replace(currencySymbol, ''));
                $('#price-max').val(values[1].replace(currencySymbol, ''));
            });
        }
    });
</script>

<script>
    $(document).ready(function() {
        $('#filterForm, #brandFilterForm, #price-min, #price-max').on('change', function() {
            let startValue = $('#price-min').val().replace('$', '');
            let endValue = $('#price-max').val().replace('$', '');
            let selectedCategoryId = $('input[name="category"]:checked').val();
            // let selectedSize = $('input[name="size"]:checked').val();
            // let selectedColor = $('input[name="color"]:checked').val();
            let selectedBrandId = $('input[name="brand"]:checked').val();
            let csrfToken = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: '/products/filter',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                dataType: 'json',
                data: {
                    start_price: startValue,
                    end_price: endValue,
                    category: selectedCategoryId,
                    // size: selectedSize,
                    // color: selectedColor,
                    brand: selectedBrandId
                },
                success: function(response) {
                    console.log(response);
                    var products = response.products;
                    var productListHtml = '';

                    if (products.length === 0) {
                        $('#product-list').empty();
                        swal({
                            title: 'Oops...',
                            text: 'No products found!',
                            icon: 'error',
                        });
                    } else {
                        $.each(products, function(index, product) {
                            if (product.slug && product.feature_image && product.name && product.price !== undefined) {
                                productListHtml += `
                                    <div class="col-6 col-md-4 col-lg-4 mb-4">
                                        <div class="product product-2" style="height: 100%; display: flex; flex-direction: column;">
                                            <figure class="product-media">
                                                <a href="{{ route('product.show', '') }}/${product.slug}">
                                                    <x-image-with-loader src="{{ asset('/images/products/') }}/${product.feature_image}" alt="${product.name}" class="product-image" style="height: 200px; object-fit: cover;" />
                                                </a>
                                                ${product.stock && product.stock.quantity > 0 ? `
                                                    <div class="product-action-vertical">
                                                        <a href="#" class="btn-product-icon btn-wishlist add-to-wishlist btn-expandable" title="Add to wishlist" data-product-id="${product.id}" data-offer-id="0" data-price="${product.price}">
                                                            <span>Add to wishlist</span>
                                                        </a>
                                                    </div>
                                                    <div class="product-action">
                                                        <a href="#" class="btn-product btn-cart add-to-cart" title="Add to cart" data-product-id="${product.id}" data-offer-id="0" data-price="${product.price}">
                                                            <span>add to cart</span>
                                                        </a>
                                                    </div>
                                                ` : `<span class="product-label label-out-stock">Out of stock</span>`}
                                            </figure>
                                            <div class="product-body" style="flex-grow: 1;">
                                                <h3 class="product-title">
                                                    <a href="{{ route('product.show', '') }}/${product.slug}">${product.name}</a>
                                                </h3>
                                                <div class="product-price">
                                                    {{ $currency }} ${parseFloat(product.price).toFixed(2)}
                                                </div>
                                            </div>
                                        </div>
                                    </div>`;
                            }
                        });

                    $('#product-list').html(productListHtml);

                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });
    });
</script>

@endsection