<script>
    $(document).ready(function() {
        function updateCartCount() {
            var cart = JSON.parse(localStorage.getItem('cart')) || [];
            var cartCount = cart.length;
            $('.cartCount').text(cartCount);
        }

        $(document).on('click', '.add-to-cart', function(e) {
            e.preventDefault();

            var productId = $(this).data('product-id') || null;
            var offerId = $(this).data('offer-id');
            var price = $(this).data('price');
            var campaignId = $(this).data('campaign-id') || null;
            var supplierId = $(this).data('supplier-id') || null;
            var bogoId = $(this).data('bogo-id') || null;
            var bundleId = $(this).data('bundle-id') || null;

            var selectedSize = $('input[name="size"]:checked').val() || 'M';
            var selectedColor = $('input[name="color"]:checked').val() || 'Black'; 
            var quantity = parseInt($('.quantity input').val()) || 1;

            var cart = JSON.parse(localStorage.getItem('cart')) || [];

            var existingItem = cart.find(function(item) {
                return item.productId === productId && 
                       item.size === selectedSize && 
                       item.color === selectedColor && 
                       item.offerId === offerId && 
                       item.bogoId === bogoId && 
                       item.supplierId === supplierId &&
                       item.campaignId === campaignId;
            });

            if (existingItem) {
                existingItem.quantity += quantity;
            } else {
                var cartItem = {
                    productId: productId,
                    offerId: offerId,
                    price: price,
                    size: selectedSize,
                    color: selectedColor,
                    quantity: quantity,
                    supplierId: supplierId,
                    bogoId: bogoId,
                    bundleId: bundleId,
                    campaignId: campaignId
                };
                cart.push(cartItem);
            }

            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartCount();

            // console.log(JSON.parse(localStorage.getItem('cart')));

            swal({
                text: "Added to cart",
                icon: "success",
                button: {
                    text: "OK",
                    className: "swal-button--confirm"
                }
            });
        });

        $(document).on('click', '.remove-from-cart', function() {
            var cart = JSON.parse(localStorage.getItem('cart')) || [];
            var index = $(this).data('cart-index');

            if (index !== undefined) {
                cart.splice(index, 1);
                localStorage.setItem('cart', JSON.stringify(cart));

                $.ajax({
                    url: "{{ route('cart.store') }}",
                    method: "PUT",
                    data: {
                        _token: "{{ csrf_token() }}",
                        cart: JSON.stringify(cart)
                    },
                    success: function() {
                        swal({
                            text: "Removed from cart",
                            icon: "success",
                            button: {
                                text: "OK",
                                className: "swal-button--confirm"
                            }
                        });
                        updateCartCount();
                    }
                });
            }
        });

        $(document).on('click', '.cartBtn', function(e){
            e.preventDefault();
            var cartlist = JSON.parse(localStorage.getItem('cart')) || [];
            console.log(JSON.parse(localStorage.getItem('cart')));
            
            $.ajax({
                url: "{{ route('cart.store') }}",
                method: "PUT",
                data: {
                    _token: "{{ csrf_token() }}",
                    cart: JSON.stringify(cartlist)
                },
                success: function() {
                    window.location.href = "{{ route('cart.index') }}";
                }
            });
        });

        updateCartCount();
    });
</script>
