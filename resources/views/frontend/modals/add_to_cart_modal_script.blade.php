<script>
    $(document).ready(function() {
        $('#quickAddToCartModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var imageSrc = button.data('image');
            var price = ' {{ $currency }}' + button.data('price');
            var maxQuantity = button.data('stock');

            var productId = button.data('product-id');
            var offerId = button.data('offer-id');
            
            var modal = $(this);
            modal.find('#modalProductImage').attr('src', imageSrc);
            modal.find('#productPrice').text(price);
            modal.find('#qty').attr('max', maxQuantity);

            modal.find('.add-to-cart').attr({
                'data-product-id': productId,
                'data-price': button.data('price'),
                'data-offer-id': offerId,
            });
        });

        $('#quickAddToCartModal').on('hide.bs.modal', function() {
            var modal = $(this);
            
            modal.find('#modalProductImage').attr('src', '');
            modal.find('#productPrice').text('');
            modal.find('#qty').val(1).attr('max', '');

            modal.find('input[type="radio"]').prop('checked', false);

            modal.find('.add-to-cart').attr({
                'data-product-id': '',
                'data-price': '',
                'data-offer-id': '',
            });
        });
    });
</script>