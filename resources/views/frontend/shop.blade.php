@extends('frontend.layouts.app')

@section('content')

<div class="page-content">
    <div class="container">
        <div class="row">
            <!-- Shop Sidebar Start -->
            <aside class="col-lg-3 mt-9">
                <div class="sidebar sidebar-shop">

                    <!-- Category Filter Start -->
                     @if($categories->count() > 0)
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
                    @endif
                    <!-- Category Filter End -->

                    <!-- Size Filter Start -->
                    @if($sizes->count() > 0)
                    <div class="widget widget-collapsible">
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
                                            <input type="radio" class="custom-control-input" id="size-all" name="shop-size" value="">
                                            <label class="custom-control-label d-flex justify-content-between w-100" for="size-all">
                                                <span>All Sizes</span>
                                                <span class="ml-auto"></span>
                                            </label>
                                        </div>

                                        @foreach ($sizes as $size)
                                            <div class="custom-control custom-radio d-flex align-items-center justify-content-between mb-3">
                                                <input type="radio" class="custom-control-input" id="size-{{ $size->size }}" name="shop-size" value="{{ $size->size }}">
                                                <label class="custom-control-label" for="size-{{ $size->size }}">
                                                    <span>{{ strtoupper($size->size) }}</span>
                                                    <span class="ml-auto"></span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                    <!-- Size Filter End -->

                    <!-- Color Filter Start -->
                    @if($colors->count() > 0)
                    <div class="widget widget-collapsible">
                        <h3 class="widget-title">
                            <a data-toggle="collapse" href="#widget-color" role="button" aria-expanded="true" aria-controls="widget-color">
                                Filter by Color
                            </a>
                        </h3>

                        <div class="collapse" id="widget-color">
                            <div class="widget-body">
                                <form id="colorFilterForm">
                                    <div class="custom-control custom-radio d-flex align-items-center justify-content-between mb-3">
                                        <input type="radio" class="custom-control-input" id="shop-color-all" name="shop-color" value="">
                                        <label class="custom-control-label d-flex justify-content-between w-100" for="shop-color-all">
                                            <span>All Colors</span>
                                            <span class="ml-auto"></span>
                                        </label>
                                    </div>
                                    <div class="filter-items">
                                        @foreach ($colors as $color)
                                            <div class="custom-control custom-radio d-flex align-items-center justify-content-between mb-3">
                                                <input type="radio" class="custom-control-input" id="shop-color-{{ $loop->index }}" name="shop-color" value="{{ $color->color }}">
                                                <label class="custom-control-label" for="shop-color-{{ $loop->index }}">
                                                    <span>{{ ucfirst($color->color) }}</span>
                                                    <span class="ml-auto"></span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                    <!-- Color Filter End -->

                    <!-- Brand Filter Start -->
                    @if($brands->count() > 0)
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
                                        <!-- All Brands Option -->
                                        <div class="custom-control custom-radio d-flex align-items-center justify-content-between mb-3">
                                            <input type="radio" class="custom-control-input" id="brand-all" name="brand" value="">
                                            <label class="custom-control-label d-flex justify-content-between w-100" for="brand-all">
                                                <span>All Brands</span>
                                                <span class="ml-auto"></span>
                                            </label>
                                        </div>

                                        <!-- Individual Brands -->
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
                    @endif
                    <!-- Brand Filter End -->

                    <!-- Price Filter Start -->
                    <div class="widget widget-collapsible">
                        <h3 class="widget-title">
                            <a data-toggle="collapse" href="#widget-price" role="button" aria-expanded="true" aria-controls="widget-price">
                                Filter by Price
                            </a>
                        </h3>

                        <div class="collapse show" id="widget-price">
                            <div class="widget-body">
                                <div class="filter-price" style="padding-right: 20px;">
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

                    <div class="row" id="product-list">
                        
                    </div>

                    <div class="col-12" id="pagination">
                        <nav>
                           
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

        // Debounce function to limit how often triggerFilter is called
        function debounce(func, delay) {
            let debounceTimer;
            return function() {
                const context = this;
                const args = arguments;
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => func.apply(context, args), delay);
            };
        }

        function triggerFilter() {
            let startValue = $('#price-min').val().replace(currencySymbol, '');
            let endValue = $('#price-max').val().replace(currencySymbol, '');
            let selectedCategoryId = $('input[name="category"]:checked').val();
            let selectedBrandId = $('input[name="brand"]:checked').val();
            let selectedColor = $('input[name="shop-color"]:checked').val();
            let selectedSize = $('input[name="shop-size"]:checked').val();
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
                    brand: selectedBrandId,
                    color: selectedColor,
                    size: selectedSize
                },
                success: function(response) {
                    console.log(response);
                    var products = response.products;
                    var productListHtml = '';

                    if (products.length === 0) {
                        $('#product-list').empty();
                        toastr.error("No products found", "", {
                            closeButton: true,
                            progressBar: true,
                            timeOut: 3000,
                            positionClass: "toast-top-center",
                        });
                        return;
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
                                                ${product.total_stock > 0 ? `
                                                    <div class="product-action-vertical">
                                                        <a href="#" class="btn-product-icon btn-wishlist add-to-wishlist btn-expandable" title="Add to wishlist" data-product-id="${product.id}" data-offer-id="0" data-price="${product.price}">
                                                            <span>Add to wishlist</span>
                                                        </a>
                                                    </div>
                                                    <div class="product-action">
                                                        <a href="#" class="btn-product btn-cart" 
                                                        title="Add to cart" 
                                                        data-product-id="${product.id}" 
                                                        data-offer-id="0" 
                                                        data-price="${product.price}" 
                                                        data-toggle="modal" 
                                                        data-target="#quickAddToCartModal" 
                                                        data-image="{{ asset('images/products/') }}/${product.feature_image}" 
                                                        data-stock="${product.total_stock}"
                                                        data-colors='${JSON.stringify(product.colors)}' 
                                                        data-sizes='${JSON.stringify(product.sizes)}'
                                                        data-name="${product.name}">
                                                            <span>Add to cart</span>
                                                        </a>
                                                    </div>
                                                ` : `<span class="product-label label-out-stock">Out of stock</span>`}
                                            </figure>
                                            <div class="product-body" style="flex-grow: 1;">
                                                <h3 class="product-title">
                                                    <a href="{{ route('product.show', '') }}/${product.slug}">${product.name}</a>
                                                </h3>
                                                <div class="product-price">
                                                    {{ $currency }}${parseFloat(product.price).toFixed(2)}
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
        }

        if (minPrice === maxPrice) {
            maxPrice = minPrice + 1;
        }
        
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

            priceSlider.noUiSlider.on('update', debounce(function(values, handle) {
                $('#filter-price-range').text(values.join(' - '));
                $('#price-min').val(values[0].replace(currencySymbol, ''));
                $('#price-max').val(values[1].replace(currencySymbol, ''));
                triggerFilter();
            }, 500));
        }


        $('#filterForm, #brandFilterForm, #price-min, #price-max, #colorFilterForm, #sizeFilterForm').on('change', debounce(function() {
            triggerFilter();
        }, 500));

    });
</script>

@endsection