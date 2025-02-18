@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-sm-12">
                <div class="card card-secondary card-tabs">
                    <div class="card-header p-0 pt-1">
                        <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="custom-tabs-one-profile-tab" data-toggle="pill" href="#custom-tabs-one-profile" role="tab" aria-controls="custom-tabs-one-profile" aria-selected="false">Stock List (Filtered by Warehouse)</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="custom-tabs-one-messages-tab" data-toggle="pill" href="#custom-tabs-one-messages" role="tab" aria-controls="custom-tabs-one-messages" aria-selected="false">Total Stock (All Warehouses)</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="custom-tabs-one-tabContent">
                            <div class="tab-pane fade active show" id="custom-tabs-one-profile" role="tabpanel" aria-labelledby="custom-tabs-one-profile-tab">
                                <form action="#" method="GET">
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
                                        <div class="col-md-3">
                                            <label class="label label-primary">Warehouses</label>
                                            <select class="form-control select2" id="warehouse_id" name="warehouse_id">
                                                <option value="">Select...</option>
                                                @foreach($warehouses as $warehouse)
                                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}-{{ $warehouse->location }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="label label-primary">Size</label>
                                            <select class="form-control select2" id="size_id" name="size_id">
                                                <option value="">Select...</option>
                                                @foreach($sizes as $size)
                                                <option value="{{ $size }}">{{ $size }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="label label-primary">Color</label>
                                            <select class="form-control select2" id="color_id" name="color_id">
                                                <option value="">Select...</option>
                                                @foreach($colors as $color)
                                                <option value="{{ $color }}">{{ $color }}</option>
                                                @endforeach
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
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#requestStockModal">
                                            Request Stock
                                        </button>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="stock-table">
                                        <thead>
                                            <tr>
                                                <th>Sl</th>
                                                <th>Product</th>
                                                <th>Warehouse</th>
                                                <th>Size</th>
                                                <th>Color</th>
                                                <th>Quantity</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th colspan="5" class="text-right">Total Quantity:</th>
                                                <th id="total-quantity">0</th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="custom-tabs-one-messages" role="tabpanel" aria-labelledby="custom-tabs-one-messages-tab">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Total Stock</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped" id="total-stock-table">
                                                <thead>
                                                    <tr>
                                                        <th>Sl</th>
                                                        <th>Product</th>
                                                        <th>Size</th>
                                                        <th>Color</th>
                                                        <th>Qty</th>
                                                    </tr>
                                                </thead>
                                            </table>
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

<div class="modal fade" id="requestStockModal" tabindex="-1" role="dialog" aria-labelledby="requestStockModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="requestStockModalLabel">Request Stock</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="requestStockForm">
                    <div class="form-group">
                        <label for="productId">Product</label>
                        <select class="form-control select2" id="productId" name="productId">
                            <option value="">Select Product...</option>
                            @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} - {{ $product->product_code }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="warehouse">Source Warehouse</label>
                                <select class="form-control select2" id="warehouse" name="warehouse" disabled>
                                    <option value="">Select Warehouse...</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="warehouse">Destination Warehouse</label>
                                <select class="form-control select2" id="toWarehouse" name="toWarehouse">
                                    <option value="">Select Warehouse...</option>
                                    @foreach($filteredWarehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }} ({{ $warehouse->location }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="color">Color</label>
                                <select class="form-control select2" id="color" name="color" disabled>
                                    <option value="">Select Color...</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">

                            <div class="form-group">
                                <label for="size">Size</label>
                                <select class="form-control select2" id="size" name="size" disabled>
                                    <option value="">Select Size...</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="max_quantity">Max Transfer Quantity</label>
                                <input type="text" class="form-control" id="max_quantity" name="max_quantity" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="quantity">Quantity</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="note">Note</label>
                                <textarea class="form-control" id="note" name="note" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="submitRequest">Submit Request</button>
            </div>
        </div>
    </div>
</div>

<!-- System Loss Modal -->
<div class="modal fade" id="systemLossModal" tabindex="-1" aria-labelledby="systemLossModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="systemLossModalLabel">System Loss</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="systemLossForm">
                <div class="modal-body">
                    <input type="hidden" id="lossProductId" name="productId">

                    <span id="allError" class="text-danger"></span>

                    <div class="form-group">
                        <label class="label label-primary">Warehouses</label>
                        <select class="form-control" id="warehouses" name="warehouses">
                            <option value="">Select...</option>
                            @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}-{{ $warehouse->location }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="lossQuantity">Loss Quantity:</label>
                        <input type="number" class="form-control" id="lossQuantity" name="lossQuantity" required>
                        <span id="quantityError" class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <label for="lossReason">Loss Reason:</label>
                        <textarea class="form-control" id="lossReason" name="lossReason" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')

<script>
    $(document).ready(function() {
        $('#productId').change(function() {
            var productId = $(this).val();
            if (productId) {
                $.ajax({
                    url: '/admin/get-warehouses',
                    method: 'POST',
                    data: {
                        product_id: productId,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        $('#warehouse').empty().append('<option value="">Select Warehouse...</option>');
                        $.each(data.warehouses, function(index, stock) {
                            $('#warehouse').append('<option value="' + stock.warehouse.id + '">' + stock.warehouse.name + ' (' + stock.warehouse.location + ')</option>');
                        });
                        $('#warehouse').prop('disabled', false);
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        alert('An error occurred while fetching warehouses');
                    }
                });
            } else {
                resetDropdowns();
            }
        });

        $('#warehouse').change(function() {
            var warehouseId = $(this).val();
            var productId = $('#productId').val();
            if (warehouseId && productId) {
                $.ajax({
                    url: '/admin/get-colors',
                    method: 'POST',
                    data: {
                        product_id: productId,
                        warehouse_id: warehouseId,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        $('#color').empty().append('<option value="">Select Color...</option>');
                        $.each(data.colors, function(index, color) {
                            $('#color').append('<option value="' + color.color + '">' + color.color + '</option>');
                        });
                        $('#color').prop('disabled', false);
                    },
                    error: function(xhr) {
                        alert('An error occurred while fetching colors');
                    }
                });
            } else {
                $('#color').empty().append('<option value="">Select Color...</option>').prop('disabled', true);
                $('#size').empty().append('<option value="">Select Size...</option>').prop('disabled', true);
                $('#max_quantity').val('');
            }
        });

        $('#color').change(function() {
            var warehouseId = $('#warehouse').val();
            var productId = $('#productId').val();
            var selectedColor = $(this).val();
            if (warehouseId && productId && selectedColor) {
                $.ajax({
                    url: '/admin/get-sizes',
                    method: 'POST',
                    data: {
                        product_id: productId,
                        warehouse_id: warehouseId,
                        color: selectedColor,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        $('#size').empty().append('<option value="">Select Size...</option>');
                        $.each(data.sizes, function(index, size) {
                            $('#size').append('<option value="' + size.size + '">' + size.size + '</option>');
                        });
                        $('#size').prop('disabled', false);
                    },
                    error: function(xhr) {
                        alert('An error occurred while fetching sizes');
                    }
                });
            } else {
                $('#size').empty().append('<option value="">Select Size...</option>').prop('disabled', true);
            }
        });

        $('#size').change(function() {
            var warehouseId = $('#warehouse').val();
            var productId = $('#productId').val();
            var selectedColor = $('#color').val();
            var selectedSize = $(this).val();
            if (warehouseId && productId && selectedColor && selectedSize) {
                $.ajax({
                    url: '/admin/get-max-quantity',
                    method: 'POST',
                    data: {
                        product_id: productId,
                        warehouse_id: warehouseId,
                        color: selectedColor,
                        size: selectedSize,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        $('#max_quantity').val(data.max_quantity);
                    },
                    error: function(xhr) {
                        alert('An error occurred while fetching max quantity: ' + xhr.responseText);
                    }
                });
            } else {
                $('#max_quantity').val('');
            }
        });

        function resetDropdowns() {
            $('#warehouse').empty().append('<option value="">Select Warehouse...</option>').prop('disabled', true);
            $('#color').empty().append('<option value="">Select Color...</option>').prop('disabled', true);
            $('#size').empty().append('<option value="">Select Size...</option>').prop('disabled', true);
            $('#max_quantity').val('');
        }

        $('#requestStockModal').on('hidden.bs.modal', function() {
            $('#requestStockForm')[0].reset();

            $('#productId').val('').trigger('change');
            $('#warehouse').val('').trigger('change');
            $('#toWarehouse').val('').trigger('change');
            $('#color').val('').trigger('change');
            $('#size').val('').trigger('change');
            $('#max_quantity').val('');
        });

        $('#submitRequest').on('click', function(e) {
            e.preventDefault();

            $('#submitRequest').prop('disabled', true);
            $('#submitRequest').html('<i class="fas fa-spinner fa-spin"></i> Submitting...');

            var isValid = true;
            var quantity = parseInt($('#quantity').val().trim());
            var maxQuantity = parseInt($('#max_quantity').val().trim());

            // Validate required fields
            $('#requestStockForm').find('input, select').each(function() {
                if ($(this).val().trim() === '') {
                    isValid = false;
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            if (isValid && (quantity > maxQuantity)) {
                isValid = false;
                $('#quantity').addClass('is-invalid');
                swal({
                    text: "Quantity must be less than or equal to Max Transfer Quantity.",
                    icon: "error",
                });
                $('#submitRequest').prop('disabled', false);
                $('#submitRequest').html('Submit Request');
                return;
            }

            if (!isValid) {
                swal({
                    text: "Please fill out all required fields before submitting.",
                    icon: "error",
                });
                $('#submitRequest').prop('disabled', false);
                $('#submitRequest').html('Submit Request');
                return;
            }

            swal({
                text: "Are you sure you want to send this request?",
                icon: "warning",
                buttons: ["Cancel", "Yes"],
                dangerMode: true,
            }).then((willSend) => {
                if (willSend) {
                    var formData = new FormData($('#requestStockForm')[0]);
                    var csrfToken = $('meta[name="csrf-token"]').attr('content');

                    $.ajax({
                        url: '/admin/stock-transfer-requests',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        success: function(response) {
                            $('#submitRequest').prop('disabled', false);
                            $('#submitRequest').html('Submit Request');
                            swal({
                                text: "Request sent successfully",
                                icon: "success"
                            });
                            $('#requestStockForm')[0].reset();
                            $('#requestStockModal').modal('hide');
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        },
                        error: function(xhr) {
                            swal({
                                text: JSON.parse(xhr.responseText).errors.toWarehouse[0] || "An error occurred while sending the request.",
                                icon: "error"
                            });
                            $('#submitRequest').prop('disabled', false);
                            $('#submitRequest').html('Submit Request');
                        }
                    });
                } else {
                    swal({
                        text: "Request cancelled.",
                        icon: "info",
                    });
                    $('#submitRequest').prop('disabled', false);
                    $('#submitRequest').html('Submit Request');
                }
            });
        });

    });
</script>

<script>
    $(document).ready(function() {
        function openLossModal(productId, size, color, warehouse) {
            // console.log(productId, size, warehouse); 

            $('#systemLossForm')[0].reset();
            $('#lossProductId').val(productId);
            $('#warehouses').val(warehouse).prop('disabled', true);
            $('#systemLossModal').modal('show');

            $('#systemLossForm').submit(function(e) {
                e.preventDefault();
                let lossQuantity = parseInt($('#lossQuantity').val());

                // if (lossQuantity > currentQuantity) {
                //     $('#quantityError').text('Quantity cannot be more than current stock quantity.');
                //     return;
                // } else {
                //     $('#quantityError').text('');
                // }

                let lossReason = $('#lossReason').val();
                let warehouse = $('#warehouses').val();

                $.ajax({
                    url: "{{ route('process.system.loss') }}",
                    type: 'POST',
                    data: {
                        color: color,
                        size: size,
                        productId: productId,
                        warehouse: warehouse,
                        lossQuantity: lossQuantity,
                        lossReason: lossReason,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        swal({
                            text: "Sent to system loss",
                            icon: "success",
                            button: {
                                text: "OK",
                                className: "swal-button--confirm"
                            }
                        });
                        $('#systemLossModal').modal('hide');
                        $('#stock-table').DataTable().ajax.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);

                        let response = JSON.parse(xhr.responseText);

                        // If there are validation errors, get the message
                        let errorMessage = response.message;
                        // Insert the error message into the modal
                        $('#allError').html(errorMessage);
                    }
                });
            });
        }

        var table = $('#stock-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('allstocks') }}",
                data: function(d) {
                    d.warehouse_id = $('#warehouse_id').val();
                    d.product_id = $('#product_id').val();
                    d.color = $('#color_id').val();
                    d.size = $('#size_id').val();
                },
                error: function(xhr, error, code) {
                    console.error(xhr.responseText);
                }
            },
            pageLength: 50,
            columns: [{
                    data: 'sl',
                    name: 'sl',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'product_details',
                    name: 'product_details'
                },
                {
                    data: 'warehouse_name',
                    name: 'warehouse_name'
                },
                {
                    data: 'size',
                    name: 'size'
                },
                {
                    data: 'color',
                    name: 'color'
                },
                {
                    data: 'quantity',
                    name: 'quantity'
                },

                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            // columnDefs: [
            //     {
            //         targets: [6],
            //         visible: false,
            //         searchable: false
            //     }
            // ],
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            drawCallback: function(settings) {
                var api = this.api();
                var total = api.column(5, { page: 'current' }).data().reduce(function(a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0);
                $('#total-quantity').html(total);
            }
        });

        var totalStockTable = $('#total-stock-table').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            responsive: true,
            ajax: {
                url: "{{ route('totalstocks') }}",
                error: function(xhr, error, code) {
                    console.error(xhr.responseText);
                }
            },
            pageLength: 50,
            columns: [
                {
                    data: 'sl',
                    name: 'sl',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'product_details',
                    name: 'product_details'
                },
                {
                    data: 'size',
                    name: 'size'
                },
                {
                    data: 'color',
                    name: 'color'
                },
                {
                    data: 'quantity',
                    name: 'quantity'
                }
            ]
        });

        $('form').on('submit', function(e) {
            e.preventDefault();
            table.draw();
        });

        $('#reset-button').on('click', function() {
            $('#warehouse_id').val(null).trigger('change');
            $('#product_id').val(null).trigger('change');
            $('#color_id').val(null).trigger('change');
            $('#size_id').val(null).trigger('change');
            table.draw();
        });

        $('#stock-table').on('click', '.btn-open-loss-modal', function() {
            let productId = $(this).data('id');
            let size = $(this).data('size');
            let color = $(this).data('color');
            let warehouse = $(this).data('warehouse');
            openLossModal(productId, size, color, warehouse);
        });

        $('#systemLossModal').on('hidden.bs.modal', function() {
            $('#systemLossForm')[0].reset();
            $('#quantityError').text('');
        });

        $('#product_id').select2({
            placeholder: "Select product...",
            allowClear: true,
            width: '100%'
        });

        $('.select2').select2({
            placeholder: "Select product...",
            allowClear: true,
            width: '100%'
        });
    });
</script>

@endsection