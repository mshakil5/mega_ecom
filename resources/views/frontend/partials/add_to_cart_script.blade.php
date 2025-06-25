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
            var typeId = $(this).data('type-id') || null;

            var selectedColor = $('input[name="color"]:checked').val(); 
            var selectedSize = $('input[name="size"]:checked').val();

            if (!selectedColor) {
                toastr.error("Please select a color.", "");
                return;
            }

            if (!selectedSize) {
                toastr.error("Please select a size.", "");
                return;
            }

            var quantity = parseInt($('.quantity-input').val()) || 1;

            var cart = JSON.parse(localStorage.getItem('cart')) || [];

            var existingItem = cart.find(function(item) {
                return item.productId === productId && 
                       item.size === selectedSize && 
                       item.color === selectedColor && 
                       item.offerId === offerId && 
                       item.bogoId === bogoId && 
                       item.supplierId === supplierId &&
                       item.campaignId === campaignId &&
                       item.typeId === typeId;
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
                    campaignId: campaignId,
                    typeId: typeId
                };
                cart.push(cartItem);
            }

            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartCount();

            console.log(JSON.parse(localStorage.getItem('cart')));

            toastr.success("Added to cart", "");
            setTimeout(() => {
                $('#quickAddToCartModal').hide(300);
                $('body').removeClass('modal-open').css('overflow', '');
                $('.modal-backdrop.fade').remove();
            }, 100);

        });

        $('#quickAddToCartModal').on('hidden.bs.modal', function() {
            var modal = $(this);

            modal.find('#modalProductImage').attr('src', '');  
            modal.find('#productPrice').text('');       
            modal.find('.quantity-input').val(1);        
            modal.find('input[name="size"]').prop('checked', false);  
            modal.find('input[name="color"]').prop('checked', false);
            modal.find('.add-to-cart').removeData(); 
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
                        toastr.success("Removed from cart", "");
                        updateCartCount();
                    }
                });
            }
        });

        $(document).on('click', '.cartBtn', function(e){
            e.preventDefault();
            // localStorage.removeItem('cart');
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
