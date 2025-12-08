{{-- <script>
    $(document).ready(function() {

        function getSelectedSizes() {
            let sizes = [];
            $('.qty-input').each(function() {
                let qty = parseInt($(this).val());
                if (qty > 0) {
                    sizes.push({
                        size_id: $(this).data('size-id'),
                        ean: $(this).data('ean') || null,
                        quantity: qty
                    });
                }
            });
            return sizes;
        }

        function updateCartCount() {
            $.ajax({
                url: "{{ route('cart.getCount') }}",
                method: "GET",
                success: function(res) {
                    $('.cartCount').text(res.count || 0);
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }

        $('.add-to-cart-btn').on('click', function(e) {
            e.preventDefault();
            let action = $(this).data('action');
            let productId = $(this).data('product-id');
            let productName = $(this).data('product-name');
            let productImage = $(this).data('image');
            let selectedColorId = $('input[name="color"]:checked').val();
            let sizes = getSelectedSizes();

            if (!selectedColorId) {
                toastr.warning("Please select a color.");
                return;
            }

            if (sizes.length === 0) {
                toastr.warning("Please select quantity.");
                return;
            }


            $.ajax({
                url: "{{ route('cart.addSession') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    product_id: productId,
                    color_id: selectedColorId,
                    sizes: sizes,
                    product_name: productName,
                    product_image: productImage
                },
                success: function(res) {
                    toastr.success("Product added to cart!");
                    console.log('Session cart:', res.cart);
                    updateCartCount();
                    $('input[name="color"]').prop('checked', false);
                    $('.qty-input').val(0);

                    if (action === 'cart') {
                        $('#product-name').text(productName);
                        $('#product-image').attr('src', productImage);
                        $('#offcanvas').addClass('show');
                        $('.offcanvas-overlay').show();
                    } else if (action === 'customize') {
                        setTimeout(() => {
                            const encodedId = btoa(productId);
                            window.location.href = "/customize?product=" +
                            encodedId;
                        }, 500);
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        });



        $('.plus-btn').on('click', function() {
            const input = $(this).prev('.qty-input');
            input.val(parseInt(input.val() || 0) + 1);
        });

        $('.minus-btn').on('click', function() {
            const input = $(this).next('.qty-input');
            if (parseInt(input.val() || 0) > 0) {
                input.val(parseInt(input.val() || 0) - 1);
            }
        });

        updateCartCount();
    });
</script> --}}

<script>
$(function () {

    // ---------- Utilities ----------
    function updateCartCount() {
        $.ajax({
            url: "{{ route('cart.getCount') }}",
            method: "GET",
            success: function(res) {
                $('.cartCount').text(res.count || 0);
            },
            error: function(xhr) {
                console.log('cartCount error:', xhr.responseText);
            }
        });
    }



    function getSelectedSizes() {
        let sizes = [];

        $('.qty-input').each(function() {
            let qty = parseInt($(this).val());
            if (qty > 0) {
                sizes.push({
                    stock_id: $(this).data('variant-id'),  // FIXED
                    size: $(this).data('size'),
                    ean: $(this).data('ean') || null,
                    quantity: qty
                });
            }
        });

        return sizes;
    }


    // ---------- Plus / Minus handlers (delegated) ----------
    $(document).on('click', '.plus-btn', function (e) {
        e.preventDefault();
        // depending on markup, plus is before input; handle generically:
        let $input = $(this).siblings('.qty-input').first();
        if (!$input.length) $input = $(this).prevAll('.qty-input').first();
        if (!$input.length) $input = $(this).nextAll('.qty-input').first();
        if ($input.length) $input.val(parseInt($input.val() || 0) + 1).trigger('change');
    });

    $(document).on('click', '.minus-btn', function (e) {
        e.preventDefault();
        let $input = $(this).siblings('.qty-input').first();
        if (!$input.length) $input = $(this).prevAll('.qty-input').first();
        if (!$input.length) $input = $(this).nextAll('.qty-input').first();
        if ($input.length) {
            const current = parseInt($input.val() || 0);
            if (current > 0) $input.val(current - 1).trigger('change');
        }
    });

    // ---------- Thumbnail click (single handler) ----------
    $(document).on('click', '.thumb-item', function () {
        const largeImage = $(this).find('img').data('large');
        if (largeImage) {
            $('#main-product-image').attr('src', largeImage);
        }
        $('.thumb-item').removeClass('active');
        $(this).addClass('active');
    });
    $('.thumb-item:first').addClass('active');


    // ---------- Color option click (delegated) ----------
    $(document).on('click', '.color-option', function () {
        const colorImage = $(this).data('image');
        const colorId = $(this).data('color-id');
        // set main image if image exists
        if (colorImage) {
            $('#main-product-image').attr('src', colorImage);
        }
        // mark active UI
        $('.color-option').removeClass('active');
        $(this).addClass('active');

        // check the actual radio
        const radio = $('#color-' + colorId);
        if (radio.length) radio.prop('checked', true);
    });


    // ---------- Add to cart (handles both "cart" and "customize") ----------
    // Note: your blade uses class="add-to-cart" on <a> elements, so listen for that.
    $(document).on('click', '.add-to-cart', function (e) {
        e.preventDefault();

        const $btn = $(this);
        const action = $btn.data('action') || 'cart';
        const productId = $btn.data('product-id');
        const productName = $btn.data('product-name');
        // product image might be a file name — produce a full url if needed by reading data-image attr
        const productImage = $btn.data('image');

        const selectedColorId = $('input[name="color"]:checked').val();
        const sizes = getSelectedSizes();

        console.log(sizes, selectedColorId, productImage, productId, action);

        if (!selectedColorId) {
            toastr.warning("Please select a color.");
            return;
        }

        if (sizes.length === 0) {
            toastr.warning("Please select quantity.");
            return;
        }

        $.ajax({
            url: "{{ route('cart.addSession') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                product_id: productId,
                color_id: selectedColorId,
                sizes: sizes,
                product_name: productName,
                product_image: productImage
            },
            success: function (res) {
                toastr.success("Product added to cart!");
                console.log('Session cart:', res.cart);

                updateCartCount();

                // reset UI
                $('input[name="color"]').prop('checked', false);
                $('.color-option').removeClass('active');
                $('.qty-input').val(0);

                if (action === 'cart') {
                    // If you use an offcanvas to show mini cart
                    $('#product-name').text(productName);
                    if (productImage) {
                        $('#product-image').attr('src', productImage);
                    }
                    $('#offcanvas').addClass('show');
                    $('.offcanvas-overlay').show();
                } else if (action === 'customize') {
                    // forward to customize
                    const encodedId = btoa(String(productId));
                    window.location.href = "/customize?product=" + encodedId;
                }
            },
            error: function (xhr) {
                console.error('Add to cart error:', xhr.responseText);
                // optionally show message
                toastr.error("Could not add to cart. Try again.");
            }
        });

    });

    // initial cart count load
    updateCartCount();

}); // end ready
</script>
