@extends('admin.layouts.admin')

@section('content')
<section class="content" id="newBtnSection">
    <div class="container-fluid">
      <div class="row">
        <div class="col-2">
            <a href="{{route('allstock')}}" class="btn btn-secondary my-3">Back</a>
        </div>
      </div>
    </div>
</section>
<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Product Name</h3>
                    </div>
                    <div class="card-body">

                    <!-- Filter Form Section -->
                    {{-- <form action="#" method="GET">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="label label-primary">Filter By</label>
                                <select class="form-control" id="filterBy" name="filterBy">
                                    <option value="today">Today</option>
                                    <option value="this_week">This Week</option>
                                    <option value="this_month">This Month</option>
                                    <option value="start_of_month">Start of the Month</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="label label-primary">From Date</label>
                                <input type="date" class="form-control" id="fromDate" name="fromDate">
                            </div>
                            <div class="col-md-2">
                                <label class="label label-primary">To Date</label>
                                <input type="date" class="form-control" id="toDate" name="toDate">
                            </div>
                            <div class="col-md-3">
                                <label class="label label-primary">Warehouses</label>
                                <select class="form-control select2" id="supplierCustomer" name="supplierCustomer">
                                    <option value="">Select...</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="label label-primary" style="visibility:hidden;">Action</label>
                                <button type="submit" class="btn btn-secondary btn-block">Search</button>
                            </div>
                        </div>
                    </form> --}}
                    <!-- End of Filter Form Section -->

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="p-table">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Date</th>
                                        <th>Size</th>
                                        <th>Colour</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Vat Amount</th>
                                        <th>Total Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $key => $data)
                                        <tr>
                                            <td>{{ $key + 1}}</td>
                                            <td>{{ date('d-m-Y', strtotime($data->created_at))}}</td>
                                            <td>{{ $data->product_size}}</td>
                                            <td>{{ $data->product_color}}</td>
                                            <td>{{ $data->quantity}}</td>
                                            <td>{{ $data->purchase_price}}</td>
                                            <td>{{ $data->total_vat}}</td>
                                            <td>{{ $data->total_amount_with_vat}}</td>
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
        

        $('#p-table').DataTable();




        // $('.select2').select2({
        //     placeholder: 'Select a warehouse',
        //     allowClear: true
        // });
        // $('.select2').css('width', '100%');
    });
</script>

@endsection