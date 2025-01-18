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

                                    <div class="col-sm-4">

                                        <div class="row mt-1">
                                            <div class="col-sm-12 d-flex align-items-center">
                                                <span>Total Profit:</span>
                                                <input type="number" class="form-control" id="total_profit" style="width: 100px; margin-left: auto;" min="0" readonly>
                                                <input id="budgetDifference" type="hidden">
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-sm-12 d-flex align-items-center">
                                                <span>Target Budget:</span>
                                                <input type="number" class="form-control" id="target_budget" style="width: 100px; margin-left: auto;" min="0">
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-sm-12 d-flex align-items-center">
                                                <span>Total Purchase Cost:</span>
                                                <input type="text" class="form-control" id="direct_cost" style="width: 100px; margin-left: auto;" readonly value="">
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-sm-12 d-flex align-items-center">
                                                <span>Total Additional Cost:</span>
                                                <input type="number" class="form-control" id="total_additional_cost" style="width: 100px; margin-left: auto;" min="0" readonly value="">
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-sm-12 d-flex align-items-center">
                                                <span>Total Cost Of This Shipment:</span>
                                                <input type="number" class="form-control" id="total_cost_of_the_shipment" style="width: 100px; margin-left: auto;" min="0" readonly value="">
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-sm-12 d-flex align-items-center">
                                                <span>Total Quantiy In PCS:</span>
                                                <input type="number" class="form-control" id="totalQuantityInPcs" style="width: 100px; margin-left: auto;"  value="{{ $shipping->total_product_quantity }}" readonly>
                                            </div>
                                        </div>

                                        <div class="row mt-2">
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
                                            <div class="row mt-1 expense-row" id="row-default">
                                                <div class="col-sm-12 d-flex align-items-center">
                                                    <select class="form-control expense-type" style="width: 200px;" onchange="checkDuplicateExpense(this)">
                                                        <option value="" selected>Select Expense</option>
                                                        @foreach($expenses as $expense)
                                                            <option value="{{ $expense->id }}">{{ $expense->account_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <select class="form-control payment-type" style="width: 100px; margin-left: 10px;">
                                                        <option value="Bank">Bank</option>
                                                        <option value="Cash">Cash</option>
                                                    </select>
                                                    <input type="number" class="form-control expense-amount" style="width: 100px; margin-left: 10px;" min="0" placeholder="Amount">                              
                                                    <input type="text" class="form-control expense-description" style="width: 150px; margin-left: 10px;" min="0" placeholder="Description">
                                                    <input type="text" class="form-control expense-note" style="width: 150px; margin-left: 10px;" min="0" placeholder="Note">
                                                    <button type="button" class="btn btn-success add-expense btn-sm" style="margin-left: 10px;"><i class="fas fa-plus"></i></button>
                                                </div>
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
                    <input type="text" class="form-control expense-description" style="width: 150px; margin-left: 10px;" min="0" placeholder="Description">
                    <input type="text" class="form-control expense-note" style="width: 150px; margin-left: 10px;" min="0" placeholder="Note">
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
        updateCosts();
        updateCalculations();
    });

    $(document).on('click', '.add-expense', function () {
        addExpenseRow();
    });

    $(document).on('input', '.expense-amount', function() {
        calculateTotalAdditionalCost();
        updateCosts();
        updateCalculations();
    });

    const $targetBudget = $('#target_budget');
    const $directCost = $('#direct_cost');
    const $additionalCost = $('#total_additional_cost');
    const $totalCost = $('#total_cost_of_the_shipment');
    const $messageContainer = $('#budget-message');
    const $messageText = $messageContainer.find('span');

    function updateCosts() {
        const targetBudget = parseFloat($targetBudget.val()) || 0;
        const directCost = parseFloat($directCost.val()) || 0;
        const additionalCost = parseFloat($additionalCost.val()) || 0;

        const totalCost = directCost + additionalCost;
        $totalCost.val(totalCost.toFixed(2));

        if (targetBudget > 0) {
            const difference = targetBudget - totalCost;
            $('#budgetDifference').val(difference.toFixed(2));
            $messageContainer.removeClass('d-none');

            if (difference < 0) {
                $messageText.html(`<span style="color: red;">You're over budget by ${difference.toFixed(2)}</span>`);
            } else {
                $messageText.html(`<span style="color: green;">You're under budget by ${difference.toFixed(2)}</span>`);
            }
        } else {
            $messageContainer.addClass('d-none');
        }
    }

    $targetBudget.on('input', updateCosts);
    $directCost.on('input', updateCosts);
    $additionalCost.on('input', updateCosts);

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
        $('#totalQuantityInPcs').val(totalQuantity);
        $('#totalMissingQuantity').text(totalMissingQuantity);

        const totalCostOfShipment = parseFloat($('#total_cost_of_the_shipment').val()) || 0;

        if (totalQuantity > 0) {
            const costPerPiece = totalCostOfShipment / totalQuantity;
            $('#costPerPieces').val(costPerPiece.toFixed(2));
        } else {
            $('#costPerPieces').val('0.00');
        }

        const totalSharedCosts = parseFloat($('#total_additional_cost').val()) || 0;

        const totalShipmentCost = totalPurchaseCost + totalSharedCosts;
        $('#direct_cost').val(totalPurchaseCost.toFixed(2));

        tableRows.each(function() {
            const productTotal = parseFloat($(this).find('.ground_cost').text());
            const quantity = parseInt($(this).find('.shipped_quantity').val());

            const sharedCostForProduct = (productTotal / totalPurchaseCost) * totalSharedCosts;
            const groundCostPerUnit = (productTotal + sharedCostForProduct) / quantity;

            $(this).find('.ground_cost').text(groundCostPerUnit.toFixed(2));
        });

        let totalProfit = 0;

        tableRows.each(function() {
            const groundCostPerUnit = parseFloat($(this).find('.ground_cost').text());
            const profitMargin = parseFloat($(this).find('.profit_margin').val()) || 0;
            const shippedQuantity = parseFloat($(this).find('.shipped_quantity').val()) || 0;

            const sellingPrice = groundCostPerUnit * (1 + profitMargin / 100);
            const profitAmount = (sellingPrice - groundCostPerUnit) * shippedQuantity;

            $(this).find('.selling_price').text(sellingPrice.toFixed(2));
            totalProfit += profitAmount;
        });

        $('#total_profit').val(totalProfit.toFixed(2));
    }

