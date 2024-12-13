@extends('admin.layouts.admin')

@section('content')

<!-- Previous Shipment -->
<section class="content pt-3">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <a href="{{ route('reports.index') }}" class="btn btn-secondary mb-3">Back</a>
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Date To Date - Shipping List</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('reports.filterDateToDateShipments') }}">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="start-date" class="form-label">Start Date</label>
                                    <input type="date" name="start_date" id="start-date" class="form-control" value="{{ request('start_date') }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="end-date" class="form-label">End Date</label>
                                    <input type="date" name="end_date" id="end-date" class="form-control" value="{{ request('end_date') }}" required>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-secondary me-2">Filter</button>
                                    <button type="button" class="btn btn-secondary mx-2" onclick="window.location.href='{{ route('reports.dateToDateShipments') }}';"><i class="fas fa-sync-alt"></i></button>
                                </div>
                            </div>
                        </form>
                        <table id="shipmentTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Date</th>
                                    <th>Shipped Qty</th>
                                    <th>Missing Qty</th>
                                    <th>Purchase Cost</th>
                                    <th>Additional Cost</th>
                                    <th>Total Cost</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($shipments as $shipment)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $shipment->shipping->shipping_id }}</td>
                                    <td>{{ $shipment->shipping->shipping_name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($shipment->shipping->shipping_date)->format('d-m-Y') }}</td>
                                    <td>{{ $shipment->total_product_quantity }}</td>
                                    <td>{{ $shipment->total_missing_quantity }}</td>
                                    <td>{{ $shipment->total_purchase_cost }}</td>
                                    <td>{{ $shipment->total_additional_cost }}</td>
                                    <td>{{ $shipment->total_purchase_cost + $shipment->total_additional_cost }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info view-details" 
                                                data-shipping-id="{{ $shipment->shipping->shipping_id }}"
                                                data-shipping-date="{{ \Carbon\Carbon::parse($shipment->shipping->shipping_date)->format('d-m-Y') }}"
                                                data-shipping-name="{{ $shipment->shipping->shipping_name }}"
                                                data-total-quantity="{{ $shipment->total_product_quantity }}"
                                                data-total-missing-quantity="{{ $shipment->total_missing_quantity }}"
                                                data-total-purchase="{{ $shipment->total_purchase_cost }}"
                                                data-total-additional="{{ $shipment->total_additional_cost }}"
                                                data-warehouse-name="{{ $shipment->shipmentDetails[0]->warehouse->name }}"
                                                data-shipment-details="{{ json_encode($shipment->shipmentDetails) }}">
                                                Details
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
                                        <th>Shipped Quantity</th>
                                        <th>Missing Quantity</th>
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

@endsection

@section('script')

<script>
    $(document).ready(function() {

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

    });
</script>

@endsection