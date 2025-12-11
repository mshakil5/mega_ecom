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
                                        {{-- <th>Purchase</th> --}}
                                        {{-- <th>Zip</th> --}}
                                        <th>Reason</th>
                                        {{-- <th>Added By</th> --}}
                                        {{-- <th>Action</th> --}}
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

<!-- View Modal -->
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

<!-- Edit Modal -->
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
                    // use raw response text here
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
                // { data: 'purchase_info', name: 'purchase_info' },
                // { data: 'zip_status', name: 'zip_status' },
                { data: 'reason', name: 'reason' },
                // { data: 'added_by', name: 'added_by' },
                // { data: 'action', name: 'action', orderable: false, searchable: false }
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

        // View button click
        $(document).on('click', '.view-btn', function() {
            var id = $(this).data('id');
            
            $.ajax({
                url: '/admin/sample-products/' + id + '/details',
                type: 'GET',
                success: function(response) {
                    $('#viewDetails').html(response);
                    $('#viewModal').modal('show');
                },
                error: function(xhr) {
                    toastr.error('Error loading details');
                }
            });
        });

        // Edit button click
        $(document).on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            
            $.ajax({
                url: '/admin/sample-products/' + id + '/edit',
                type: 'GET',
                success: function(response) {
                    $('#edit_id').val(response.id);
                    $('#edit_quantity').val(response.quantity);
                    $('#edit_size').val(response.size);
                    $('#edit_color').val(response.color);
                    $('#edit_reason').val(response.reason);
                    $('#editModal').modal('show');
                },
                error: function(xhr) {
                    toastr.error('Error loading data');
                }
            });
        });

        // Edit form submission
        $('#editForm').submit(function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            
            $.ajax({
                url: '/admin/sample-products/' + $('#edit_id').val(),
                type: 'PUT',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('#editModal').modal('hide');
                        table.ajax.reload();
                    }
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        toastr.error(value[0]);
                    });
                }
            });
        });

        // Delete button click
        $(document).on('click', '.delete-btn', function() {
            if (confirm('Are you sure you want to delete this sample product?')) {
                var id = $(this).data('id');
                
                $.ajax({
                    url: '/admin/sample-products/' + id,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            table.ajax.reload();
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Error deleting sample product');
                    }
                });
            }
        });

        // Initialize Select2
        $('.select2').select2({
            placeholder: "Select...",
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endsection