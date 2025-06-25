<script>
    $(document).ready(function () {
        $('#quickAddToCartModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var modal = $(this);
            var productId = button.data('product-id');
            var modal = $(this);

            modal.find('#modalProductName').text(button.data('name'));
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
            var modal = $('#quickAddToCartModal');
            var productId = modal.find('.add-to-cart').data('product-id');

            if (!productId || !selectedColor) {
                modal.find('#sizeForm').empty();
                modal.find('#typeForm').empty();
                return;
            }

            modal.find('#sizeForm').empty();
            modal.find('#typeForm').empty();
            modal.find('#productPrice').text('{{ $currency }}' + modal.find('.add-to-cart').data('price'));

            $.ajax({
                url: '/get-sizes',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: productId,
                    color: selectedColor,
                },
                success: function (response) {
                    let sizeForm = modal.find('#sizeForm');
                    response.sizes.forEach(function (size, index) {
                        sizeForm.append(`
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" class="custom-control-input size-input" id="size-${index}" name="size" value="${size}">
                                <label class="custom-control-label" for="size-${index}">${size}</label>
                            </div>
                        `);
                    });

                    modal.find('#qty').attr('max', response.max_quantity).val(1);
                    modal.find('.add-to-cart').data('price', response.selling_price);
                    modal.find('#productPrice').text('{{ $currency }}' + response.selling_price);
                }
            });
        });

        $(document).on('change', '.size-input', function () {
            var modal = $('#quickAddToCartModal');
            var productId = modal.find('.add-to-cart').data('product-id');
            var selectedColor = modal.find('input[name="color"]:checked').val();
            var selectedSize = $(this).val();

            if (!productId || !selectedColor || !selectedSize) {
                modal.find('#typeForm').empty();
                return;
            }

            modal.find('#typeForm').empty();

            $.ajax({
                url: '/get-types',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: productId,
                    color: selectedColor,
                    size: selectedSize,
                },
                success: function(response) {
                    let typeForm = modal.find('#typeForm');
                    if (response.types && response.types.length > 0) {
                        response.types.forEach((type, index) => {
                            typeForm.append(`
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" class="custom-control-input type-input" 
                                          id="type-${index}" name="type" 
                                          value="${type.id}" data-price="${type.price}"
                                          ${index === 0 ? 'checked' : ''}>
                                    <label class="custom-control-label" for="type-${index}">${type.name}</label>
                                </div>
                            `);
                        });

                        let firstType = response.types[0];
                        modal.find('#productPrice').text('{{ $currency }}' + firstType.price);
                        modal.find('.add-to-cart').data('price', firstType.price);
                        modal.find('.add-to-cart').data('type-id', firstType.id);
                    }
                }
            });
        });

        $(document).on('change', '.type-input', function () {
            let price = $(this).data('price');
            let typeId = $(this).val();
            let modal = $('#quickAddToCartModal');
            modal.find('#productPrice').text('{{ $currency }}' + price);
            modal.find('.add-to-cart').data('price', price);
            modal.find('.add-to-cart').data('type-id', typeId);
        });

        $('#quickAddToCartModal').on('hidden.bs.modal', function () {
            var modal = $(this);
            modal.find('#modalProductImage').attr('src', '');
            modal.find('#productPrice').text('');
            modal.find('#colorForm').empty();
            modal.find('#sizeForm').empty();
            modal.find('#typeForm').empty();
            modal.find('#qty').attr('max', '').val(1);
            modal.find('.add-to-cart').removeData();
        });
    });
</script>