@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Sell Product</h3>
                    </div>
                    <div class="card-body">
                        <div class="ermsg"></div>
                        <form id="createThisForm">
                            @csrf
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="purchase_date">Selling Date</label>
                                        <input type="date" class="form-control" id="purchase_date" name="purchase_date" placeholder="Enter date">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="supplier_id">Select Customer</label>
                                        <select class="form-control" id="user_id" name="user_id">
                                            <option value="" >Select...</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="purchase_type">Transaction Type</label>
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
                                        <select class="form-control" id="size" name="size">
                                            <option value="">Select...</option>
                                            <option value="XS">XS</option>
                                            <option value="S">S</option>
                                            <option value="M">M</option>
                                            <option value="L">L</option>
                                            <option value="XL">XL</option>
                                        </select>
                                    </div>
                                </div>
                                 <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="color">Color</label>
                                        <select class="form-control" id="color" name="color">
                                            <option value="">Select...</option>
                                            <option value="Black">Black</option>
                                            <option value="White">White</option>
                                            <option value="Red">Red</option>
                                            <option value="Blue">Blue</option>
                                            <option value="Green">Green</option>                                     
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
                                            <div class="row mb-3">
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
                                <button type="submit" id="addBtn" class="btn btn-secondary" value="Create"><i class="fas fa-plus"></i> Create</button>  
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

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

        $('#createThisForm').submit(function(e) {
            e.preventDefault();

            var formData = $(this).serializeArray();
            var products = [];

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
                    console.log(xhr.responseText);
                }
            });
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#product_id').select2({
            placeholder: "Select product...",
            allowClear: true,
            width: '100%'
        });
    });
</script>

<script>
    window.onload = function() {
        document.getElementById("purchase_date").value = new Date().toISOString().split('T')[0];
    };
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

@endsection