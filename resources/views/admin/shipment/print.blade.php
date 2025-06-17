@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3 bg-white">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="d-flex justify-content-between no-print mb-3">
                    <a href="{{ route('admin.shipping') }}" class="btn btn-info">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <button onclick="window.print()" class="btn btn-info">
                        <i class="fas fa-print"></i> Print
                    </button>
                </div>

                <div class="">
                    <div class="card-header bg-info text-white text-center">
                        <h1 class="mb-0">Left Over Garments Costing Sheet SapphireTradeLinks</h1>
                    </div>
                    <div class="card-body">
                        <div class="mb-4 d-flex">
                            <div class="col-4">
                                <div class="alert alert-primary d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Target Budget:</h6>
                                    <h6 class="mb-0">£{{ number_format($shipment->target_budget, 2) }}</h6>
                                </div>
                                <div class="alert alert-warning d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Total Cost Shipment:</h6>
                                    <h6 class="mb-0">£{{ number_format($shipment->total_cost_of_shipment, 2) }}</h6>
                                </div>
                                <div class="alert {{ $shipment->budget_over < 0 ? 'alert-danger' : 'alert-success' }} d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">{{ $shipment->budget_over < 0 ? 'Over Budget By:' : 'Under Budget By:' }}</h6>
                                    <h6 class="mb-0">£{{ number_format($shipment->budget_over, 2) }}</h6>
                                </div>

                                <div class="card bg-light mt-4">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0">Shipment Summary</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-3">
                                            <span class="font-weight-bold fs-5">Shipment Date:</span>
                                            <span class="fs-5">{{ \Carbon\Carbon::parse($shipment->shipping->shipping_date)->format('d-m-Y') }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-3">
                                            <span class="font-weight-bold fs-5">Shipment ID:</span>
                                            <span class="fs-5">{{ $shipment->shipping->shipping_id }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-3">
                                            <span class="font-weight-bold fs-5">Shipment Name:</span>
                                            <span class="fs-5">{{ $shipment->shipping->shipping_name }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-3">
                                            <span class="font-weight-bold fs-5">Total Purchase Cost:</span>
                                            <span class="fs-5">£{{ number_format($shipment->total_purchase_cost ?? 0, 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-3">
                                            <span class="font-weight-bold fs-5">Total Additional Cost:</span>
                                            <span class="fs-5">£{{ number_format($shipment->total_additional_cost ?? 0, 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-3">
                                            <span class="font-weight-bold fs-5">Total Cost Of The Shipment:</span>
                                            <span class="fs-5">£{{ number_format($shipment->total_cost_of_shipment ?? 0, 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-3">
                                            <span class="font-weight-bold fs-5">Shipped Qty (PCS):</span>
                                            <span class="fs-5">{{ $shipment->shipmentDetails->sum('shipped_quantity') ?? '0' }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-3">
                                            <span class="font-weight-bold fs-5">Missing/Damaged Qty (PCS):</span>
                                            <span class="fs-5">{{ $shipment->total_missing_quantity ?? '0' }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-3">
                                            <span class="font-weight-bold fs-5">Total Sample Qty (PCS):</span>
                                            <span class="fs-5">{{ $shipment->shipmentDetails->sum('sample_quantity') ?? '0' }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-3">
                                            <span class="font-weight-bold fs-5">Total Saleable Qty (PCS):</span>
                                            <span class="fs-5">{{ $shipment->total_product_quantity ?? '0' }}</span>
                                        </div>

                                        @php
                                            $totalSellingPrice = $shipment->shipmentDetails->sum(function($detail) {
                                                return $detail->selling_price * $detail->quantity;
                                            });
                                            $totalQuantity = $shipment->shipmentDetails->sum('quantity');
                                            $sellingPricePerPiece = $totalQuantity > 0 ? $totalSellingPrice / $totalQuantity : 0;
                                        @endphp
                                        {{-- 
                                        <div class="d-flex justify-content-between mb-3">
                                            <span class="font-weight-bold fs-5">Cost Per Piece:</span>
                                            <span class="fs-5">£{{ $shipment->total_product_quantity > 0 ? number_format($shipment->total_cost_of_shipment / $shipment->total_product_quantity, 2) : '0.00' }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-3">
                                            <span class="font-weight-bold fs-5">Selling Price With Markup Per Piece:</span>
                                            <span class="fs-5">
                                            </span>
                                        </div>
                                        --}}
                                        <div class="d-flex justify-content-between mb-3">
                                            <span class="font-weight-bold fs-5">Total Selling Price:</span>
                                            <span class="fs-5">£{{ number_format($totalSellingPrice, 2) }}</span>
                                        </div>

                                        @php
                                            $totalProfitMargin = 0;
                                            $totalDetails = count($shipment->shipmentDetails);

                                            foreach ($shipment->shipmentDetails as $detail) {
                                                $totalProfitMargin += $detail->profit_margin;
                                            }

                                            $averageMarkupPercentage = $totalDetails > 0 ? $totalProfitMargin / $totalDetails : 0;
                                        @endphp
                                        <div class="d-flex justify-content-between mb-3">
                                            <span class="font-weight-bold fs-5">Markup Percentange:</span>
                                            <span class="fs-5">{{ number_format($averageMarkupPercentage, 0) }}%</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-3">
                                            <span class="font-weight-bold fs-5 bg-success text-white rounded px-2 py-1">Profit On The Full Shipment:</span>
                                            <span class="fs-5 bg-success text-white rounded px-2 py-1">£{{ number_format($shipment->total_profit ?? 0, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-8">
                                <div class="card bg-light">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0">Cost Details</h5>
                                    </div>
                                    <div class="card-body">
                                    <div class="col-12">
                                        <div class="row font-weight-bold text-center">
                                            <div class="col-3">Item</div>
                                            <div class="col-2">Description</div>
                                            <div class="col-2">Cost</div>
                                            <div class="col-1">Qty</div>
                                            <div class="col-2">Amount</div>
                                            <div class="col-2">Notes</div>
                                        </div>
                                        <hr>
                                        <div class="row text-center">
                                            <div class="col-3">Product Cost</div>
                                            <div class="col-2">{{ $shipment->shipping->shipping_name }} (Dated {{ \Carbon\Carbon::parse($shipment->shipping->shipping_date)->format('d/M/Y') }})
                                            </div>
                                            <div class="col-2">£{{ number_format($shipment->total_purchase_cost, 2) }}</div>
                                            <div class="col-1">{{ $shipment->shipmentDetails->sum('shipped_quantity') ?? '0' }}</div>
                                            <div class="col-2">£{{ number_format($shipment->total_purchase_cost, 2) }}</div>
                                            <div class="col-2"></div>
                                        </div>
                                        <hr>
                                        @foreach($shipment->transactions as $transaction)
                                        <div class="row text-center">
                                            <div class="col-3">{{ $transaction->chartOfAccount->account_name ?? '' }}</div>
                                            <div class="col-2">{{ $transaction->description ?? '' }}</div>
                                            <div class="col-2">£{{ number_format($transaction->amount, 2) ?? '0.00' }}</div>
                                            <div class="col-1">1</div>
                                            <div class="col-2">£{{ number_format($transaction->amount, 2) ?? '0.00' }}</div>
                                            <div class="col-2">{{ $transaction->note ?? '' }}</div>
                                        </div>
                                        <hr>
                                        @endforeach
                                        <div class="row font-weight-bold text-center">
                                            <div class="col-3">Total</div>
                                            <div class="col-2"></div>
                                            <div class="col-2"></div>
                                            <div class="col-1"></div>
                                            <div class="col-2">£{{ number_format($shipment->total_cost_of_shipment, 2) }}</div>
                                            <div class="col-2"></div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="card bg-light">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">Product Description Including Price</h5>
                                </div>
                                <div class="card-body">
                                    <div class="col-12">
                                        <div class="row font-weight-bold text-center">
                                            <div class="col-1">Sl</div>
                                            <div class="col-1">PO Number</div>
                                            <div class="col-2">Item Description With Fabrication & HS Code</div>
                                            <div class="col-2">Quantity(Set) 1 Set = 2 Pcs</div>
                                            <div class="col-2">Unit Price</div>
                                            <div class="col-1">Ground Price</div>
                                            <div class="col-1">Selling Price</div>
                                            <div class="col-2">Total Amount</div>
                                        </div>
                                        <hr>
                                        @foreach($shipment->shipmentDetails as $detail)
                                        <div class="row text-center">
                                            <div class="col-1">{{ $loop->iteration }}</div>
                                            <div class="col-1"></div>
                                            <div class="col-2">
                                                {{ $detail->product->name ?? '' }} 
                                                ({{ $detail->size ?? '' }} {{ $detail->color ?? '' }})
                                                @if($detail->product->isZip())
                                                    (Zip: {{ $detail->zip == 1 ? 'Yes' : 'No' }})
                                                @endif
                                            </div>
                                            <div class="col-2">{{ $detail->quantity / 2 }} ({{ $detail->quantity }} Pcs)</div>
                                            <div class="col-2">£{{ number_format($detail->price_per_unit, 2) }}</div>
                                            <div class="col-1">£{{ number_format($detail->ground_price_per_unit, 2) }}</div>
                                            <div class="col-1">£{{ number_format($detail->selling_price, 2) }}</div>
                                            <div class="col-2">£{{ number_format($detail->price_per_unit * $detail->quantity, 2) }}</div>
                                        </div>
                                        <hr>
                                        @endforeach
                                        <div class="row font-weight-bold text-center">
                                            <div class="col-1">Total</div>
                                            <div class="col-3"></div>
                                            <div class="col-2">{{ $shipment->shipmentDetails->sum('quantity') / 2 }}Set ({{ $shipment->shipmentDetails->sum('quantity') }} Pcs)</div>
                                            <div class="col-4"></div>
                                            <div class="col-2">£{{ number_format($shipment->shipmentDetails->sum(function($detail) { return $detail->price_per_unit * $detail->quantity; }), 2) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    @media print {
        body {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            font-size: 12px;
        }
    }
</style>

@endsection

@section('script')
<script>
    $(window).on('load', function() {
        setTimeout(function() {
            window.print();
        }, 2000);
    });
</script>
@endsection