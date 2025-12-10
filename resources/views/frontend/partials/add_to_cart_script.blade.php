

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
                updateMiniCart(res.cart); 

                // reset UI
                $('input[name="color"]').prop('checked', false);
                $('.color-option').removeClass('active');
                $('.qty-input').val(0);

                if (action === 'customize') {
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
    // updateMiniCart();

    // function updateMiniCart(cart) {

    //     let miniCartList = document.getElementById("miniCartList");
    //     let footer = document.getElementById("miniCartFooter");
    //     let divider = document.getElementById("miniCartDivider");
    //     let emptyMsg = document.getElementById("emptyCartMsg");

    //     // If mini cart elements do not exist on this page
    //     if (!miniCartList || !footer || !divider || !emptyMsg) {
    //         return; 
    //     }

    //     miniCartList.innerHTML = ""; // clear

    //     let totalAmount = 0;
    //     let itemCount = 0;

    //     if (Object.keys(cart).length === 0) {
    //         emptyMsg.classList.remove("d-none");
    //         footer.classList.add("d-none");
    //         divider.classList.add("d-none");
    //         // document.querySelector(".cartCount").textContent = 0;
    //         return;
    //     }

    //     emptyMsg.classList.add("d-none");

    //     Object.keys(cart).forEach(key => {
    //         let item = cart[key];

    //         let price = item.selling_price ?? 0;
    //         totalAmount += price * item.quantity;
    //         itemCount += parseInt(item.quantity);

    //         miniCartList.innerHTML += `
    //             <li class="cart-item d-flex justify-content-between align-items-center p-2 border-bottom">
    //                 <div class="d-flex align-items-center">
    //                     <img src="${item.product_image_link}" width="50" class="me-2 rounded">
    //                     <div>
    //                         <p class="mb-0 fw-bold">${item.product_name}</p>
    //                         <small class="text-muted">${item.sizeName} | Qty: ${item.quantity} × £${price}</small>
    //                     </div>
    //                 </div>

    //                 <button type="button" class="btn btn-sm btn-outline-danger removeCartItem" data-key="${key}">
    //                     <i class="fas fa-times"></i>
    //                 </button>
    //             </li>
    //         `;
    //     });

    //     console.log(itemCount);

    //     // document.querySelector(".cartCount").textContent = itemCount;

    //     document.getElementById("miniCartTotal").textContent = "£" + totalAmount.toFixed(2);
        
    //     footer.classList.remove("d-none");
    //     divider.classList.remove("d-none");
    // }

    function updateMiniCart(cart) {

        let miniCartItems = document.getElementById("miniCartItems");
        let footer = document.getElementById("miniCartFooter");
        let divider = document.getElementById("miniCartDivider");
        let emptyMsg = document.getElementById("emptyCartMsg");
        let cartCount = document.querySelector(".cartCount");

        if (!miniCartItems || !footer || !divider || !emptyMsg || !cartCount) {
            return;
        }

        miniCartItems.innerHTML = ""; // only clear items

        let totalAmount = 0;
        let itemCount = 0;

        if (Object.keys(cart).length === 0) {
            emptyMsg.classList.remove("d-none");
            footer.classList.add("d-none");
            divider.classList.add("d-none");
            cartCount.textContent = 0;
            return;
        }

        emptyMsg.classList.add("d-none");

        Object.keys(cart).forEach(key => {
            let item = cart[key];

            let price = item.selling_price ?? 0;
            totalAmount += price * item.quantity;
            itemCount += parseInt(item.quantity);

            miniCartItems.innerHTML += `
                <li class="cart-item d-flex justify-content-between align-items-center p-2 border-bottom">
                    <div class="d-flex align-items-center">
                        <img src="${item.product_image_link}" width="50" class="me-2 rounded">
                        <div>
                            <p class="mb-0 fw-bold">${item.product_name}</p>
                            <small class="text-muted">${item.sizeName} | Qty: ${item.quantity} × £${price}</small>
                        </div>
                    </div>

                    <button type="button" class="btn btn-sm btn-outline-danger removeCartItem" data-key="${key}">
                        <i class="fas fa-times"></i>
                    </button>
                </li>
            `;
        });

        cartCount.textContent = itemCount;
        document.getElementById("miniCartTotal").textContent = "£" + totalAmount.toFixed(2);

        footer.classList.remove("d-none");
        divider.classList.remove("d-none");
    }


    $(document).ready(function () {
    // Load cart items on page load
        $.ajax({
            url: "{{ route('cart.sessionData') }}",
            method: "GET",
            success: function (res) {
                updateMiniCart(res.cart);
            }
        });
    });



}); // end ready
</script>



