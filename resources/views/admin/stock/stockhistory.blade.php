@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">All Stocks</h3>
                    </div>
                    <div class="card-body">

                    <!-- Filter Form Section -->
                    <form action="#" method="GET">
                        <div class="row mb-3">
                            
                            <div class="col-md-4">
                                <label class="label label-primary">Product</label>
                                <select class="form-control select2" id="product_id" name="product_id">
                                    <option value="">Select...</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->product_code }}-{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="label label-primary">Warehouses</label>
                                <select class="form-control select2" id="warehouse_id" name="warehouse_id">
                                    <option value="">Select...</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}-{{ $warehouse->location }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="label label-primary">Type</label>
                                <select class="form-control select2" id="type_id" name="type_id">
                                    <option value="">Select...</option>
                                    @foreach($types as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-1">
                                <label class="label label-primary">Size</label>
                                <select class="form-control select2" id="size_id" name="size_id">
                                    <option value="">Select...</option>
                                    @foreach($sizes as $size)
                                    <option value="{{ $size }}">{{ $size }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-1">
                                <label class="label label-primary">Color</label>
                                <select class="form-control select2" id="color_id" name="color_id">
                                    <option value="">Select...</option>
                                    @foreach($colors as $color)
                                    <option value="{{ $color }}">{{ $color }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-1 d-none">
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
                            <table class="table table-bordered table-striped" id="stock-table">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Date</th>
                                        <th>Product</th>
                                        <th>Size</th>
                                        <th>Color</th>
                                        <th>Type</th>
                                        <th>Stocked</th>
                                        <th>Available</th>
                                        <th>Sold</th>
                                        <th>Selling Price</th>
                                        <th>Purchase Price</th>
                                        <th>Warehouse</th>
                                        <!-- <th>Action</th> -->
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

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
                        <select class="form-control" id="warehouse" name="warehouse">
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
    $(document).ready(function () {
        function openLossModal(productId, size, color) {
            // console.log(productId, size, color);

            $('#systemLossForm')[0].reset();
            $('#lossProductId').val(productId);
            $('#systemLossModal').modal('show');

            $('#systemLossForm').submit(function (e) {
                e.preventDefault();
                let lossQuantity = parseInt($('#lossQuantity').val());

                // if (lossQuantity > currentQuantity) {
                //     $('#quantityError').text('Quantity cannot be more than current stock quantity.');
                //     return;
                // } else {
                //     $('#quantityError').text('');
                // }

                let lossReason = $('#lossReason').val();
                let warehouse = $('#warehouse').val();

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
                    success: function (response) {
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
                    error: function (xhr, status, error) {
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
                url: "{{ route('getstockhistory') }}",
                data: function(d) {
                    d.warehouse_id = $('#warehouse_id').val();
                    d.product_id = $('#product_id').val();
                    d.color = $('#color_id').val();
                    d.size = $('#size_id').val();
                    d.zip = $('#zip').val();
                    d.type_id = $('#type_id').val();
                },
                error: function(xhr, error, code) {
                    console.error(xhr.responseText);
                }
            },
            pageLength: 100,
            dom: 'Bfrtip',
            columns: [
                { data: 'sl', name: 'sl', orderable: false, searchable: false },
                { data: 'date', name: 'date' },
                { data: 'product_details', name: 'product_details' },
                { data: 'size', name: 'size' },
                { data: 'color', name: 'color' },
                { data: 'type', name: 'type' },
                { data: 'quantity_formatted', name: 'quantity' },
                { data: 'available_qty', name: 'available_qty' },
                { data: 'selling_qty', name: 'selling_qty' },
                { data: 'selling_price', name: 'selling_price' },
                { data: 'ground_price_per_unit', name: 'ground_price_per_unit' },
                { data: 'warehouse', name: 'warehouse' },
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
            $('#zip').val(null).trigger('change');
            $('#type_id').val(null).trigger('change');
            table.draw();
        });

        $('#stock-table').on('click', '.btn-open-loss-modal', function () {
            let productId = $(this).data('id');
            let size = $(this).data('size');
            let color = $(this).data('color');
            openLossModal(productId, size, color);
        });

        $('#systemLossModal').on('hidden.bs.modal', function () {
            $('#systemLossForm')[0].reset();
            $('#quantityError').text('');
        });

        $('.select2').select2({
            placeholder: "Select...",
            allowClear: true,
            width: '100%'
        });
    });
</script>

@endsection