@extends('admin.layouts.admin')

@section('content')
<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">All Orders</h3>
                    </div>
                    <div class="card-body">
                        <table id="pending-orders-table" class="table table-bordered table-striped table-fluid">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Subtotal</th>
                                    <th>Shipping</th>
                                    <th>Discount</th>
                                    <th>Total</th>
                                    <th>Payment Method</th>
                                    <th>Status</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
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
        $('#pending-orders-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('allorder') }}",
            columns: [
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'phone', name: 'phone' },
                { data: 'subtotal_amount', name: 'subtotal_amount' },
                { data: 'shipping_amount', name: 'shipping_amount' },
                { data: 'discount_amount', name: 'discount_amount' },
                { data: 'net_amount', name: 'net_amount' },
                { data: 'payment_method', name: 'payment_method' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>
@endsection
