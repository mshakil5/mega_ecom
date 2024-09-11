@extends('frontend.layouts.app')

@section('title', $title)

@section('content')

    <style>
        #search-results li {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        #search-results li a {
            text-decoration: none;
            color: #333;
        }

        #search-results li:hover {
            background-color: #f8f8f8;
        }
    </style>

    <div class="col-lg-4 col-6 mx-xl-5">
        <form id="supplier-search-form" class="position-relative">
            <div class="input-group">
                <input type="hidden" id="supplier-search-supplier-id" value="{{ $supplier->id }}">
                <input type="text" id="supplier-search-input" class="form-control" placeholder="Search for products from {{ $supplier->name }}">
                <div class="input-group-append">
                    <span class="input-group-text bg-transparent text-primary" id="supplier-search-icon" style="cursor: pointer;">
                        <i class="fa fa-search"></i>
                    </span>
                </div>
            </div>
        </form>
        <div id="supplier-search-results" class="bg-light position-absolute w-100" style="z-index: 1000;"></div>
    </div>


<div class="container-fluid pt-5 pb-3">
    <h2 class="section-title position-relative text-uppercase mx-xl-5 mb-4">
        <span class="bg-secondary pr-3">Explore Products from {{ $supplier->name }}</span>
    </h2>
    <div class="row px-xl-5">
        @foreach($products as $product)
            <div class="col-lg-3 col-md-4 col-sm-6 pb-1 mb-4">
                <div class="product-item bg-light d-flex flex-column h-100">
                    <div class="product-img position-relative overflow-hidden" style="height: 250px;">
                        <x-image-with-loader class="img-fluid w-100 h-100" src="{{ asset('/images/products/' . $product->feature_image) }}" alt="{{ $product->name }}" style="object-fit: cover;"/>
                        <div class="product-action">
                            @php
                                $stock = $product->supplierStocks->first();
                            @endphp
                            @if ($stock && $stock->quantity > 0)
                                <a class="btn btn-outline-dark btn-square add-to-cart" data-product-id="{{ $product->id }}" data-offer-id="0" data-price="{{ $stock->price }}" data-supplier-id="{{ $supplier->id }}">
                                    <i class="fa fa-shopping-cart"></i>
                                </a>
                            @else
                                <a class="btn btn-outline-dark btn-square disabled" aria-disabled="true">
                                    <i class="fa fa-shopping-cart"></i>
                                </a>
                            @endif
                            <a class="btn btn-outline-dark btn-square add-to-wishlist" data-product-id="{{ $product->id }}" data-offer-id="0" data-price="{{ $stock ? $stock->price : $product->price }}">
                                <i class="fa fa-heart"></i>
                            </a>
                        </div>
                    </div>
                    <div class="text-center py-4 mt-auto">
                       <a class="h6 text-decoration-none text-truncate" href="{{ route('product.show.supplier', [$product->slug, $supplier->id]) }}">{{ $product->name }}</a>
                        <div class="d-flex align-items-center justify-content-center mt-2">
                            <h5>{{ $currency }} {{ $stock ? $stock->price : $product->price }}</h5>
                        </div>
                        <div class="d-flex align-items-center justify-content-center mt-2">
                            @if (!$stock || $stock->quantity <= 0)
                                <p>Out of Stock</p>
                            @else
                                <span>&nbsp;</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

@section('script')

<script>
    $(document).ready(function() {
        const $searchInput = $('#supplier-search-input');
        const $searchResults = $('#supplier-search-results');
        const $searchIcon = $('#supplier-search-icon');
        const $supplierId = $('#supplier-search-supplier-id').val();

        function performSearch() {
            let query = $searchInput.val();
            console.log('Query:', query);
            console.log('Supplier ID:', $supplierId);

            if (query.length > 2) {
                $.ajax({
                    url: "{{ route('search.supplier.products') }}",
                    method: 'GET',
                    data: { query: query, supplier_id: $supplierId },
                    success: function(data) {
                        console.log('Success:', data);
                        $searchResults.html(data);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching search results:', {
                            status: status,
                            error: error,
                            responseText: xhr.responseText
                        });
                        $searchResults.html('<div class="p-2">An error occurred</div>');
                    }
                });
            } else {
                $searchResults.html('');
            }
        }

        $searchInput.on('keyup', performSearch);
        $searchIcon.on('click', performSearch);

        $(document).on('click', function(e) {
            if (!$(e.target).closest('#supplier-search-form').length) {
                $searchResults.html('');
            }
        });
    });
</script>

@endsection