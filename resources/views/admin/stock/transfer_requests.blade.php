@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">System Losses</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="systemLossTable">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Date</th>                           
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Source Warehouse</th>
                                        <th>Destination Warehouse</th>
                                        <th>Note</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($stockTransferRequests as $StockTransferRequest)
                                    <tr class="{{ $StockTransferRequest->status == 0 ? 'table-warning' : ($StockTransferRequest->status == 1 ? 'table-success' : 'table-danger') }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ \Carbon\Carbon::parse($StockTransferRequest->created_at)->format('d-m-Y') }}</td>
                                        <td>{{ $StockTransferRequest->product->name }} - {{ $StockTransferRequest->size }} - {{ $StockTransferRequest->color }}</td>
                                        <td>{{ $StockTransferRequest->request_quantity }}</td>
                                        <td>{{ $StockTransferRequest->fromWarehouse->name }}</td>
                                        <td>{{ $StockTransferRequest->toWarehouse->name }}</td>
                                        <td>{!! $StockTransferRequest->note !!}</td>
                                        <td>
                                            @if ($StockTransferRequest->status == 0)
                                                <button class="btn btn-primary btn-sm edit-btn" 
                                                        data-id="{{ $StockTransferRequest->id }}" 
                                                        data-quantity="{{ $StockTransferRequest->request_quantity }}" 
                                                        data-toggle="modal" 
                                                        data-target="#editQuantityModal" 
                                                        data-max-quantity="{{ $StockTransferRequest->max_quantity }}">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <button class="btn btn-success btn-sm accept-btn" 
                                                        data-id="{{ $StockTransferRequest->id }}">
                                                    <i class="fas fa-check"></i> Accept
                                                </button>
                                                <button class="btn btn-danger btn-sm reject-btn" 
                                                        data-id="{{ $StockTransferRequest->id }}">
                                                    <i class="fas fa-times"></i> Reject
                                                </button>
                                            @elseif ($StockTransferRequest->status == 1)
                                                <span class="badge badge-success">Accepted</span>
                                            @elseif ($StockTransferRequest->status == 2)
                                            <span class="badge badge-danger">Rejected</span>
                                            @endif
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
    </div>
</section>

<div class="modal fade" id="editQuantityModal" tabindex="-1" role="dialog" aria-labelledby="editQuantityLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editQuantityLabel">Edit Quantity</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editQuantityForm">
                    <input type="hidden" id="requestId" name="requestId">
                    <div class="form-group">
                        <label for="quantity">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    $(function () {
        $('#systemLossTable').DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print"]
        }).buttons().container().appendTo('#systemLossTable_wrapper .col-md-6:eq(0)');
    });
</script>

<script>
    $(document).ready(function() {
        $('.edit-btn').on('click', function() {
            var id = $(this).data('id');
            var quantity = $(this).data('quantity');
            var maxQuantity = $(this).data('max-quantity');

            $('#requestId').val(id);
            $('#quantity').val(quantity);
            $('#quantity').attr('max', maxQuantity);
        });

        $('#editQuantityForm').on('submit', function(e) {
            e.preventDefault();

            var id = $('#requestId').val();
            var quantity = $('#quantity').val();
            var maxQuantity = $('#quantity').attr('max');

            if (quantity > maxQuantity) {
                alert('Quantity cannot exceed the maximum allowed quantity of ' + maxQuantity + '.');
                return;
            }

            $.ajax({
                url: '/admin/stock-transfer-requests/' + id,
                type: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    quantity: quantity
                },
                success: function(response) {
                    swal("Success!", "Quantity updated successfully.", "success");
                    $('#editQuantityModal').modal('hide');
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                },
                error: function(xhr) {
                    alert('An error occurred while updating the quantity.');
                }
            });
        });

        $('.accept-btn').on('click', function() {
            var id = $(this).data('id');

            if (confirm('Are you sure you want to accept this request?')) {
                $.ajax({
                    url: '/admin/stock-transfer-requests/accept/' + id,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        swal("Success!", "Request accepted successfully.", "success");
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    },
                    error: function(xhr) {
                        alert('An error occurred while accepting the request.');
                    }
                });
            }
        });

        $('.reject-btn').on('click', function() {
            var id = $(this).data('id');

            if (confirm('Are you sure you want to reject this request?')) {
                $.ajax({
                    url: '/admin/stock-transfer-requests/reject/' + id,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        swal("Success!", "Request rejected successfully.", "success");
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    },
                    error: function(xhr) {
                        alert('An error occurred while rejecting the request.');
                    }
                });
            }
        });
    });
</script>
@endsection