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
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Subtotal</th>
                                    <th>Shipping</th>
                                    <th>Discount</th>
                                    <th>Total</th>
                                    <th>Transaction Method</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($inHouseOrders as $order)
                                <tr>
                                    <td>
                                        {{ optional($order->user)->name ?? $order->name }} {{ optional($order->user)->surname ?? '' }}
                                    </td>
                                    <td>
                                        {{ optional($order->user)->email ?? $order->email }}
                                    </td>
                                    <td>
                                        {{ optional($order->user)->phone ?? $order->phone }}
                                    </td>
                                    <td>
                                        {{ optional($order->user)->house_number ?? $order->house_number }},
                                        {{ optional($order->user)->street_name ?? $order->street_name }},
                                        <br>
                                        {{ optional($order->user)->town ?? $order->town }},
                                        {{ optional($order->user)->postcode ?? $order->postcode }}
                                    </td>
                                    <td>{{ number_format($order->subtotal_amount, 2) }}</td>
                                    <td>{{ number_format($order->shipping_amount, 2) }}</td>
                                    <td>{{ number_format($order->discount_amount, 2) }}</td>
                                    <td>{{ number_format($order->net_amount, 2) }}</td>
                                    <td>{{ ucfirst($order->payment_method) }}</td>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.orders.details', ['orderId' => $order->id]) }}" class="btn btn-primary">Details</a>
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
    });
</script>

@endsection