<!-- Category Wise Subcategory Start -->
<script>
    $(document).ready(function() {
        $('#category').change(function() {
            var categoryId = $(this).val();
            if (categoryId) {
                $('#subcategory').val('').find('option').hide();
                $('.category-' + categoryId).show();
            } else {
                $('#subcategory').val('').find('option').hide();
                $('#subcategory').find('.subcategory-option').show();
            }
        });
    });
</script>
<!-- Category Wise Subcategory End -->

<!-- Data Table and Select2 -->
<script>
    $(function() {
        $('.select2').select2({
            placeholder: "Select sizes",
            width: '100%'
        });

        $('#long_description, #short_description').summernote({
            height: 100,
        });

        $("#feature-img").change(function(e) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $("#preview-image").attr("src", e.target.result);
            };
            reader.readAsDataURL(this.files[0]);
        });
    });
</script>

<!-- Dynamic Row Script -->
<script>
    $(document).ready(function() {
        $(document).on('click', '.add-row', function() {
            let newRow = `
            <div class="form-row dynamic-row">
                <div class="form-group col-md-5">
                    <label for="color_id">Select Color</label>
                    <select class="form-control" name="color_id[]" id="color_id">
                        <option value="">Choose Color</option>
                        @foreach($colors as $color)
                            <option value="{{ $color->id }}" style="background-color: {{ $color->color_code }};">
                                {{ $color->color }} ({{ $color->color_code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-5">
                    <label for="image">Select Image</label>
                    <input type="file" class="form-control" name="image[]" accept="image/*">
                </div>
                <div class="form-group col-md-1">
                    <label>Action</label>
                    <button type="button" class="btn btn-danger remove-row"><i class="fas fa-minus"></i></button>
                </div>
            </div>`;

            $('#dynamic-rows').append(newRow);
        });

        $(document).on('click', '.remove-row', function() {
            $(this).closest('.dynamic-row').remove();
        });
    });
</script>