</script>

<script>
    $(document).ready(function() {

        $('#calculateSalesPriceBtn').on('click', function(event) {
            event.preventDefault();

            if (!confirm("Are you sure you want to proceed with creating the shipment?")) {
                return;
            }

            let shippingId = $('#shipping_id').val();
            let warehouseId = $('#warehouse_id').val();
            let target_budget = $('#target_budget').val();
            let totalProfit = $('#total_profit').val();
            let budget_over = $('#budgetDifference').val();
            let total_cost_of_the_shipment = $('#total_cost_of_the_shipment').val();

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
            let expenses = [];

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

            $('.expense-row').each(function() {
                let expenseId = $(this).find('.expense-type').val();
                let paymentType = $(this).find('.payment-type').val();
                let amount = $(this).find('.expense-amount').val();
                let description = $(this).find('.expense-description').val();
                let note = $(this).find('.expense-note').val();


                if (expenseId && amount > 0) {
                    expenses.push({
                        chart_of_account_id: expenseId,
                        payment_type: paymentType,
                        amount: parseFloat(amount),
                        description: description,
                        note: note
                    });
                }
            });

            if (!expenses || expenses.length === 0) {
                $(".ermsg").html(`
                    <div class='alert alert-danger'>
                        <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                        <b>Please add at least one expense before proceeding.</b>
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
                return;
            }

            let dataToSend = {
                shipping_id: shippingId,
                warehouse_id: warehouseId,
                total_quantity: totalQuantity,
                total_missing_quantity: totalMissingQuantity,
                total_purchase_cost: $('#direct_cost').val().replace(/,/g, ''),
                total_additional_cost: $('#total_additional_cost').val().replace(/,/g, ''),
                total_profit: totalProfit,
                target_budget: target_budget,
                budget_over: budget_over,
                total_cost_of_shipment: total_cost_of_the_shipment,
                shipment_details: shipmentDetails,
                expenses: expenses
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
            $('#totalQuantityInPcs').val(totalQuantity);
            $('#totalMissingQuantity').text(totalMissingQuantity);

            const totalCostOfShipment = parseFloat($('#total_cost_of_the_shipment').val()) || 0;

            if (totalQuantity > 0) {
                const costPerPiece = totalCostOfShipment / totalQuantity;
                $('#costPerPieces').val(costPerPiece.toFixed(2));
            } else {
                $('#costPerPieces').val('0.00');
            }

            const totalSharedCosts = parseFloat($('#total_additional_cost').val()) || 0;

            const totalShipmentCost = totalPurchaseCost + totalSharedCosts;
            $('#direct_cost').val(totalPurchaseCost.toFixed(2));

            tableRows.each(function() {
                const productTotal = parseFloat($(this).find('.ground_cost').text());
                const quantity = parseInt($(this).find('.shipped_quantity').val());

                const sharedCostForProduct = (productTotal / totalPurchaseCost) * totalSharedCosts;
                const groundCostPerUnit = (productTotal + sharedCostForProduct) / quantity;

                $(this).find('.ground_cost').text(groundCostPerUnit.toFixed(2));
            });

            let totalProfit = 0;

            tableRows.each(function() {
                const groundCostPerUnit = parseFloat($(this).find('.ground_cost').text());
                const profitMargin = parseFloat($(this).find('.profit_margin').val()) || 0;
                const shippedQuantity = parseFloat($(this).find('.shipped_quantity').val()) || 0;

                const sellingPrice = groundCostPerUnit * (1 + profitMargin / 100);
                const profitAmount = (sellingPrice - groundCostPerUnit) * shippedQuantity;

                $(this).find('.selling_price').text(sellingPrice.toFixed(2));
                totalProfit += profitAmount;
            });

            $('#total_profit').val(totalProfit.toFixed(2));
        }

        $(document).on('input', '.shipped_quantity, .missing_quantity, .profit_margin', function() {
            updateCalculations();
            updateCosts();
        });

        updateCalculations();

        $('#warehouse_id').select2({
            placeholder: "Select a Warehouse",
            allowClear: true
        });

        const $targetBudget = $('#target_budget');
        const $directCost = $('#direct_cost');
        const $additionalCost = $('#total_additional_cost');
        const $totalCost = $('#total_cost_of_the_shipment');
        const $messageContainer = $('#budget-message');
        const $messageText = $messageContainer.find('span');

        function updateCosts() {
            const targetBudget = parseFloat($targetBudget.val()) || 0;
            const directCost = parseFloat($directCost.val()) || 0;
            const additionalCost = parseFloat($additionalCost.val()) || 0;

            const totalCost = directCost + additionalCost;
            const $totalCost = $('#total_cost_of_the_shipment');

            if (targetBudget > 0) {
                const difference = targetBudget - totalCost;
                $('#budgetDifference').val(difference.toFixed(2));
                $messageContainer.removeClass('d-none');
                if (difference < 0) {
                    $messageText.html(`<span style="color: red;">You're over budget by ${difference.toFixed(2)}</span>`);
                } else {
                    $messageText.html(`<span style="color: green;">You're under budget by ${difference.toFixed(2)}</span>`);
                }
            } else {
                $messageContainer.addClass('d-none');
            }
        }
    });
</script>

@endsection