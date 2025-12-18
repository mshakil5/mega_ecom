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

<section class="content pt-3">
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
            </div>
            
            <!-- info row -->
            <div class="row invoice-info">
              <div class="col-sm-4 invoice-col">
                <address>
                    <strong>Business Name:</strong> {{ $order->user->surname ?? $order->surname }} <br>
                    <strong>Name:</strong> {{ $order->user->name ?? '' }}<br>
                    <strong>Email:</strong> {{ $order->user->email ?? $order->email }}<br>
                    <strong>Phone:</strong> {{ $order->user->phone ?? $order->phone }}<br>
                    @if ($order->user?->address)
                      <strong>Address:</strong> {!! $order->user?->address ?? '' !!}
                    @else
                      <strong>Address:</strong>
                      {{ $order->address ?? $order->house_number ?? '' }}
                      {{ $order->street_name ?? '' }}
                      {{ $order->address_first_line ?? $order->house_number ?? '' }}
                      {{ $order->address_second_line ?? '' }}
                      {{ $order->town ?? '' }}
                      {{ $order->postcode ?? '' }}
                    @endif
                </address>
              </div>
              
              <div class="col-sm-4 invoice-col"></div>
              
              <div class="col-sm-4 invoice-col">
                <h4 class="mb-3">@if($order->order_type == 1 )Order Information @elseif($order->order_type == 2)Quotation Information @endif</h4>

                <strong>{{ $order->order_type == 1 ? 'Invoice:' : ($order->order_type == 2 ? 'Quotation:' : '') }}</strong> {{ $order->invoice }} <br>

                <strong>Date:</strong> {{ \Carbon\Carbon::parse($order->purchase_date)->format('d-m-Y') }} <br>

                @if($order->order_type != 2)
                <strong>Payment Method:</strong> 
                    @switch($order->payment_method)
                        @case('paypal')
                            PayPal
                            @break
                        @case('stripe')
                            Stripe
                            @break
                        @case('cash_on_delivery')
                        @case('cashOnDelivery')
                            Cash On Delivery
                            @break
                        @default
                            {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}
                    @endswitch
                    <br>
                    
                <strong>Status:</strong> 
                    @switch($order->status)
                        @case(1)
                        @case('pending')
                            <span class="badge badge-warning">Pending</span>
                            @break
                        @case(2)
                        @case('processing')
                            <span class="badge badge-info">Processing</span>
                            @break
                        @case(3)
                        @case('packed')
                            <span class="badge badge-primary">Packed</span>
                            @break
                        @case(4)
                        @case('shipped')
                            <span class="badge badge-info">Shipped</span>
                            @break
                        @case(5)
                        @case('delivered')
                            <span class="badge badge-success">Delivered</span>
                            @break
                        @case(6)
                        @case('returned')
                            <span class="badge badge-secondary">Returned</span>
                            @break
                        @case(7)
                        @case('cancelled')
                            <span class="badge badge-danger">Cancelled</span>
                            @break
                        @default
                            <span class="badge badge-secondary">Unknown</span>
                    @endswitch
                    <br>
                    
                <strong>Order Type:</strong> 
                    @switch($order->order_type)
                        @case(1)
                            In House
                            @break
                        @case(2)
                            Quotation
                            @break
                        @case(3)
                            Wholesale
                            @break
                        @default
                            Frontend
                    @endswitch
                    <br>
                <div class="d-none"> <strong>Note:</strong> {!! $order->note !!} </div>
                @endif
              </div>
            </div>

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
                            <th style="text-align: center">Type</th>
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
                                    @if($orderDetail->product?->isZip())
                                        <br><small>(Zip: {{ $orderDetail->zip == 1 ? 'Yes' : 'No' }})</small>
                                    @endif
                                </td>
                                <td style="text-align: center">{{ $orderDetail->quantity }}</td>
                                <td style="text-align: center">{{ $orderDetail->size ?? 'N/A' }}</td>
                                <td style="text-align: center">{{ $orderDetail->color ?? 'N/A' }}</td>
                                <td style="text-align: center">{{ $orderDetail->type->name ?? '' }}</td>
                                <td style="text-align: right">£{{ number_format($orderDetail->price_per_unit, 2) }}</td>
                                <td style="text-align: right">£{{ number_format($orderDetail->total_price, 2) }}</td>
                            </tr>
                            
                            <!-- Customizations Section -->
                            @if ($orderDetail->orderCustomisations && $orderDetail->orderCustomisations->count())
                                <tr>
                                    <td colspan="8" style="background-color: #f8f9fa; padding: 15px;">
                                        <div class="mt-2">
                                            <small><strong><i class="fas fa-pen-fancy"></i> Customizations:</strong></small>
                                            <div class="accordion mt-2" id="customizationAccordion{{ $orderDetail->id }}">
                                                @foreach ($orderDetail->orderCustomisations as $index => $c)
                                                    @php
                                                        $data = json_decode($c->data, true) ?? [];
                                                        $cardId = 'card-' . $orderDetail->id . '-' . $index;
                                                    @endphp
                                                    <div class="card mb-1">
                                                        <div class="card-header p-0" id="heading{{ $cardId }}">
                                                            <h2 class="mb-0">
                                                                <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" 
                                                                    data-target="#collapse{{ $cardId }}" 
                                                                    aria-expanded="false" aria-controls="collapse{{ $cardId }}"
                                                                    style="padding: 8px 12px; font-size: 13px; text-decoration: none;">
                                                                    <i class="fas fa-chevron-down"></i>
                                                                    {{ ucfirst($c->method ?? 'Custom') }}
                                                                    @if ($c->position)
                                                                        - {{ $c->position }}
                                                                    @endif
                                                                    <small>({{ ucfirst($c->customization_type) }})</small>
                                                                </button>
                                                            </h2>
                                                        </div>
                                                        <div id="collapse{{ $cardId }}" class="collapse" aria-labelledby="heading{{ $cardId }}" data-parent="#customizationAccordion{{ $orderDetail->id }}">
                                                            <div class="card-body" style="padding: 12px;">
                                                                
                                                                {{-- Text Customization --}}
                                                                @if ($c->customization_type === 'text' && isset($data['text']))
                                                                    <div class="mb-2">
                                                                        <strong>Text:</strong> {{ $data['text'] }}<br>
                                                                        <strong>Font:</strong> {{ $data['fontFamily'] ?? 'Default' }}<br>
                                                                        <strong>Size:</strong> {{ $data['fontSize'] ?? 'N/A' }}<br>
                                                                        <strong>Color:</strong> 
                                                                        <span style="display: inline-block; width: 20px; height: 20px; background-color: {{ $data['color'] ?? '#000000' }}; border: 1px solid #ccc; vertical-align: middle;"></span>
                                                                        {{ $data['color'] ?? 'N/A' }}
                                                                    </div>
                                                                @endif

                                                                {{-- Image Customization --}}
                                                                @if ($c->customization_type === 'image' && isset($data['src']))
                                                                    <div class="mb-2">
                                                                        <div style="margin-bottom: 10px;">
                                                                            <img src="{{ $data['src'] }}" alt="Custom Image" style="max-height: 120px; border: 1px solid #ddd; padding: 5px; border-radius: 4px;">
                                                                        </div>
                                                                        <strong>Method:</strong> {{ $c->method ?? 'N/A' }}<br>
                                                                        <strong>Position:</strong> {{ $c->position ?? 'N/A' }}<br>
                                                                    </div>
                                                                @endif

                                                                {{-- Other customization types --}}
                                                                @if ($c->customization_type !== 'text' && $c->customization_type !== 'image')
                                                                    <div class="mb-2">
                                                                        <strong>Type:</strong> {{ ucfirst($c->customization_type) }}<br>
                                                                        <strong>Method:</strong> {{ $c->method ?? 'N/A' }}<br>
                                                                        <strong>Position:</strong> {{ $c->position ?? 'N/A' }}<br>
                                                                        @if(is_array($data) && count($data) > 0)
                                                                            <strong>Details:</strong><br>
                                                                            @foreach($data as $key => $value)
                                                                                <small>{{ ucfirst(str_replace('_', ' ', $key)) }}: {{ is_array($value) ? json_encode($value) : $value }}</small><br>
                                                                            @endforeach
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            
                            <!-- Buy One Get One Section -->
                            @if($orderDetail->buyOneGetOne)
                                <tr>
                                    <td colspan="8" style="background-color: #f9f9f9;">
                                        <strong style="display: block; margin-bottom: 10px;">
                                            <i class="fas fa-gift"></i> Free Products
                                        </strong>
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
                            
                            <!-- Bundle Products Section -->
                            @if($order->bundleProduct && $orderDetail->bundle_product_ids)
                                <tr>
                                    <td colspan="8" style="background-color: #f1f1f1;">
                                        <strong style="display: block; margin-bottom: 10px;">
                                            <i class="fas fa-boxes"></i> Bundle Products
                                        </strong>
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
                                                                <span class="text-muted">Product not found</span>
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

            <!-- Summary Section -->
            <div class="row">
              <div class="col-8">
                @if($order->order_type === 2)
                    <a href="{{ route('order-edit', ['orderId' => $order->id]) }}" class="btn btn-success">
                        <i class="far fa-credit-card"></i> Make Order
                    </a>
                @endif
                
                @if ($order->order_type === 0)
                    <a href="{{ route('generate-pdf', ['encoded_order_id' => base64_encode($order->id)]) }}" class="btn btn-success" target="_blank" style="margin-right: 5px;">
                        <i class="fas fa-receipt"></i> Download Invoice
                    </a>
                @elseif ($order->order_type === 2)
                    <a href="{{ route('orders.download-pdf', ['encoded_order_id' => base64_encode($order->id)]) }}" class="btn btn-success" target="_blank" style="margin-right: 5px;">
                        <i class="fas fa-receipt"></i> Download Quotation
                    </a>
                @else
                    <a href="{{ route('in-house-sell.generate-pdf', ['encoded_order_id' => base64_encode($order->id)]) }}" class="btn btn-success" target="_blank" style="margin-right: 5px;">
                        <i class="fas fa-receipt"></i> Download Invoice
                    </a>
                @endif
              </div>
              
              <div class="col-4">
                <div class="table-responsive">
                  <table class="table quotation-table">
                    <tr>
                      <th>Subtotal:</th>
                      <td>£{{ number_format($order->subtotal_amount, 2) }}</td>
                    </tr>
                    @if($order->vat_amount > 0)
                        <tr>
                          <th>VAT ({{ $order->vat_percent ?? 20 }}%):</th>
                          <td>£{{ number_format($order->vat_amount, 2) }}</td>
                        </tr>
                    @endif
                    @if($order->shipping_amount > 0)
                        <tr>
                          <th>Shipping:</th>
                          <td>£{{ number_format($order->shipping_amount, 2) }}</td>
                        </tr>
                    @endif
                    @if($order->discount_amount > 0)
                        <tr>
                          <th>Discount:</th>
                          <td>-£{{ number_format($order->discount_amount, 2) }}</td>
                        </tr>
                    @endif
                    <tr style="font-weight: bold; font-size: 16px; border-top: 2px solid #333;">
                      <th>Total:</th>
                      <td>£{{ number_format($order->net_amount, 2) }}</td>
                    </tr>
                  </table>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
</section>

@endsection