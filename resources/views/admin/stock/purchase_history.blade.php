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
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Date</th>
                                    <th>Invoice</th>
                                    <th>Supplier</th>
                                    <th>Transaction Type</th>
                                    <th>Ref</th>
                                    <th>Total Amount</th>
                                    <th>Due Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchases as $key => $purchase)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d-m-Y') }}</td>
                                    <td>{{ $purchase->invoice }}</td>
                                    <td>{{ $purchase->supplier->name }}</td>
                                    <td>{{ $purchase->purchase_type }}</td>
                                    <td>{{ $purchase->ref }}</td>
                                    <td>{{ $purchase->net_amount }}</td>
                                    <td>{{ $purchase->due_amount }}</td>
                                    <td>
                                        <a class="btn btn-sm btn-info" onclick="showViewPurchaseModal({{ $purchase->id }})">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('purchase.edit', $purchase->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('returnProduct', $purchase->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-undo-alt"></i>
                                        </a>
                                        <a href="{{ route('transferToWarehouse', $purchase->id) }}" class="btn btn-sm btn-success">
                                            <i class="fas fa-arrow-right"></i> Transfer to Warehouse
                                        </a>
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
<div class="modal fade" id="viewPurchaseModal" tabindex="-1" aria-labelledby="viewPurchaseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewPurchaseModalLabel">View Purchase Details</h5>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col"><strong>Date:</strong> <span id="purchaseDate"></span></div>
                    <div class="col"><strong>Invoice:</strong> <span id="purchaseInvoice"></span></div>
                </div>
                <div class="row mb-3">
                    <div class="col"><strong>Supplier:</strong> <span id="supplierName"></span></div>
                    <div class="col"><strong>Transaction Type:</strong> <span id="purchaseType"></span></div>
                </div>
                <div class="row mb-3">
                    <div class="col"><strong>Ref:</strong> <span id="purchaseRef"></span></div>
                    <div class="col"><strong>Total Amount:</strong> <span id="purchaseNetAmount"></span></div>
                </div>
                <div class="row mb-3">
                    <div class="col"><strong>Paid Amount:</strong> <span id="purchasePaidAmount"></span></div>
                    <div class="col"><strong>Due Amount:</strong> <span id="purchaseDueAmount"></span></div>
                </div>

                <div class="mb-3">
                    <h5>Purchase History</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Total Vat</th>
                                <th>Net Total</th>
                            </tr>
                        </thead>
                        <tbody id="purchaseHistoryTableBody">
                           
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')

<script>
    $(function () {
      $("#example1").DataTable({
        "responsive": true, 
        "lengthChange": false, 
        "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>

<script>
    function showViewPurchaseModal(purchaseId) {
        $.ajax({
            url: '/admin/purchase/' + purchaseId + '/history',
            type: 'GET',
            success: function(response) {
                console.log(response);
                var formattedDate = moment(response.purchase_date).format('DD-MM-YYYY');
                $('#purchaseDate').text(formattedDate);
                $('#purchaseInvoice').text(response.invoice);
                $('#supplierName').text(response.supplier ? response.supplier.name : 'Unknown Supplier');
                $('#purchaseType').text(response.purchase_type);
                $('#purchaseRef').text(response.ref);
                $('#purchaseNetAmount').text(response.net_amount);
                $('#purchasePaidAmount').text(response.paid_amount);
                $('#purchaseDueAmount').text(response.due_amount);

                if (response.purchase_history && response.purchase_history.length > 0) {
                    let purchaseHistoryHtml = '';
                    response.purchase_history.forEach(function(history) {
                        purchaseHistoryHtml += `
                            <tr>
                                <td>${history.product.name}</td>
                                <td>${history.quantity}</td>
                                <td>${history.purchase_price}</td>
                                <td>${history.total_vat}</td>
                                <td>${history.total_amount_with_vat}</td>
                            </tr>`;
                    });

                    $('#purchaseHistoryTableBody').html(purchaseHistoryHtml);
                } else {
                    $('#purchaseHistoryTableBody').html('<tr><td colspan="5">No purchase history found.</td></tr>');
                }

                $('#viewPurchaseModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
            }
        });

        $(document).on('click', '[data-bs-dismiss="modal"]', function(event) {
            $('#viewPurchaseModal').modal('hide');
        });
    }
</script>

@endsection
