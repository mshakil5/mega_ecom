<script>
    $(document).ready(function() {
        const $desktopSearchInput = $('.search-input');
        const $mobileSearchInput = $('#mobile-search-input');
        const $searchResults = $('.search-products');
        const $desktopSearchIcon = $('.search-icon');
        const $mobileSearchIcon = $('#mobile-search-icon');

        function performSearch($input) {
            let query = $input.val();
            // console.log(query);
            if (query.length > 2) {
                $.ajax({
                    url: "{{ route('search.products') }}",
                    method: 'GET',
                    data: { query: query },
                    success: function(response) {
                        // console.log(response.products);
                        const products = response.products;
                        let productListHtml = '';

                        products.forEach(product => {
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
                                                        <a href="#" class="btn-product btn-cart" 
                                                        title="Add to cart" 
                                                        data-product-id="${product.id}" 
                                                        data-offer-id="0" 
                                                        data-price="${product.price}" 
                                                        data-toggle="modal" 
                                                        data-target="#quickAddToCartModal" 
                                                        data-image="{{ asset('images/products/') }}/${product.feature_image}" 
                                                        data-stock="${product.stock.quantity}"
                                                        data-colors='${JSON.stringify(product.colors)}' 
                                                        data-sizes='${JSON.stringify(product.sizes)}'>
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
                                                    {{ $currency }} ${parseFloat(product.price).toFixed(2)}
                                                </div>
                                            </div>
                                        </div>
                                    </div>`;
                            }
                        });

                        $searchResults.html(productListHtml);
                        $('.mobile-menu-close').click();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching search results:', error);
                        $searchResults.html('<div class="p-2">An error occurred</div>');
                    }
                });
            } else {
                $searchResults.html(''); 
            }
        }

        $desktopSearchInput.on('keyup', function() {
            performSearch($(this));
        });

        $desktopSearchIcon.on('click', function() {
            performSearch($desktopSearchInput);
        });

        $mobileSearchInput.on('keyup', function() {
            performSearch($(this));
        });

        $mobileSearchIcon.on('click', function() {
            performSearch($mobileSearchInput);
        });
    });
</script>