<!-- Create Product Start -->
<script>
    $(document).ready(function() {
        $(document).on('click', '#productaddBtn', function(e) {
            e.preventDefault();

            $(this).attr('disabled', true);
            $('#loader').show();

            var formData = new FormData($('#productCreateThisForm')[0]);

            $.ajax({
                url: '{{ route("store.product") }}',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                cache: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {

                    $('#product_id').append(`
                        <option value="${response.product.id}" data-name="${response.product.name}" data-price="${response.product.price}">
                            ${response.product.product_code} - ${response.product.name}
                        </option>
                    `);
                    $('#newProductModal').modal('hide');
                    $('#productCreateThisForm')[0].reset();
                    swal({
                        text: "Created successfully",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.log(xhr);
                    swal({
                        text: xhr.responseJSON.message,
                        icon: "error",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    })
                    console.error(xhr.responseText);
                },
                complete: function() {
                    $('#loader').hide();
                    $('#productaddBtn').attr('disabled', false);
                }
            });
        });

        // $('#product_code').on('keyup', function() {
        //     let productCode = $(this).val().trim();

        //     if (productCode.length >= 2) {
        //         $.ajax({
        //             url: "{{ route('check.product.code') }}",
        //             method: "GET",
        //             data: { product_code: productCode },
        //             success: function(response) {
        //                 if (response.exists) {
        //                     $('#productCodeError').text('This product code is already in use.');
        //                     $('#productaddBtn').attr('disabled', true);
        //                 } else {
        //                     $('#productCodeError').text('');
        //                     $('#productaddBtn').attr('disabled', false);
        //                 }
        //             }
        //         });
        //     } else {
        //         $('#productCodeError').text('');
        //         $('#productaddBtn').attr('disabled', true);
        //     }
        // });
    });
</script>
<!-- Create Product End -->

<script>

    $(document).ready(function() {

        // new supplier add 
        $('#saveSupplierBtn').on('click', function() {

            let password = $('#password').val();
            let confirmPassword = $('#confirm_password').val();

            if (password !== confirmPassword) {
                
                swal({
                    text: "Passwords do not match !",
                    icon: "error",
                    button: {
                        text: "OK",
                        className: "swal-button--confirm"
                    }
                });

                return false;
            }

            let formData = {
                id_number: $('#supplier_id_number').val(),
                name: $('#supplier_name').val(),
                email: $('#supplier_email').val(),
                phone: $('#supplier_phone').val(),
                password: $('#password').val(),
                vat_reg: $('#vat_reg1').val(),
                contract_date: $('#contract_date').val(),
                address: $('#address').val(),
                company: $('#company').val(),
                _token: '{{ csrf_token() }}'
            };

            $.ajax({
                url: '{{ route('supplier.store') }}',
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('#supplier_id').append(`<option value="${response.data.id}">${response.data.name}</option>`);
                        $('#newSupplierModal').modal('hide');
                        $('#newSupplierForm')[0].reset();
                        swal({
                            text: "Created successfully",
                            icon: "success",
                            button: {
                                text: "OK",
                                className: "swal-button--confirm"
                            }
                        });
                    } else {
                        alert('Failed to add supplier.');
                    }
                },
                error: function(xhr, status, error) {
                    // console.log(xhr.responseText);
                    alert('Error adding supplier. Please try again.');
                }
            });
        });

        // new color add 
        $('#saveColorBtn').click(function() {
            let colorName = $('#color_name').val();
            let color_code = $('#color_code').val();
            let price = $('#color_price').val();

            $.ajax({
                url: '{{ route('color.store') }}',
                type: 'POST',
                data: {
                    color_name: colorName,
                    color_code: color_code,
                    price: price,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        swal({
                            text: "Color added successfully",
                            icon: "success",
                            button: {
                                text: "OK",
                                className: "swal-button--confirm"
                            }
                        }).then(() => {
                            $('#product_color').append(`<option value="${response.data.color}">${response.data.color}</option>`);
                            $('#addColorModal').modal('hide');
                            $('#newColorForm')[0].reset();
                        });
                    } else {
                        swal({
                            text: "Failed to add color",
                            icon: "error",
                            button: {
                                text: "OK",
                                className: "swal-button--error"
                            }
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessage = "Error adding color. Please try again.";
                    
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).join("\n");
                    }
                    
                    swal({
                        text: errorMessage,
                        icon: "error",
                        button: {
                            text: "OK",
                            className: "swal-button--error"
                        }
                    });
                }
            });
        });

        // size
        $('#saveSizeBtn').click(function() {

            let size = $('#size_name').val();
            let price = $('#size_price').val();

            $.ajax({
                url: '{{ route('size.store') }}',
                type: 'POST',
                data: {
                    size: size,
                    price: price,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        swal({
                            text: "Size added successfully",
                            icon: "success",
                            button: {
                                text: "OK",
                                className: "swal-button--confirm"
                            }
                        }).then(() => {
                            $('#product_size').append(`<option value="${response.data.size}">${response.data.size}</option>`);
                            
                            $('#addSizeModal').modal('hide');
                            $('#newSizeForm')[0].reset();
                        });
                    } else {
                        swal({
                            text: "Failed to add size",
                            icon: "error",
                            button: {
                                text: "OK",
                                className: "swal-button--error"
                            }
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessage = "Error adding size. Please try again.";
                    
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).join("\n");
                    }
                    
                    swal({
                        text: errorMessage,
                        icon: "error",
                        button: {
                            text: "OK",
                            className: "swal-button--error"
                        }
                    });
                }
            });
        });
    });

</script>

<script>
    $('#quick-add-type').on('click', function () {
      $('#addTypeModal').modal('show');
    });

    $('#add-type-form').on('submit', function (e) {
        e.preventDefault();

        const productId = $('#product_id').val();
        const typeName = $('input[name="new_type_id"]').val();

        if (!productId) {
            alert('Please select a product first.');
            return;
        }

        $.ajax({
            url: '{{ route("types.quickAddWithProduct") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                product_id: productId,
                name: typeName
            },
            success: function (res) {
                if (res.status === 'exists') {
                    alert('Type already exists.');
                } else if (res.status === 'success' && res.data?.id && res.data?.name) {
                    let newOption = new Option(res.data.name, res.data.id, true, true);
                    $('#product-type-select').append(newOption).val(res.data.id);
                    $('#addTypeModal').modal('hide');
                    $('input[name="new_type_id"]').val('');
                }
            }
        });
    });
</script>