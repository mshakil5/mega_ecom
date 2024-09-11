@extends('admin.layouts.admin')

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
                            <!-- User Information -->
                            <div class="col-md-6">
                                <h4 class="mb-3">User Information</h4>
                                <p><strong>Name:</strong> {{ $order->user->name ?? $order->name }} {{ $order->user->surname ?? '' }}</p>
                                <p><strong>Email:</strong> {{ $order->user->email ?? $order->email }}</p>
                                <p><strong>Phone:</strong> {{ $order->user->phone ?? $order->phone }}</p>
                                <p><strong>Address:</strong> 
                                    {{ $order->user->house_number ?? $order->house_number }},
                                    {{ $order->user->street_name ?? $order->street_name }},
                                    <br>
                                    {{ $order->user->town ?? $order->town }},
                                    {{ $order->user->postcode ?? $order->postcode }}
                                </p>
                            </div>
                            <!-- Order Information -->
                            <div class="col-md-6">
                                <h4 class="mb-3">Order Information</h4>
                                <p><strong>Invoice:</strong> {{ $order->invoice }}</p>
                                <p><strong>Purchase Date:</strong> {{ \Carbon\Carbon::parse($order->purchase_date)->format('d-m-Y') }}</p>
                                <p><strong>VAT (%):</strong> {{ $order->vat_percent }}</p>
                                <p><strong>VAT Amount:</strong> {{ number_format($order->vat_amount, 2) }}</p>
                                <p><strong>Subtotal:</strong> {{ number_format($order->subtotal_amount, 2) }}</p>
                                <p><strong>Shipping Amount:</strong> {{ number_format($order->shipping_amount, 2) }}</p>
                                <p><strong>Discount Amount:</strong> {{ number_format($order->discount_amount, 2) }}</p>
                                <p><strong>Total Amount:</strong> {{ number_format($order->net_amount, 2) }}</p>
                                <p><strong>Payment Method:</strong> 
                                    @if($order->payment_method === 'paypal')
                                        PayPal
                                    @elseif($order->payment_method === 'stripe')
                                        Stripe
                                    @elseif($order->payment_method === 'cashOnDelivery')
                                        Cash On Delivery
                                    @else
                                        {{ ucfirst($order->payment_method) }}
                                    @endif
                                </p>
                                <p><strong>Status:</strong> 
                                    @if ($order->status === 1)
                                        Pending
                                    @elseif ($order->status === 2)
                                        Processing
                                    @elseif ($order->status === 3)
                                        Packed
                                    @elseif ($order->status === 4)
                                        Shipped
                                    @elseif ($order->status === 5)
                                        Delivered
                                    @elseif ($order->status === 6)
                                        Returned
                                    @elseif ($order->status === 7)
                                        Cancelled
                                    @else
                                        Unknown
                                    @endif
                                </p>
                                @if ($order->order_type === 0)
                                <a href="{{ route('generate-pdf', ['encoded_order_id' => base64_encode($order->id)]) }}" class="btn btn-success" target="_blank">
                                    <i class="fas fa-receipt"></i> Invoice
                                </a>
                                @elseif ($order->order_type === 1)
                                <a href="{{ route('in-house-sell.generate-pdf', ['encoded_order_id' => base64_encode($order->id)]) }}" class="btn btn-success" target="_blank">
                                    <i class="fas fa-receipt"></i> Invoice
                                </a>
                                @endif
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
                                            <th>Supplier</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->orderDetails as $orderDetail)
                                            <tr>
                                                <td>
                                                    @if($orderDetail->product)
                                                        <img src="{{ asset('/images/products/' . $orderDetail->product->feature_image) }}" alt="{{ $orderDetail->product->name }}" style="width: 100px; height: auto;">
                                                    @elseif($order->bundleProduct)
                                                        <img src="{{ asset('/images/bundle_product/' . $order->bundleProduct->feature_image) }}" alt="{{ $order->bundleProduct->name }}" style="width: 100px; height: auto;">
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($orderDetail->product)
                                                        {{ $orderDetail->product->name ?? 'N/A' }}
                                                    @elseif($order->bundleProduct)
                                                        {{ $order->bundleProduct->name }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>

                                                <td>{{ $orderDetail->quantity }}</td>
                                                <td>{{ $orderDetail->size }}</td>
                                                <td>{{ $orderDetail->color }}</td>
                                                <td>{{ number_format($orderDetail->price_per_unit, 2) }}</td>
                                                <td>{{ number_format($orderDetail->total_price, 2) }}</td>
                                                <td>
                                                    @if($orderDetail->supplier)
                                                        {{ $orderDetail->supplier->name }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                            </tr>
                                            @if($orderDetail->buyOneGetOne)
                                                <tr>
                                                    <td colspan="8" style="background-color: #f9f9f9;">
                                                        <strong style="display: block; margin-bottom: 10px;">Free Products:</strong>
                                                        <div style="display: flex; flex-wrap: wrap;">
                                                            @php
                                                                $bogoProductIds = json_decode($orderDetail->buyOneGetOne->get_product_ids);
                                                            @endphp
                                                            @if(is_array($bogoProductIds))
                                                                @foreach($bogoProductIds as $productId)
                                                                    @if($productId)
                                                                        @php
                                                                            $bogoProduct = \App\Models\Product::find($productId);
                                                                        @endphp
                                                                        @if($bogoProduct)
                                                                            <div style="display: flex; flex-direction: column; align-items: center; margin-right: 20px; margin-bottom: 10px;">
                                                                                <img src="{{ asset('/images/products/' . $bogoProduct->feature_image) }}" alt="{{ $bogoProduct->name }}" style="width: 100px; height: auto; margin-bottom: 5px;">
                                                                                <span>{{ $bogoProduct->name }}</span>
                                                                            </div>
                                                                        @endif
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                            @if($order->bundleProduct)
                                                <tr>
                                                    <td colspan="8" style="background-color: #f1f1f1;">
                                                        <strong style="display: block; margin-bottom: 10px;">Bundle Products:</strong>
                                                        <div style="display: flex; flex-wrap: wrap;">
                                                            @php
                                                                $bundleProductIds = json_decode($orderDetail->bundle_product_ids);
                                                            @endphp
                                                            @if(is_array($bundleProductIds))
                                                                @foreach($bundleProductIds as $productId)
                                                                    @if($productId)
                                                                        @php
                                                                            $bundleProduct = \App\Models\Product::find($productId);
                                                                        @endphp
                                                                        @if($bundleProduct)
                                                                            <div style="display: flex; flex-direction: column; align-items: center; margin-right: 20px; margin-bottom: 10px;">
                                                                                <img src="{{ asset('images/products/' . $bundleProduct->feature_image) }}" alt="{{ $bundleProduct->name }}" style="width: 100px; height: auto; margin-bottom: 5px;">
                                                                                <span>{{ $bundleProduct->name }}</span>
                                                                            </div>
                                                                        @else
                                                                            <div style="display: flex; flex-direction: column; align-items: center; margin-right: 20px; margin-bottom: 10px;">
                                                                                <span>Product not found</span>
                                                                            </div>
                                                                        @endif
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
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
