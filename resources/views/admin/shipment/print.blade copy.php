@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3">
    <div class="ermsg"></div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-12">
                <div class="d-flex justify-content-between no-print mb-3">
                    <a href="{{ route('admin.shipping') }}" class="btn btn-secondary">Back</a>
                    <button onclick="window.print()" class="fa fa-print btn btn-default">Print</button>
                </div>
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Shipment Details - {{ $shipment->shipping->shipping_name }}</h3>
                    </div>
                    <div class="card-body">
                        <form id="editShipmentForm">
                            <input type="hidden" id="id" value="{{ $shipment->id }}">
                            <div class="mb-4 d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Shipping ID:</strong> <span>{{ $shipment->shipping->shipping_id }}</span><br>
                                    <strong>Shipping Date:</strong> <span>{{ \Carbon\Carbon::parse($shipment->shipping->shipping_date)->format('d-m-Y') }}</span><br>        
                                </div>
                                <div>
                                    <strong>Total Product Quantity:</strong> <span>{{ $shipment->total_product_quantity }}</span> <br>
                                    <strong>Total Missing Product Quantity:</strong> <span >{{ $shipment->total_missing_quantity }}</span> <br>
                                    <strong>Warehouse:</strong> <span>{{ $shipment->shipmentDetails->first()->warehouse->name }} - {{ $shipment->shipmentDetails->first()->warehouse->location }}</span>
                                </div>
                            </div>

                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Supplier</th>
                                        <th>Product</th>
                                        <th>Size</th>
                                        <th>Color</th>
                                        <th>Purchased Quantity</th>
                                        <th>Shipped Quantity</th>
                                        <th>Missing Quantity</th>
                                        <th>Purchase Price Per Unit</th>
                                        <th>Ground Price</th>
                                        <th>Profit Margin(%)</th>
                                        <th>Current Selling Price</th>
                                        <th>New Selling Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($shipment->shipmentDetails as $detail)
                                    <tr>
                                        <td>
                                            {{ $detail->supplier->name ?? '' }}
                                        </td>
                                        <td>
                                            {{ $detail->product->product_code ? $detail->product->product_code . '-' : '' }}{{ $detail->product->name ?? '' }}
                                        </td>
                                        <td>{{ $detail->size ?? '' }}</td>
                                        <td>{{ $detail->color ?? '' }}</td>
                                        @php
                                        $filteredStock = $detail->purchaseHistory->product->stock ? $detail->purchaseHistory->product->stock
                                        ->where('product_id', $detail->purchaseHistory->product_id)
                                        ->where('size', $detail->purchaseHistory->product_size)
                                        ->where('color', $detail->purchaseHistory->product_color)
                                        ->where('quantity', '>', 0)
                                        ->orderBy('id', 'desc')
                                        : collect();

                                        $currentStock = $filteredStock->sum('quantity');
                                        $currentSellingPrice = $filteredStock->first()->selling_price ?? 0;
                                        @endphp
                                        <td>{{ $detail->quantity }}</td>
                                        <td>{{ $detail->quantity + $detail->missing_quantity }}</td>
                                        <td>{{ $detail->missing_quantity }}</td>
                                        <td>{{ number_format($detail->price_per_unit, 2) }}</td>
                                        <td>{{ number_format($detail->ground_price_per_unit, 2) }}</td>
                                        <td>
                                          {{ number_format($detail->profit_margin, 0) }}
                                        </td>
                                        <td>{{ number_format($currentSellingPrice, 2) }}</td>
                                        <td>{{ number_format($detail->selling_price, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div class="col-sm-10 mt-5 mb-5">
                                <div class="row">
                                    <div class="col-sm-4">

                                        <div class="row mt-1">
                                            <div class="col-sm-12 d-flex align-items-center">
                                                <strong>Total Profit:</strong> <span style="width: 100px; margin-left: auto;">{{ $shipment->total_profit ?? '0.00' }}</span>                                 
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-sm-12 d-flex align-items-center">
                                                <strong>Target Budget:</strong> <span id="targetBudget" style="width: 100px; margin-left: auto;">{{ $shipment->target_budget ?? '0.00' }}</span>  
                                            </div>
                                        </div>

                                        <div class="row mt-1">
                                            <div class="col-sm-12 d-flex align-items-center">
                                                <strong>Total Purchase Cost:</strong> <span style="width: 100px; margin-left: auto;">{{ $shipment->total_purchase_cost ?? '0.00' }}</span>
                                            </div>
                                        </div>

                                        <div class="row mt-1">
                                            <div class="col-sm-12 d-flex align-items-center">
                                                <strong>Target Additional Cost:</strong> <span style="width: 100px; margin-left: auto;">{{ $shipment->total_additional_cost ?? '0.00' }}</span>
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-sm-12 d-flex align-items-center">
                                                <strong>Total Cost Of This Shipment:</strong> <span id="totalCostOfShipment" style="width: 100px; margin-left: auto;">{{ $shipment->total_cost_of_shipment ?? '0.00' }}</span>
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-sm-12 d-flex align-items-center">
                                                <strong>Total Quantiy In PCS:</strong> <span id="totalQuantityInPcs" style="width: 100px; margin-left: auto;">{{ $shipment->total_product_quantity ?? '0.00' }}</span>
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-sm-12 d-flex align-items-center">
                                                <strong>Cost Per Piece:</strong> <span id="costPerPiece" style="width: 100px; margin-left: auto;"></span>
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-sm-12 mt-1" id="messageText">
                                                <span></span>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="col-sm-2">
                                    </div>

                                    <div class="col-sm-6">
                                        <table style="width: 100%; border-collapse: collapse; text-align: left;" border="1">
                                            <thead>
                                                <tr>
                                                    <th style="padding: 8px;">Chart of Account</th>
                                                    <th style="padding: 8px;">Payment Type</th>
                                                    <th style="padding: 8px;">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($shipment->transactions as $transaction)
                                                    <tr>
                                                        <td style="padding: 8px;">{{ $transaction->chartOfAccount->account_name ?? '' }}</td>
                                                        <td style="padding: 8px;">{{ $transaction->payment_type ?? '' }}</td>
                                                        <td style="padding: 8px;">{{ number_format($transaction->amount, 2) ?? '0.00' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('script')
<script>
    $(window).on('load', function() {
        setTimeout(function() {
            window.print();
        }, 2000);
    });
</script>

<script>
    $(document).ready(function () {
        const totalCostOfShipment = parseFloat($('#totalCostOfShipment').text()) || 0;
        const totalQuantityInPcs = parseFloat($('#totalQuantityInPcs').text()) || 0;

        let costPerPiece = 0;
        if (totalQuantityInPcs > 0) {
            costPerPiece = totalCostOfShipment / totalQuantityInPcs;
        }
        $('#costPerPiece').text(costPerPiece.toFixed(2));

        const targetBudget = parseFloat($('#targetBudget').text()) || 0;
        let difference = 0;
        const $budgetDifference = $('#budgetDifference');
        const $messageContainer = $('#messageContainer');
        const $messageText = $('#messageText');

        if (targetBudget > 0) {
            difference = targetBudget - totalCostOfShipment;
            $budgetDifference.val(difference.toFixed(2));
            $messageContainer.removeClass('d-none');

            if (difference < 0) {
                $messageText.html(`<span style="color: red;">You're over budget by ${difference.toFixed(2)}</span>`);
            } else {
                $messageText.html(`<span style="color: green;">You're under budget by ${difference.toFixed(2)}</span>`);
            }
        }
    });
</script>

@endsection