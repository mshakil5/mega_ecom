@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="d-flex justify-content-between no-print mb-3">
                    <a href="{{ route('admin.shipping') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <button onclick="window.print()" class="btn btn-info">
                        <i class="fas fa-print"></i> Print
                    </button>
                </div>

                <div class="card shadow-lg">
                    <div class="card-header bg-info text-white text-center">
                        <h1 class="mb-0">Left Over Garments Costing Sheet SapphireTradeLinks</h1>
                    </div>
                    <div class="card-body">
                        <div class="mb-4 d-flex">
                            <div class="col-4">
                                <div class="alert alert-primary d-flex justify-content-between align-items-center">
                                    <h4 class="mb-0">Target Budget:</h4>
                                    <h4 class="mb-0">€ {{ number_format($shipment->target_budget, 2) }}</h4>
                                </div>
                                <div class="alert alert-warning d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Total Cost of Shipment:</h5>
                                    <h5 class="mb-0">€ {{ number_format($shipment->total_cost_of_shipment, 2) }}</h5>
                                </div>
                                <div class="alert {{ $shipment->budget_over < 0 ? 'alert-danger' : 'alert-success' }} d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">{{ $shipment->budget_over < 0 ? 'Over Budget By:' : 'Under Budget By:' }}</h6>
                                    <h6 class="mb-0">€ {{ number_format($shipment->budget_over, 2) }}</h6>
                                </div>
                            </div>
                            <div class="col-8">
                                <table class="table table-hover table-striped text-center">
                                    <thead class="bg-primary text-white">
                                        <tr>
                                            <th>Item</th>
                                            <th>Description</th>
                                            <th>Cost</th>
                                            <th>Qty</th>
                                            <th>Amount</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Product Cost</td>
                                            <td>Cost of product purchase</td>
                                            <td>€ {{ number_format($shipment->total_purchase_cost, 2) }}</td>
                                            <td>1</td>
                                            <td>€ {{ number_format($shipment->total_purchase_cost, 2) }}</td>
                                            <td></td>
                                        </tr>
                                        @foreach($shipment->transactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->chartOfAccount->account_name ?? '' }}</td>
                                            <td></td>
                                            <td>€ {{ number_format($transaction->amount, 2) ?? '0.00' }}</td>
                                            <td>1</td>
                                            <td>€ {{ number_format($transaction->amount, 2) ?? '0.00' }}</td>
                                            <td>{{ $transaction->payment_type ?? '' }} Payment</td>
                                        </tr>
                                        @endforeach
                                        <tr>
                                            <td colspan="2" class="text-center"><strong>Total</strong></td>
                                            <td colspan="3" class="text-right"><strong>€ {{ number_format($shipment->total_cost_of_shipment, 2) }}</strong></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mb-4 d-flex">
                            <div class="col-md-4">
                                <div class="card bg-light shadow">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0">Summary</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-3">
                                            <span class="font-weight-bold fs-5">Total Purchase Cost:</span>
                                            <span class="fs-5">€ {{ number_format($shipment->total_purchase_cost ?? 0, 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-3">
                                            <span class="font-weight-bold fs-5">Total Additional Cost:</span>
                                            <span class="fs-5">€ {{ number_format($shipment->total_additional_cost ?? 0, 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-3">
                                            <span class="font-weight-bold fs-5">Total Cost Of The Shipment:</span>
                                            <span class="fs-5">€ {{ number_format($shipment->total_cost_of_shipment ?? 0, 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-3">
                                            <span class="font-weight-bold fs-5">Total Quantity (PCS):</span>
                                            <span class="fs-5">{{ $shipment->total_product_quantity ?? '0' }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-3">
                                            <span class="font-weight-bold fs-5">Cost Per Piece:</span>
                                            <span class="fs-5">€ {{ $shipment->total_product_quantity > 0 ? number_format($shipment->total_cost_of_shipment /$shipment->total_product_quantity, 2) : '0.00' }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-3">
                                            <span class="font-weight-bold fs-5">Selling Price With Markup Per Piece:</span>
                                            <span class="fs-5">
                                                @php
                                                    $totalSellingPrice = $shipment->shipmentDetails->sum(function($detail) {
                                                        return $detail->selling_price * $detail->quantity;
                                                    });
                                                    $totalQuantity = $shipment->shipmentDetails->sum('quantity');
                                                    $sellingPricePerPiece = $totalQuantity > 0 ? $totalSellingPrice / $totalQuantity : 0;
                                                @endphp
                                                € {{ number_format($sellingPricePerPiece, 2) }}
                                            </span>
                                        </div>  
                                        <div class="d-flex justify-content-between mb-3">
                                            <span class="font-weight-bold fs-5">Total Selling Price:</span>
                                            <span class="fs-5">€ {{ number_format($totalSellingPrice, 2) }}</span>
                                        </div>                           
                                        <div class="d-flex justify-content-between mb-3">
                                            <span class="font-weight-bold fs-5 bg-success text-white rounded px-2 py-1">Profit On The Full Shipment:</span>
                                            <span class="fs-5 bg-success text-white rounded px-2 py-1">€ {{ number_format($shipment->total_profit ?? 0, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-8">
                                <table class="table table-hover table-striped text-center">
                                    <thead class="bg-primary text-white">
                                        <tr>
                                            <th>Sl</th>
                                            <th>Item Description</th>
                                            <th>Profit Margin (Percentange)</th>
                                            <th>Quantity</th>
                                            <th>Unit Price</th>
                                            <th>Total Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($shipment->shipmentDetails as $detail)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $detail->product->product_code ? $detail->product->product_code . '-' : '' }}{{ $detail->product->name ?? '' }} ({{ $detail->size ?? '' }} {{ $detail->color ?? '' }})</td>
                                            <td>{{ number_format($detail->profit_margin, 0) }}%</td>
                                            <td>{{ $detail->quantity }}</td>
                                            <td>€ {{ number_format($detail->price_per_unit, 2) }}</td>
                                            <td>€ {{ number_format($detail->price_per_unit * $detail->quantity, 2) }}</td>
                                        </tr>
                                        @endforeach
                                        <tr>
                                            <td colspan="3" class="text-center"><strong>Total</strong></td>
                                            <td colspan="1" class="text-center"><strong>{{ $shipment->shipmentDetails->sum('quantity') }}</strong></td>
                                            <td colspan="2" class="text-right"><strong>€ {{ number_format($shipment->shipmentDetails->sum(function($detail) { return $detail->price_per_unit * $detail->quantity; }), 2) }}</strong></td>
                                        </tr>
                                    </tbody>
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

@section('script')
<!-- <script>
    $(window).on('load', function() {
        setTimeout(function() {
            window.print();
        }, 2000);
    });
</script> -->

@endsection