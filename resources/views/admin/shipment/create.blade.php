@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3">
    <div class="ermsg"></div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-12">
                <a href="{{ route('admin.shipping') }}" class="btn btn-secondary mb-3">Back</a>
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Create Shipment</h3>
                    </div>
                    <div class="card-body">
                        <form id="editShipmentForm">
                            <input type="hidden" id="shipping_id" value="{{ $shipping->id }}">

                            <div class="mb-4 d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Shipping ID:</strong> <span id="shippingId">{{ $shipping->shipping_id }}</span><br>
                                    <strong>Shipping Date:</strong> <span id="shippingDate">{{ \Carbon\Carbon::parse($shipping->shipping_date)->format('d-m-Y') }}</span><br>
                                    <strong>Shipping Name:</strong> <span id="shippingName">{{ $shipping->shipping_name }}</span><br>
                                    <strong>Total Product Quantity:</strong> <span id="totalQuantity">{{ $shipping->total_product_quantity }}</span><br>
                                    <strong>Total Missing Product Quantity:</strong> <span id="totalMissingQuantity">{{ $shipping->total_missing_quantity }}</span>
                                </div>

                                <div>
                                    <strong>Select Warehouse: <span class="text-danger">*</span></strong>
                                    <select id="warehouse_id" class="form-control">
                                        <option value="">Select Warehouse</option>
                                        @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }} - {{ $warehouse->location }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Supplier</th>
                                        <th>Product</th>
                                        <th>Size</th>
                                        <th>Color</th>
                                        <th>Current Stock</th>
                                        <th>Purchased Quantity</th>
                                        <th>Shipped Quantity</th>
                                        <th>Missing Quantity</th>           
                                        <th>Remaining Quantity</th>           
                                        <th>Purchase Price Per Unit</th>
                                        <th>Ground Price</th>
                                        <th>Profit Margin(%)</th>
                                        <th>Current Selling Price</th>
                                        <th>New Selling Price</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="purchaseData">
                                    @foreach($purchaseHistories as $detail)
                                        @if($detail->remaining_product_quantity == 0)
                                            @continue
                                        @endif
                                    <tr>
                                        <td>
                                            {{ $detail->purchase->supplier->name ?? '' }}
                                            <input type="hidden" value="{{ $detail->purchase->supplier->id ?? '' }}" class="supplier_id">
                                            <input type="hidden" value="{{ $detail->product_id }}" class="product_id">
                                            <input type="hidden" value="{{ $detail->id }}" class="purchase_history_id">
                                            <input type="hidden" value="{{ $detail->product_size }}" class="product_size">
                                            <input type="hidden" value="{{ $detail->product_color }}" class="product_color">
                                            <input type="hidden" value="{{ $detail->purchase_price }}" class="purchase_price">
                                        </td>
                                        <td>
                                            {{ $detail->product->product_code ? $detail->product->product_code . '-' : '' }}{{ $detail->product->name ?? '' }}             
                                        </td>
                                        <td>{{ $detail->product_size ?? '' }}</td>
                                        <td>{{ $detail->product_color ?? '' }}</td>
                                        @php
                                            $filteredStock = $detail->product->stock
                                                ? $detail->product->stock
                                                    ->where('product_id', $detail->product_id)
                                                    ->where('size', $detail->product_size)
                                                    ->where('color', $detail->product_color)
                                                    ->where('quantity', '>', 0)
                                                    ->orderBy('id', 'desc')
                                                : collect();

                                            $currentStock = $filteredStock->sum('quantity');
                                            $currentSellingPrice = $filteredStock->first()->selling_price ?? 0;
                                        @endphp
                                        <td>{{ $currentStock }}</td>
                                        <td>{{ $detail->quantity }}</td>
                                        <td>
                                            <input type="number" value="{{ $detail->remaining_product_quantity }}" max="{{ $detail->remaining_product_quantity }}" min="1" class="form-control shipped_quantity"/>
                                            <input type="hidden" value="{{ $detail->remaining_product_quantity }}" max="{{ $detail->remaining_product_quantity }}" class="max-quantity"/>
                                        </td>              
                                        <td>
                                            <input type="number" value="0" max="{{ $detail->remaining_product_quantity }}" min="0" class="form-control missing_quantity"/>
                                        </td>
                                        <td>
                                            <input type="number" value="0" max="{{ $detail->remaining_product_quantity }}" min="0" class="form-control remaining_quantity" readonly>
                                        </td>
                                        <td>{{ number_format($detail->purchase_price, 2) }}</td>
                                        <td class="ground_cost"></td>
                                        <td>
                                            <input type="number" value="30" min="1" class="form-control profit_margin" />
                                        </td>
                                        <td>{{ number_format($currentSellingPrice, 2) }}</td>
                                        <td class="selling_price"></td>
                                        <td>
                                            <button type="button" class="btn btn-danger remove-row"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div class="col-sm-10 my-5">
                                <div class="row">

                                    <div class="col-sm-6">

                                        <div class="row mt-1">
                                            <div class="col-sm-12 d-flex align-items-center">
                                                <span>Total Purchase Cost:</span>
                                                <input type="text" class="form-control" id="direct_cost" style="width: 100px; margin-left: auto;" readonly value="">
                                            </div>
                                        </div>

                                        <div class="row mt-1">
                                            <div class="col-sm-12 d-flex align-items-center">
                                                <span>Total Additional Cost:</span>
                                                <input type="number" class="form-control" id="total_additional_cost" style="width: 100px; margin-left: auto;" min="0" readonly value="">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">

                                        <div class="row mt-1">
                                            <div class="col-sm-12 d-flex align-items-center">
                                                <span>CNF Cost:</span>
                                                <select class="form-control" id="cnf_payment_type" style="width: 100px; margin-left: auto;">
                                                    <option value="Bank">Bank</option>
                                                    <option value="Cash">Cash</option>   
                                                </select>
                                                <input type="number" class="form-control" id="cnf_cost" style="width: 100px; margin-left: 10px;" min="0" value="">
                                            </div>
                                        </div>

                                        <div class="row mt-1">
                                            <div class="col-sm-12 d-flex align-items-center">
                                                <span>Import Duties & Taxes:</span>
                                                <select class="form-control" id="import_payment_type" style="width: 100px; margin-left: auto;">                        
                                                    <option value="Bank">Bank</option>
                                                    <option value="Cash">Cash</option>
                                                </select>
                                                <input type="number" class="form-control" id="import_taxes" style="width: 100px; margin-left: 10px;" min="0" value="">
                                            </div>
                                        </div>

                                        <div class="row mt-1">
                                            <div class="col-sm-12 d-flex align-items-center">
                                                <span>Warehouse & Handling Costs:</span>
                                                <select class="form-control" id="warehouse_payment_type" style="width: 100px; margin-left: auto;">
                                                    <option value="Bank">Bank</option>
                                                    <option value="Cash">Cash</option>  
                                                </select>
                                                <input type="number" class="form-control" id="warehouse_cost" style="width: 100px; margin-left: 10px;" min="0" value="">
                                            </div>
                                        </div>

                                        <div class="row mt-1">
                                            <div class="col-sm-12 d-flex align-items-center">
                                                <span>Other Costs:</span>
                                                <select class="form-control" id="other_payment_type" style="width: 100px; margin-left: auto;">                                    
                                                    <option value="Bank">Bank</option>
                                                    <option value="Cash">Cash</option>
                                                </select>
                                                <input type="number" class="form-control" id="other_cost" style="width: 100px; margin-left: 10px;" min="0" value="">         
                                            </div>
                                        </div>

                                    </div>
                                    
                                    <div class="col-sm-12 d-flex justify-content-center mt-5">
                                        <button id="calculateSalesPriceBtn" class="btn btn-success" style="margin-left: 10px;">Calculate Sales Price</button>
                                    </div>
                                    
                                </div>
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

        $('#calculateSalesPriceBtn').on('click', function(event) {
            event.preventDefault();

            if (!confirm("Are you sure you want to proceed with creating the shipment?")) {
                return;
            }

            let shippingId = $('#shipping_id').val();
            let warehouseId = $('#warehouse_id').val();

            if (!warehouseId) {
                $(".ermsg").html(`
                    <div class='alert alert-danger'>
                        <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                        <b>Please select a warehouse before proceeding.</b>
                    </div>
                `).show();
                pagetop();
                return;
            }

            let totalQuantity = $('#totalQuantity').text();
            let totalMissingQuantity = $('#totalMissingQuantity').text();
            let shipmentDetails = [];

            $('#purchaseData tr').each(function() {
                let supplierId = $(this).find('.supplier_id').val();
                let purchaseHistoryId = $(this).find('.purchase_history_id').val();
                let productId = $(this).find('.product_id').val();
                let size = $(this).find('.product_size').val();
                let color = $(this).find('.product_color').val();
                let shippedQuantity = $(this).find('.shipped_quantity').val();
                let missingQuantity = $(this).find('.missing_quantity').val();
                let remainingQuantity = $(this).find('.remaining_quantity').val();
                let pricePerUnit = $(this).find('.purchase_price').val();
                let groundCost = $(this).find('.ground_cost').text().replace(/,/g, '');
                let profitMargin = $(this).find('.profit_margin').val().replace(/,/g, '');
                let sellingPrice = $(this).find('.selling_price').text().replace(/,/g, '');

                if (productId && shippedQuantity > 0) {
                    shipmentDetails.push({
                        purchase_history_id: purchaseHistoryId,
                        supplier_id: supplierId,
                        product_id: productId,
                        size: size,
                        color: color,
                        shipped_quantity: shippedQuantity,
                        missing_quantity: missingQuantity,
                        remaining_quantity: remainingQuantity,
                        price_per_unit: pricePerUnit,
                        ground_cost: groundCost,
                        profit_margin: profitMargin,
                        selling_price: sellingPrice
                    });
                }
            });

            if (shipmentDetails.length === 0) {
                $(".ermsg").html(`
                    <div class='alert alert-danger'>
                        <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                        <b>Please add at least one valid product to the shipment.</b>
                    </div>
                `).show();
                return;
            }

            let dataToSend = {
                shipping_id: shippingId,
                warehouse_id: warehouseId,
                total_quantity: totalQuantity,
                total_missing_quantity: totalMissingQuantity,
                total_purchase_cost: $('#direct_cost').val().replace(/,/g, ''),
                cnf_cost: $('#cnf_cost').val().replace(/,/g, ''),
                import_taxes: $('#import_taxes').val().replace(/,/g, ''),
                warehouse_cost: $('#warehouse_cost').val().replace(/,/g, ''),
                other_cost: $('#other_cost').val().replace(/,/g, ''),
                cnf_payment_type: $('#cnf_payment_type').val(),
                import_payment_type: $('#import_payment_type').val(),
                warehouse_payment_type: $('#warehouse_payment_type').val(),
                other_payment_type: $('#other_payment_type').val(),
                total_additional_cost: $('#total_additional_cost').val().replace(/,/g, ''),
                shipment_details: shipmentDetails,
            };

            let _token = $('meta[name="csrf-token"]').attr('content');

            // console.log(dataToSend);

            $.ajax({
                url: '/admin/shipment-store',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': _token
                },
                data: dataToSend,
                success: function(response) {
                    // console.log(response);
                    $(".ermsg").html(`
                        <div class='alert alert-success'>
                            <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                            <b>Shipment Created successfully!</b>
                        </div>
                    `).show();
                    pagetop();

                    window.location.href = "{{ route('admin.shipping') }}";
                },
                error: function(xhr, status, error) {
                    // console.error(xhr.responseText);
                    $(".ermsg").html(`
                        <div class='alert alert-danger'>
                            <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                            <b>Error creating shipment. Please try again.</b>
                        </div>
                    `).show();
                    pagetop();
                }
            });
        });

        $('.remove-row').on('click', function() {
            $(this).closest('tr').remove();
            updateCalculations();
        });

        function updateCalculations() {
            const tableRows = $('#purchaseData tr');
            let totalPurchaseCost = 0;
            let totalQuantity = 0;
            let totalMissingQuantity = 0;

            tableRows.each(function () {
                const purchasePrice = parseFloat($(this).find('.purchase_price').val());
                let maxQuantity = parseInt($(this).find('.max-quantity').val()) || 0;

                let shippedQuantity = parseInt($(this).find('.shipped_quantity').val()) || 0;
                let missingQuantity = parseInt($(this).find('.missing_quantity').val()) || 0;

                if (shippedQuantity + missingQuantity > maxQuantity) {
                    missingQuantity = maxQuantity - shippedQuantity;
                    $(this).find('.missing_quantity').val(missingQuantity);
                }

                const remainingQuantity = maxQuantity - (shippedQuantity + missingQuantity);
                $(this).find('.remaining_quantity').val(remainingQuantity < 0 ? 0 : remainingQuantity);

                const productTotal = purchasePrice * shippedQuantity;
                $(this).find('.ground_cost').text(productTotal.toFixed(2));

                totalPurchaseCost += productTotal;
                totalQuantity += shippedQuantity;
                totalMissingQuantity += missingQuantity;
            });

            $('#totalQuantity').text(totalQuantity);
            $('#totalMissingQuantity').text(totalMissingQuantity);

            // Shared costs
            const cnfCost = parseFloat($('#cnf_cost').val()) || 0;
            const importTaxes = parseFloat($('#import_taxes').val()) || 0;
            const warehouseCost = parseFloat($('#warehouse_cost').val()) || 0;
            const otherCost = parseFloat($('#other_cost').val()) || 0;

            const totalSharedCosts = cnfCost + importTaxes + warehouseCost + otherCost;

            // Update shipment costs
            const totalShipmentCost = totalPurchaseCost + totalSharedCosts;
            $('#total_additional_cost').val(totalSharedCosts.toFixed(2));
            $('#direct_cost').val(totalPurchaseCost.toFixed(2));

            // Update ground cost per unit
            tableRows.each(function() {
                const productTotal = parseFloat($(this).find('.ground_cost').text());
                const quantity = parseInt($(this).find('.shipped_quantity').val());

                const sharedCostForProduct = (productTotal / totalPurchaseCost) * totalSharedCosts;
                const groundCostPerUnit = (productTotal + sharedCostForProduct) / quantity;

                $(this).find('.ground_cost').text(groundCostPerUnit.toFixed(2));
            });

            // Update selling price
            tableRows.each(function() {
                const groundCostPerUnit = parseFloat($(this).find('.ground_cost').text());
                const profitMargin = parseFloat($(this).find('.profit_margin').val()) || 0;

                const sellingPrice = groundCostPerUnit * (1 + profitMargin / 100);
                $(this).find('.selling_price').text(sellingPrice.toFixed(2));
            });
        }

        $(document).on('input', '.shipped_quantity, .missing_quantity, .profit_margin, #cnf_cost, #import_taxes, #warehouse_cost, #other_cost', function() {
            updateCalculations();
        });

        updateCalculations();

        $('#warehouse_id').select2({
            placeholder: "Select a Warehouse",
            allowClear: true
        });
    });
</script>

@endsection