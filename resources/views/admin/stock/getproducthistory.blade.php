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
                                        <th>Available Stock</th>
                                        <th>Previous Purchase</th>
                                        <th>Today Purchase</th>
                                        <th>Previous Sales</th>
                                        <th>Today Sales</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $key => $data)
                                    <tr>
                                        <td>{{ $key + 1}}</td>
                                        <td>{{ $data->name }}</td>
                                        <td>{{ $data->previous_day_stock + $data->today_stock_qty }}</td>
                                        <td>{{ $data->previous_day_purchase }}</td>
                                        <td>{{ $data->today_purchase_qty }}</td>
                                        <td>{{ $data->previous_sales_qty }}</td>
                                        <td>{{ $data->today_sales_qty }}</td>
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
        

        $('#reset-button').on('click', function() {
            location.reload();
        });

        $('#stock-table').on('click', '.btn-open-loss-modal', function () {
            let productId = $(this).data('id');
            let size = $(this).data('size');
            let color = $(this).data('color');
            openLossModal(productId, size, color);
        });

        

        $('#product_id').select2({
            placeholder: "Select product...",
            allowClear: true,
            width: '100%'
        });
    });
</script>

@endsection