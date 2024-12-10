<script>
    $(document).ready(function () {
        $('#quickAddToCartModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var productId = button.data('product-id');
            var modal = $(this);

            modal.find('#modalProductImage').attr('src', button.data('image'));
            modal.find('#productPrice').text('{{ $currency }}' + button.data('price'));
            modal.find('.add-to-cart').data('product-id', productId);
            modal.find('#qty').attr('max', button.data('stock')).val(1);

            var colors = button.data('colors');
            var colorForm = modal.find('#colorForm');
            colorForm.empty();
            if (Array.isArray(colors)) {
                colors.forEach(function (color, index) {
                    colorForm.append(`
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input color-input" id="color-${index}" name="color" value="${color}">
                            <label class="custom-control-label" for="color-${index}">${color}</label>
                        </div>
                    `);
                });
            } else {
                Object.keys(colors).forEach(function (key, index) {
                    colorForm.append(`
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input color-input" id="color-${index}" name="color" value="${colors[key]}">
                            <label class="custom-control-label" for="color-${index}">${colors[key]}</label>
                        </div>
                    `);
                });
            }

            modal.find('#sizeForm').empty();
            modal.find('#qty').attr('max', '').val(1);
        });

        $(document).on('change', '.color-input', function () {
            var selectedColor = $(this).val();
            var productId = $('#quickAddToCartModal').find('.add-to-cart').data('product-id');
            var modal = $('#quickAddToCartModal');

            // console.log(selectedColor, productId);
            if (!productId) {
                return;
            }

            $.ajax({
                url: '/get-sizes',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: productId,
                    color: selectedColor,
                },
                success: function (response) {
                    // console.log(response);
                    var sizeForm = modal.find('#sizeForm');
                    sizeForm.empty();
                    response.sizes.forEach(function (size, index) {
                        sizeForm.append(`
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" class="custom-control-input size-input" id="size-${index}" name="size" value="${size}">
                                <label class="custom-control-label" for="size-${index}">${size}</label>
                            </div>
                        `);
                    });

                    var price = response.price;
                    $('.add-to-cart').attr('data-price', price);
                    modal.find('#qty').attr('max', response.max_quantity).val(1);
                    modal.find('.add-to-cart').data('price', response.selling_price);
                    modal.find('#productPrice').text('{{ $currency }}' + response.selling_price);
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching sizes:', xhr.responseText);
                },
            });
        });

        $('#quickAddToCartModal').on('hidden.bs.modal', function () {
            var modal = $(this);
            modal.find('#modalProductImage').attr('src', '');
            modal.find('#productPrice').text('');
            modal.find('#colorForm').empty();
            modal.find('#sizeForm').empty();
            modal.find('#qty').attr('max', '').val(1);
        });
    });
</script>