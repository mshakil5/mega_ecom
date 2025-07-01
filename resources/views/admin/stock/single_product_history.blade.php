@extends('admin.layouts.admin')

@section('content')

<section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-2">
            <a href="{{ route('allstock') }}" class="btn btn-secondary my-3">Back</a>
        </div>
      </div>
    </div>
</section>

<div class="card card-primary sticky-top mx-3 mb-3 bg-success">
    <div class="card-body">
        <h4>
            <strong>Product:</strong> {{ $product->name }} ({{ $product->product_code }}) |
            <strong>Size:</strong> {{ $size }} |
            <strong>Color:</strong> {{ $color }} |
            <strong>Warehouse:</strong> {{ optional($warehouses->firstWhere('id', $warehouse_id))->name }}
            @if (!empty($type))
                | <strong>Type:</strong> {{ $type->name }}
            @endif
        </h4>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        <div class="row d-none">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Search</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{route('admin.product.purchasehistorysearch',['id' => $id, 'size' => $size, 'color' => $color, 'warehouse_id' => $warehouse_id])}}" method="POST">
                            @csrf
                            <div class="row mb-3 ">
                                <input type="hidden" id="product_id" name="product_id" value="{{$id}}">
                                <input type="hidden" id="size" name="size" value="{{$size}}">
                                <input type="hidden" id="color" name="color" value="{{$color}}">
                                <input type="hidden" id="warehouse_id" name="warehouse_id" value="{{$warehouse_id}}">
                                <div class="col-md-3 d-none">
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
                                    <input type="date" class="form-control" id="fromDate" name="fromDate" 
                                        value="{{ request('fromDate') ?? '' }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="label label-primary">To Date</label>
                                    <input type="date" class="form-control" id="toDate" name="toDate" 
                                        value="{{ request('toDate') ?? '' }}">
                                </div>
                                <div class="col-md-3 d-none">
                                    <label class="label label-primary">Warehouses</label>
                                    <select class="form-control select2" id="warehouse_id" name="warehouse_id">
                                        <option value="">Select...</option>
                                        @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" 
                                                {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                                {{ $warehouse->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="label label-primary" style="visibility:hidden;">Action</label>
                                    <button type="submit" class="btn btn-secondary btn-block">Search</button>
                                </div>
                                <div class="col-md-12">
                                    @if ($errors->any())
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li class="text-danger">{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                    @endif
                                </div>
                            </div>
                        </form>
                        <!-- End of Filter Form Section -->
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Shipment history</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped p-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Supplier</th>
                                        <th>Quantity</th>
                                        <th>Purchase Price</th>
                                        <th>Selling Price</th>
                                        <!-- <th>Vat Amount</th> -->
                                        <th>Total Price</th>
                                        <!-- <th>Total</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($shipmentDetails as $key => $data)
                                        <tr>
                                            <td>{{ date('d-m-Y', strtotime($data->created_at))}}</td>
                                            <td>
                                                @if ($data->supplier)
                                                {{ $data->supplier->name}}
                                                <a href="{{route('supplier.purchase', $data->supplier->id)}}" class="btn btn-sm btn-success" target="blank">
                                                    <i class="fas fa-arrow-right"></i>
                                                </a>
                                                @endif
                                            </td>
                                            <td>{{ $data->quantity}}</td>
                                            <td>{{ $data->price_per_unit}}</td>
                                            <td>{{ $data->selling_price}}</td>
                                            <!-- <td>{{ $data->total_vat}}</td> -->
                                            <td>{{ $data->selling_price * $data->quantity}}</td>
                                            <!-- <td>{{ $data->shipment->total_product_quantity}}</td> -->
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Sales history</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped p-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Whole Saler</th>
                                        <th>Warehouse</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Total Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($salesHistories as $key => $data)
                                        <tr>
                                            <td>{{ date('d-m-Y', strtotime($data->created_at))}}</td>
                                            <td>{{ $data->order->user->name}} 
                                                <a href="{{route('getallorder', $data->order->user->id )}}" class="btn btn-sm btn-success">
                                                    <i class="fas fa-arrow-right"></i>
                                                </a>
                                            </td>
                                            <td>{{ $data->warehouse_id ? $data->warehouse->name : " "}}</td>
                                            <td>{{ $data->quantity}}</td>
                                            <td>{{ $data->price_per_unit}}</td>
                                            <td>
                                            @if ($data->order->status == 1)
                                                <span class="btn btn-sm btn-primary">Pending</span>
                                            @elseif ($data->order->status == 2)
                                               <span class="btn btn-sm btn-info">Processing</span> 
                                            @elseif ($data->order->status == 3)
                                                <span class="btn btn-sm btn-primary">Packed</span>
                                            @elseif ($data->order->status == 4)
                                                <span class="btn btn-sm btn-info">Shipped</span>
                                            @elseif ($data->order->status == 5)                                            
                                                <span class="btn btn-sm btn-success">Delivered</span>
                                            @elseif ($data->order->status == 6)                                   
                                                <span class="btn btn-sm btn-warning">Returned</span>
                                            @elseif ($data->order->status == 7)
                                                <span class="btn btn-sm btn-danger">Cancelled</span>
                                            @else
                                                
                                            @endif</td>
                                            <td>{{ $data->total_price}}</td>
                                            <td>{{ $salesHistories->sum('total_price')}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Transfer History</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped p-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Transfer Direction</th>
                                        <th>Source Warehouse</th>
                                        <th>Destination Warehouse</th>
                                        <th>Quantity</th>
                                        <th>Note</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($stockTransferRequests as $request)
                                        <tr>
                                            <td>{{ date('d-m-Y', strtotime($request->created_at)) }}</td>
                                            <td>
                                                @if ($request->from_warehouse_id == $warehouse_id)                                               
                                                    <span class="btn btn-sm btn-danger">Transferred Out</span> 
                                                @elseif ($request->to_warehouse_id == $warehouse_id)
                                                   <span class="btn btn-sm btn-success">Received</span> 
                                                @endif
                                            </td>
                                            <td>{{ $request->fromWarehouse->name ?? '' }}</td>
                                            <td>{{ $request->toWarehouse->name ?? '' }}</td>
                                            <td>{{ $request->request_quantity }}</td>
                                            <td>{{ $request->note }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">System Losses</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped p-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Warehouse</th>
                                        <th>Performed By</th>
                                        <th>Reason</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($systemLosses as $key => $systemLoss)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($systemLoss->created_at)->format('d-m-Y') }}</td>
                                        <td>{{ ($systemLoss->product->product_code ?? '') . '-' . ($systemLoss->product->name ?? '') . '-' . ($systemLoss->size ?? '') . '-' . ($systemLoss->color ?? '') }}</td>
                                        <td>{{ $systemLoss->quantity }}</td>
                                        <td>
                                            @if ($systemLoss->shipment_detail_id)
                                                <span class="text-danger">Before Stocking</span>
                                            @else
                                                {{ $systemLoss->warehouse->name ?? '' }}
                                            @endif
                                        </td>
                                        <td>{{ $systemLoss->user->name ?? '' }}</td>
                                        <td>{!! $systemLoss->reason !!}</td>
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
        $('.p-table').DataTable();
    });
</script>

@endsection