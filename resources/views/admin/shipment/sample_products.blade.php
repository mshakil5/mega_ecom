@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Sample Products</h3>
                    </div>
                    <div class="card-body">

                    <!-- Filter Form Section -->
                    <form id="filterForm">
                        <div class="row mb-3">
                            
                            <div class="col-md-3">
                                <label class="label label-primary">Product</label>
                                <select class="form-control select2" id="product_id" name="product_id">
                                    <option value="">Select...</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->product_code }}-{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="label label-primary">Warehouse</label>
                                <select class="form-control select2" id="warehouse_id" name="warehouse_id">
                                    <option value="">Select...</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}-{{ $warehouse->location }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <label class="label label-primary">Size</label>
                                <select class="form-control select2" id="size" name="size">
                                    <option value="">Select...</option>
                                    @foreach($sizes as $size)
                                        <option value="{{ $size }}">{{ $size }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <label class="label label-primary">Color</label>
                                <select class="form-control select2" id="color" name="color">
                                    <option value="">Select...</option>
                                    @foreach($colors as $color)
                                        <option value="{{ $color }}">{{ $color }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-2 d-none">
                                <label class="label label-primary">Zip</label>
                                <select class="form-control select2" id="zip" name="zip">
                                    <option value="">Select...</option>
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                            
                            <div class="col-md-1">
                                <label class="label label-primary" style="visibility:hidden;">Action</label>
                                <button type="submit" class="btn btn-secondary btn-block">Search</button>
                            </div>
                            <div class="col-md-1">
                                <label class="label label-primary" style="visibility:hidden;">Action</label>
                                <button type="button" id="reset-button" class="btn btn-secondary btn-block">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                    <!-- End of Filter Form Section -->

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="sampleProductsTable">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Date</th>
                                        <th>Product</th>
                                        <th>Size</th>
                                        <th>Color</th>
                                        <th>Quantity</th>
                                        <th>Warehouse</th>
                                        <th>Shipment Info</th>
                                        <th>Reason</th>
                                        <th>Distribution</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- DataTable will populate this -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- View Modal (existing) -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">Sample Product Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="viewDetails">
                    <!-- Details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal (existing) -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Sample Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editForm">
                @csrf
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_quantity">Quantity *</label>
                        <input type="number" class="form-control" id="edit_quantity" name="quantity" min="1" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_size">Size</label>
                        <input type="text" class="form-control" id="edit_size" name="size">
                    </div>
                    <div class="form-group">
                        <label for="edit_color">Color</label>
                        <input type="text" class="form-control" id="edit_color" name="color">
                    </div>
                    <div class="form-group">
                        <label for="edit_reason">Reason</label>
                        <textarea class="form-control" id="edit_reason" name="reason" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="distributeModal" tabindex="-1" aria-labelledby="distributeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="distributeModalLabel">Distribute to Wholesaler</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="distributeForm">
                @csrf
                <input type="hidden" id="dist_sample_product_id" name="sample_product_id">
                <div class="modal-body">
                    <div class="alert alert-info" id="productInfoAlert">
                        <!-- Product info will be loaded here -->
                    </div>

                    <div class="form-group">
                        <label for="dist_wholesaler_id">Wholesaler <span class="text-danger">*</span></label>
                        <select class="form-control select2" id="dist_wholesaler_id" name="wholesaler_id" required style="width: 100%;">
                            <option value="">Select Wholesaler</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="dist_assignment_date">Assignment Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="dist_assignment_date" name="assignment_date" required value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="form-group">
                        <label for="dist_quantity">Quantity <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="dist_quantity" name="quantity" min="1" required placeholder="Enter quantity">
                        <small class="form-text text-muted" id="distQuantityHelp"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Distribute
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="distributionListModal" tabindex="-1" aria-labelledby="distributionListModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="distributionListModalLabel">Distribution List</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="distributionListTable">
                        <thead class="thead-light">
                            <tr>
                                <th>Wholesaler</th>
                                <th>Quantity</th>
                                <th>Date</th>
                                <th>Assigned By</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody id="distributionListBody">
                        </tbody>
                        <tfoot>
                            <tr class="font-weight-bold">
                                <td>Total</td>
                                <td id="distributionTotal">0</td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    $(document).ready(function () {
        // Initialize DataTable
        var table = $('#sampleProductsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.sample.products.data') }}",
                error: function (xhr) {
                    let msg = xhr.responseText || "Something went wrong";
                    $("#search-results")
                        .removeClass("d-none")
                        .html(`<div class="p-2 text-danger small">${msg}</div>`);
                },
                data: function(d) {
                    d.warehouse_id = $('#warehouse_id').val();
                    d.product_id = $('#product_id').val();
                    d.color = $('#color').val();
                    d.size = $('#size').val();
                    d.zip = $('#zip').val();
                }
            },
            pageLength: 50,
            dom: 'Bfrtip',
            columns: [
                { data: 'sl', name: 'sl', orderable: false, searchable: false },
                { data: 'date', name: 'date' },
                { data: 'product_details', name: 'product_details' },
                { data: 'size_formatted', name: 'size' },
                { data: 'color_formatted', name: 'color' },
                { data: 'quantity_formatted', name: 'quantity' },
                { data: 'warehouse', name: 'warehouse' },
                { data: 'shipment_info', name: 'shipment_info' },
                { data: 'reason', name: 'reason' },
                { data: 'assignment_action', name: 'assignment_action', orderable: false, searchable: false },
            ],
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });

        // Filter form submission
        $('#filterForm').on('submit', function(e) {
            e.preventDefault();
            table.draw();
        });

        // Reset filters
        $('#reset-button').on('click', function() {
            $('#warehouse_id').val(null).trigger('change');
            $('#product_id').val(null).trigger('change');
            $('#color').val(null).trigger('change');
            $('#size').val(null).trigger('change');
            $('#zip').val(null).trigger('change');
            table.draw();
        });

        $(document).on('click', '.distribute-btn', function() {
            console.log(row);
            var sampleProductId = $(this).data('id');
            $('#dist_sample_product_id').val(sampleProductId);

            var row = table.row($(this).closest('tr')).data();
            
            var productInfo = '<strong>Product:</strong> ' + row.product_details;
            productInfo += '<br><strong>Total Qty:</strong> ' + row.quantity_formatted;
            productInfo += '<br><strong>Distributed:</strong> ' + row.distributed_quantity;
            productInfo += '<br><strong>Available:</strong> ' + row.available_qty;
            
            $('#productInfoAlert').html(productInfo);
            $('#distQuantityHelp').text('Maximum ' + row.available_qty + ' units available.');
            $('#dist_quantity').attr('max', row.available_qty).val('');

            loadWholesalers();
            $('#distributeModal').modal('show');
        });

        function loadWholesalers() {
            $.ajax({
                url: "/admin/sample-products/wholesalers",
                type: 'GET',
                success: function(response) {
                    var select = $('#dist_wholesaler_id');
                    select.find('option:not(:first)').remove();
                    
                    $.each(response, function(key, wholesaler) {
                        select.append('<option value="' + wholesaler.id + '">' + wholesaler.name + ' (' + wholesaler.email + ')</option>');
                    });

                    select.select2({
                        placeholder: "Select Wholesaler",
                        allowClear: true,
                        width: '100%'
                    });
                },
                error: function() {
                    aler('Error loading wholesalers');
                }
            });
        }

        $('#distributeForm').on('submit', function(e) {
            e.preventDefault();
            
            var maxQty = parseInt($('#dist_quantity').attr('max'));
            var quantity = parseInt($('#dist_quantity').val());

            if (quantity > maxQty) {
                swal({
                    text: "Quantity exceeds available amount",
                    icon: "success",
                    button: {
                        text: "OK",
                        className: "swal-button--confirm"
                    }
                });
                return;
            }

            $.ajax({
                url: "/admin/sample-products/assignment/store",
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        swal({
                            text: response.message,
                            icon: "success",
                            button: {
                                text: "OK",
                                className: "swal-button--confirm"
                            }
                        });
                        $('#distributeModal').modal('hide');
                        $('#distributeForm')[0].reset();
                        table.ajax.reload();
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            swal({
                                text: value[0],
                                icon: "success",
                                button: {
                                    text: "OK",
                                    className: "swal-button--confirm"
                                }
                            });
                        });
                    } else {
                        var response = xhr.responseJSON;
                        swal({
                            text: response.message,
                            icon: "success",
                            button: {
                                text: "OK",
                                className: "swal-button--confirm"
                            }
                        });
                    }
                }
            });
        });

        $(document).on('click', '.list-btn', function() {
            var sampleProductId = $(this).data('id');
            var row = table.row($(this).closest('tr')).data();

            var modalTitle = 'Distribution List - ' + row.product_details;
            $('#distributionListModalLabel').text(modalTitle);

            $.ajax({
                url: "/admin/sample-products/assignment/" + sampleProductId + "/list",
                type: 'GET',
                success: function(response) {
                    var tbody = $('#distributionListBody');
                    tbody.empty();

                    $.each(response.assignments, function(key, assignment) {
                        var row = '<tr>';
                        row += '<td>' + assignment.wholesaler.name + '</td>';
                        row += '<td>' + assignment.quantity + '</td>';
                        row += '<td>' + (assignment.assignment_date ? new Date(assignment.assignment_date).toLocaleDateString() : 'N/A') + '</td>';
                        row += '<td>' + (assignment.created_by ? assignment.created_by.name : 'System') + '</td>';
                        row += '<td>' + (assignment.created_at ? new Date(assignment.created_at).toLocaleDateString() : 'N/A') + '</td>';
                        row += '</tr>';
                        tbody.append(row);
                    });

                    $('#distributionTotal').text(response.total);
                    $('#distributionListModal').modal('show');
                },
                error: function() {
                    swal({
                        text: "Error loading distribution list",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    });
                }
            });
        });
    });
</script>
@endsection