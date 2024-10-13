<script>
    $(document).ready(function() {
        function updateWishlistCount() {
            var wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
            var wishlistCount = wishlist.length;
            $('.wishlistCount').text(wishlistCount);
        }

        function updateHeartIcon(productId, offerId) {
            var wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
            var isInWishlist = wishlist.some(item => item.productId === productId && item.offerId === offerId);
            var wishlistButton = $('.add-to-wishlist[data-product-id="' + productId + '"][data-offer-id="' + offerId + '"]');
            if (isInWishlist) {
                wishlistButton.attr('title', 'Remove from wishlist'); 
                wishlistButton.find('span').text('Remove from wishlist');
            } else {
                wishlistButton.attr('title', 'Add to wishlist');
                wishlistButton.find('span').text('Add to wishlist');
            }
        }

        updateWishlistCount();

        $(document).on('click', '.add-to-wishlist', function(e){
            e.preventDefault();
            var productId = $(this).data('product-id');
            var offerId = $(this).data('offer-id');
            var price = $(this).data('price');
            var campaignId = $(this).data('campaign-id') || null;
            var wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];

            var itemIndex = wishlist.findIndex(item => item.productId === productId && item.offerId === offerId && item.campaignId === campaignId);
            if (itemIndex !== -1) {
                wishlist.splice(itemIndex, 1);
                swal({
                    text: "Removed from wishlist",
                    icon: "success",
                    button: {
                        text: "OK",
                        className: "swal-button--confirm"
                    }
                });
            } else {
                wishlist.push({ productId: productId, offerId: offerId, price: price, campaignId: campaignId });
                swal({
                    text: "Added to wishlist",
                    icon: "success",
                    button: {
                        text: "OK",
                        className: "swal-button--confirm"
                    }
                });
            }

            localStorage.setItem('wishlist', JSON.stringify(wishlist));
            updateHeartIcon(productId, offerId);
            updateWishlistCount();
        });

        $(document).on('click', '.wishlistBtn', function(e){
            e.preventDefault();
            var wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];

            console.log(JSON.parse(localStorage.getItem('wishlist')));
            // localStorage.removeItem('wishlist');
            
            $.ajax({
                url: "{{ route('wishlist.store') }}",
                method: "PUT",
                data: {
                    _token: "{{ csrf_token() }}",
                    wishlist: JSON.stringify(wishlist)
                },
                success: function() {
                    window.location.href = "{{ route('wishlist.index') }}";
                }
            });
        });

        $('.add-to-wishlist').each(function() {
            var productId = $(this).data('product-id');
            var offerId = $(this).data('offer-id');
            updateHeartIcon(productId, offerId);
        });
    });
</script>