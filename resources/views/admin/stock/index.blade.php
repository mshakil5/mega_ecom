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
                            <div class="col-md-3">
                                <label class="label label-primary">Filter By</label>
                                <select class="form-control" id="filterBy" name="filterBy">
                                    <option value="today">Today</option>
                                    <option value="this_week">This Week</option>
                                    <option value="this_month">This Month</option>
                                    <option value="start_of_month">Start of the Month</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="label label-primary">From Date</label>
                                <input type="date" class="form-control" id="fromDate" name="fromDate">
                            </div>
                            <div class="col-md-2">
                                <label class="label label-primary">To Date</label>
                                <input type="date" class="form-control" id="toDate" name="toDate">
                            </div>
                            <div class="col-md-3">
                                <label class="label label-primary">Warehouses</label>
                                <select class="form-control select2" id="supplierCustomer" name="supplierCustomer">
                                    <option value="">Select...</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}-{{ $warehouse->location }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="label label-primary" style="visibility:hidden;">Action</label>
                                <button type="submit" class="btn btn-secondary btn-block">Search</button>
                            </div>
                        </div>
                    </form>
                    <!-- End of Filter Form Section -->

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="stock-table">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Product Name</th>
                                        <th>Product Code</th>
                                        <th>Quantity</th>
                                        <th>Size</th>
                                        <th>Color</th>
                                        <th>Warehouse Name</th>
                                        <th>Action</th>
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
        function openLossModal(productId, currentQuantity) {
            $('#systemLossForm')[0].reset();
            $('#lossProductId').val(productId);
            $('#systemLossModal').modal('show');

            $('#systemLossForm').submit(function (e) {
                e.preventDefault();
                let lossQuantity = parseInt($('#lossQuantity').val());

                if (lossQuantity > currentQuantity) {
                    $('#quantityError').text('Quantity cannot be more than current stock quantity.');
                    return;
                } else {
                    $('#quantityError').text('');
                }

                let lossReason = $('#lossReason').val();

                $.ajax({
                    url: "{{ route('process.system.loss') }}", 
                    type: 'POST',
                    data: {
                        productId: productId,
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
                    }
                });
            });
        }

        $('#stock-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('allstocks') }}",
            columns: [
                { data: 'sl', name: 'sl', orderable: false, searchable: false },
                { data: 'product_name', name: 'product_name' },
                { data: 'product_code', name: 'product_code' },
                { data: 'quantity_formatted', name: 'quantity' },
                { data: 'size', name: 'size' },
                { data: 'color', name: 'color' },
                { data: 'warehouse', name: 'warehouse' },
                {data: 'action', name: 'action', orderable: false, searchable: false}
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

        $('#stock-table').on('click', '.btn-open-loss-modal', function () {
            let productId = $(this).data('id');
            let currentQuantity = $(this).data('quantity');
            openLossModal(productId, currentQuantity);
        });

        $('#systemLossModal').on('hidden.bs.modal', function () {
            $('#systemLossForm')[0].reset();
            $('#quantityError').text('');
        });

        // $('.select2').select2({
        //     placeholder: 'Select a warehouse',
        //     allowClear: true
        // });
        // $('.select2').css('width', '100%');
    });
</script>

@endsection