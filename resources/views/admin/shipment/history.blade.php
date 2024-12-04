@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Shipment History</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Shipping ID</th>
                                    <th>Total Product</th>
                                    <th>Total Purchase Cost</th>
                                    <th>Total Additional Cost</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($shipments as $key => $shipment)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $shipment->shipping->shipping_id }}</td>
                                    <td>{{ $shipment->total_product_quantity }}</td>
                                    <td>{{ $shipment->total_purchase_cost }}</td>
                                    <td>{{ $shipment->total_additional_cost }}</td>
                                    <td>
                                        <button class="btn btn-info view-details" 
                                                data-shipping-id="{{ $shipment->shipping->shipping_id }}"
                                                data-shipping-date="{{ \Carbon\Carbon::parse($shipment->shipping->shipping_date)->format('d-m-Y') }}"
                                                data-shipping-name="{{ $shipment->shipping->shipping_name }}"
                                                data-total-quantity="{{ $shipment->total_product_quantity }}"
                                                data-total-purchase="{{ $shipment->total_purchase_cost }}"
                                                data-total-additional="{{ $shipment->total_additional_cost }}"
                                                data-shipment-details="{{ json_encode($shipment->shipmentDetails) }}">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <a href="{{ route('admin.shipments.edit', $shipment->id) }}" class="btn btn-primary">
                                            <i class="fas fa-edit"></i>
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
                                        <strong>Total Purchase Cost:</strong> <span id="totalPurchase">0</span>
                                    </div>
                                    <div>
                                        <strong>Total Additional Cost:</strong> <span id="totalAdditional">0</span>
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
                                        <th>Quantity</th>
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
                const totalPurchase = this.getAttribute('data-total-purchase');
                const totalAdditional = this.getAttribute('data-total-additional');
                const shipmentDetails = JSON.parse(this.getAttribute('data-shipment-details'));

                document.getElementById('shippingId').textContent = shippingId;
                document.getElementById('shippingDate').textContent = shippingDate;
                document.getElementById('shippingName').textContent = shippingName;
                document.getElementById('totalQuantity').textContent = totalQuantity;
                document.getElementById('totalPurchase').textContent = totalPurchase;
                document.getElementById('totalAdditional').textContent = totalAdditional;

                const purchaseDataBody = document.getElementById('purchaseData');
                purchaseDataBody.innerHTML = '';

                shipmentDetails.forEach(detail => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${detail.supplier.name}</td>
                        <td>${detail.product && detail.product.product_code ? `${detail.product.product_code} - ${detail.product.name}` : (detail.product ? detail.product.name : '')}</td>
                        <td>${detail.size || '-'}</td>
                        <td>${detail.color || '-'}</td>
                        <td>${detail.quantity}</td>
                        <td>${detail.price_per_unit}</td>
                        <td>${detail.ground_price_per_unit}</td>
                        <td>${detail.profit_margin}%</td>
                        <td>${detail.selling_price}</td>
                    `;
                    purchaseDataBody.appendChild(row);
                });

                $('#example2').DataTable().destroy();
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
    $(function () {
      $("#example1").DataTable({
        "responsive": true, 
        "lengthChange": false, 
        "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>

@endsection