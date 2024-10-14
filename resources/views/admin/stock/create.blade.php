@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Add new stock</h3>
                    </div>
                    <div class="card-body">
                        <div class="ermsg"></div>
                        <form id="createThisForm">
                            @csrf
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="purchase_date">Purchase Date</label>
                                        <input type="date" class="form-control" id="purchase_date" name="purchase_date" placeholder="Enter purchase date">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="supplier_id">Select Supplier</label>
                                        <select class="form-control" id="supplier_id" name="supplier_id">
                                            <option value="" >Select...</option>
                                            @foreach($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}" data-balance="{{ $supplier->balance }}">{{ $supplier->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <label>New</label>
                                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#newSupplierModal">
                                            <i class="fas fa-plus"></i> Add
                                        </button>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="supplier_balance">Supplier Balance</label>
                                        <input type="text" class="form-control" id="supplier_balance" name="supplier_balance" readonly placeholder="Enter supplier previous due">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="invoice">Invoice</label>
                                        <input type="text" class="form-control" id="invoice" name="invoice" placeholder="Enter invoice">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="vat_reg">VAT Reg#</label>
                                        <input type="text" class="form-control" id="vat_reg" name="vat_reg" placeholder="Enter VAT Reg#">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="purchase_type">Payment Type</label>
                                        <select class="form-control" id="purchase_type" name="purchase_type">
                                            <option value="">Select...</option>
                                            <option value="cash">Cash</option>
                                            <option value="bank">Bank</option>
                                            <option value="bank">Credit</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
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
                                        <label for="unit_price">Unit Price</label>
                                        <input type="number" step="0.01" class="form-control" id="unit_price" name="unit_price" placeholder="Enter unit price">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="product_size">Size</label>
                                        <button type="button" class="btn btn-success btn-sm ml-2" id="addSizeBtn" data-toggle="modal" data-target="#addSizeModal"> <i class="fas fa-plus"></i> Add</button>
                                        <select class="form-control" id="product_size" name="product_size">
                                            <option value="">Select...</option>
                                            @foreach ($sizes as $size)
                                                <option value="{{ $size->size }}">{{ $size->size }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                 <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="product_color">Color</label>
                                        <button type="button" class="btn btn-success btn-sm ml-2" id="addColorBtn" data-toggle="modal" data-target="#addColorModal"><i class="fas fa-plus"></i> Add</button>
                                        <select class="form-control" id="product_color" name="product_color">
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
                                                <th>VAT %</th>
                                                <th>VAT Amount</th>
                                                <th>Total Price</th>
                                                <th>Total Price with VAT</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                        </tbody>
                                    </table>
                                </div>

                                <div class="col-sm-12 mt-4 mb-5">
                                    <div class="row justify-content-end">
                                        <div class="col-sm-3 d-flex align-items-center">
                                            <span class="">Item Total Amount:</span>
                                            <input type="text" class="form-control" id="item_total_amount" readonly style="width: 100px; margin-left: auto;">
                                        </div>
                                    </div>
                                    <div class="row justify-content-end mt-3">
                                        <div class="col-sm-3 d-flex align-items-center">
                                            <span class="">Discount Amount:</span>
                                            <input type="number" step="0.01" class="form-control" id="discount" name="discount" style="width: 100px; margin-left: auto;">
                                        </div>
                                    </div>
                                    <div class="row justify-content-end mt-3">
                                        <div class="col-sm-3 d-flex align-items-center">
                                            <span class="">Total VAT Amount:</span>
                                            <input type="text" class="form-control" id="total_vat_amount" readonly style="width: 100px; margin-left: auto;">
                                        </div>
                                    </div>
                                    <div class="row justify-content-end mt-3">
                                        <div class="col-sm-3 d-flex align-items-center">
                                            <span class="">Net Amount:</span>
                                            <input type="text" class="form-control" id="net_amount" readonly style="width: 100px; margin-left: auto;">
                                        </div>
                                    </div>
                                    <div class="row justify-content-end mt-3">
                                        <div class="col-sm-3 d-flex align-items-center">
                                            <span class="">Paid Amount:</span>
                                            <input type="number" step="0.01" class="form-control" id="paid_amount" name="paid_amount" style="width: 100px; margin-left: auto;">
                                        </div>
                                    </div>
                                    <div class="row justify-content-end mt-3">
                                        <div class="col-sm-3 d-flex align-items-center">
                                            <span class="">Due Amount:</span>
                                            <input type="text" class="form-control" id="due_amount" readonly style="width: 100px; margin-left: auto;">
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

<!-- New Supplier Modal -->
<div class="modal fade" id="newSupplierModal" tabindex="-1" aria-labelledby="newSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newSupplierModalLabel">Add New Supplier</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- New Supplier Form -->
                <form id="newSupplierForm">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Code*</label>
                                <input type="number" class="form-control" id="supplier_id_number" name="id_number" placeholder="Enter code" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Name*</label>
                                <input type="text" class="form-control" id="supplier_name" name="name" placeholder="Enter name" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" class="form-control" id="supplier_email" name="email" placeholder="Enter email">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="number" class="form-control" id="supplier_phone" name="phone" placeholder="Enter phone">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter password">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Enter password">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Vat Reg</label>
                                <input type="number" class="form-control" id="vat_reg1" name="vat_reg" placeholder="Enter vat reg">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Contract Date</label>
                                <input type="date" class="form-control" id="contract_date" name="contract_date" placeholder="Enter contract date">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3" placeholder="Enter address"></textarea>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Company</label>
                                <textarea class="form-control" id="company" name="company" rows="3" placeholder="Enter company"></textarea>
                            </div>
                        </div>
                    </div>
                    <!-- Submit Button -->
                    <button type="button" class="btn btn-success" id="saveSupplierBtn">Save Supplier</button>
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
            var totalVatAmount = 0;

            $('#productTable tbody tr').each(function() {
                var quantity = parseFloat($(this).find('input.quantity').val()) || 0;
                var unitPrice = parseFloat($(this).find('input.unit_price').val()) || 0;
                var totalPrice = (quantity * unitPrice).toFixed(2);
                var vatPercent = parseFloat($(this).find('input.vat_percent').val()) || 0;
                var vatAmount = (totalPrice * vatPercent / 100).toFixed(2);
                var totalPriceWithVat = (parseFloat(totalPrice) + parseFloat(vatAmount)).toFixed(2);

                $(this).find('td:eq(7)').text(totalPrice);
                $(this).find('td:eq(8)').text(totalPriceWithVat);
                $(this).find('td:eq(6)').text(vatAmount);

                itemTotalAmount += parseFloat(totalPrice) || 0;
                totalVatAmount += parseFloat(vatAmount) || 0;
            });

            $('#item_total_amount').val(itemTotalAmount.toFixed(2) || '0.00');

            var discount = parseFloat($('#discount').val()) || 0;
            var netAmount = itemTotalAmount + totalVatAmount - discount;
            $('#total_vat_amount').val(totalVatAmount.toFixed(2) || '0.00');
            $('#net_amount').val(netAmount.toFixed(2) || '0.00');

            var paidAmount = parseFloat($('#paid_amount').val()) || 0;
            var dueAmount = isNaN(paidAmount) ? netAmount : netAmount - paidAmount;
            $('#due_amount').val(dueAmount.toFixed(2) || '0.00');
        }

        updateSummary();

        $('#addProductBtn').click(function() {
            var selectedSize = $('#product_size').val() || 'M';
            var selectedColor = $('#product_color').val() || 'Black';
            
            var selectedProduct = $('#product_id option:selected');
            var productId = selectedProduct.val();
            var productName = selectedProduct.data('name');
            var quantity = $('#quantity').val();
            var unitPrice = $('#unit_price').val();
            var vatPercent = 5;

            if (isNaN(quantity) || quantity <= 0) {
                alert('Quantity must be a positive number.');
                return;
            }

            var totalPrice = (quantity * unitPrice).toFixed(2);
            var vatAmount = (totalPrice * vatPercent / 100).toFixed(2);
            var totalPriceWithVat = (parseFloat(totalPrice) + parseFloat(vatAmount)).toFixed(2);

            var productExists = false;
            $('#productTable tbody tr').each(function() {
                var existingProductId = $(this).data('product-id');
                var existingSize = $(this).find('td:eq(2)').text();
                var existingColor = $(this).find('td:eq(3)').text();

                if (productId == existingProductId && selectedSize == existingSize && selectedColor == existingColor) {
                    productExists = true;
                    return false;
                }
            });

            if (productId && quantity && unitPrice) {
                var productRow = `<tr data-id="${productId}">
                                    <td>${productName}</td>
                                    <td><input type="number" class="form-control quantity" value="${quantity}" /></td>
                                    <td>${selectedSize}</td>
                                    <td>${selectedColor}</td>
                                    <td><input type="number" step="0.01" class="form-control unit_price" value="${unitPrice}" /></td>
                                    <td><input type="number" step="0.01" class="form-control vat_percent" value="${vatPercent}" /></td>
                                    <td>${vatAmount}</td>
                                    <td>${totalPrice}</td>
                                    <td>${totalPriceWithVat}</td>
                                    <td><button type="button" class="btn btn-sm btn-danger remove-product">Remove</button></td>
                                </tr>`;
                $('#productTable tbody').append(productRow);
                $('#quantity').val('');
                $('#unit_price').val('');
                $('#product_size').val('');
                $('#product_color').val('');

                updateSummary();
            }
        });

        $(document).on('click', '.remove-product', function() {
            $(this).closest('tr').remove();

            updateSummary();
        });

        $(document).on('input', '#productTable input.quantity, #productTable input.unit_price, #productTable input.vat_percent', function() {
            updateSummary();
        });

        $('#paid_amount').on('input', function() {
            var paidAmount = parseFloat($(this).val()) || 0;
            var netAmount = parseFloat($('#net_amount').val()) || 0;
            var dueAmount = isNaN(paidAmount) ? netAmount : netAmount - paidAmount;
            $('#due_amount').val(dueAmount.toFixed(2) || '0.00');
        });

        $(document).on('input', '#productTable input.quantity, #productTable input.unit_price, #productTable input.vat_percent', function() {
            updateSummary();
        });

        $('#discount').on('input', function() {
            updateSummary();
        });

        $('#addBtn').on('click', function(e) {
            e.preventDefault();
            var formData = {};
            var selectedProducts = [];

            formData.invoice = $('#invoice').val();
            formData.purchase_date = $('#purchase_date').val();
            formData.supplier_id = $('#supplier_id').val();
            formData.vat_reg = $('#vat_reg').val();
            formData.purchase_type = $('#purchase_type').val();
            formData.ref = $('#ref').val();
            formData.remarks = $('#remarks').val();

            formData.total_amount = $('#item_total_amount').val();
            formData.discount = $('#discount').val();
            formData.total_vat_amount = $('#total_vat_amount').val();
            formData.net_amount = $('#net_amount').val();
            formData.paid_amount = $('#paid_amount').val();
            formData.due_amount = $('#due_amount').val();

            $('#productTable tbody tr').each(function() {
                var productId = $(this).data('id');
                var quantity = $(this).find('input.quantity').val();
                var unitPrice = $(this).find('input.unit_price').val();
                var productSize = $(this).find('td:eq(2)').text(); 
                var productColor = $(this).find('td:eq(3)').text();
                var vatPercent = $(this).find('input.vat_percent').val();
                var vatAmount = $(this).find('td:eq(6)').text();
                var totalPrice = $(this).find('td:eq(7)').text();
                var totalPriceWithVat = $(this).find('td:eq(8)').text();

                selectedProducts.push({
                    product_id: productId,
                    quantity: quantity,
                    product_size: productSize,
                    product_color: productColor,
                    unit_price: unitPrice,
                    vat_percent: vatPercent,
                    vat_amount: vatAmount,
                    total_price: totalPrice,
                    total_price_with_vat: totalPriceWithVat
                });
            });

            var finalData = { ...formData, products: selectedProducts };
            // console.log(finalData);

            $.ajax({
                url: '/admin/add-stock',
                method: 'POST',
                data: finalData,
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    swal({
                        text: "Created successfully",
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
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessage = '';
                        $.each(errors, function(key, value) {
                            errorMessage += value[0] + '\n';
                        });

                        swal({
                            title: "Validation Error",
                            text: errorMessage,
                            icon: "error",
                            button: {
                                text: "OK",
                                className: "swal-button--confirm"
                            }
                        });
                    }
                }
            });

        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#product_id').change(function() {
            var selectedOption = $(this).find('option:selected');
            var price = selectedOption.data('price');

            if (price !== undefined) {
                $('#unit_price').val(price);
            } else {
                $('#unit_price').val('');
            }
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
    document.addEventListener('DOMContentLoaded', function() {
        const supplierSelect = document.getElementById('supplier_id');
        const supplierPrevDue = document.getElementById('supplier_balance');

        function updateSupplierBalance() {
            const selectedOption = supplierSelect.options[supplierSelect.selectedIndex];
            const balance = selectedOption.getAttribute('data-balance');
            supplierPrevDue.value = balance ? balance : '0.00';
        }
        updateSupplierBalance();
        supplierSelect.addEventListener('change', updateSupplierBalance);
    });
</script>

<script>
    window.onload = function() {
        document.getElementById("purchase_date").value = new Date().toISOString().split('T')[0];
    };
</script>

<script>

$(document).ready(function() {
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

@endsection