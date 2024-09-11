@extends('supplier.supplier')

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
                                    <th>Payment Method</th>
                                    <th>Status</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                <tr>
                                    <td>
                                        {{ optional($order->user)->name ?? $order->name }} {{ optional($order->user)->surname ?? '' }}
                                    </td>
                                    <td>{{ ucfirst($order->payment_method) }}</td>
                                    <td>
                                        @if($order->status == 1)
                                            Pending
                                        @elseif($order->status == 2)
                                            Processing
                                        @elseif($order->status == 3)
                                            Packed
                                        @elseif($order->status == 4)
                                            Shipped
                                        @elseif($order->status == 5)
                                            Delivered
                                        @elseif($order->status == 6)
                                            Returned
                                        @elseif($order->status == 7)
                                            Cancelled
                                        @else
                                            Unknown Status
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('supplier.orders.details', ['hashedOrderId' => Crypt::encryptString($order->id)]) }}" class="btn btn-primary">Details</a>
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