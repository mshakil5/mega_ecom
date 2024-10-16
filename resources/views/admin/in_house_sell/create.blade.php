@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Sale Product</h3>
                    </div>
                    <div class="card-body">
                        <div class="ermsg"></div>
                        <form id="createThisForm">
                            @csrf
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="purchase_date">Selling Date*</label>
                                        <input type="date" class="form-control" id="purchase_date" name="purchase_date" placeholder="Enter date" value="{{ now()->format('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="supplier_id">Select Wholesaler*</label>
                                        <select class="form-control" id="user_id" name="user_id">
                                            <option value="" >Select...</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <label>New</label>
                                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#newWholeSalerModal">
                                            <i class="fas fa-plus"></i> Add
                                        </button>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="purchase_type">Transaction Type*</label>
                                        <select class="form-control" id="payment_method" name="payment_method">
                                            <option value="">Select...</option>
                                            <option value="cash">Cash</option>
                                            <option value="bank">Bank</option>
                                            <option value="bank">Credit</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="ref">Ref</label>
                                        <input type="text" class="form-control" id="ref" name="ref" placeholder="Enter reference">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="remarks">Remarks</label>
                                        <textarea class="form-control" id="remarks" name="remarks" rows="1" placeholder="Enter remarks"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="product_id">Choose Product</label>
                                        <select class="form-control" id="product_id" name="product_id">
                                            <option value="">Select...</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->price }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="quantity">Quantity</label>
                                        <input type="number" class="form-control" id="quantity" name="quantity" placeholder="Enter quantity">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="price_per_unit">Unit Price</label>
                                        <input type="number" step="0.01" class="form-control" id="price_per_unit" name="price_per_unit" placeholder="Enter unit price">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="size">Size</label>
                                        <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addSizeModal">Add New</span>
                                        <select class="form-control" id="size" name="size">
                                            <option value="">Select...</option>
                                            @foreach ($sizes as $size)
                                                <option value="{{ $size->size }}">{{ $size->size }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                 <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="color">Color</label>
                                        <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addColorModal">Add New</span>
                                        <select class="form-control" id="color" name="color">
                                            <option value="">Select...</option>
                                            @foreach ($colors as $color)
                                                <option value="{{ $color->color }}">{{ $color->color }}</option>
                                            @endforeach                                     
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <label for="addProductBtn">Action</label>
                                    <div class="col-auto d-flex align-items-end">
                                        <button type="button" id="addProductBtn" class="btn btn-secondary">Add</button>
                                     </div>
                                </div>
                                <div class="col-sm-12 mt-3">
                                    <h2>Product List:</h2>
                                    <table class="table table-bordered" id="productTable">
                                        <thead>
                                            <tr>
                                                <th>Product Name</th>
                                                <th>Quantity</th>
                                                <th>Size</th>
                                                <th>Color</th>
                                                <th>Unit Price</th>
                                                <th>Total Price</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                        </tbody>
                                    </table>
                                </div>

                                <div class="container mt-4 mb-5">
                                    <div class="row">
                                        <!-- Left side -->
                                        <div class="col-md-6">
                                            <div class="mb-2">
                                                <div class="d-flex align-items-center">
                                                    <span>Coupon:</span>
                                                    <input type="text" class="form-control ml-2" id="couponName">
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-center">
                                                <button id="applyCoupon" class="btn btn-secondary">Apply Coupon</button>
                                            </div>
                                        </div>

                                        <!-- Right side -->
                                        <div class="col-sm-6">
                                            <div class="row mb-3">
                                                <div class="col-sm-6 d-flex align-items-center justify-content-end">
                                                    <span>Item Total Amount:</span>
                                                </div>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="item_total_amount" readonly>
                                                </div>
                                            </div>
                                            <div class="row mb-3 d-none">
                                                <div class="col-sm-6 d-flex align-items-center justify-content-end">
                                                    <span>Vat Amount:</span>
                                                </div>
                                                <div class="col-sm-6">
                                                    <input type="number" step="0.01" class="form-control" id="vat" name="vat">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-6 d-flex align-items-center justify-content-end">
                                                    <span>Discount Amount:</span>
                                                </div>
                                                <div class="col-sm-6">
                                                    <input type="number" step="0.01" class="form-control" id="discount" name="discount">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-6 d-flex align-items-center justify-content-end">
                                                    <span>Net Amount:</span>
                                                </div>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="net_amount" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer">
                                <button id="addBtn" class="btn btn-success" value="Create"><i class="fas fa-cart-plus"></i> Make Sales</button>  
                                <button id="quotationBtn" class="btn btn-secondary" value="Create"><i class="fas fa-file-invoice"></i> Make Quotation</button>  
                                <div id="loader" style="display: none;">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    Loading...
                                </div> 
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- New Whole Saler Modal -->
<div class="modal fade" id="newWholeSalerModal" tabindex="-1" aria-labelledby="newWholeSalerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newWholeSalerModalLabel">Add New WholeSaler</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- New Supplier Form -->
                <form id="newWholeSalerForm">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Name*</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter name" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Surname</label>
                                <input type="text" class="form-control" id="surname" name="surname" placeholder="Enter surname">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Email*</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter email">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="number" class="form-control" id="phone" name="phone" placeholder="Enter phone">
                            </div>
                        </div>
                        <div class="col-sm-6 d-none">
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" class="form-control" id="password" name="password" value="123456" placeholder="Enter password">
                            </div>
                        </div>
                        <div class="col-sm-6 d-none">
                            <div class="form-group">
                                <label>Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" value="123456" placeholder="Enter password">
                            </div>
                        </div>
                    </div>
                    <!-- Submit Button -->
                    <button type="button" class="btn btn-success" id="saveWholeSalerBtn">Save Supplier</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Color Modal -->
<div class="modal fade" id="addColorModal" tabindex="-1" role="dialog" aria-labelledby="addColorModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-lg">
            <div class="modal-header">
                <h5 class="modal-title" id="addColorModalLabel">Add New Color</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="newColorForm">
                    <div class="form-group">
                        <label for="color_name">Color</label>
                        <input type="text" class="form-control" id="color_name" name="color_name" placeholder="Enter color">
                    </div>
                    <div class="form-group">
                        <label for="color_code">Color Code</label>
                        <input type="color" class="form-control" id="color_code" name="color_code" placeholder="Enter color code">
                    </div>
                    <div class="form-group">
                        <label for="color_price">Price</label>
                        <input type="number" class="form-control" id="color_price" name="color_price" placeholder="Enter price">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveColorBtn">Save Color</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Size Modal -->
<div class="modal fade" id="addSizeModal" tabindex="-1" role="dialog" aria-labelledby="addSizeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSizeModalLabel">Add New Size</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="newSizeForm">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="size_name">Size</label>
                            <input type="text" class="form-control" id="size_name" name="size_name" placeholder="Enter size">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="size_price">Price</label>
                            <input type="number" class="form-control" id="size_price" name="size_price" placeholder="Enter price">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveSizeBtn">Save Size</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')

<script>
    $(document).ready(function() {
        function updateSummary() {
            var itemTotalAmount = 0;

            $('#productTable tbody tr').each(function() {
                var quantity = parseFloat($(this).find('input.quantity').val()) || 0;
                var unitPrice = parseFloat($(this).find('input.price_per_unit').val()) || 0;
                var totalPrice = (quantity * unitPrice).toFixed(2);

                // console.log(`Quantity: ${quantity}, Unit Price: ${unitPrice}, Total Price: ${totalPrice}`);

                $(this).find('td:eq(4)').find('input.price_per_unit').val(unitPrice.toFixed(2));
                $(this).find('td:eq(5)').text(totalPrice); 
                itemTotalAmount += parseFloat(totalPrice) || 0;
            });

            $('#item_total_amount').val(itemTotalAmount.toFixed(2) || '0.00');
            // console.log(`Item Total Amount: ${itemTotalAmount}`);

            var discount = parseFloat($('#discount').val()) || 0; 
            var vat = parseFloat($('#vat').val()) || 0; 
            var netAmount = itemTotalAmount - discount + vat;
            $('#net_amount').val(netAmount.toFixed(2) || '0.00');
            // console.log(`Discount: ${discount}, Net Amount: ${netAmount}`);
        }

        $('#addProductBtn').click(function() {
            var selectedProduct = $('#product_id option:selected');
            var productId = selectedProduct.val();
            var productName = selectedProduct.data('name');
            var unitPrice = parseFloat($('#price_per_unit').val()) || 0;
            var quantity = parseFloat($('#quantity').val()) || 1;
            var selectedSize = $('#size').val() || '';
            var selectedColor = $('#color').val() || '';

            if (isNaN(quantity) || quantity <= 0) {
                alert('Quantity must be a positive number.');
                return;
            }

            var totalPrice = (quantity * unitPrice).toFixed(2);

            var productRow = `<tr>
                                <td>${productName}
                                <input type="hidden" name="product_id[]" value="${productId}"></td> 
                                <td><input type="number" class="form-control quantity" value="${quantity}" /></td>
                                <td>${selectedSize || 'M'}</td>
                                <td>${selectedColor || 'Black'}</td>
                                <td><input type="number" step="0.01" class="form-control price_per_unit" value="${unitPrice.toFixed(2)}" /></td>
                                <td>${totalPrice}</td>
                                <td><button type="button" class="btn btn-sm btn-danger remove-product">Remove</button></td>
                            </tr>`;

            $('#productTable tbody').append(productRow);
            $('#quantity').val('');
            $('#price_per_unit').val('');

            updateSummary();
        });

        $(document).on('click', '.remove-product', function() {
            $(this).closest('tr').remove();
            updateSummary();
        });

        $(document).on('input', '#productTable input.quantity, #productTable input.price_per_unit, #vat', function() {
            updateSummary();
        });

        $('#discount').on('input', function() {
            updateSummary();
        });

        $('#applyCoupon').click(function(e) {
            e.preventDefault();
            var couponName = $('#couponName').val();

            $.ajax({
                url: '/check-coupon',
                type: 'GET',
                data: { coupon_name: couponName },
                success: function(response) {
                    if (response.success) {
                        var isFixedAmount = response.coupon_type === 1;
                        var discountValue = parseFloat(response.coupon_value);

                        var itemTotalAmount = parseFloat($('#item_total_amount').val()) || 0;
                        var calculatedDiscount = isFixedAmount ? discountValue : (itemTotalAmount * (discountValue / 100));
                        $('#discount').val(calculatedDiscount.toFixed(2) || '0.00');

                        updateSummary();

                        swal("Valid Coupon", "Coupon applied successfully!", "success");
                    } else {
                        swal("Invalid Coupon", "Please enter a valid coupon.", "error");
                    }
                },
                error: function() {
                    swal("Error", "Error applying coupon.", "error");
                }
            });
        });

        $('#addBtn').on('click', function(e) {
            e.preventDefault();

            $(this).attr('disabled', true);
            $('#loader').show();

            var formData = $(this).serializeArray();
            var products = [];
            
            formData.push({ name: 'purchase_date', value: $('#purchase_date').val() });
            formData.push({ name: 'user_id', value: $('#user_id').val() });
            formData.push({ name: 'payment_method', value: $('#payment_method').val() });
            formData.push({ name: 'ref', value: $('#ref').val() });
            formData.push({ name: 'remarks', value: $('#remarks').val() });
            formData.push({ name: 'item_total_amount', value: $('#item_total_amount').val() });
            formData.push({ name: 'vat', value: $('#vat').val() });
            formData.push({ name: 'discount', value: $('#discount').val() });
            formData.push({ name: 'net_amount', value: $('#net_amount').val() });

            $('#productTable tbody tr').each(function() {
                var productId = $(this).find('input[name="product_id[]"]').val();
                var quantity = $(this).find('input.quantity').val();
                var unitPrice = parseFloat($(this).find('input.price_per_unit').val());
                var productSize = $(this).find('td:eq(2)').text();
                var productColor = $(this).find('td:eq(3)').text();
                var totalPrice = $(this).find('td:eq(5)').text();

                products.push({
                    product_id: productId,
                    quantity: quantity,
                    unit_price: unitPrice,
                    product_size: productSize,
                    product_color: productColor,
                    total_price: totalPrice
                });
            });

            formData.push({ name: 'vat', value: $('#vat').val() });

            formData = formData.filter(function(item) {
                return item.name !== 'product_id' && item.name !== 'quantity' && item.name !== 'price_per_unit' && item.name !== 'size' && item.name !== 'color';
            });

            formData.push({ name: 'products', value: JSON.stringify(products) });

            // console.log(formData);

            $.ajax({
                url: '/admin/in-house-sell',
                method: 'POST',
                data: formData,
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    swal({
                        text: "Created Successfully",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    }).then(() => {
                        window.location.href = response.pdf_url;

                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    });
                },
                error: function(xhr) {
                    swal({
                        text: xhr.responseJSON.message,
                        icon: "error",
                        button: {
                            text: "OK",
                            className: "swal-button--error"
                        }
                    })
                    console.log(xhr.responseText);
                },
                complete: function() {
                    $('#loader').hide();
                    $('#addBtn').attr('disabled', false);
                }
            });
        });

        $('#quotationBtn').on('click', function(e) {
            e.preventDefault();

            $(this).attr('disabled', true);
            $('#loader').show();

            var formData = $(this).serializeArray();
            var products = [];

            formData.push({ name: 'purchase_date', value: $('#purchase_date').val() });
            formData.push({ name: 'user_id', value: $('#user_id').val() });
            formData.push({ name: 'payment_method', value: $('#payment_method').val() });
            formData.push({ name: 'ref', value: $('#ref').val() });
            formData.push({ name: 'remarks', value: $('#remarks').val() });
            formData.push({ name: 'item_total_amount', value: $('#item_total_amount').val() });
            formData.push({ name: 'vat', value: $('#vat').val() });
            formData.push({ name: 'discount', value: $('#discount').val() });
            formData.push({ name: 'net_amount', value: $('#net_amount').val() });

            $('#productTable tbody tr').each(function() {
                var productId = $(this).find('input[name="product_id[]"]').val();
                var quantity = $(this).find('input.quantity').val();
                var unitPrice = parseFloat($(this).find('input.price_per_unit').val());
                var productSize = $(this).find('td:eq(2)').text();
                var productColor = $(this).find('td:eq(3)').text();
                var totalPrice = $(this).find('td:eq(5)').text();

                products.push({
                    product_id: productId,
                    quantity: quantity,
                    unit_price: unitPrice,
                    product_size: productSize,
                    product_color: productColor,
                    total_price: totalPrice
                });
            });

            formData.push({ name: 'vat', value: $('#vat').val() });

            formData = formData.filter(function(item) {
                return item.name !== 'product_id' && item.name !== 'quantity' && item.name !== 'price_per_unit' && item.name !== 'size' && item.name !== 'color';
            });

            formData.push({ name: 'products', value: JSON.stringify(products) });

            console.log(formData);

            $.ajax({
                url: '/admin/make-quotation',
                method: 'POST',
                data: formData,
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    swal({
                        text: "Quotation created successfully",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    swal({
                        text: xhr.responseJSON.message,
                        icon: "error",
                        button: {
                            text: "OK",
                            className: "swal-button--error"
                        }
                    })
                    console.log(xhr.responseText);
                },
                complete: function() {
                    $('#loader').hide();
                    $('#quotationBtn').attr('disabled', false);
                }
            });
        });

        $('#product_id').select2({
            placeholder: "Select product...",
            allowClear: true,
            width: '100%'
        });
    });
</script>

<script>
    $(document).ready(function() {

        $('#quantity').on('input', function() {
            if ($(this).val() < 0) {
                $(this).val(1);
            }
        });

        $('#product_id').change(function() {
            var selectedProduct = $(this).find(':selected');
            var pricePerUnit = selectedProduct.data('price');
            $('#quantity').val(1);
            
            if(pricePerUnit) {
                $('#price_per_unit').val(pricePerUnit);
            } else {
                $('#price_per_unit').val('');
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#saveWholeSalerBtn').on('click', function() {
            var formData = new FormData($('#newWholeSalerForm')[0]);
            formData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                type: 'POST',
                url: "{{ route('customer.store') }}",
                data: formData,
                processData: false,
                contentType: false,
                success: function() {
                    $('#user_id').append(`<option value="">${$('#name').val()} ${$('#surname').val() || ''}</option>`);
                    $('#newWholeSalerForm')[0].reset();
                    $('#newWholeSalerModal').modal('hide');

                    swal({
                        text: "Created successfully",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    });
                },
                error: function(xhr) {
                    swal({
                        text: xhr.responseJSON.message,
                        icon: "error",
                        button: {
                            text: "OK",
                            className: "swal-button--error"
                        }
                    })
                    // console.error(xhr.responseText);
                }
            });
        });

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
                            $('#color').append(`<option value="${response.data.color}">${response.data.color}</option>`);
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
                            $('#size').append(`<option value="${response.data.size}">${response.data.size}</option>`);
                            
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

@endsection