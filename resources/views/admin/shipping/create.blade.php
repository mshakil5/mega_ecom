@extends('admin.layouts.admin')

@section('content')

<section class="content" id="newBtnSection">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 d-flex justify-content-start align-items-center">
                <button type="button" class="btn btn-secondary my-3 mr-2" id="newBtn" data-toggle="modal" data-target="#createShipmentModal">
                    Create Shipping
                </button>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="ermsg mt-3"></div>
</section>

<!-- Previous Shipment -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Shipping List</h3>
                    </div>
                    <div class="card-body">
                        <table id="shipmentTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Shipping ID</th>
                                    <th>Shipping Name</th>
                                    <th>Shipping Date</th>
                                    <th>Invoices</th>
                                    <th>Details</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $shipment)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $shipment->shipping_id }}</td>
                                    <td>{{ $shipment->shipping_name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($shipment->shipping_date)->format('d-m-Y') }}</td>
                                    <td>{{ $shipment->invoice_numbers  }}</td>
                                    <td>
                                        @if($shipment->shipment)
                                        <a href="{{ route('admin.shipment.edit', $shipment->id) }}" class="btn btn-info btn-sm">Pricing</a>
                                        @else
                                        <a href="{{ route('admin.shipment.create', $shipment->id) }}" class="btn btn-info btn-sm">Pricing</a>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-warning btn-sm"
                                            onclick="editShipment(this)"
                                            data-id="{{ $shipment->id }}"
                                            data-shipping-id="{{ $shipment->shipping_id }}"
                                            data-shipping-name="{{ $shipment->shipping_name }}"
                                            data-shipping-date="{{ $shipment->shipping_date }}"
                                            data-purchase-ids="{{ $shipment->purchase_ids }}">
                                            Edit
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal -->
<div class="modal fade" id="createShipmentModal" tabindex="-1" role="dialog" aria-labelledby="createShipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createShipmentModalLabel">Create Shipping</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="shipmentForm">
                    <input type="hidden" id="shipment_id" name="shipment_id">
                    <div class="form-group">
                        <label for="shipping_id">Shipping ID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="shipping_id" name="shipping_id" placeholder="Enter Shipping ID" required>
                        <div id="shipping_id_error"></div>
                    </div>
                    <div class="form-group">
                        <label for="shipping_name">Shipping Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="shipping_name" name="shipping_name" placeholder="Enter Shipping Name" required>
                    </div>
                    <div class="form-group">
                        <label for="shipping_date">Shipping Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="shipping_date" name="shipping_date" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label for="purchase_search">Search Invoice</label>
                        <input type="text" class="form-control" id="purchase_search" placeholder="Search by Invoice" oninput="searchPurchases()">
                        <div id="purchase_results" class="list-group mt-2" style="max-height: 200px; overflow-y: auto; display: none;"></div>
                    </div>
                    <input type="hidden" id="selected_purchase_ids" name="purchase_ids">
                    <div class="form-group">
                        <label>Selected Invoices <span class="text-danger">*</span></label>
                        <ul id="selected_invoices" class="list-group mt-2"></ul>
                    </div>
                    <button type="submit" class="btn btn-secondary" id="createShipmentBtn">Create Shipping</button>
                </form>
            </div>
        </div>
    </div>
</div>

<section class="content d-none">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-body">
                        <div class="row align-items-end">
                            <div class="col-sm-4">
                                <div class="form-group mb-0">
                                    <label for="invoice">Search Shipping <span class="text-danger">*</span>
                                        <small><span class="text-danger" id="shipmentError" style="display: none;"></span></small></label>
                                    <input type="text" class="form-control" id="shipment-id" name="shipment-id" placeholder="Enter shipment id" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="label label-primary" style="visibility:hidden;">Action</label>
                                <button type="button" id="searchShipmentBtn" class="btn btn-secondary w-100">Search</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="content d-none" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Price Calculation</h3>
                    </div>
                    <div class="card-body">
                        <div class="row mt-4">
                            <div class="col-md-12">

                            <div class="mb-4 d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Shipping ID:</strong> <span id="shippingId">-</span><br>
                                    <strong>Shipping Date:</strong> <span id="shippingDate">-</span><br>
                                    <strong>Shipping Name:</strong> <span id="shippingName">-</span>
                                </div>
                                <div>
                                    <strong>Total Product Quantity:</strong> <span id="totalQuantity">0</span><br>
                                    <strong>Total Missing Product Quantity:</strong> <span id="totalMissingQuantity">0</span>
                                </div>
                            </div>

                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Supplier</th>
                                            <th>Product</th>
                                            <th>Size</th>
                                            <th>Color</th>
                                            <th>Missing Quantity</th>
                                            <th>Net Quantity</th>
                                            <th>Purchase Price Per Unit</th>
                                            <th>Ground Price</th>
                                            <th>Profit Margin</th>
                                            <th>Selling Price</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="purchaseData">
                                        <!-- Appended data -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-sm-6 mt-5 mb-5">
                            <div class="row">
                                <!-- Left Column -->
                                <div class="col-sm-6">
                                    
                                    <div class="row mt-1">
                                        <div class="col-sm-12 d-flex align-items-center">
                                            <span>Total Purchase Cost:</span>
                                            <input type="text" class="form-control" id="direct_cost" style="width: 100px; margin-left: auto;" readonly>
                                            <input type="hidden" id="id" value="">
                                        </div>
                                    </div>

                                    <div class="row mt-1">
                                        <div class="col-sm-12 d-flex align-items-center">
                                            <span>CNF Cost:</span>
                                            <input type="number" class="form-control" id="cnf_cost" style="width: 100px; margin-left: auto;" min="0">
                                        </div>
                                    </div>

                                    <div class="row mt-1">
                                        <div class="col-sm-12 d-flex align-items-center">
                                            <span>Import Duties & Taxes:</span>
                                            <input type="number" class="form-control" id="import_taxes" style="width: 100px; margin-left: auto;" min="0">
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column -->
                                <div class="col-sm-6">
                                    <div class="row mt-1">
                                        <div class="col-sm-12 d-flex align-items-center">
                                            <span>Warehouse & Handling Costs:</span>
                                            <input type="number" class="form-control" id="warehouse_cost" style="width: 100px; margin-left: auto;" min="0">
                                        </div>
                                    </div>

                                    <div class="row mt-1">
                                        <div class="col-sm-12 d-flex align-items-center">
                                            <span>Other Costs:</span>
                                            <input type="number" class="form-control" id="other_cost" style="width: 100px; margin-left: auto;" min="0">
                                        </div>
                                    </div>

                                    <div class="row mt-1">
                                        <div class="col-sm-12 d-flex align-items-center">
                                            <span>Total Additional Cost:</span>
                                            <input type="number" class="form-control" id="total_additional_cost" style="width: 100px; margin-left: auto;" min="0" readonly>
                                        </div>
                                    </div>

                                    <div class="row mt-5">
                                        <div class="col-sm-12 d-flex justify-content-start">
                                            <button id="calculateSalesPriceBtn" class="btn btn-success" style="margin-left: 10px;">Calculate</button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    let allPurchases = @json($purchases);

    function editShipment(button) {
        const id = $(button).data('id');
        const shippingId = $(button).data('shipping-id');
        const shippingName = $(button).data('shipping-name');
        const shippingDate = $(button).data('shipping-date');
        const purchaseIds = $(button).data('purchase-ids');

        $('#shipment_id').val(id);
        $('#shipping_id').val(shippingId);
        $('#shipping_name').val(shippingName);
        $('#shipping_date').val(shippingDate);

        selectedInvoices = [];
        selectedPurchaseIds = [];
        $('#selected_invoices').empty();

        purchaseIds.forEach(function(purchaseId) {
            const purchase = allPurchases.find(p => p.id == purchaseId);
            if (purchase) {
                selectedInvoices.push(purchase.invoice);
                selectedPurchaseIds.push(purchase.id);
            }
        });

        updateSelectedInvoices();

        $('#createShipmentModalLabel').text('Update Shipping');
        $('#createShipmentBtn').text('Update Shipping');

        $('#createShipmentModal').modal('show');
    }

    function updateSelectedInvoices() {
        $('#selected_invoices').empty();
        selectedInvoices.forEach(function(invoice, index) {
            $('#selected_invoices').append(`
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    ${invoice}
                    <button class="btn btn-danger btn-sm" onclick="removeInvoice('${index}')">Remove</button>
                </li>
            `);
        });

        $('#selected_purchase_ids').val(selectedPurchaseIds.join(','));
    }

    function removeInvoice(index) {
        selectedInvoices.splice(index, 1);
        selectedPurchaseIds.splice(index, 1);
        updateSelectedInvoices();
    }
</script>

@endsection

@section('script')

<script>
    $(document).ready(function() {
        $('#calculateSalesPriceBtn').on('click', function() {
            let idValue = $('#id').val();
            let totalQuantity = $('#totalQuantity').text();
            let totalMissingQuantity = $('#totalMissingQuantity').text();
            let direct_cost = $('#direct_cost').val();
            let cnf_cost = $('#cnf_cost').val();
            let import_taxes = $('#import_taxes').val();
            let warehouse_cost = $('#warehouse_cost').val();
            let other_cost = $('#other_cost').val();
            let total_additional_cost = $('#total_additional_cost').val();

            let shipmentDetails = [];
            let productSelected = false;

            $('#purchaseData tr').each(function () {
                let supplierId = $(this).find('.supplier_id').val();
                let productId = $(this).find('.product_id').val();
                let size = $(this).find('td:nth-child(3)').text();
                let color = $(this).find('td:nth-child(4)').text();
                let quantity = $(this).find('.product_quantity').val();
                let missingQuantity = $(this).find('.missing_quantity').val();
                let pricePerUnit = $(this).find('.purchase_price').text();
                let groundCost = $(this).find('.ground_cost').text();
                let profitMargin = $(this).find('.profit_margin').val();
                let sellingPrice = $(this).find('.selling_price').text();

                if (productId && quantity > 0) {
                    shipmentDetails.push({
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
                    productSelected = true;
                }
            });

            if (!productSelected) {
                $(".ermsg").html(`
                    <div class='alert alert-danger'>
                        <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                        <b>Please add at least one product to the shipment.</b>
                    </div>
                `).show();
                return;
            }

            let dataToSend = {
                id: idValue,
                total_quantity: totalQuantity,
                total_missing_quantity: totalMissingQuantity,
                direct_cost: direct_cost,
                cnf_cost: cnf_cost,
                import_taxes: import_taxes,
                warehouse_cost: warehouse_cost,
                other_cost: other_cost,
                total_additional_cost: total_additional_cost,
                shipment_details: shipmentDetails
            };
            // console.log(dataToSend);

            _token = $('meta[name="csrf-token"]').attr('content');

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
                            <b>Shipment stored successfully.</b>
                        </div>
                    `);
                    $(".ermsg").show();

                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        });
    });
</script>

<script>
    $(document).ready(function() {

        $('#previousShipmentBtn').on('click', function() {
            $('#createShipmentModal').modal('hide');
            $('#previousShipmentSection').toggle();
        });

        $('#newBtn').on('click', function() {
            $('#createShipmentModal').modal('show');
            $('#previousShipmentSection').hide();
        });

        $('#shipmentTable').DataTable();
    });
</script>

<!-- Make shipping -->
<script>
    let selectedInvoices = [];
    let selectedPurchaseIds = [];

    function searchPurchases() {
        const query = $('#purchase_search').val();
        if (query.length > 0) {
            $.ajax({
                url: '/admin/search-purchases',
                method: 'GET',
                data: {
                    invoice: query
                },
                success: function(data) {
                    if (data.id) {
                        if (!selectedInvoices.includes(data.invoice)) {
                            $('#purchase_results').html(`
                                <a href="#" class="list-group-item list-group-item-action" onclick="selectPurchase('${data.id}', '${data.invoice}')">${data.invoice}</a>
                            `).show();
                        } else {
                            $('#purchase_results').html('<div class="list-group-item">Invoice already added.</div>').show();
                        }
                    } else {
                        $('#purchase_results').html('<div class="list-group-item">No matching purchase found.</div>').show();
                    }
                },
                error: function() {
                    $('#purchase_results').html('<div class="list-group-item">No matching purchase found.</div>').show();
                }
            });
        } else {
            $('#purchase_results').hide();
        }
    }

    function selectPurchase(id, invoice) {
        if (!selectedInvoices.includes(invoice)) {
            selectedInvoices.push(invoice);
            selectedPurchaseIds.push(id);
            $('#purchase_search').val('');
            $('#purchase_results').hide();
            updateSelectedInvoices();
        } else {
            alert('This invoice is already added.');
        }
    }

    function updateSelectedInvoices() {
        $('#selected_invoices').empty();
        selectedInvoices.forEach(function(invoice, index) {
            $('#selected_invoices').append(`
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    ${invoice}
                    <button class="btn btn-danger btn-sm" onclick="removeInvoice('${index}')">Remove</button>
                </li>
            `);
        });

        $('#selected_purchase_ids').val(selectedPurchaseIds.join(','));
    }

    function removeInvoice(index) {
        const invoice = selectedInvoices[index];
        const purchaseId = selectedPurchaseIds[index];
        selectedInvoices.splice(index, 1);
        selectedPurchaseIds.splice(index, 1);
        updateSelectedInvoices();
    }

    $('#shipmentForm').on('submit', function(e) {
        e.preventDefault();

        if (selectedPurchaseIds.length === 0) {
            alert('Please select at least one purchase ID before submitting.');
            return;
        }

        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        const formData = {
            shipping_id: $('#shipping_id').val(),
            shipping_name: $('#shipping_name').val(),
            shipping_date: $('#shipping_date').val(),
            purchase_ids: selectedPurchaseIds,
            _token: csrfToken
        };

        const shipmentId = $('#shipment_id').val();

        if (shipmentId) {

            formData.id = shipmentId;


            $.ajax({
                url: '/admin/update-shipment/' + shipmentId,
                method: 'PUT',
                data: formData,
                success: function(response) {
                    $('#createShipmentModal').modal('hide');
                    $('#shipmentForm')[0].reset();
                    selectedInvoices = [];
                    selectedPurchaseIds = [];
                    updateSelectedInvoices();

                    $(".ermsg").html(`
                        <div class='alert alert-success'>
                            <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                            <b>Data Updated Successfully.</b>
                        </div>
                    `);
                    $(".ermsg").show();

                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                },
                error: function(xhr) {
                    console.error('Error updating shipment:', xhr.responseJSON);
                    alert('Error updating shipment: ' + xhr.responseJSON.message);
                }
            });
        } else {
            $.ajax({
                url: '/admin/create-shipment',
                method: 'POST',
                data: formData,
                success: function(response) {
                    $('#createShipmentModal').modal('hide');
                    $('#shipmentForm')[0].reset();
                    selectedInvoices = [];
                    selectedPurchaseIds = [];
                    updateSelectedInvoices();

                    $(".ermsg").html(`
                        <div class='alert alert-success'>
                            <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                            <b>Data Created Successfully.</b>
                        </div>
                    `);

                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                },
                error: function(xhr) {
                    console.error('Error creating shipment:', xhr.responseJSON);
                    alert('Error creating shipment: ' + xhr.responseJSON.message);
                }
            });
        }
    });

    $('#shipping_id').on('input', function () {
        const shippingId = $(this).val().trim();
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        const shipmentId = $('#shipment_id').val();

        if (shippingId !== '') {
            $.ajax({
                url: '/admin/check-shipping-id',
                method: 'POST',
                data: {
                    shipping_id: shippingId,
                    shipment_id: shipmentId,
                    _token: csrfToken
                },
                success: function (response) {
                    if (response.exists) {
                        $('#shipping_id_error').html(`
                            <small class="text-danger">Shipping ID already exists. Please use a different ID.</small>
                        `);
                        $('#createShipmentBtn').prop('disabled', true);
                    } else {
                        $('#shipping_id_error').html('');
                        $('#createShipmentBtn').prop('disabled', false);
                    }
                },
                error: function (xhr) {
                    console.error('Error checking Shipping ID:', xhr.responseText);
                }
            });
        } else {
            $('#shipping_id_error').html('');
            $('#createShipmentBtn').prop('disabled', false);
        }
    });


    $('#createShipmentModal').on('hidden.bs.modal', function() {
        $('#shipmentForm')[0].reset();

        selectedInvoices = [];
        selectedPurchaseIds = [];
        $('#selected_invoices').empty();

        $('#createShipmentModalLabel').text('Create Shipping');
        $('#shipmentSubmitButton').text('Create Shipping');
    });
</script>

<!-- Search Shipping and calculate sales price -->
<script>
    $('#searchShipmentBtn').on('click', function(e) {
        e.preventDefault();
        const shippingId = $('#shipment-id').val();

        if (!shippingId) {
            $('#shipmentError').text('Please enter a valid shipping ID.').show();
            return;
        }

        $('#shipmentError').hide();

        $.ajax({
            url: '/admin/search-shipment',
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                shipping_id: shippingId
            },
            success: function(response) {
                // console.log(response);
                if (response.success) {
                    const tableBody = $('#purchaseData');
                    tableBody.empty();

                    $('#id').val(response.id);
                    $('#shippingId').text(response.shipping_id);
                    $('#shippingDate').text(response.shipping_date);
                    $('#shippingName').text(response.shipping_name);

                    response.purchase_histories.forEach(history => {
                        const supplierName = history.purchase?.supplier?.name ?? '';
                        const productName = `${history.product?.product_code ? history.product.product_code + '-' : ''}${history.product?.name ?? ''}`;
                        const productSize = history.product_size ?? '';
                        const productColor = history.product_color ?? '';
                        const productQuantity = history.quantity ?? '';
                        const purchasePrice = history.purchase_price.toFixed(2);

                        const row = `
                            <tr>
                                <td>
                                    ${supplierName}
                                    <input type="hidden" value="${history.purchase.supplier_id}" class="supplier_id">
                                    <input type="hidden" value="${history.product_id}" class="product_id">
                                </td>
                                <td>${productName}</td>
                                <td>${productSize}</td>
                                <td>${productColor}</td>
                                <td>
                                    <input type="number" value="0" max="${productQuantity}" min="0" class="form-control missing_quantity" />
                                </td>
                                <td>
                                    <input type="number" value="${productQuantity}" max="${productQuantity}" min="1" class="form-control product_quantity" />
                                </td>
                                <td class="purchase_price">${purchasePrice}</td>
                                <td class="ground_cost">0.00</td>
                                <td>
                                    <input type="number" value="30" min="1" class="form-control profit_margin" />
                                </td>
                                <td class="selling_price">0.00</td>
                                <td>
                                    <button class="btn btn-danger remove-row"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>`;
                        tableBody.append(row);
                    });

                    updateCalculations();
                    $('.remove-row').on('click', function() {
                        $(this).closest('tr').remove();
                        updateCalculations();
                    });
                } else {
                    $('#shipmentError').text(response.message).show();
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
            },
        });
    });

    function updateCalculations() {
        const tableRows = $('#purchaseData tr');
        let totalPurchaseCost = 0;
        let totalQuantity = 0;
        let totalMissingQuantity = 0;

        tableRows.each(function () {
            const purchasePrice = parseFloat($(this).find('.purchase_price').text());
            let missingQuantity = parseInt($(this).find('.missing_quantity').val()) || 0;
            const maxQuantity = parseInt($(this).find('.product_quantity').attr('max'));

            if (missingQuantity >= maxQuantity) {
                missingQuantity = maxQuantity - 1;
                $(this).find('.missing_quantity').val(missingQuantity);
            }

            let updatedQuantity = maxQuantity - missingQuantity;

            if (updatedQuantity < 1) {
                updatedQuantity = 1;
            }

            $(this).find('.product_quantity').val(updatedQuantity);

            const productTotal = purchasePrice * updatedQuantity;

            totalPurchaseCost += productTotal;
            totalQuantity += updatedQuantity;
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
</script>

@endsection