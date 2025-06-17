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
                                    <strong>Total Missing/Damaged Product Quantity:</strong> <span id="totalMissingQuantity">{{ $shipment->total_missing_quantity }}</span>
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

                            <div class="table-responsive" style="max-height: 550px; overflow-y: auto; overflow-x: auto;">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Supplier</th>
                                        <th>Product</th>
                                        <th>Size</th>
                                        <th>Color</th>
                                        <th>Stock <br> <small>(previous)</small> </th>
                                        <th>Purchased</th>
                                        <th>Shipped</th>
                                        <th>Damaged</th>
                                        <th>Sample</th>
                                        <th>Saleable</th>
                                        <th>To Ship</th>
                                        <th>Unit Price</th>
                                        <th>Ground Price</th>
                                        <th>Margin(%)</th>
                                        <th>Selling Price <br> <small>(previous)</small> </th>
                                        <th>New Price</th>
                                        <th>Min Margin(%)</th>
                                        <th>Min Price</th>
                                        <!-- <th>Action</th> -->
                                    </tr>
                                </thead>
                                <tbody id="purchaseData">
                                    @foreach($shipment->shipmentDetails as $detail)
                                    <tr>
                                        <td>
                                            {{ $detail->supplier->name ?? '' }}
                                            <input type="hidden" value="{{ $detail->product_id }}" class="product_id">
                                            <input type="hidden" value="{{ $detail->supplier_id }}" class="supplier_id">
                                            <input type="hidden" value="{{ $detail->id }}" class="id">
                                            <input type="hidden" value="{{ $detail->purchase_history_id }}" class="purchase_history_id">
                                            <input type="hidden" value="{{ $detail->size }}" class="product_size">
                                            <input type="hidden" value="{{ $detail->color }}" class="product_color">
                                            <input type="hidden" value="{{ $detail->ground_price_per_unit }}" class="ground_price_per_unit">
                                            <input type="hidden" value="{{ $detail->systemLose->id ?? '' }}" class="system_lose_id">
                                            <input type="hidden" value="{{ $detail->price_per_unit }}" class="purchase_price">
                                        </td>
                                        <td>
                                            {{ $detail->product->product_code ? $detail->product->product_code . '-' : '' }}{{ $detail->product->name ?? '' }}
                                            @if($detail->product->isZip())
                                                <br>
                                                (Zip: {{ $detail->zip == 1 ? 'Yes' : 'No' }})
                                            @endif 
                                        </td>
                                        <td>{{ $detail->size ?? '' }}</td>
                                        <td>{{ $detail->color ?? '' }}</td>
                                        @php
                                            $filteredStock = $detail->purchaseHistory->product->stock ? $detail->purchaseHistory->product->stock
                                            ->where('product_id', $detail->purchaseHistory->product_id)
                                            ->where('size', $detail->purchaseHistory->product_size)
                                            ->where('color', $detail->purchaseHistory->product_color)
                                            ->where('zip', $detail->product->isZip())
                                            ->where('quantity', '>', 0)
                                            ->orderBy('id', 'desc')
                                            : collect();

                                            $currentStock = number_format ($filteredStock->sum('quantity'), 0);
                                            $currentSellingPrice = $filteredStock->first()->selling_price ?? 0;
                                        @endphp
                                        <td>{{ $currentStock }}</td>
                                        <td>{{ $detail->purchaseHistory->quantity }}</td>
                                        <td>
                                            <input type="number" value="{{ $detail->shipped_quantity }}" max="{{ $detail->shipped_quantity }}" min="1" class="form-control shipped_quantity" @if($detail->shipment->shipping->status == 4) readonly @endif />
                                            <input 
                                                type="hidden" 
                                                value="{{ $detail->purchaseHistory->remaining_product_quantity + $detail->shipped_quantity }}"
                                                max="{{ $detail->purchaseHistory->remaining_product_quantity + $detail->shipped_quantity }}" 
                                                class="max-quantity"
                                            />
                                        </td>
                                        <td>
                                            <input type="number" value="{{ $detail->missing_quantity }}" max="{{ $detail->quantity }}" min="0" class="form-control missing_quantity" @if($detail->shipment->shipping->status == 4) readonly @endif/>
                                        </td>
                                        <td>
                                            <input type="number" value="{{ $detail->sample_quantity }}" min="0" class="form-control sample_quantity" @if($detail->shipment->shipping->status == 4) readonly @endif/>
                                        </td>
                                        <td>
                                            <input type="number" value="{{ $detail->quantity }}" max="{{ $detail->quantity }}" min="0" class="form-control saleable_quantity" readonly />
                                        </td>
                                        <td>
                                            <input type="number" value="0" max="" min="0" class="form-control remaining_quantity" readonly>
                                        </td>
                                        <td>{{ number_format($detail->price_per_unit, 2) }}</td>
                                        <td class="ground_cost">{{ number_format($detail->ground_price_per_unit, 2) }}</td>
                                        {{-- <td>
                                            <input type="number" value="{{ number_format($detail->profit_margin, 0) }}" min="0" class="form-control profit_margin" />
                                        </td> --}}
                                        <td>
                                            <input type="number" value="{{ (int) $detail->profit_margin }}" min="0" class="form-control profit_margin"/>
                                        </td>
                                        <td>{{ number_format($currentSellingPrice, 2) }}</td>
                                        <td class="selling_price">{{ number_format($detail->selling_price, 2) }}</td>
                                        <td>
                                            <input type="number" value="{{ number_format($detail->considerable_margin, 0) }}" min="1" class="form-control considerable_margin" />
                                        </td>
                                        <td class="considerable_price">{{ number_format($detail->considerable_price, 2) }}</td>
                                        <!-- <td>
                                            <button type="button" class="btn btn-danger remove-row"><i class="fas fa-trash"></i></button>
                                        </td> -->
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            </div>

                            <div class="col-sm-10 mt-5 mb-5">
                                <div class="row">
                                    <!-- Left Column -->
                                    <div class="col-sm-4">

                                        <div class="row mt-1">
                                            <div class="col-sm-12 d-flex align-items-center">
                                                <span>Total Profit:</span>
                                                <input type="number" class="form-control" id="total_profit" style="width: 100px; margin-left: auto;" min="0" readonly value="{{ $shipment->total_profit }}">
                                                <input id="budgetDifference" type="hidden" value="{{ $shipment->budget_over }}">
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-sm-12 d-flex align-items-center">
                                                <span>Target Budget:</span>
                                                <input type="number" class="form-control" id="target_budget" style="width: 100px; margin-left: auto;" min="0" value="{{ $shipment->target_budget }}">
                                            </div>
                                        </div>

                                        <div class="row mt-1">
                                            <div class="col-sm-12 d-flex align-items-center">
                                                <span>Total Purchase Cost:</span>
                                                <input type="text" class="form-control" id="direct_cost" style="width: 100px; margin-left: auto;" readonly value="{{ $shipment->total_purchase_cost }}">
                                            </div>
                                        </div>

                                        <div class="row mt-1">
                                            <div class="col-sm-12 d-flex align-items-center">
                                                <span>Total Additional Cost:</span>
                                                <input type="number" class="form-control" id="total_additional_cost" style="width: 100px; margin-left: auto;" min="0" readonly value="{{ $shipment->total_additional_cost }}">
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-sm-12 d-flex align-items-center">
                                                <span>Total Cost Of This Shipment:</span>
                                                <input type="number" class="form-control" id="total_cost_of_the_shipment" style="width: 100px; margin-left: auto;" min="0" readonly value="{{ $shipment->total_cost_of_shipment }}">
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-sm-12 d-flex align-items-center">
                                                <span>Total Quantiy In PCS:</span>
                                                <input type="number" class="form-control" id="totalQuantityInPcs" style="width: 100px; margin-left: auto;"  value="{{ $shipment->total_product_quantity }}" readonly>
                                            </div>
                                        </div>

                                        <div class="row mt-2 d-none">
                                            <div class="col-sm-12 d-flex align-items-center">
                                                <span>Cost Per Piece:</span>
                                                <input type="number" class="form-control" id="costPerPieces" style="width: 100px; margin-left: auto;" readonly>
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-sm-12 mt-1 d-none" id="budget-message">
                                                <span></span>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="col-sm-1">
                                    </div>

                                    <div class="col-sm-7">
                                        <div id="expense-container">
                                            @foreach($shipment->transactions as $index => $transaction)
                                                <div class="row mt-1 expense-row" id="expense-row-{{ $transaction->id }}" data-expense-id="{{ $transaction->chart_of_account_id }}">
                                                    <div class="col-sm-12 d-flex align-items-center">
                                                        <input type="hidden" class="id" value="{{ $transaction->id }}">
                                                        <select class="form-control expense-type" style="width: 200px;">
                                                            <option value="">Select Expense</option>
                                                            @foreach($expenses as $expense)
                                                                <option value="{{ $expense->id }}" {{ $expense->id == $transaction->chart_of_account_id ? 'selected' : '' }}>
                                                                    {{ $expense->account_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <select class="form-control payment-type" style="width: 100px; margin-left: 10px;">
                                                            <option value="Bank" {{ $transaction->payment_type == 'Bank' ? 'selected' : '' }}>Bank</option>
                                                            <option value="Cash" {{ $transaction->payment_type == 'Cash' ? 'selected' : '' }}>Cash</option>
                                                        </select>
                                                        <input type="number" class="form-control expense-amount" style="width: 100px; margin-left: 10px;" min="0" value="{{ $transaction->amount }}" placeholder="Amount">
                                                        <input type="text" class="form-control expense-description" style="width: 150px; margin-left: 10px;" min="0" placeholder="Description" value="{{ $transaction->description }}">
                                                        <input type="text" class="form-control expense-note" style="width: 150px; margin-left: 10px;" min="0" placeholder="Note" value="{{ $transaction->note }}">
                                                        
                                                        @if($index == 0)
                                                            <button type="button" class="btn btn-success add-expense btn-sm" style="margin-left: 10px;">
                                                                <i class="fas fa-plus"></i>
                                                            </button>
                                                        @else
                                                            <button type="button" class="btn btn-danger remove-expense btn-sm" style="margin-left: 10px;">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>


                                    <div class="col-sm-12 d-flex justify-content-center mt-5">
                                        @if($status)
                                        <button id="receivedButton" class="btn btn-success" style="margin-left: 10px;">Confirm Received</button>                              
                                        @else
                                        <button id="calculateSalesPriceBtn" class="btn btn-success" style="margin-left: 10px;">Update Sales Price</button>
                                        @endif
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

<style>
    th, td {
        white-space: nowrap;
        min-width: 120px;
    },
    .table {
        table-layout: fixed;
        width: 100%;
    }
</style>

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

            let warehouseId = $('#warehouse_id').val();
            let shipmentId = $('#id').val();
            let totalQuantity = $('#totalQuantity').text();
            let totalMissingQuantity = $('#totalMissingQuantity').text();
            let target_budget = $('#target_budget').val();
            let totalProfit = $('#total_profit').val();
            let budget_over = $('#budgetDifference').val();
            let total_cost_of_the_shipment = $('#total_cost_of_the_shipment').val();
            let shipmentDetails = [];
            let hasIncompleteEntry = false;
            let expenses = [];

            $('#purchaseData tr').each(function() {
                let id = $(this).find('.id').val();
                let supplierId = $(this).find('.supplier_id').val();
                let purchaseHistoryId = $(this).find('.purchase_history_id').val();
                let systemLoseId = $(this).find('.system_lose_id').val();
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
                let considerableMargin = $(this).find('.considerable_margin').val();
                let considerablePrice = $(this).find('.considerable_price').text().replace(/,/g, '');
                let sampleQuantity = $(this).find('.sample_quantity').val();
                let saleableQuantity = $(this).find('.saleable_quantity').val();

                if (productId) {
                    shipmentDetails.push({
                        id: id,
                        purchase_history_id: purchaseHistoryId,
                        system_lose_id: systemLoseId,
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
                        selling_price: sellingPrice,
                        considerable_margin: considerableMargin,
                        considerable_price: considerablePrice,
                        sample_quantity: sampleQuantity,
                        quantity: saleableQuantity
                    });
                }
            });

            $('.expense-row').each(function() {
                let transactionId = $(this).find('.id').val();
                let expenseId = $(this).find('.expense-type').val();
                let paymentType = $(this).find('.payment-type').val();
                let amount = $(this).find('.expense-amount').val();
                let description = $(this).find('.expense-description').val();
                let note = $(this).find('.expense-note').val();

                if ((expenseId && amount === 0) || (!expenseId && amount > 0)) {
                    hasIncompleteEntry = true;
                }

                if (expenseId && amount > 0) {
                    expenses.push({
                        chart_of_account_id: expenseId,
                        transaction_id: transactionId,
                        payment_type: paymentType,
                        amount: parseFloat(amount),
                        description: description,
                        note: note
                    });
                }
            });

            if (hasIncompleteEntry || expenses.length === 0) {
                $(".ermsg").html(`
                    <div class='alert alert-danger'>
                        <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                        <b>Please select at least one expense with a valid amount.</b>
                    </div>
                `).show();
                pagetop();
                return;
            }

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
                id: shipmentId,
                warehouse_id: warehouseId,
                target_budget: target_budget,
                total_profit: totalProfit,
                budget_over: budget_over,
                total_cost_of_the_shipment: total_cost_of_the_shipment,
                total_quantity: totalQuantity,
                total_missing_quantity: totalMissingQuantity,
                total_purchase_cost: $('#direct_cost').val().replace(/,/g, ''),
                total_additional_cost: $('#total_additional_cost').val().replace(/,/g, ''),
                shipment_details: shipmentDetails,
                removed_ids: removedShipmentDetailIds,
                expenses: expenses
            };

            let _token = $('meta[name="csrf-token"]').attr('content');

            // console.log(dataToSend);


            $.ajax({
                url: '/admin/shipment-update/' + shipmentId,
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
                        window.location.href = "{{ route('admin.shipping') }}";
                    }, 500);
                },
                error: function(xhr, status, error) {
                    let response = Object.values(xhr.responseJSON.errors)[0][0];
                    $(".ermsg").html(`
                        <div class='alert alert-danger'>
                            <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                            <b>${response}</b>
                        </div>
                    `).show();
                    pagetop();
                }
            });
        });

        $('#receivedButton').on('click', function(event) {
            event.preventDefault();

            if (!confirm("Are you sure to make this shipment received?")) {
                return;
            }

            if (!confirm("You can't update this shipment further. Are you sure?")) {
                return;
            }

            let warehouseId = $('#warehouse_id').val();
            let shipmentId = $('#id').val();
            let totalQuantity = $('#totalQuantity').text();
            let totalMissingQuantity = $('#totalMissingQuantity').text();
            let target_budget = $('#target_budget').val();
            let totalProfit = $('#total_profit').val();
            let budget_over = $('#budgetDifference').val();
            let total_cost_of_the_shipment = $('#total_cost_of_the_shipment').val();
            let shipmentDetails = [];
            let hasIncompleteEntry = false;
            let expenses = [];

            $('#purchaseData tr').each(function() {
                let id = $(this).find('.id').val();
                let supplierId = $(this).find('.supplier_id').val();
                let purchaseHistoryId = $(this).find('.purchase_history_id').val();
                let systemLoseId = $(this).find('.system_lose_id').val();
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
                let considerableMargin = $(this).find('.considerable_margin').val();
                let considerablePrice = $(this).find('.considerable_price').text().replace(/,/g, '');
                let sampleQuantity = $(this).find('.sample_quantity').val();
                let saleableQuantity = $(this).find('.saleable_quantity').val();

                if (productId && shippedQuantity > 0) {
                    shipmentDetails.push({
                        id: id,
                        purchase_history_id: purchaseHistoryId,
                        system_lose_id: systemLoseId,
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
                        selling_price: sellingPrice,
                        considerable_margin: considerableMargin,
                        considerable_price: considerablePrice,
                        sample_quantity: sampleQuantity,
                        quantity: saleableQuantity
                    });
                }
            });

            $('.expense-row').each(function() {
                let transactionId = $(this).find('.id').val();
                let expenseId = $(this).find('.expense-type').val();
                let paymentType = $(this).find('.payment-type').val();
                let amount = $(this).find('.expense-amount').val();
                let description = $(this).find('.expense-description').val();
                let note = $(this).find('.expense-note').val();

                if ((expenseId && amount === 0) || (!expenseId && amount > 0)) {
                    hasIncompleteEntry = true;
                }

                if (expenseId && amount > 0) {
                    expenses.push({
                        chart_of_account_id: expenseId,
                        transaction_id: transactionId,
                        payment_type: paymentType,
                        amount: parseFloat(amount),
                        description: description,
                        note: note
                    });
                }
            });

            if (hasIncompleteEntry || expenses.length === 0) {
                $(".ermsg").html(`
                    <div class='alert alert-danger'>
                        <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                        <b>Please select at least one expense with a valid amount.</b>
                    </div>
                `).show();
                pagetop();
                return;
            }

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
                id: shipmentId,
                warehouse_id: warehouseId,
                target_budget: target_budget,
                total_profit: totalProfit,
                budget_over: budget_over,
                total_cost_of_the_shipment: total_cost_of_the_shipment,
                total_quantity: totalQuantity,
                total_missing_quantity: totalMissingQuantity,
                total_purchase_cost: $('#direct_cost').val().replace(/,/g, ''),
                total_additional_cost: $('#total_additional_cost').val().replace(/,/g, ''),
                shipment_details: shipmentDetails,
                removed_ids: removedShipmentDetailIds,
                expenses: expenses
            };

            let _token = $('meta[name="csrf-token"]').attr('content');

            // console.log(dataToSend);


            $.ajax({
                url: '/admin/shipment-received/' + shipmentId,
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
                        window.location.href = "{{ route('admin.shipping') }}";
                    }, 500);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    let response = Object.values(xhr.responseJSON.errors)[0][0];
                    $(".ermsg").html(`
                        <div class='alert alert-danger'>
                            <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                            <b>${response}</b>
                        </div>
                    `).show();
                    pagetop();
                }
            });
        });

        function updateCalculations() {
            let totalPurchaseCost = 0, totalQuantity = 0, totalMissingQuantity = 0, totalSharedCosts = 0;

            $('.expense-amount').each(function () {
                totalSharedCosts += parseFloat($(this).val()) || 0;
            });
            $('#total_additional_cost').val(totalSharedCosts.toFixed(2));

            $('#purchaseData tr').each(function () {
                const $row = $(this);
                const purchasePrice = parseFloat($row.find('.purchase_price').val()) || 0;
                const maxQuantity = parseInt($row.find('.max-quantity').val()) || 0;
                let shippedQuantity = parseInt($row.find('.shipped_quantity').val()) || 0;
                let missingQuantity = parseInt($row.find('.missing_quantity').val()) || 0;
                let sampleQuantity = parseInt($row.find('.sample_quantity').val()) || 0;

                let availableQuantity = Math.max(0, shippedQuantity - (missingQuantity + sampleQuantity));
                sampleQuantity = Math.min(sampleQuantity, availableQuantity);
                missingQuantity = Math.min(missingQuantity, availableQuantity - sampleQuantity);

                const saleableQuantity = shippedQuantity - missingQuantity - sampleQuantity;
                const remainingQuantity = Math.max(0, maxQuantity - shippedQuantity);
                const productTotal = purchasePrice * shippedQuantity;

                $row.find('.saleable_quantity').val(saleableQuantity);
                $row.find('.remaining_quantity').val(remainingQuantity);

                totalPurchaseCost += productTotal;
                totalQuantity += saleableQuantity;
                totalMissingQuantity += missingQuantity;
            });

            $('#totalQuantity').text(totalQuantity);
            $('#totalQuantityInPcs').val(totalQuantity);
            $('#totalMissingQuantity').text(totalMissingQuantity);
            $('#direct_cost').val(totalPurchaseCost.toFixed(2));

            let totalShipmentCost = totalPurchaseCost + totalSharedCosts;
            $('#total_cost_of_the_shipment').val(totalShipmentCost.toFixed(2));
            
            let costPerPiece = totalQuantity > 0 ? totalShipmentCost / totalQuantity : 0;
            $('#costPerPieces').val(costPerPiece.toFixed(2));

            let totalProfit = 0;
            $('#purchaseData tr').each(function () {
                const $row = $(this);
                const purchasePrice = parseFloat($row.find('.purchase_price').val()) || 0;
                const saleableQuantity = parseFloat($row.find('.saleable_quantity').val()) || 0;
                const profitMargin = parseFloat($row.find('.profit_margin').val()) || 0;
                const considerableMargin = parseFloat($row.find('.considerable_margin').val()) || 0;

                let groundCostPerUnit = totalQuantity > 0 ? purchasePrice + (totalSharedCosts / totalQuantity) : purchasePrice;
                let sellingPrice = groundCostPerUnit * (1 + profitMargin / 100);
                let profitAmount = (sellingPrice - groundCostPerUnit) * saleableQuantity;
                let considerablePrice = groundCostPerUnit * (1 + considerableMargin / 100);

                $row.find('.ground_cost').text(groundCostPerUnit.toFixed(2));
                $row.find('.selling_price').text(sellingPrice.toFixed(2));
                $row.find('.considerable_price').text(considerablePrice.toFixed(2));

                totalProfit += profitAmount;
            });

            $('#total_profit').val(totalProfit.toFixed(2));

            const targetBudget = parseFloat($('#target_budget').val()) || 0;
            const difference = targetBudget - totalShipmentCost;
            const $messageContainer = $('#budget-message');
            const $messageText = $messageContainer.find('span');

            if (targetBudget > 0) {
                $('#budgetDifference').val(difference.toFixed(2));
                $messageContainer.removeClass('d-none');
                $messageText.html(`<span style="color: ${difference < 0 ? 'red' : 'green'};">You're ${difference < 0 ? 'over' : 'under'} budget by ${difference.toFixed(2)}</span>`);
            } else {
                $('#budgetDifference').val('');
                $messageContainer.addClass('d-none');
            }
        }

        $(document).on('input', '.shipped_quantity, .missing_quantity, .expense-amount, .profit_margin, .considerable_margin, .sample_quantity, .saleable_quantity,  #target_budget', function() {
            updateCalculations();
        });

        updateCalculations();

        $(document).on('click', '.add-expense', function () {
            const selectedIds = [];
            let hasDuplicate = false;

            $('.expense-row').each(function () {
                const expenseId = $(this).find('.expense-type').val();
                if (expenseId) {
                    if (selectedIds.includes(expenseId)) {
                        hasDuplicate = true;
                        return false;
                    }
                    selectedIds.push(expenseId);
                }
            });

            if (hasDuplicate) {
                swal({
                    title: "Duplicate Expense",
                    text: "This expense is already added!",
                    icon: "warning",
                })
                return;
            }

            const newRow = $('.expense-row:first').clone();

            newRow.find('.id').val('');
            newRow.find('.expense-type').val('');
            newRow.find('.payment-type').val('Bank');
            newRow.find('.expense-amount').val('');
            newRow.find('.expense-description').val('');
            newRow.find('.expense-note').val('');

            newRow.find('.add-expense')
                .removeClass('btn-success add-expense')
                .addClass('btn-danger remove-expense')
                .html('<i class="fas fa-trash"></i>');

            $('#expense-container').append(newRow);
        });

        $(document).on('change', '.expense-type', function () {
            const selectedId = $(this).val();
            const selectedIds = [];

            let isDuplicate = false;

            $('.expense-row').each(function () {
                const expenseId = $(this).find('.expense-type').val();
                if (expenseId) {
                    selectedIds.push(expenseId);
                }
            });

            if (selectedIds.filter(id => id === selectedId).length > 1) {
                alert('Duplicate expense type detected! Please select a unique expense.');
                $(this).val('');
            }
        });

        $(document).on('click', '.remove-expense', function () {
            $(this).closest('.expense-row').remove();
            updateCalculations();
        });

        $('#warehouse_id').select2({
            placeholder: "Select a Warehouse",
            allowClear: true
        });
    });
</script>

@endsection