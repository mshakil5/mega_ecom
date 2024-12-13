@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3">

<section class="content">
    <div class="ermsg mt-3"></div>
</section>

    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Edit Shipment</h3>
                    </div>
                    <div class="card-body">
                        <form id="editShipmentForm">
                            <input type="hidden" id="id" value="{{ $shipment->id }}">
                            <div class="mb-4 d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Shipping ID:</strong> <span id="shippingId">{{ $shipment->shipping->shipping_id }}</span><br>
                                    <strong>Shipping Date:</strong> <span id="shippingDate">{{ \Carbon\Carbon::parse($shipment->shipping->shipping_date)->format('d-m-Y') }}</span><br>
                                    <strong>Shipping Name:</strong> <span id="shippingName">{{ $shipment->shipping->shipping_name }}</span> <br>
                                    <strong>Total Product Quantity:</strong> <span id="totalQuantity">{{ $shipment->total_product_quantity }}</span> <br>
                                    <strong>Total Missing Product Quantity:</strong> <span id="totalMissingQuantity">{{ $shipment->total_missing_quantity }}</span>
                                </div>
                                <div>
                                    <strong>Select Warehouse: <span class="text-danger">*</span></strong>
                                    <select id="warehouse_id" class="form-control" disabled>
                                        @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" 
                                                @if($shipment->shipmentDetails->first()->warehouse_id == $warehouse->id) selected @endif>
                                                {{ $warehouse->name }} - {{ $warehouse->location }}
                                            </option>
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
                                        <th>Shipped Quantity</th>
                                        <th>Missing Quantity</th>    
                                        <th>Purchase Price Per Unit</th>
                                        <th>Ground Price</th>
                                        <th>Profit Margin(%)</th>
                                        <th>Selling Price</th>
                                        <!-- <th>Action</th> -->
                                    </tr>
                                </thead>
                                <tbody id="purchaseData">
                                    @foreach($shipment->shipmentDetails as $detail)
                                    <tr>
                                        <td>
                                            {{ $detail->supplier->name ?? '' }}
                                            <input type="hidden" value="{{ $detail->supplier_id }}" class="supplier_id">
                                            <input type="hidden" value="{{ $detail->id }}" class="id">
                                            <input type="hidden" value="{{ $detail->purchase_history_id }}" class="purchase_history_id">
                                        </td>
                                        <td>
                                            {{ $detail->product->product_code ? $detail->product->product_code . '-' : '' }}{{ $detail->product->name ?? '' }}
                                            <input type="hidden" value="{{ $detail->product_id }}" class="product_id">
                                        </td>
                                        <td>{{ $detail->size ?? '' }}</td>
                                        <td>{{ $detail->color ?? '' }}</td>
                                        <td>
                                            <input type="number" value="{{ $detail->quantity }}" max="{{ $detail->quantity }}" min="1" class="form-control product_quantity" readonly/>
                                            <input type="hidden" value="{{ $detail->quantity + $detail->missing_quantity }}" max="{{ $detail->quantity + $detail->missing_quantity }}" class="max-quantity"/>
                                        </td>
                                        <td>
                                            <input type="number" value="{{ $detail->missing_quantity }}" max="{{ $detail->quantity }}" min="0" class="form-control missing_quantity" readonly/>
                                        </td>
                                        <td class="purchase_price">{{ number_format($detail->price_per_unit, 2) }}</td>
                                        <td class="ground_cost">{{ number_format($detail->ground_cost, 2) }}</td>
                                        <td>
                                            <input type="number" value="{{ $detail->profit_margin }}" min="0" class="form-control profit_margin" />
                                        </td>
                                        <td class="selling_price">{{ number_format($detail->selling_price, 2) }}</td>
                                        <!-- <td>
                                            <button type="button" class="btn btn-danger remove-row"><i class="fas fa-trash"></i></button>
                                        </td> -->
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div class="col-sm-6 mt-5 mb-5">
                                <div class="row">
                                    <!-- Left Column -->
                                    <div class="col-sm-6">

                                        <div class="row mt-1">
                                            <div class="col-sm-12 d-flex align-items-center">
                                                <span>Total Purchase Cost:</span>
                                                <input type="text" class="form-control" id="direct_cost" style="width: 100px; margin-left: auto;" readonly value="{{ $shipment->total_purchase_cost }}">
                                            </div>
                                        </div>

                                        <div class="row mt-1">
                                            <div class="col-sm-12 d-flex align-items-center">
                                                <span>CNF Cost:</span>
                                                <input type="number" class="form-control" id="cnf_cost" style="width: 100px; margin-left: auto;" min="0" value="{{ $shipment->cnf_cost }}">
                                            </div>
                                        </div>

                                        <div class="row mt-1">
                                            <div class="col-sm-12 d-flex align-items-center">
                                                <span>Import Duties & Taxes:</span>
                                                <input type="number" class="form-control" id="import_taxes" style="width: 100px; margin-left: auto;" min="0" value="{{ $shipment->import_duties_tax }}">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right Column -->
                                    <div class="col-sm-6">
                                        <div class="row mt-1">
                                            <div class="col-sm-12 d-flex align-items-center">
                                                <span>Warehouse & Handling Costs:</span>
                                                <input type="number" class="form-control" id="warehouse_cost" style="width: 100px; margin-left: auto;" min="0" value="{{ $shipment->warehouse_and_handling_cost }}">
                                            </div>
                                        </div>

                                        <div class="row mt-1">
                                            <div class="col-sm-12 d-flex align-items-center">
                                                <span>Other Costs:</span>
                                                <input type="number" class="form-control" id="other_cost" style="width: 100px; margin-left: auto;" min="0" value="{{ $shipment->other_cost }}">
                                            </div>
                                        </div>

                                        <div class="row mt-1">
                                            <div class="col-sm-12 d-flex align-items-center">
                                                <span>Total Additional Cost:</span>
                                                <input type="number" class="form-control" id="total_additional_cost" style="width: 100px; margin-left: auto;" min="0" readonly value="{{ $shipment->total_additional_cost }}">
                                            </div>
                                        </div>

                                        <div class="row mt-5">
                                            <div class="col-sm-12 d-flex justify-content-start">
                                                <button id="calculateSalesPriceBtn" class="btn btn-success" style="margin-left: 10px;">Update Sales Price</button>
                                            </div>
                                        </div>

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

        let removedShipmentDetailIds = [];

        $('.remove-row').on('click', function() {
            const shipmentDetailId = $(this).closest('tr').find('.id').val();
            if (shipmentDetailId) {
                removedShipmentDetailIds.push(shipmentDetailId);
            }
            $(this).closest('tr').remove();
            updateCalculations();
        });

        $('#calculateSalesPriceBtn').on('click', function(event) {
            event.preventDefault();

            if (!confirm("Are you sure you want to proceed with updating the shipment?")) {
                return;
            }

            let idValue = $('#id').val();
            let totalQuantity = $('#totalQuantity').text();
            let totalMissingQuantity = $('#totalMissingQuantity').text();
            let shipmentDetails = [];

            $('#purchaseData tr').each(function() {
                let id = $(this).find('.id').val();
                let purchaseHistoryId = $(this).find('.purchase_history_id').val();
                let supplierId = $(this).find('.supplier_id').val();
                let productId = $(this).find('.product_id').val();
                let size = $(this).find('td:nth-child(3)').text();
                let color = $(this).find('td:nth-child(4)').text();
                let quantity = $(this).find('.product_quantity').val();
                let missingQuantity = $(this).find('.missing_quantity').val();
                let pricePerUnit = $(this).find('.purchase_price').text().replace(/,/g, '');
                let groundCost = $(this).find('.ground_cost').text().replace(/,/g, '');
                let profitMargin = $(this).find('.profit_margin').val().replace(/,/g, '');
                let sellingPrice = $(this).find('.selling_price').text().replace(/,/g, '');

                if (productId && quantity > 0) {
                    shipmentDetails.push({
                        id: id,
                        purchase_history_id: purchaseHistoryId,
                        supplier_id: supplierId,
                        product_id: productId,
                        size: size,
                        color: color,
                        quantity: quantity,
                        missing_quantity: missingQuantity,
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
                pagetop();
                return;
            }

            let dataToSend = {
                id: idValue,
                total_quantity: totalQuantity,
                total_missing_quantity: totalMissingQuantity,
                total_purchase_cost: $('#direct_cost').val().replace(/,/g, ''),
                cnf_cost: $('#cnf_cost').val().replace(/,/g, ''),
                import_taxes: $('#import_taxes').val().replace(/,/g, ''),
                warehouse_cost: $('#warehouse_cost').val().replace(/,/g, ''),
                other_cost: $('#other_cost').val().replace(/,/g, ''),
                total_additional_cost: $('#total_additional_cost').val().replace(/,/g, ''),
                shipment_details: shipmentDetails,
                removed_ids: removedShipmentDetailIds
            };

            let _token = $('meta[name="csrf-token"]').attr('content');

            // console.log(dataToSend);

            $.ajax({
                url: '/admin/shipment-update/' + idValue,
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': _token
                },
                data: dataToSend,
                success: function(response) {
                    // console.log(response);
                    $(".ermsg").html(`
                        <div class='alert alert-success'>
                            <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                            <b>Shipment updated successfully!</b>
                        </div>
                    `).show();
                    pagetop();

                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                },
                error: function(xhr, status, error) {
                    // console.error(xhr.responseText);
                    $(".ermsg").html(`
                        <div class='alert alert-danger'>
                            <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                            <b>Error updating shipment. Please try again.</b>
                        </div>
                    `).show();
                    pagetop();
                }
            });
        });

        function updateCalculations() {
            const tableRows = $('#purchaseData tr');
            let totalPurchaseCost = 0;
            let totalQuantity = 0;
            let totalMissingQuantity = 0;

            tableRows.each(function() {
                const purchasePrice = parseFloat($(this).find('.purchase_price').text().replace(/,/g, ''));
                let missingQuantity = parseInt($(this).find('.missing_quantity').val()) || 0;
                let maxQuantity = parseInt($(this).find('.max-quantity').val()) || 0;

                const quantity = parseInt($(this).find('.product_quantity').val());
                if(missingQuantity >= quantity){
                    missingQuantity = quantity;
                    $(this).find('.missing_quantity').val(missingQuantity);
                }

                let updatedQuantity = maxQuantity - missingQuantity;

                if (updatedQuantity < 1) {
                    updatedQuantity = 1;
                }

                $(this).find('.product_quantity').val(updatedQuantity);

                const productTotal = purchasePrice * quantity;

                totalPurchaseCost += productTotal;
                totalQuantity += quantity;
                totalMissingQuantity += missingQuantity;
                $(this).find('.ground_cost').text(productTotal.toFixed(2));
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
                const quantity = parseInt($(this).find('.product_quantity').val());

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

        $(document).on('input', '.product_quantity, .missing_quantity, .profit_margin, #cnf_cost, #import_taxes, #warehouse_cost, #other_cost', function() {
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