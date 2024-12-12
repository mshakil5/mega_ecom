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
                                <input type="hidden" id="orderId" value="{{ $order->id }}">
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="purchase_date">Selling Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="purchase_date" name="purchase_date" placeholder="Enter date" value="{{ $order->purchase_date }}">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="supplier_id">Select Wholesaler 
                                        @if($order->user_id)
                                            <span class="text-danger">*</span>
                                        @endif
                                        </label>
                                        <select class="form-control" id="user_id" name="user_id">
                                            <option value="" >Select...</option>
                                            @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ $customer->id == $order->user_id ? 'selected' : '' }}>{{ $customer->name }}</option>
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
                                        <label for="warehouse_id">Warehouse <span class="text-danger">*</span></label>
                                        <select name="warehouse_id" id="warehouse_id" class="form-control">
                                            <option value="">Select</option>
                                            @foreach ($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" {{ $warehouse->id == $order->warehouse_id ? 'selected' : '' }}>{{ $warehouse->name }}-{{ $warehouse->location }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-2 d-none">
                                    <div class="form-group">
                                        <label for="purchase_type">Transaction Type <span class="text-danger">*</span></label>
                                        <select class="form-control" id="payment_method" name="payment_method">
                                            <option value="Credit" selected>Credit</option>
                                            <option value="Cash">Cash</option>
                                            <option value="Bank">Bank</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="ref">Ref</label>
                                        <input type="text" class="form-control" id="ref" name="ref" placeholder="Enter reference" value="{{ $order->ref }}">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="remarks">Remarks</label>
                                        <textarea class="form-control" id="remarks" name="remarks" rows="1" placeholder="Enter remarks"> {{ $order->remarks }}</textarea>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="product_id">Choose Product <span class="text-danger">*</span></label>
                                        <select class="form-control" id="product_id" name="product_id">
                                            <option value="">Select...</option>
                                            @foreach($products as $product)

                                            @php
                                                $latestStock = $product->stock()
                                                    ->where('quantity', '>', 0)
                                                    ->latest()
                                                    ->first();

                                                $sellingPrice = $latestStock->selling_price ?? 0;
                                                $groundPrice = $latestStock->ground_price_per_unit ?? 0;
                                                $profitMargin = $latestStock->profit_margin ?? 0;
                                            @endphp 

                                            <option value="{{ $product->id }}" 
                                                    data-name="{{ $product->name }}" 
                                                    data-price="{{ $sellingPrice }}" 
                                                    data-ground-price="{{ $groundPrice }}" 
                                                    data-profit-margin="{{ $profitMargin }}"
                                                    >
                                                    {{ $product->name }} - {{ $product->product_code }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="quantity">Quantity <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control quantity" id="quantity" name="quantity" placeholder="Enter quantity" min="1">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="price_per_unit">Unit Price <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control" id="price_per_unit" name="price_per_unit" placeholder="Enter unit price">
                                        <input type="hidden" step="0.01" class="form-control" id="ground_price">
                                        <input type="hidden" step="0.01" class="form-control" id="profit_margin">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="size">Size <span class="text-danger">*</span></label>
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
                                        <label for="color">Color <span class="text-danger">*</span></label>
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
                                                <th>Product Details</th>
                                                <th>Quantity</th>
                                                <th>Size</th>
                                                <th>Color</th>
                                                <th>Selling Price</th>
                                                <th>VAT %</th>
                                                <th>VAT Amount</th>
                                                <th>Total Price</th>
                                                <th>Total Price with VAT</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($order->orderDetails as $detail)
                                            <tr data-product-id="{{ $detail->product_id }}">
                                                <td>{{ $detail->product->name }}
                                                    <input type="hidden" name="product_id[]" value="{{ $detail->product_id }}">
                                                </td>
                                                <td><input type="number" class="form-control quantity" value="{{ $detail->quantity }}" min="1" name="" /></td>
                                                <td>{{ $detail->size }}</td>
                                                <td>{{ $detail->color }}</td>
                                                <td><input type="number" step="0.01" class="form-control price_per_unit" value="{{ $detail->price_per_unit }}" /></td>
                                                <td><input type="number" step="0.01" class="form-control vat_percent" name="" value="{{ $detail->vat_percent }}" /></td>
                                                <td>{{ $detail->total_vat }}</td>
                                                <td>{{ $detail->total_price }}</td>
                                                <td>{{ $detail->total_price_with_vat }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-danger remove-product">Remove</button>
                                                </td>
                                            </tr>
                                        @endforeach

                                        </tbody>
                                    </table>
                                </div>

                                <div class="container mt-4 mb-5">
                                    <div class="row">
                                        <!-- Left side -->
                                        <div class="col-md-6 d-none">
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
                                        <div class="col-md-6 offset-md-6">
                                            <div class="row mb-3">
                                                <div class="col-sm-6 d-flex align-items-center justify-content-end">
                                                    <span>Item Total Amount:</span>
                                                </div>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="item_total_amount" readonly value="{{ $order->subtotal_amount }}">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-6 d-flex align-items-center justify-content-end">
                                                    <span>Vat Amount:</span>
                                                </div>
                                                <div class="col-sm-6">
                                                    <input type="number" step="0.01" class="form-control" id="vat" name="vat" value="{{ $order->vat_amount }}" disabled>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-6 d-flex align-items-center justify-content-end">
                                                    <span>Discount Amount:</span>
                                                </div>
                                                <div class="col-sm-6">
                                                    <input type="number" step="0.01" class="form-control" id="discount" name="discount"
                                                    value="{{ $discountAmount ? $discountAmount->discount : '' }}">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-6 d-flex align-items-center justify-content-end">
                                                    <span>Net Amount:</span>
                                                </div>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="net_amount" readonly value="{{ $order->net_amount }}">
                                                </div>
                                            </div>
                                            
                                            <div class="row mb-3">
                                                <div class="col-sm-6 d-flex align-items-center justify-content-end">
                                                    <span>Cash Payment:</span>
                                                </div>
                                                <div class="col-sm-6">
                                                    <input type="number" class="form-control" id="cash_payment" name="cash_payment" value="{{ $cashAmount ? $cashAmount->at_amount : '' }}">
                                                    <span class="errmsg text-danger"></span>
                                                </div>
                                            </div>

                                            
                                            <div class="row">
                                                <div class="col-sm-6 d-flex align-items-center justify-content-end">
                                                    <span>Bank Payment:</span>
                                                </div>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="bank_payment" name="bank_payment" value="{{ $bankAmount ? $bankAmount->at_amount : '' }}">
                                                    <span class="errmsg text-danger"></span>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer">
                                <button id="addBtn" class="btn btn-success" value="Create"><i class="fas fa-cart-plus"></i> @if ($order->order_type == 2)
                                    Make Order
                                    @else
                                        Update Order  
                                    @endif
                                </button>  
                                <button id="quotationBtn" class="btn btn-secondary d-none" value="Create"><i class="fas fa-file-invoice"></i> Make Quotation</button>  
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

@include('admin.inc.modal.whole_saler_modal')
@include('admin.inc.modal.size_modal')
@include('admin.inc.modal.color_modal')

@endsection

@section('script')

<script>
    $(document).ready(function() {
        function updateSummary() {
            var itemTotalAmount = 0;
            var totalVatAmount = 0;

            $('#productTable tbody tr').each(function() {
                var quantity = parseFloat($(this).find('input.quantity').val()) || 0;
                var unitPrice = parseFloat($(this).find('input.price_per_unit').val()) || 0;
                var vatPercent = parseFloat($(this).find('input.vat_percent').val()) || 0;

                var totalPrice = (quantity * unitPrice).toFixed(2);
                var vatAmount = (totalPrice * vatPercent / 100).toFixed(2);
                var totalPriceWithVat = (parseFloat(totalPrice) + parseFloat(vatAmount)).toFixed(2);

                $(this).find('td:eq(6)').text(vatAmount);
                $(this).find('td:eq(7)').text(totalPrice);
                $(this).find('td:eq(8)').text(totalPriceWithVat);

                itemTotalAmount += parseFloat(totalPrice) || 0;
                totalVatAmount += parseFloat(vatAmount) || 0;
            });

            $('#item_total_amount').val(itemTotalAmount.toFixed(2) || '0.00');
            $('#vat').val(totalVatAmount.toFixed(2) || '0.00');

            var discount = parseFloat($('#discount').val()) || 0; 
            var vat = parseFloat($('#vat').val()) || 0;
            var netAmount = itemTotalAmount - discount + vat;
            $('#net_amount').val(netAmount.toFixed(2) || '0.00');
        }

        $('#addProductBtn').click(function() {

            var warehouseId = $('#warehouse_id').val();
            if (!warehouseId) {
                swal({
                    text: 'Please select warehouse',
                    icon: "error",
                    button: {
                        text: "OK",
                        className: "swal-button--error"
                    }
                });
                return;
            }

            var selectedProduct = $('#product_id option:selected');
            var productId = selectedProduct.val();
            var productName = selectedProduct.data('name');
            var unitPrice = parseFloat($('#price_per_unit').val()) || 0;
            var groundPrice = parseFloat($('#ground_price').val()) || 0;
            var profitMargin = parseFloat($('#profit_margin').val()) || 0;
            var quantity = parseFloat($('#quantity').val()) || 1;
            var selectedSize = $('#size').val();
            var selectedColor = $('#color').val();
            var vatPercent = 0;

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

            if (!productId || !quantity || !unitPrice || !selectedSize || !selectedColor) {
                alert('Please fill in all required fields: product, quantity, unit price, size, and color.');
                return;
            }

            if (productId && quantity && unitPrice && selectedSize && selectedColor) {
                $.ajax({
                    type: 'POST',
                    url: '/admin/check-product-stock',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        warehouse_id: warehouseId,
                        product_id: productId,
                        size: selectedSize,
                        color: selectedColor,
                    },
                    success: function(response) {

                        if (!response.in_stock) {
                            swal({
                                text: "Sorry, this product is out of stock.",
                                icon: "error",
                                button: {
                                    text: "OK",
                                    className: "swal-button--error"
                                }
                            });

                            return;
                        }

                        if(quantity > response.stock_quantity){
                            quantity = response.stock_quantity;
                        }

                        var productExists = false;

                        $('#productTable tbody tr').each(function() {
                            var existingProductId = $(this).data('product-id');
                            var existingSize = $(this).find('td:eq(2)').text().trim();
                            var existingColor = $(this).find('td:eq(3)').text().trim();

                            if (productId == existingProductId && selectedSize == existingSize && selectedColor == existingColor) {
                                productExists = true;
                                return false;
                            }
                        });
                        if (productExists) {
                            swal({
                                text: "This product with the same size and color is already added.",
                                icon: "warning",
                                button: {
                                    text: "OK",
                                    className: "swal-button--warning"
                                }
                            });
                            return;
                        }

                        var productRow = `<tr data-product-id="${productId}">
                            <td>
                                ${productName} <br>
                                <span>Ground Price: <strong>${groundPrice.toFixed(2)}</strong></span> <br>
                                <span>Profit Margin: <strong>${profitMargin.toFixed(2)}%</strong></span>
                                <input type="hidden" name="product_id[]" value="${productId}">
                            </td> 
                            <td>
                                <input type="number" class="form-control quantity" 
                                    value="${quantity}" 
                                    min="1" 
                                    max="${response.stock_quantity}" 
                                    data-max="${response.stock_quantity}" />
                            </td>
                            <td>${selectedSize}</td>
                            <td>${selectedColor}</td>
                            <td><input type="number" step="0.01" class="form-control price_per_unit" value="${unitPrice.toFixed(2)}" /></td>
                            <td><input type="number" step="0.01" class="form-control vat_percent" value="${vatPercent}" /></td>
                            <td>${vatAmount}</td>
                            <td>${totalPrice}</td>
                            <td>${totalPriceWithVat}</td>
                            <td><button type="button" class="btn btn-sm btn-danger remove-product">Remove</button></td>
                        </tr>`;

                        $('#productTable tbody').append(productRow);

                        $('.quantity').on('input', function () {
                            var maxQuantity = $(this).data('max');
                            if (parseInt(this.value) > maxQuantity) {
                                this.value = maxQuantity;
                            }
                            if (this.value < 1) {
                                this.value = 1;
                            }
                        });

                        $('#quantity').val('');
                        $('#price_per_unit').val('');
                        updateSummary();
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
                    },
                });
            }
        });

        $(document).on('click', '.remove-product', function() {
            $(this).closest('tr').remove();
            updateSummary();
            $('#product_id').val(null).trigger('change');
        });

        $(document).on('input', '.quantity', function() {
            if ($(this).val() < 1) {
                $(this).val(1);
            }
        });

        $(document).on('input', '#productTable input.quantity, #productTable input.price_per_unit, #productTable input.vat_percent', function() {
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
            $('#quotationBtn').attr('disabled', true);
            $('#loader').show();

            var formData = $(this).serializeArray();
            var products = [];
            
            formData.push({ name: 'purchase_date', value: $('#purchase_date').val() });
            formData.push({ name: 'user_id', value: $('#user_id').val() });
            formData.push({ name: 'warehouse_id', value: $('#warehouse_id').val() });
            formData.push({ name: 'payment_method', value: $('#payment_method').val() });
            formData.push({ name: 'ref', value: $('#ref').val() });
            formData.push({ name: 'remarks', value: $('#remarks').val() });
            formData.push({ name: 'item_total_amount', value: $('#item_total_amount').val() });
            formData.push({ name: 'vat', value: $('#vat').val() });
            formData.push({ name: 'discount', value: $('#discount').val() });
            formData.push({ name: 'net_amount', value: $('#net_amount').val() });
            formData.push({ name: 'cash_payment', value: $('#cash_payment').val() });
            formData.push({ name: 'bank_payment', value: $('#bank_payment').val() });
            formData.push({ name: 'id', value: $('#orderId').val() });

            $('#productTable tbody tr').each(function() {
                var productId = $(this).find('input[name="product_id[]"]').val();
                var quantity = parseFloat($(this).find('input.quantity').val()) || 0;
                var unitPrice = parseFloat($(this).find('input.price_per_unit').val()) || 0;
                var vatPercent = parseFloat($(this).find('input.vat_percent').val()) || 0;
                var vatAmount = parseFloat($(this).find('td:nth-child(7)').text()) || 0;
                var total_price_with_vat = parseFloat($(this).find('td:nth-child(9)').text()) || 0;
                var productSize = $(this).find('td:eq(2)').text();
                var productColor = $(this).find('td:eq(3)').text();
                var totalPrice = (quantity * unitPrice).toFixed(2);

                products.push({
                    product_id: productId,
                    quantity: quantity,
                    unit_price: unitPrice,
                    product_size: productSize,
                    product_color: productColor,
                    total_price: totalPrice,
                    vat_percent: vatPercent,
                    total_vat: vatAmount,
                    total_price_with_vat: total_price_with_vat
                });
            });

            formData = formData.filter(function(item) {
                return item.name !== 'product_id' && item.name !== 'quantity' && item.name !== 'price_per_unit' && item.name !== 'size' && item.name !== 'color';
            });

            formData.push({ name: 'products', value: JSON.stringify(products) });

            // console.log(formData);

            $.ajax({
                url: '/admin/order-update',
                method: 'POST',
                data: formData,
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    swal({
                        text: "Success",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    }).then((value) => {
                        window.location.href = "{{ route('getinhouseorder') }}";
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
                    });
                    // console.log(xhr.responseText);
                },
                complete: function() {
                    $('#loader').hide();
                    $('#addBtn').attr('disabled', false);
                    $('#quotationBtn').attr('disabled', false);
                }
            });
        });

        $('#quotationBtn').on('click', function(e) {
            e.preventDefault();

            $(this).attr('disabled', true);
            $('#addBtn').attr('disabled', true);
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
                var quantity = parseFloat($(this).find('input.quantity').val()) || 0;
                var unitPrice = parseFloat($(this).find('input.price_per_unit').val()) || 0;
                var productSize = $(this).find('td:eq(2)').text();
                var productColor = $(this).find('td:eq(3)').text();
                var totalPrice = (quantity * unitPrice).toFixed(2);

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
                        window.location.href = "{{ route('allquotations') }}";
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
                    // console.log(xhr.responseText);
                },
                complete: function() {
                    $('#loader').hide();
                    $('#quotationBtn').attr('disabled', false);
                    $('#addBtn').attr('disabled', false);
                }
            });
        });


        $('#cash_payment').on('keyup', function() {
            paymentCheck($(this).val(), 'Cash Payment');
        });

        $('#bank_payment').on('keyup', function() {
            paymentCheck($(this).val(), 'Bank Payment');
        });

        function paymentCheck(payment, paymentType) {
            var netAmount = parseFloat($('#net_amount').val());
            var paymentValue = parseFloat(payment);

            if (!isNaN(netAmount) && !isNaN(paymentValue)) {
                if (paymentValue > netAmount) {
                    $('.errmsg').text(paymentType + ' is greater than Net Amount');
                    $('#cash_payment').val('0.00');
                    $('#bank_payment').val('0.00');
                }
            } else {
                $('.errmsg').text('Please enter valid numbers.');
            }
        }


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
            var groundPrice = selectedProduct.data('ground-price');
            var profitMargin = selectedProduct.data('profit-margin');
            $('#quantity').val(1);
            
            if(pricePerUnit) {
                $('#price_per_unit').val(pricePerUnit);
                $('#ground_price').val(groundPrice);
                $('#profit_margin').val(profitMargin);
            } else {
                $('#price_per_unit').val('');
                $('#ground_price').val('');
                $('#profit_margin').val('');
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
                success: function(response) {
                    // console.log(response);
                    $('#user_id').append(`<option value="${response.id}" selected>${$('#name').val()} ${$('#surname').val() || ''}</option>`);
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