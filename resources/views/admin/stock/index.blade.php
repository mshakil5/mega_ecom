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
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="stock-table">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Product Name</th>
                                        <th>Quantity</th>
                                        <th>Size</th>
                                        <th>Color</th>
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
                { data: 'quantity_formatted', name: 'quantity' },
                { data: 'size', name: 'size' },
                { data: 'color', name: 'color' },
                { 
                    data: 'action', 
                    name: 'action', 
                    orderable: false, 
                    searchable: false,
                    render: function (data, type, full, meta) {
                        return '<button type="button" class="btn btn-primary btn-open-loss-modal" data-id="' + full.product_id + '" data-quantity="' + full.quantity + '">System Loss</button>';
                    }
                }
            ],
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
    });
</script>

@endsection