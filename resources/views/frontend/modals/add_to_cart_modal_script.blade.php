<script>
    $(document).ready(function() {
        $('#quickAddToCartModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var imageSrc = button.data('image');
            var price = '{{ $currency }}' + button.data('price');
            var maxQuantity = button.data('stock');

            var productId = button.data('product-id');
            var offerId = button.data('offer-id');
            var supplierId = button.data('supplier-id') || '';
            var campaignId = button.data('campaign-id') || '';
            
            var modal = $(this);
            modal.find('#modalProductImage').attr('src', imageSrc);
            modal.find('#productPrice').text(price);
            modal.find('#qty').attr('max', maxQuantity);

            modal.find('.add-to-cart').attr({
                'data-product-id': productId,
                'data-price': button.data('price'),
                'data-offer-id': offerId,
                'data-supplier-id': supplierId,
                'data-campaign-id': campaignId
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
                'data-supplier-id': '',
                'data-campaign-id': ''
            });

            modal.find('.modal-body').find('input, textarea').val('');
            modal.find('.modal-body').find('select').val('').trigger('change');
        });
    });
</script>