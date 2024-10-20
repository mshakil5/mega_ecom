@extends('admin.layouts.admin')

@section('content')
<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">All Data</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Name/Email/Phone</th>
                                    <th>Subtotal</th>
                                    <th>Total</th> 
                                    <th>Type</th>  
                                    <th>Status</th>                                       
                                     @if (!empty($orders) && $orders->contains(function ($order) {
                                            return $order->status == 4;
                                        }))
                                        <th>Action</th>
                                     @endif
                                     <th>Details</th>     
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($order->purchase_date)->format('d-m-Y') }}</td>
                                    <td>
                                        {{ optional($order->user)->name ?? $order->name }} {{ optional($order->user)->surname ?? '' }} <br> {{ optional($order->user)->email ?? $order->email }} <br> {{ optional($order->user)->phone ?? $order->phone }}
                                    </td>
                                    <td>{{ number_format($order->subtotal_amount, 2) }}</td>
                                    <td>{{ number_format($order->net_amount, 2) }}</td>
                                    <td>{{ $order->order_type == 0 ? 'Frontend' : 'In-house Sale' }}</td>
                                    <td>
                                        <select class="form-control order-status" data-order-id="{{ $order->id }}">
                                            <option value="1" {{ $order->status == 1 ? 'selected' : '' }}>Pending</option>
                                            <option value="2" {{ $order->status == 2 ? 'selected' : '' }}>Processing</option>
                                            <option value="3" {{ $order->status == 3 ? 'selected' : '' }}>Packed</option>
                                            <option value="4" {{ $order->status == 4 ? 'selected' : '' }}>Shipped</option>
                                            <option value="5" {{ $order->status == 5 ? 'selected' : '' }}>Delivered</option>
                                            <option value="6" {{ $order->status == 6 ? 'selected' : '' }}>Returned</option>
                                            <option value="7" {{ $order->status == 7 ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </td>
                                    @if ($order->status == 4)
                                        <td>
                                            <select class="form-control select-delivery-man" data-order-id="{{ $order->id }}">
                                                <option value="">Select Delivery Man</option>
                                                @foreach ($deliveryMen as $deliveryMan)
                                                    <option value="{{ $deliveryMan->id }}" {{ $order->delivery_man_id == $deliveryMan->id ? 'selected' : '' }}>
                                                        {{ $deliveryMan->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                    @endif
                                    <td>
                                        <a href="{{ route('in-house-sell.generate-pdf', ['encoded_order_id' => base64_encode($order->id)]) }}" class="btn btn-success btn-round btn-shadow" target="_blank">
                                            <i class="fas fa-receipt"></i> Invoice
                                        </a>
                                        <a href="{{ route('admin.orders.details', ['orderId' => $order->id]) }}" class="btn btn-info btn-round btn-shadow">
                                            <i class="fas fa-info-circle"></i> Details
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
@endsection

@section('script')
<script>
    $(function () {
        $("#example1").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

        $('.order-status').change(function() {
            const orderId = $(this).data('order-id');
            const status = $(this).val();

            $.ajax({
                url: '/admin/orders/update-status',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    order_id: orderId,
                    status: status
                },
                success: function(response) {
                    swal({
                        text: "Status Changed",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('.select-delivery-man').change(function() {
            const orderId = $(this).data('order-id');
            const deliveryManId = $(this).val();
            // console.log(orderId, deliveryManId);

            $.ajax({
                url: '/admin/orders/update-delivery-man', 
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    order_id: orderId,
                    delivery_man_id: deliveryManId
                },
                success: function(response) {
                    // console.log(response);
                    swal({
                        text: "Delivery man assigned",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        });
    });
</script>

@endsection