@extends('frontend.layouts.app')

@section('content')

<div class="page-content">
    <div class="container">
        <div class="row">
            <!-- Shop Sidebar Start -->
            <aside class="col-lg-3 order-lg-first mt-9">
                <div class="sidebar sidebar-shop">
                    <!-- Price Filter Start -->
                    <div class="widget widget-collapsible">
                        <h3 class="widget-title">
                            <a data-toggle="collapse" href="#widget-price" role="button" aria-expanded="true" aria-controls="widget-price">
                                Filter by Price
                            </a>
                        </h3>

                        <div class="collapse show" id="widget-price">
                            <div class="widget-body">
                                <form id="filterForm">
                                    <div class="filter-items">
                                        <div class="custom-control custom-radio d-flex align-items-center justify-content-between mb-3">
                                            <input type="radio" class="custom-control-input" name="price" value="0" checked id="price-all" start_value="" end_value="">
                                            <label class="custom-control-label" for="price-all">All Price</label>
                                        </div>
                                        <div class="custom-control custom-radio d-flex align-items-center justify-content-between mb-3">
                                            <input type="radio" class="custom-control-input" name="price" value="1" id="price-1" start_value="0" end_value="99">
                                            <label class="custom-control-label" for="price-1">$0 - $99</label>
                                        </div>
                                        <div class="custom-control custom-radio d-flex align-items-center justify-content-between mb-3">
                                            <input type="radio" class="custom-control-input" name="price" value="2" id="price-2" start_value="100" end_value="199">
                                            <label class="custom-control-label" for="price-2">$100 - $199</label>
                                        </div>
                                        <div class="custom-control custom-radio d-flex align-items-center justify-content-between mb-3">
                                            <input type="radio" class="custom-control-input" name="price" value="3" id="price-3" start_value="200" end_value="299">
                                            <label class="custom-control-label" for="price-3">$200 - $299</label>
                                        </div>
                                        <div class="custom-control custom-radio d-flex align-items-center justify-content-between mb-3">
                                            <input type="radio" class="custom-control-input" name="price" value="4" id="price-4" start_value="300" end_value="399">
                                            <label class="custom-control-label" for="price-4">$300 - $399</label>
                                        </div>
                                        <div class="custom-control custom-radio d-flex align-items-center justify-content-between mb-3">
                                            <input type="radio" class="custom-control-input" name="price" value="5" id="price-5" start_value="400" end_value="499">
                                            <label class="custom-control-label" for="price-5">$400 - $499</label>
                                        </div>
                                        <div class="custom-control custom-radio d-flex align-items-center justify-content-between mb-3">
                                            <input type="radio" class="custom-control-input" name="price" value="6" id="price-6" start_value="500" end_value="599">
                                            <label class="custom-control-label" for="price-6">$500 - $599</label>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Price Filter End -->

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
                                                <label class="custom-control-label" for="category-{{ $category->id }}">{{ $category->name }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Category Filter End -->
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
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="product product-2">
                                <figure class="product-media">
                                    <a href="{{ route('product.show', $product->slug) }}">
                                        <x-image-with-loader src="{{ asset('/images/products/' . $product->feature_image) }}" alt="{{ $product->name }}" class="product-image" />
                                    </a>

                                    @if ($product->stock && $product->stock->quantity > 0)
                                        <div class="product-action-vertical">
                                            <a href="#" class="btn-product-icon btn-wishlist add-to-wishlist" title="Add to wishlist" data-product-id="{{ $product->id }}" data-offer-id="0" data-price="{{ $product->price }}"></a>
                                        </div>

                                        <div class="product-action">
                                            <a href="#" class="btn-product btn-cart add-to-cart" title="Add to cart" data-product-id="{{ $product->id }}" data-offer-id="0" data-price="{{ $product->price }}"><span>add to cart</span></a>
                                        </div>
                                    @else
                                        <span class="product-label label-out-stock">Out of stock</span>
                                    @endif
                                </figure>

                                <div class="product-body">
                                    <h3 class="product-title"><a href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a></h3>
                                    <div class="product-price">
                                        {{ $currency }} {{ number_format($product->price, 2) }}
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

<!-- <style>
    .col-lg-4 {
        min-width: 350px;
    }
</style> -->

@endsection

@section('script')

<script>
    $(document).ready(function() {
        $('#filterForm input[type="radio"]').on('change', function() {
            var selectedPriceInput = $('input[name="price"]:checked');
            var startValue = selectedPriceInput.attr('start_value');
            var endValue = selectedPriceInput.attr('end_value');
            var selectedCategoryId = $('input[name="category"]:checked').val();
            var csrfToken = $('meta[name="csrf-token"]').attr('content');

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
                    category: selectedCategoryId
                },
                success: function(response) {
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
                            productListHtml += `
                                <div class="col-6 col-md-4 col-lg-3">
                                    <div class="product product-2">
                                        <figure class="product-media">
                                            <a href="{{ route('product.show', '') }}/${product.slug}">
                                                <x-image-with-loader src="{{ asset('/images/products/') }}/${product.feature_image}" alt="${product.name}" class="product-image" />
                                            </a>
                                            ${product.stock && product.stock.quantity > 0 ? `
                                                <div class="product-action-vertical">
                                                    <a href="#" class="btn-product-icon btn-wishlist add-to-wishlist" title="Add to wishlist" data-product-id="${product.id}" data-offer-id="0" data-price="${product.price}"></a>
                                                </div>
                                                <div class="product-action">
                                                    <a href="#" class="btn-product btn-cart add-to-cart" title="Add to cart" data-product-id="${product.id}" data-offer-id="0" data-price="${product.price}"><span>add to cart</span></a>
                                                </div>
                                            ` : `<span class="product-label label-out-stock">Out of stock</span>`}
                                        </figure>
                                        <div class="product-body">
                                            <h3 class="product-title"><a href="{{ route('product.show', '') }}/${product.slug}">${product.name}</a></h3>
                                            <div class="product-price">
                                                {{$currency}} ${parseFloat(product.price).toFixed(2)}
                                            </div>
                                        </div>
                                    </div>
                                </div>`;
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