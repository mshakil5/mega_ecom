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
                                    <strong>Total Product Quantity:</strong> <span id="totalQuantity">{{ $shipment->total_shipped_quantity }}</span> <br>
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
                                            <input type="hidden" value="{{ $detail->size }}" class="product_size">
                                            <input type="hidden" value="{{ $detail->color }}" class="product_color">
                                        </td>
                                        <td>
                                            {{ $detail->product->product_code ? $detail->product->product_code . '-' : '' }}{{ $detail->product->name ?? '' }}
                                            <input type="hidden" value="{{ $detail->product_id }}" class="product_id">
                                        </td>
                                        <td>{{ $detail->size ?? '' }}</td>
                                        <td>{{ $detail->color ?? '' }}</td>
                                        @php
                                        $filteredStock = $detail->purchaseHistory->product->stock ? $detail->purchaseHistory->product->stock
                                        ->where('product_id', $detail->purchaseHistory->product_id)
                                        ->where('size', $detail->purchaseHistory->product_size)
                                        ->where('color', $detail->purchaseHistory->product_color)
                                        ->where('quantity', '>', 0)
                                        ->orderBy('id', 'desc')
                                        : collect();

                                        $currentStock = $filteredStock->sum('quantity');
                                        $currentSellingPrice = $filteredStock->first()->selling_price ?? 0;
                                        @endphp
                                        <td>{{ $currentStock }}</td>
                                        <td>{{ $detail->quantity }}</td>
                                        <td>
                                            <input type="number" value="{{ $detail->quantity }}" max="{{ $detail->quantity }}" min="1" class="form-control shipped_quantity" readonly />
                                            <input type="hidden" value="{{ $detail->quantity + $detail->missing_quantity }}" max="{{ $detail->quantity + $detail->missing_quantity }}" class="max-quantity" />
                                        </td>
                                        <td>
                                            <input type="number" value="{{ $detail->missing_quantity }}" max="{{ $detail->quantity }}" min="0" class="form-control missing_quantity" readonly />
                                        </td>
                                        <td>
                                            <input type="number" value="0" max="" min="0" class="form-control remaining_quantity" readonly>
                                        </td>
                                        <td class="purchase_price">{{ number_format($detail->price_per_unit, 2) }}</td>
                                        <td class="ground_cost">{{ number_format($detail->ground_cost, 2) }}</td>
                                        <td>
                                            <input type="number" value="{{ $detail->profit_margin }}" min="0" class="form-control profit_margin" />
                                        </td>
                                        <td>{{ number_format($currentSellingPrice, 2) }}</td>
                                        <td class="selling_price">{{ number_format($detail->selling_price, 2) }}</td>
                                        <!-- <td>
                                            <button type="button" class="btn btn-danger remove-row"><i class="fas fa-trash"></i></button>
                                        </td> -->
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div class="col-sm-10 mt-5 mb-5">
                                <div class="row">
                                    <!-- Left Column -->
                                    <div class="col-sm-4">

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

                                    </div>

                                    <div class="col-sm-2">
                                    </div>

                                    <div class="col-sm-6">
                                        <div id="expense-container">
                                            @foreach($shipment->transactions as $index => $transaction)
                                                <div class="row mt-1 expense-row" id="expense-row-{{ $transaction->id }}" data-expense-id="{{ $transaction->chart_of_account_id }}">
                                                    <div class="col-sm-12 d-flex align-items-center">
                                                        <input type="hidden" class="id" value="{{ $transaction->id }}">
                                                        <select class="form-control expense-type" style="width: 200px;" onchange="checkDuplicateExpense(this)">
                                                            <option value="" disabled>Select Expense</option>
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
                                                        
                                                        @if($index == 0)
                                                            <button type="button" class="btn btn-success add-expense btn-sm d-none" style="margin-left: 10px;">
                                                                <i class="fas fa-plus"></i>
                                                            </button>
                                                        @else
                                                            <button type="button" class="btn btn-danger remove-expense btn-sm d-none" style="margin-left: 10px;">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>


                                    <div class="col-sm-12 d-flex justify-content-center mt-5">
                                        <button id="calculateSalesPriceBtn" class="btn btn-success" style="margin-left: 10px;">Update Sales Price</button>
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
    const addedExpenses = new Set();

    function calculateTotalAdditionalCost() {
        let total = 0;

        $('.expense-amount').each(function() {
            const value = parseFloat($(this).val()) || 0;
            total += value;
        });

        $('#total_additional_cost').val(total.toFixed(2));
    }

    function addExpenseRow() {
        const expenseDropdown = generateDropdownOptions();
        const uniqueId = Date.now();

        const row = `
            <div class="row mt-1 expense-row" id="row-${uniqueId}">
                <div class="col-sm-12 d-flex align-items-center">
                    <select class="form-control expense-type" style="width: 200px;" onchange="checkDuplicateExpense(this)">
                        <option value="" selected>Select Expense</option>
                        ${expenseDropdown}
                    </select>
                    <select class="form-control payment-type" style="width: 100px; margin-left: 10px;">
                        <option value="Bank">Bank</option>
                        <option value="Cash">Cash</option>
                    </select>
                    <input type="number" class="form-control expense-amount" style="width: 100px; margin-left: 10px;" min="0" placeholder="Amount">
                    <button type="button" class="btn btn-danger remove-expense btn-sm" style="margin-left: 10px;"><i class="fas fa-trash"></i></button>
                </div>
            </div>
        `;
        $('#expense-container').append(row);
    }

    function generateDropdownOptions() {
        return `
            @foreach($expenses as $expense)
                <option value="{{ $expense->id }}">{{ $expense->account_name }}</option>
            @endforeach
        `;
    }

    function checkDuplicateExpense(selectElement) {
        const selectedValue = $(selectElement).val();
        const rowElement = $(selectElement).closest('.expense-row');

        if (addedExpenses.has(selectedValue)) {
            swal({
                title: "Duplicate Expense",
                text: "This expense is already added!",
                icon: "warning",
            })
            $(selectElement).val("");
        } else {
            addedExpenses.add(selectedValue);
            rowElement.attr('data-expense-id', selectedValue);
        }
    }

    $(document).on('click', '.remove-expense', function () {
        const rowElement = $(this).closest('.expense-row');
        const expenseId = rowElement.attr('data-expense-id');

        if (expenseId) {
            addedExpenses.delete(expenseId);
        }

        rowElement.remove();
        calculateTotalAdditionalCost();
    });

    $(document).on('click', '.add-expense', function () {
        addExpenseRow();
    });

    $(document).on('input', '.expense-amount', function() {
        calculateTotalAdditionalCost();
    });
