@extends('frontend.layouts.app')

@section('title', $title)

@section('content')

{{--  
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
    </div> --}}


<div class="container for-you">
    <h2 class="title text-center mb-5 mt-4">Explore Products from {{ $supplier->name }}</h2>
    <div class="products">
        <div class="row justify-content-center">
        @foreach($products as $product)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="product product-2">
                    <figure class="product-media">
                        <a href="{{ route('product.show.supplier', [$product->slug, $supplier->id]) }}">
                            <x-image-with-loader src="{{ asset('/images/products/' . $product->feature_image) }}" alt="{{ $product->name }}" class="product-image" />
                        </a>
                        @php
                            $stock = $product->supplierStocks->first();
                        @endphp
                        @if ($stock && $stock->quantity > 0)
                        <div class="product-action-vertical">                  
                            <a href="#" class="btn-product-icon btn-wishlist add-to-wishlist btn-expandable" title="Add to wishlist" data-product-id="{{ $product->id }}" data-offer-id="0" data-price="{{ $stock->price }}" data-supplier-id="{{ $supplier->id }}"><span>Add to wishlist</span>
                            </a>
                        </div>
                        <div class="product-action">
                            <a href="#" class="btn-product btn-cart add-to-cart" title="Add to cart" data-product-id="{{ $product->id }}" data-offer-id="0" data-price="{{ $stock->price }}" data-supplier-id="{{ $supplier->id }}"><span>add to cart</span></a>
                        </div>
                        @else
                        <span class="product-label label-out-stock">Out of stock</span>
                        @endif
                    </figure>
                    <div class="product-body">
                        <h3 class="product-title"><a href="{{ route('product.show.supplier', [$product->slug, $supplier->id]) }}">{{ $product->name }}</a></h3>
                        <div class="product-price">
                        {{ $currency }} {{ $stock ? number_format($stock->price, 2) : number_format($product->price, 2) }}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        </div>
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