@extends('admin.layouts.admin')

@section('content')

<style>
  .quotation-table tr {
    line-height: 1.5;
  }
  .quotation-table th, .quotation-table td {
    padding: 5px;
  }
  .quotation-table td {
    text-align: right;
  }
</style>

<section class="content  pt-3">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">

        <a href="{{ url()->previous() }}" class="btn btn-secondary mb-3">
            <i class="fas fa-arrow-left"></i> Back
        </a>

          <!-- Main content -->
          <div class="invoice p-3 mb-3">
            <!-- title row -->
            <div class="row d-none">
              <div class="col-12">
                <h4>
                  <i class="fas fa-globe"></i> Whole Saler Information
                  <small class="float-right">Date: {{ \Carbon\Carbon::parse($order->purchase_date)->format('d-m-Y') }}</small>
                </h4>
              </div>
              <!-- /.col -->
            </div>
            <!-- info row -->
            <div class="row invoice-info">
              <div class="col-sm-4 invoice-col">
                <address>
                    <strong>Business Name:</strong> {{ $order->user->surname ?? $order->surname }} <br>
                    <strong>Name:</strong> {{ $order->user->name ?? '' }}<br>
                    <strong>Email:</strong> {{ $order->user->email ?? $order->email }}<br>
                    <strong>Phone:</strong> {{ $order->user->phone ?? $order->phone }}<br>
                    <strong>Address:</strong> {!! $order->user?->address ?? '' !!}
                </address>
                
                
                
              </div>
              <!-- /.col -->
              <div class="col-sm-4 invoice-col">  </div>
              <!-- /.col -->
              <div class="col-sm-4 invoice-col">
                <h4 class="mb-3">@if($order->order_type == 1 )Order Information @elseif($order->order_type == 2)Quotation Information @endif</h4>

                <strong>{{ $order->order_type == 1 ? 'Invoice:' : ($order->order_type == 2 ? 'Quotation:' : '') }}</strong> {{ $order->invoice }} <br>

                <strong>Date:</strong> {{ \Carbon\Carbon::parse($order->purchase_date)->format('d-m-Y') }} <br>

                @if($order->order_type != 2)
                <strong>Payment Method:</strong> 
                    @if($order->payment_method === 'paypal')
                        PayPal
                    @elseif($order->payment_method === 'stripe')
                        Stripe
                    @elseif($order->payment_method === 'cashOnDelivery')
                        Cash On Delivery
                    @else
                        {{ ucfirst($order->payment_method) }}
                    @endif
                    <br>
                <strong>Status:</strong> 
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
                    <br>
                        <strong>Order Type:</strong> 
                        {{ $order->order_type === 1 ? 'In House' : ($order->order_type === 2 ? 'Quotation' : 'Frontend') }}
                    <br>
                <div class="d-none"> <strong>Note:</strong> {!! $order->note !!} </div>
                @endif
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->

            <!-- Table row -->
            <div class="row mt-3">
              <div class="col-12 table-responsive">
                
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="text-align: center">Product Image</th>
                            <th style="text-align: center">Product</th>
                            <th style="text-align: center">Quantity</th>
                            <th style="text-align: center">Size</th>
                            <th style="text-align: center">Color</th>
                            <th style="text-align: right">Price per Unit</th>
                            <th style="text-align: right">Total Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderDetails as $orderDetail)
                            <tr>
                                <td style="text-align: center">
                                    @if($orderDetail->product)
                                        <img src="{{ asset('/images/products/' . $orderDetail->product->feature_image) }}" alt="{{ $orderDetail->product->name }}" style="width: 100px; height: auto;">
                                    @elseif($order->bundleProduct)
                                        <img src="{{ asset('/images/bundle_product/' . $order->bundleProduct->feature_image) }}" alt="{{ $order->bundleProduct->name }}" style="width: 100px; height: auto;">
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td style="text-align: center">
                                    @if($orderDetail->product)
                                    {{ $orderDetail->product->product_code ?? '' }}-{{ $orderDetail->product->name ?? '' }}
                                    @elseif($order->bundleProduct)
                                        {{ $order->bundleProduct->name }}
                                    @else
                                        N/A
                                    @endif
                                </td>

                                <td style="text-align: center">{{ $orderDetail->quantity }}</td>
                                <td style="text-align: center">{{ $orderDetail->size }}</td>
                                <td style="text-align: center">{{ $orderDetail->color }}</td>
                                <td style="text-align: right">{{ number_format($orderDetail->price_per_unit, 2) }}</td>
                                <td style="text-align: right">{{ number_format($orderDetail->total_price, 2) }}</td>
                                <td class="d-none">
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
              <!-- /.col -->
            </div>
            <!-- /.row -->

            <div class="row">
              <!-- accepted payments column -->
              <div class="col-8">
              @if($order->order_type === 2)
                    <a href="{{ route('order-edit', ['orderId' => $order->id]) }}" class="btn btn-success">
                        <i class="far fa-credit-card"></i> Create Order
                    </a>
                @endif
                
                @if ($order->order_type === 0)
                <a href="{{ route('generate-pdf', ['encoded_order_id' => base64_encode($order->id)]) }}" class="btn btn-success" target="_blank"  style="margin-right: 5px;">
                    <i class="fas fa-receipt"></i> Download Invoice
                </a>
                @elseif ($order->order_type === 2)
                <a href="{{ route('orders.download-pdf', ['encoded_order_id' => base64_encode($order->id)]) }}" class="btn btn-success" target="_blank"  style="margin-right: 5px;">
                    <i class="fas fa-receipt"></i> Download Quotation
                </a>
                @else
                <a href="{{ route('in-house-sell.generate-pdf', ['encoded_order_id' => base64_encode($order->id)]) }}" class="btn btn-success" target="_blank"  style="margin-right: 5px;">
                    <i class="fas fa-receipt"></i> Download Invoice
                </a>
                @endif
              </div>
              <!-- /.col -->
              <div class="col-4">
                
                <div class="table-responsive">
                  <table class="table quotation-table">
                    <tr>
                      <th>Subtotal:</th>
                      <td>{{ number_format($order->subtotal_amount, 2) }}</td>
                    </tr>
                    @if($order->vat_amount > 0)
                    <tr>
                      <th>Vat Amount</th>
                      <td> {{ $order->vat_amount }}</td>
                    </tr>
                    @endif
                    @if($order->shipping_amount > 0)
                    <tr>
                      <th>Shipping:</th>
                      <td>{{ number_format($order->shipping_amount, 2) }}</td>
                    </tr>
                    @endif
                    @if($order->discount_amount > 0)
                    <tr>
                      <th>Discount:</th>
                      <td>{{ number_format($order->discount_amount, 2) }}</td>
                    </tr>
                    @endif
                    <tr>
                      <th>Total:</th>
                      <td>{{ number_format($order->net_amount, 2) }}</td>
                    </tr>
                  </table>


                </div>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->

            <!-- this row will not appear when printing -->
          </div>
          <!-- /.invoice -->
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </section>




<section class="content pt-3 d-none" id="contentContainer">
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
                            </div>
                            <!-- Order Information -->
                            <div class="col-md-6">
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
