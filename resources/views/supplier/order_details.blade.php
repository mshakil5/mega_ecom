@extends('supplier.supplier')

@section('content')
<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Order Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="mb-3">Order Information</h4>
                                <p><strong>Name:</strong> {{ $order->user->name ?? $order->name }} {{ $order->user->surname ?? '' }}</p>
                                <p><strong>Invoice ID:</strong> {{ $order->invoice}}</p>
                            </div>
                            <div class="col-md-6">
                                <h4 class="mb-3">Invoice</h4>
                                <a href="{{ route('generate-pdf.supplier', ['encoded_order_id' => base64_encode($order->id)]) }}" class="btn btn-success" target="_blank">
                                    <i class="fas fa-receipt"></i> Invoice
                                </a>
                            </div>
                        </div>

                        <!-- Product Details -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h4 class="mb-3">Product Details</h4>
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Product Image</th>
                                            <th>Product Name</th>
                                            <th>Quantity</th>
                                            <th>Size</th>
                                            <th>Color</th>
                                            <th>Price per Unit</th>
                                            <th>Total Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($orderDetails as $orderDetail)
                                            <tr>
                                                <td>
                                                    <img src="{{ asset('/images/products/' . $orderDetail->product->feature_image) }}" alt="{{ $orderDetail->product->name }}" style="width: 100px; height: auto;">
                                                </td>
                                                <td>{{ $orderDetail->product->name }}</td>
                                                <td>{{ $orderDetail->quantity }}</td>
                                                <td>{{ $orderDetail->size }}</td>
                                                <td>{{ $orderDetail->color }}</td>
                                                <td>{{ number_format($orderDetail->price_per_unit, 2) }}</td>
                                                <td>{{ number_format($orderDetail->total_price, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection