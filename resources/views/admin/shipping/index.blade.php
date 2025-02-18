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
    <div class="ermsg"></div>
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
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Date</th>
                                    <th>Invoices</th>
                                    <th>Shipped Qty</th>
                                    <th>Missing Qty</th>
                                    <th>Purchase Cost</th>
                                    <th>Additional Cost</th>
                                    <th>Total Cost</th>
                                    <th>Status</th>
                                    <th>Details</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $shipping)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $shipping->shipping_id }}</td>
                                    <td>{{ $shipping->shipping_name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($shipping->shipping_date)->format('d-m-Y') }}</td>
                                    <td>{{ $shipping->invoice_numbers  }}</td>
                                    <td>{{ $shipping->shipment ? $shipping->shipment->total_product_quantity : '' }}</td>
                                    <td>{{ $shipping->shipment ? $shipping->shipment->total_missing_quantity : '' }}</td>
                                    <td>{{ $shipping->shipment ? $shipping->shipment->total_purchase_cost : '' }}</td>
                                    <td>{{ $shipping->shipment ? $shipping->shipment->total_additional_cost : '' }}</td>
                                    <td>{{ $shipping->shipment ? $shipping->shipment->total_purchase_cost + $shipping->shipment->total_additional_cost : '' }}</td>
                                    <td style="width: 100%;">
                                        @if($shipping)
                                            @if($shipping->status == 3)
                                            <span class="btn btn-sm btn-success">Received</span>
                                            @else
                                            <select class="form-control shipping-status" data-shipping-id="{{ $shipping->id }}" data-shipment-id="{{ $shipping->shipment ? $shipping->shipment->id : '' }}">
                                                <option value="1" {{ $shipping->status == 1 ? 'selected' : '' }}>Processing</option>
                                                <option value="2" {{ $shipping->status == 2 ? 'selected' : '' }}>On The Way</option>
                                               @if($shipping->shipment) <option value="3" {{ $shipping->status == 3 ? 'selected' : '' }}>Received</option> @endif
                                                <!-- <option value="4" {{ $shipping->status == 4 ? 'selected' : '' }}>Stocking Completed</option> -->
                                            </select>
                                            @endif
                                        @endif
                                    </td>

                                    <td>
                                        @if($shipping->shipment)
                                            <span class="badge bg-success ms-2">Price Added</span>
                                            @if($shipping->status != 3)
                                            <a href="{{ route('admin.shipment.edit', $shipping->shipment->id) }}" class="btn btn-info btn-sm"><i class="fa fa-pencil"></i></a>
                                            @endif
                                            <a href="{{ route('admin.shipment.print', $shipping->shipment->id) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            <button class="btn btn-sm btn-primary view-details" 
                                                    data-shipping-id="{{ $shipping->shipping_id }}"
                                                    data-shipping-date="{{ \Carbon\Carbon::parse($shipping->shipping_date)->format('d-m-Y') }}"
                                                    data-shipping-name="{{ $shipping->shipping_name }}"
                                                    data-total-quantity="{{ $shipping->shipment->total_product_quantity }}"
                                                    data-total-missing-quantity="{{ $shipping->shipment->total_missing_quantity }}"
                                                    data-total-purchase="{{ $shipping->shipment->total_purchase_cost }}"
                                                    data-total-additional="{{ $shipping->shipment->total_additional_cost }}"
                                                    data-warehouse-name="{{ $shipping->shipment->shipmentDetails[0]->warehouse->name }}"
                                                    data-shipment-details="{{ json_encode($shipping->shipment->shipmentDetails) }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        @else
                                            <a href="{{ route('admin.shipment.create', $shipping->id) }}" class="btn btn-info btn-sm">Set Price</a>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!$shipping->shipment) 
                                           <button class="btn btn-warning btn-sm"
                                            onclick="editShipment(this)"
                                            data-id="{{ $shipping->id }}"
                                            data-shipping-id="{{ $shipping->shipping_id }}"
                                            data-shipping-name="{{ $shipping->shipping_name }}"
                                            data-shipping-date="{{ $shipping->shipping_date }}"
                                            data-purchase-ids="{{ $shipping->purchase_ids }}"
                                            >
                                            Edit
                                        </button>  @endif
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

<!-- Modal -->
<div class="modal fade" id="shipmentModal" tabindex="-1" role="dialog" aria-labelledby="shipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title" id="cardTitle">Shipping Details</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
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
                                <div class="text-right">
                                    <div>
                                        <strong>Total Product Quantity:</strong> <span id="totalQuantity">0</span>
                                    </div>
                                    <div>
                                        <strong>Total Missing Product Quantity:</strong> <span id="totalMissingQuantity">0</span>
                                    </div>
                                    <div>
                                        <strong>Total Purchase Cost:</strong> <span id="totalPurchase">0</span>
                                    </div>
                                    <div>
                                        <strong>Total Additional Cost:</strong> <span id="totalAdditional">0</span>
                                    </div>
                                    <div>
                                        <strong>Warehouse:</strong> <span id="warehouseName"></span>
                                    </div>
                                </div>
                            </div>

                            <table class="table table-bordered" id="example2">
                                <thead>
                                    <tr>
                                        <th>Supplier</th>
                                        <th>Product</th>
                                        <th>Size</th>
                                        <th>Color</th>        
                                        <th>Shipped Qty</th>
                                        <th>Missing Qty</th>
                                        <th>Purchase Price Per Unit</th>
                                        <th>Ground Price</th>
                                        <th>Profit Margin</th>
                                        <th>Selling Price</th>
                                    </tr>
                                </thead>
                                <tbody id="purchaseData">
                                    <!-- Appended data -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.view-details').forEach(button => {
            button.addEventListener('click', function () {
                const shippingId = this.getAttribute('data-shipping-id');
                const shippingDate = this.getAttribute('data-shipping-date');
                const shippingName = this.getAttribute('data-shipping-name');
                const totalQuantity = this.getAttribute('data-total-quantity');
                const totalMissingQuantity = this.getAttribute('data-total-missing-quantity');
                const totalPurchase = this.getAttribute('data-total-purchase');
                const totalAdditional = this.getAttribute('data-total-additional');
                const warehouseName = this.getAttribute('data-warehouse-name');
                const shipmentDetails = JSON.parse(this.getAttribute('data-shipment-details'));

                document.getElementById('shippingId').textContent = shippingId;
                document.getElementById('shippingDate').textContent = shippingDate;
                document.getElementById('shippingName').textContent = shippingName;
                document.getElementById('totalQuantity').textContent = totalQuantity;
                document.getElementById('totalMissingQuantity').textContent = totalMissingQuantity;
                document.getElementById('totalPurchase').textContent = totalPurchase;
                document.getElementById('totalAdditional').textContent = totalAdditional;
                document.getElementById('warehouseName').textContent = warehouseName;

                const purchaseDataBody = document.getElementById('purchaseData');
                purchaseDataBody.innerHTML = '';

                if ($.fn.DataTable.isDataTable('#example2')) {
                    $('#example2').DataTable().clear().destroy();
                }

                shipmentDetails.forEach(detail => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${detail.supplier.name}</td>
                        <td>${detail.product && detail.product.product_code ? `${detail.product.product_code} - ${detail.product.name}` : (detail.product ? detail.product.name : '')}</td>
                        <td>${detail.size || '-'}</td>
                        <td>${detail.color || '-'}</td>
                        <td>${detail.quantity}</td>
                        <td>${detail.missing_quantity}</td>
                        <td>${detail.price_per_unit}</td>
                        <td>${detail.ground_price_per_unit}</td>
                        <td>${detail.profit_margin}%</td>
                        <td>${detail.selling_price}</td>
                    `;
                    purchaseDataBody.appendChild(row);
                });

                $('#example2').DataTable({
                    "responsive": true,
                    "lengthChange": false,
                    "autoWidth": false,
                    "buttons": ["copy", "csv", "excel", "pdf", "print"]
                }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');

                $('#shipmentModal').modal('show');
            });
        });
    });
</script>

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

        $('#previousShipmentBtn').on('click', function() {
            $('#createShipmentModal').modal('hide');
            $('#previousShipmentSection').toggle();
        });

        $('#newBtn').on('click', function() {
            $('#createShipmentModal').modal('show');
            $('#previousShipmentSection').hide();
        });

        $('#shipmentTable').DataTable({
            pageLength: 50,
            lengthMenu: [10, 25, 50, 100],
            dom: 'Bfrtip',
            buttons: [
                'copy', 
                'csv', 
                'excel',
                'pdf',  
                'print'
            ]
        });

        $(document).on('change', '.shipping-status', function() {
            const shippingId = $(this).data('shipping-id');
            const shipmentId = $(this).data('shipment-id');
            const status = $(this).val();

            if( status == '3'){
                window.location.href = "{{ route('admin.shipment.edit', ['id' => 'ID', 'status' => 'STATUS']) }}".replace('ID',shipmentId).replace('STATUS','received');
            } else { $.ajax({
                url: '/admin/shipping/update-status',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    shipping_id: shippingId,
                    status: status
                },
                success: function(response) {
                    swal({
                        text: "Shipment Status Updated",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                    swal({
                        text: "An error occurred while updating the shipment status.",
                        icon: "error",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    });
                }
            });
            }
        });

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
                method: 'POST', 
                data: {
                    invoice: query,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {                
                    if (data.status === '400') {
                        $('#purchase_results').html('<div class="list-group-item">This invoice has already been completed (all products shipped).</div>').show();
                    } else if (data.id) {
                        if (!selectedInvoices.includes(data.invoice)) {
                            $('#purchase_results').html(`
                                <a href="#" class="list-group-item list-group-item-action" onclick="selectPurchase('${data.id}', '${data.invoice}')">${data.invoice}</a>
                            `).show();
                        } else {
                            $('#purchase_results').html('<div class="list-group-item">Invoice already added.</div>').show();
                        }
                    } else {
                        $('#purchase_results').html('<div class="list-group-item">This invoice has already been completed.</div>').show();
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
                            <b>Updated Successfully.</b>
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
                            <b>Created Successfully.</b>
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

    $('#shipping_id').on('input', function() {
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
                success: function(response) {
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
                error: function(xhr) {
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

@endsection