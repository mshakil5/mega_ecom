@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Edit {{ $order->invoice }}</h3>
                    </div>
                    <div class="card-body">
                        <form id="saleForm">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="purchase_date">Selling Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="purchase_date" name="purchase_date" 
                                            value="{{ $order->purchase_date }}">
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
                                            <option value="">Select...</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}" {{ $order->user_id == $customer->id ? 'selected' : '' }}>
                                                    {{ $customer->name }}
                                                </option>
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
                                            <option value="{{$warehouse->id}}" {{ $order->warehouse_id == $warehouse->id ? 'selected' : '' }}>
                                                {{$warehouse->name}}-{{$warehouse->location}}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-2 d-none">
                                    <div class="form-group">
                                        <label for="purchase_type">Transaction Type <span class="text-danger">*</span></label>
                                        <select class="form-control" id="payment_method" name="payment_method">
                                            <option value="Credit" {{ $order->payment_method == 'Credit' ? 'selected' : '' }}>Credit</option>
                                            <option value="Cash" {{ $order->payment_method == 'Cash' ? 'selected' : '' }}>Cash</option>
                                            <option value="Bank" {{ $order->payment_method == 'Bank' ? 'selected' : '' }}>Bank</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="ref">Ref</label>
                                        <input type="text" class="form-control" id="ref" name="ref" 
                                            value="{{ $order->ref }}" placeholder="Enter reference">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="remarks">Remarks</label>
                                        <textarea class="form-control" id="remarks" name="remarks" rows="1" 
                                            placeholder="Enter remarks">{{ $order->remarks }}</textarea>
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
                                        <label for="type_id">Estimated Selling Price<span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="estimated_selling_price" name="estimated_selling_price" 
                                            placeholder="Enter estimated selling price" min="0" value="{{ $order->subtotal_amount }}">
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
                                            @foreach($order->orderDetails as $detail)
                                            <tr data-product-id="{{ $detail->product_id }}" data-type-id="{{ $detail->type_id }}">
                                                <td>
                                                    {{ $detail->product->product_code }} - {{ $detail->product->name }} <br>
                                                    <span>Margin: <strong>0%</strong></span> <br>
                                                    <span>Ground Price: <strong>0.00</strong></span> <br>
                                                    <span>Min Price: <strong>0.00</strong> (<strong>0%</strong>)</span>
                                                    <input type="hidden" name="product_id[]" value="{{ $detail->product_id }}">
                                                    <input type="hidden" name="product_name[]" value="{{ $detail->product->name }}">
                                                </td>
                                                <td>
                                                    <input type="hidden" name="quantity[]" class="quantity" value="{{ $detail->quantity }}">
                                                    <input type="number" class="form-control quantity_display" value="{{ $detail->quantity }}" disabled />
                                                </td>
                                                <td>{{ $detail->size }}</td>
                                                <td>{{ $detail->color }}</td>
                                                <td>
                                                    @if($detail->type_id)
                                                        {{ $detail->type->name ?? '' }}
                                                        <input type="hidden" name="product_type_id[]" value="{{ $detail->type_id }}">
                                                    @else
                                                        <input type="hidden" name="product_type_id[]" value="">
                                                    @endif
                                                </td>
                                                <td><input type="number" step="0.01" class="form-control price_per_unit" value="{{ $detail->price_per_unit }}" /></td>
                                                <td><input type="number" step="0.01" class="form-control vat_percent" value="{{ $detail->vat_percent }}" /></td>
                                                <td>{{ $detail->total_vat }}</td>
                                                <td class="cell-total-price">{{ $detail->total_price }}</td>
                                                <td class="cell-total-price-vat">{{ $detail->total_price_with_vat }}</td>
                                                <td><button type="button" class="btn btn-sm btn-danger remove-product">Remove</button></td>
                                            </tr>
                                            @endforeach
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
                                                    <input type="text" class="form-control" id="item_total_amount" readonly 
                                                        value="{{ $order->subtotal_amount }}">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-6 d-flex align-items-center justify-content-end">
                                                    <span>Vat Amount:</span>
                                                </div>
                                                <div class="col-sm-6">
                                                    <input type="number" step="0.01" class="form-control" id="vat" name="vat" readonly
                                                        value="{{ $order->vat_amount }}">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-6 d-flex align-items-center justify-content-end">
                                                    <span>Discount Amount:</span>
                                                </div>
                                                <div class="col-sm-6">
                                                    <input type="number" step="0.01" class="form-control" id="discount" name="discount"
                                                        value="{{ $order->discount_amount }}">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-6 d-flex align-items-center justify-content-end">
                                                    <span>Net Amount:</span>
                                                </div>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="net_amount" readonly
                                                        value="{{ $order->net_amount }}">
                                                </div>
                                            </div>
                                            
                                            @php
                                                $cashPayment = $order->transactions->where('payment_type', 'Cash')->where('transaction_type', 'Received')->first();
                                                $bankPayment = $order->transactions->where('payment_type', 'Bank')->where('transaction_type', 'Received')->first();
                                            @endphp
                                            
                                            <div class="row mb-3">
                                                <div class="col-sm-6 d-flex align-items-center justify-content-end">
                                                    <span>Cash Payment:</span>
                                                </div>
                                                <div class="col-sm-6">
                                                    <input type="number" class="form-control" id="cash_payment" name="cash_payment"
                                                        value="{{ $cashPayment->amount ?? 0 }}">
                                                    <span class="errmsg text-danger"></span>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-6 d-flex align-items-center justify-content-end">
                                                    <span>Bank Payment:</span>
                                                </div>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="bank_payment" name="bank_payment"
                                                        value="{{ $bankPayment->amount ?? 0 }}">
                                                    <span class="errmsg text-danger"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button id="updateBtn" class="btn btn-success" value="Update"><i class="fas fa-save"></i> Update Order</button>
                                <a href="{{ route('whole-sale.list') }}" class="btn btn-secondary">Cancel</a>
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
        // Initialize with existing data
        updateSummary();

        $(document).on('change', '#product_id', function () {
            var productId = $('#product_id').val();
            var warehouseId = $('#warehouse_id').val();

        if (!warehouseId) {
            swal({
                text: "Please select a warehouse first.",
                icon: "warning",
                button: {
                    text: "OK",
                    className: "swal-button--confirm"
                }
            });
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

                        if (exists) {
                            swal({
                                text: "This product already exists in the table.",
                                icon: "warning",
                                button: {
                                    text: "OK",
                                    className: "swal-button--confirm"
                                }
                            })
                            return;
                        }

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
                } else {
                    $('.errmsg').text('');
                }
            } else {
                $('.errmsg').text('Please enter valid numbers.');
            }
        }

        $('#updateBtn').on('click', function(e) {
            e.preventDefault();

            $('#loader').show();

            let formData = {
                _token: $('input[name=_token]').val(),
                _method: 'PUT',
                purchase_date: $('#purchase_date').val(),
                user_id: $('#user_id').val(),
                warehouse_id: $('#warehouse_id').val(),
                payment_method: $('#payment_method').val(),
                ref: $('#ref').val(),
                remarks: $('#remarks').val(),
                discount: parseFloat($('#discount').val()) || 0,
                vat: parseFloat($('#vat').val()) || 0,
                cash_payment: parseFloat($('#cash_payment').val()) || 0,
                bank_payment: parseFloat($('#bank_payment').val()) || 0,
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
                swal ({
                    title: "Error!",
                    text: "Please add at least one product.", 
                    type: "error"
                })
                $('#loader').hide();
                return;
            }

            $.ajax({
                url: '/admin/whole-sale/{{ $order->id }}',
                method: 'POST',
                data: { ...formData, products: JSON.stringify(formData.products) },
                success: function(response) {
                    $('#loader').hide();
                    swal({
                        title: "Success!",
                        text: "Whole Sale Updated Successfully", 
                        type: "success"
                    });
                    // if(response.pdf_url){
                    //     window.open(response.pdf_url, '_blank');
                    // }
                    window.location.href = "{{ route('whole-sale.list') }}";
                },
                error: function(xhr) {
                    $('#loader').hide();
                    let err = xhr.responseJSON?.message || 'Something went wrong';
                    swal({
                        title: "Error!",
                        text: err, 
                        type: "error"
                    });
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
                }
            });
        });
    });
</script>

@endsection