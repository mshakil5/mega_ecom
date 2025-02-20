@extends('admin.layouts.admin')

@section('content')
<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                @if(session()->has('success'))
                    <div class="alert alert-success mt-2">{{ session()->get('success') }}</div>
                @endif
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">All Data</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Date</th>
                                    <th>Name/Email/Phone</th>
                                    <th>Subtotal</th>
                                    <th>Vat</th>
                                    <th>Discount</th>
                                    <th>Total</th>
                                    <th>Action</th>                          
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($inHouseOrders as $order)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ \Carbon\Carbon::parse($order->purchase_date)->format('d-m-Y') }}</td>
                                    <td>
                                        {{ optional($order->user)->name ?? $order->name }} {{ optional($order->user)->surname ?? '' }} <br>
                                        {{ optional($order->user)->email ?? $order->email }} <br>
                                        {{ optional($order->user)->phone ?? $order->phone }}
                                    </td>
                                    <td>{{ number_format($order->subtotal_amount, 2) }}</td>
                                    <td>{{ number_format($order->vat_amount, 2) }}</td>
                                    <td>{{ number_format($order->discount_amount, 2) }}</td>
                                    <td>{{ number_format($order->net_amount, 2) }}</td>
                                    </td>
                                    <td>
                                        <form method="POST" action="{{ route('orders.send-email', $order->id) }}" class="d-inline-block">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-round btn-shadow email-btn d-none">
                                                <i class="fas fa-envelope"></i> Email
                                            </button>
                                        </form>

                                        <a href="{{route('quotation.emailform', $order->id)}}" class="btn btn-success btn-round btn-shadow"><i class="fas fa-envelope"></i> Email</a>



                                        <!-- <a href="{{ route('in-house-sell.generate-pdf', ['encoded_order_id' => base64_encode($order->id)]) }}" class="btn btn-success btn-round btn-shadow" target="_blank">
                                            <i class="fas fa-receipt"></i> Invoice
                                        </a> -->
                                        <a href="{{ route('admin.orders.details', ['orderId' => $order->id]) }}" class="btn btn-info btn-round btn-shadow">
                                            <i class="fas fa-info-circle"></i> Details
                                        </a>
                                        <a href="{{ route('orders.download-pdf', ['encoded_order_id' => base64_encode($order->id)]) }}" class="btn btn-warning btn-round btn-shadow" target="_blank">
                                            <i class="fas fa-download"></i> Download Quotation
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
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('form[action^="{{ route('orders.send-email', '') }}"]');
        
        forms.forEach(form => {
            form.addEventListener('submit', function() {
                const emailButtons = document.querySelectorAll('.email-btn');
                emailButtons.forEach(button => {
                    button.disabled = true;
                });
            });
        });
    });
</script>

@endsection