</script>

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
            let shipmentDetails = [];
            let expenses = [];

            $('#purchaseData tr').each(function() {
                let id = $(this).find('.id').val();
                let supplierId = $(this).find('.supplier_id').val();
                let purchaseHistoryId = $(this).find('.purchase_history_id').val();
                let productId = $(this).find('.product_id').val();
                let size = $(this).find('.product_size').val();
                let color = $(this).find('.product_color').val();
                let shippedQuantity = $(this).find('.shipped_quantity').val();
                let missingQuantity = $(this).find('.missing_quantity').val();
                let remainingQuantity = $(this).find('.remaining_quantity').val();
                let pricePerUnit = $(this).find('.purchase_price').text().replace(/,/g, '');
                let groundCost = $(this).find('.ground_cost').text().replace(/,/g, '');
                let profitMargin = $(this).find('.profit_margin').val().replace(/,/g, '');
                let sellingPrice = $(this).find('.selling_price').text().replace(/,/g, '');

                if (productId && shippedQuantity > 0) {
                    shipmentDetails.push({
                        id: id,
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

            $('.expense-row').each(function() {
                let transactionId = $(this).find('.id').val();
                let expenseId = $(this).find('.expense-type').val();
                let paymentType = $(this).find('.payment-type').val();
                let amount = $(this).find('.expense-amount').val();

                if (expenseId && amount > 0) {
                    expenses.push({
                        chart_of_account_id: expenseId,
                        transaction_id: transactionId,
                        payment_type: paymentType,
                        amount: parseFloat(amount)
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
                id: shipmentId,
                warehouse_id: warehouseId,
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
                        // location.reload();
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

                const quantity = parseInt($(this).find('.shipped_quantity').val());
                if (missingQuantity >= quantity) {
                    missingQuantity = quantity;
                    $(this).find('.missing_quantity').val(missingQuantity);
                }

                let updatedQuantity = maxQuantity - missingQuantity;

                if (updatedQuantity < 1) {
                    updatedQuantity = 1;
                }

                $(this).find('.shipped_quantity').val(updatedQuantity);

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
            // $('#total_additional_cost').val(totalSharedCosts.toFixed(2));
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