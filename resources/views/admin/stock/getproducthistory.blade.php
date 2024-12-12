@extends('admin.layouts.admin')

@section('content')

@php
    use Carbon\Carbon;
@endphp

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
                                    <th>Product</th>
                                    <th>Available Stock</th>
                                    <th>Previous Shipments</th>
                                    <th>Today Shipments</th>
                                    <th>Previous Sales</th>
                                    <th>Today Sales</th>
                                </tr>
                            </thead>
                                <tbody>
                                    @foreach ($products as $key => $data)
                                    <tr>
                                        <td>{{ $key + 1}}</td>
                                        <td>{{ $data->name }} - {{ $data->product_code }}</td>
                                        <td>{{ $data->total_quantity ?? 0 }}</td>
                                        <td>{{ $data->shipmentDetails()->where('created_at', '<', Carbon::today())->sum('quantity') }}</td>
                                        <td>{{ $data->shipmentDetails()->where('created_at', '=', Carbon::today())->sum('quantity') }}</td>
                                        <td>{{ $data->orderDetails()->where('created_at', '<', Carbon::today())->sum('quantity') }}</td>
                                        <td>{{ $data->orderDetails()->where('created_at', '=', Carbon::today())->sum('quantity') }}</td>
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



@endsection

@section('script')
<script>
    $(document).ready(function () {
        $('#stock-table').DataTable({
            "dom": 'Bfrtip',
            "pageLength": 100,
            "buttons": ["copy", "csv", "excel", "pdf", "print"]
        });
    });
</script>
@endsection