@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Whole Sale</h3>
                    </div>
                    <div class="card-body">
                        <form id="saleForm">
                            @csrf
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="purchase_date">Selling Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="purchase_date" name="purchase_date" placeholder="Enter date" value="{{ now()->format('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="supplier_id">Wholesaler <span class="text-danger">*</span>  
                                          <span class="badge badge-success float-right" style="cursor:pointer;" data-toggle="modal" data-target="#newWholeSalerModal">
                                              + Add New
                                          </span>
                                        </label>
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
                                        <label for="warehouse_id">Warehouse <span class="text-danger">*</span></label>
                                        <select name="warehouse_id" id="warehouse_id" class="form-control">
                                            <option value="">Select</option>
                                            @foreach ($warehouses as $warehouse)
                                            <option value="{{$warehouse->id}}">{{$warehouse->name}}-{{$warehouse->location}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-sm-2">
                                    <label for="">Invoice<span style="color: red;">*</span></label>
                                    <select class="form-control" id="invoice" name="invoice">
                                        <option value="">Select Season, system will create code based on Season</option>
                                        <option value="All">All Season</option>
                                        <option value="Spring">Spring</option>
                                        <option value="Summer">Summer</option>
                                        <option value="Autumn">Autumn</option>
                                        <option value="Winter">Winter</option>
                                    </select>
                                    <small class="text-muted">Example: <span>STL-Season-Year-XXXXX</span></small>
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
                                        <input type="text" class="form-control" id="ref" name="ref" placeholder="Enter reference">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="remarks">Remarks</label>
                                        <textarea class="form-control" id="remarks" name="remarks" rows="1" placeholder="Enter remarks"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-6">
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
                                                $considerableMargin = $latestStock->considerable_margin ?? 0;
                                                $considerablePrice = $latestStock->considerable_price ?? 0;
                                            @endphp        
                                                <option value="{{ $product->id }}" 
                                                    data-name="{{ $product->name }}" 
                                                    data-code="{{ $product->product_code }}" 
                                                    data-price="{{ $sellingPrice }}" 
                                                    data-ground-price="{{ $groundPrice }}" 
                                                    data-profit-margin="{{ $profitMargin }}"
                                                    data-considerable-margin="{{ $considerableMargin }}"
                                                    data-considerable-price="{{ $considerablePrice }}"
                                                    data-sizes="{{ json_encode($product->stockhistory->pluck('size')->unique()->values()) }}" 
                                                    data-colors="{{ json_encode($product->stockhistory->pluck('color')->unique()->values()) }}"
                                                    data-types='@json($product->types->map(fn($t) => ['id' => $t->id, 'name' => $t->name]))'
                                                    >
                                                    {{ $product->product_code }} - {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                        <label for="type_id">Estimated  Selling Price<span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="estimated_selling_price" name="estimated_selling_price" placeholder="Enter estimated selling price" value="0" min="0">
                                </div>
                                <div class="col-sm-12 mt-1">
                                    <h5>Product List:</h5>
                                    <table class="table table-bordered" id="productTable">
                                        <thead>
                                            <tr>
                                                <th>Product Details</th>
                                                <th>Quantity</th>
                                                <th>Size</th>
                                                <th>Color</th>
                                                <th>Type</th>
                                                <th>Selling Price</th>
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
                                <div class="container mt-4 mb-5">
                                    <div class="row">
                                        <div class="col-md-6 offset-md-6">
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
                                                    <input type="number" step="0.01" class="form-control" id="vat" name="vat" readonly>
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
                                            <div class="row mb-3">
                                                <div class="col-sm-6 d-flex align-items-center justify-content-end">
                                                    <span>Net Amount:</span>
                                                </div>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="net_amount" readonly>
                                                </div>
                                            </div>
                                            
                                            <div class="row mb-3">
                                                <div class="col-sm-6 d-flex align-items-center justify-content-end">
                                                    <span>Cash Payment:</span>
                                                </div>
                                                <div class="col-sm-6">
                                                    <input type="number" class="form-control" id="cash_payment" name="cash_payment">
                                                    <span class="errmsg text-danger"></span>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-6 d-flex align-items-center justify-content-end">
                                                    <span>Bank Payment:</span>
                                                </div>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="bank_payment" name="bank_payment">
                                                    <span class="errmsg text-danger"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button id="addBtn" class="btn btn-success" value="Create"><i class="fas fa-cart-plus"></i> Make Order</button>
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

@endsection

@section('script')

<script>
    $(document).ready(function() {

        $(document).on('change', '#product_id', function () {
            var productId = $('#product_id').val();
            var warehouseId = $('#warehouse_id').val();

            if (!warehouseId) {
                alert('Please select a warehouse first.');
                return;
            }

            if (!productId) return;

            $.ajax({
                url: '/admin/get-product-rows',
                type: 'POST',
                data: {
                    product_id: productId,
                    warehouse_id: warehouseId,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    if (!response.rows || !response.rows.length) {
                        return;
                    }

                    response.rows.forEach(function (item) {
                        var exists = false;
                        $('#productTable tbody tr').each(function () {
                            var existingProductId = String($(this).data('product-id'));
                            var existingSize = $(this).find('td:eq(2)').text().trim();
                            var existingColor = $(this).find('td:eq(3)').text().trim();
                            var existingTypeId = String($(this).data('type-id') || '');

                            if (
                                String(item.product_id) === existingProductId &&
                                String(item.size) === existingSize &&
                                String(item.color) === existingColor &&
                                String(item.type_id) === existingTypeId
                            ) {
                                exists = true;
                                return false;
                            }
                        });

                        if (exists) return;

                        var unitPrice = parseFloat(item.selling_price) || 0;
                        var groundPrice = parseFloat(item.ground_price) || 0;
                        var profitMargin = parseFloat(item.profit_margin) || 0;
                        var considerableMargin = parseFloat(item.considerable_margin) || 0;
                        var considerablePrice = parseFloat(item.considerable_price) || 0;

                        var qtyFromDb = parseInt(item.max_quantity, 10) || 1;
                        var totalPrice = (qtyFromDb * unitPrice).toFixed(2);
                        var vatPercent = 0;
                        var vatAmount = (totalPrice * vatPercent / 100).toFixed(2);
                        var totalPriceWithVat = (parseFloat(totalPrice) + parseFloat(vatAmount)).toFixed(2);

                        var typeCell = item.type_id
                            ? `${item.type_name}<input type="hidden" name="product_type_id[]" value="${item.type_id}">`
                            : `<input type="hidden" name="product_type_id[]" value="">`;

                        var productRow = `<tr data-product-id="${item.product_id}" data-type-id="${item.type_id}">
                            <td>
                                ${item.product_code} - ${item.product_name} <br>
                                <span>Margin: <strong>${Math.round(profitMargin)}%</strong></span> <br>
                                <span>Ground Price: <strong>${groundPrice.toFixed(2)}</strong></span> <br>
                                <span>Min Price: <strong>${considerablePrice.toFixed(2)}</strong> (<strong>${Math.round(considerableMargin)}%</strong>)</span>
                                <input type="hidden" name="product_id[]" value="${item.product_id}">
                                <input type="hidden" name="product_name[]" value="${item.product_name}">
                            </td>

                            <td>
                                <input type="hidden" name="quantity[]" class="quantity" value="${qtyFromDb}">
                                <input type="number" class="form-control quantity_display" value="${qtyFromDb}" disabled />
                            </td>

                            <td>${item.size}</td>
                            <td>${item.color}</td>
                            <td>${typeCell}</td>
                            <td><input type="number" step="0.01" class="form-control price_per_unit" value="${unitPrice.toFixed(2)}" /></td>
                            <td><input type="number" step="0.01" class="form-control vat_percent" value="${vatPercent}" /></td>
                            <td>${vatAmount}</td>
                            <td class="cell-total-price">${totalPrice}</td>
                            <td class="cell-total-price-vat">${totalPriceWithVat}</td>
                            <td><button type="button" class="btn btn-sm btn-danger remove-product">Remove</button></td>
                        </tr>`;

                        $('#productTable tbody').append(productRow);
                    });
                    recalcSellingPrices();
                    updateSummary();
                },
                error: function (xhr) {
                    console.log(xhr.responseText);
                }
            });
        });

        $(document).on('input', '#estimated_selling_price', function () {
            recalcSellingPrices();
        });

        function recalcSellingPrices() {
            let estimated = parseFloat($('#estimated_selling_price').val()) || 0;

            let totalQty = 0;
            $('#productTable tbody tr').each(function () {
                let qty = parseFloat($(this).find('input.quantity').val()) || 0;
                totalQty += qty;
            });

            if (totalQty <= 0) return;

            let perUnit = estimated / totalQty;

            $('#productTable tbody tr').each(function () {
                let $row = $(this);
                let qty = parseFloat($row.find('input.quantity').val()) || 0;
                let vatPercent = parseFloat($row.find('.vat_percent').val()) || 0;

                let totalPrice = qty * perUnit;
                let vatAmount = totalPrice * vatPercent / 100;
                let totalWithVat = totalPrice + vatAmount;

                $row.find('.price_per_unit').val(perUnit.toFixed(2)).prop('readonly', true);
                $row.find('td:eq(7)').text(vatAmount.toFixed(2));
                $row.find('.cell-total-price').text(totalPrice.toFixed(2));
                $row.find('.cell-total-price-vat').text(totalWithVat.toFixed(2));
            });

            updateSummary();
        }

        $(document).on('click', '.remove-product', function() {
            $(this).closest('tr').remove();
            updateSummary();
            recalcSellingPrices();
            $('#product_id').val(null).trigger('change');
        });

        $(document).on('input', '#productTable input.quantity, #productTable input.price_per_unit, #productTable input.vat_percent, #discount', function() {
            updateSummary();
            recalcSellingPrices();
        });

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

                $(this).find('td:eq(7)').text(vatAmount);
                $(this).find('td:eq(8)').text(totalPrice);
                $(this).find('td:eq(9)').text(totalPriceWithVat);

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

        $('#addBtn').on('click', function(e) {
            e.preventDefault();

            $('#loader').show();

            let formData = {
                _token: $('input[name=_token]').val(),
                purchase_date: $('#purchase_date').val(),
                user_id: $('#user_id').val(),
                warehouse_id: $('#warehouse_id').val(),
                invoice: $('#invoice').val(),
                payment_method: $('#payment_method').val(),
                ref: $('#ref').val(),
                remarks: $('#remarks').val(),
                discount: parseFloat($('#discount').val()) || 0,
                vat: parseFloat($('#vat').val()) || 0,
                cash_payment: parseFloat($('#cash_payment').val()) || 0,
                bank_payment: parseFloat($('#bank_payment').val()) || 0,
                order_type: 3,
                products: []
            };

            $('#productTable tbody tr').each(function() {
                let $row = $(this);
                let product = {
                    product_id: $row.data('product-id'),
                    product_name: $row.find('input[name="product_name[]"]').val(),
                    quantity: parseFloat($row.find('input.quantity').val()) || 0,
                    product_size: $row.find('td:eq(2)').text().trim(),
                    product_color: $row.find('td:eq(3)').text().trim(),
                    type_id: $row.data('type-id') || null,
                    unit_price: parseFloat($row.find('input.price_per_unit').val()) || 0,
                    vat_percent: parseFloat($row.find('input.vat_percent').val()) || 0,
                    total_price: parseFloat($row.find('.cell-total-price').text()) || 0,
                    total_vat: parseFloat($row.find('td:eq(7)').text()) || 0,
                    total_price_with_vat: parseFloat($row.find('.cell-total-price-vat').text()) || 0,
                };
                formData.products.push(product);
            });

            if (formData.products.length === 0) {
                alert('Please add at least one product');
                $('#loader').hide();
                return;
            }

            $.ajax({
                url: '/admin/whole-sale',
                method: 'POST',
                data: { ...formData, products: JSON.stringify(formData.products) },
                success: function(response) {
                    $('#loader').hide();
                    alert(response.message);
                    // if(response.pdf_url){
                    //     window.open(response.pdf_url, '_blank');
                    // }
                    $('#saleForm')[0].reset();
                    $('#productTable tbody').empty();
                },
                error: function(xhr) {
                    $('#loader').hide();
                    let err = xhr.responseJSON?.message || 'Something went wrong';
                    alert(err);
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
    });
</script>

@endsection