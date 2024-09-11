@extends('frontend.layouts.app')

@section('content')

<div class="container-fluid">
        <div class="row px-xl-5">
            <!-- Shop Sidebar Start -->
            <div class="col-lg-3 col-md-4">
                <form id="filterForm">
                    <!-- Price Filter Start -->
                    <h5 class="section-title position-relative text-uppercase mb-3"><span class="bg-secondary pr-3">Filter by price</span></h5>
                    <div class="bg-light p-4 mb-30">
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
                    <!-- Price Filter End -->
                    
                    <!-- Category Filter Start -->
                    <h5 class="section-title position-relative text-uppercase mb-3"><span class="bg-secondary pr-3">Filter by category</span></h5>
                    <div class="bg-light p-4 mb-30">
                        <div class="custom-control custom-radio d-flex align-items-center justify-content-between mb-3">
                            <input type="radio" class="custom-control-input" id="category-all" name="category" value="">
                            <label class="custom-control-label" for="category-all">All Categories</label>
                        </div>
                        @foreach( $categories as $category)
                            <div class="custom-control custom-radio d-flex align-items-center justify-content-between mb-3">
                                <input type="radio" class="custom-control-input" id="category-{{ $category->id }}" name="category" value="{{ $category->id }}">
                                <label class="custom-control-label" for="category-{{ $category->id }}">{{ $category->name }}</label>
                            </div>
                        @endforeach
                    </div>
                    <!-- Category Filter End -->
                </form>
            </div>
            <!-- Shop Sidebar End -->

            <!-- Shop Product Start -->
            <div class="col-lg-9 col-md-8">
                <div class="row pb-3">
                    <div class="col-12 pb-1">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div>
                                <!-- <button class="btn btn-sm btn-light"><i class="fa fa-th-large"></i></button>
                                <button class="btn btn-sm btn-light ml-2"><i class="fa fa-bars"></i></button> -->
                            </div>
                            <div class="ml-2">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-light dropdown-toggle" data-toggle="dropdown">Sorting</button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#">Latest</a>
                                        <a class="dropdown-item" href="#">Popularity</a>
                                        <a class="dropdown-item" href="#">Best Rating</a>
                                    </div>
                                </div>
                                <div class="btn-group ml-2">
                                    <button type="button" class="btn btn-sm btn-light dropdown-toggle" data-toggle="dropdown">
                                        Showing {{ request()->input('per_page', 10) }}
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="{{ route('frontend.shop', ['per_page' => 10]) }}">10</a>
                                        <a class="dropdown-item" href="{{ route('frontend.shop', ['per_page' => 20]) }}">20</a>
                                        <a class="dropdown-item" href="{{ route('frontend.shop', ['per_page' => 30]) }}">30</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="product-list">
                        @foreach($products as $product)
                            <div class="col-lg-4 col-md-6 col-sm-6 pb-1">
                                <div class="product-item bg-light mb-4">
                                    <div class="product-img position-relative overflow-hidden" style="height: 250px;">
                                        <x-image-with-loader class="img-fluid w-100 h-100" src="{{ asset('/images/products/' . $product->feature_image) }}" alt="{{ $product->name }}" style="object-fit: cover;"/>
                                        <div class="product-action">
                                            @if ($product->stock && $product->stock->quantity > 0)
                                                <a class="btn btn-outline-dark btn-square add-to-cart" data-product-id="{{ $product->id }}" data-offer-id="0" data-price="{{ $product->price }}">
                                                    <i class="fa fa-shopping-cart"></i>
                                                </a>
                                            @else
                                                <a class="btn btn-outline-dark btn-square disabled" aria-disabled="true">
                                                    <i class="fa fa-shopping-cart"></i>
                                                </a>
                                            @endif
                                            <a class="btn btn-outline-dark btn-square add-to-wishlist" data-product-id="{{ $product->id }}" data-offer-id="0" data-price="{{ $product->price }}">
                                                <i class="fa fa-heart"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="text-center py-4">
                                        <a class="h6 text-decoration-none text-truncate" href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a>
                                        <div class="d-flex align-items-center justify-content-center mt-2">
                                            <h5>{{$currency}} {{ $product->price }}</h5>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-center mb-1">
                                            @if ($product->stock && $product->stock->quantity > 0)
                                                <p>Available: {{ $product->stock->quantity }}</p>
                                            @else
                                                <p>Out of Stock</p>
                                            @endif
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

    <style>
    .col-lg-4 {
        min-width: 350px;
    }
</style>

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
                                <div class="col-lg-4 col-md-6 col-sm-6 pb-1">
                                    <div class="product-item bg-light mb-4">
                                        <div class="product-img position-relative overflow-hidden" style="height: 250px;">
                                            <x-image-with-loader class="img-fluid w-100 h-100" src="{{ asset('/images/products/') }}/${product.feature_image}" alt="${product.name}" style="object-fit: cover;"/>
                                            <div class="product-action">
                                                ${product.stock && product.stock.quantity > 0 ? `
                                                <a class="btn btn-outline-dark btn-square add-to-cart" data-product-id="${product.id}" data-offer-id="0" data-price="${product.price}">
                                                    <i class="fa fa-shopping-cart"></i>
                                                </a>` : `
                                                <a class="btn btn-outline-dark btn-square disabled" aria-disabled="true">
                                                    <i class="fa fa-shopping-cart"></i>
                                                </a>`}
                                                <a class="btn btn-outline-dark btn-square add-to-wishlist" data-product-id="${product.id}" data-offer-id="0" data-price="${product.price}">
                                                    <i class="fa fa-heart"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="text-center py-4">
                                            <a class="h6 text-decoration-none text-truncate" href="{{ route('product.show', '') }}/${product.slug}">${product.name}</a>
                                            <div class="d-flex align-items-center justify-content-center mt-2">
                                                <h5>{{$currency}} ${product.price}</h5>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-center mb-1">
                                                ${product.stock && product.stock.quantity > 0 ? `<p>Available: ${product.stock.quantity}</p>` : `<p>Out of Stock</p>`}
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