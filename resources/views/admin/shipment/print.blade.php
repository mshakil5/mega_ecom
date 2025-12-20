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
                        <h1 class="mb-0">Cost Sheet SapphireTradeLinks</h1>
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

                        <!-- PRODUCT DESCRIPTION TABLE WITH STICKY HEADER -->
                        <!-- PRODUCT DESCRIPTION TABLE WITH STICKY HEADER -->
                        <div class="col-md-12">
                            <div class="card bg-light">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">Product Description Including Price</h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-wrapper print-optimize">
                                        <table class="table table-bordered table-hover">
                                            <thead class="table-header-light">
                                                <tr>
                                                    <th class="col-1 text-center">Sl</th>
                                                    <th class="col-1 text-center">PO Number</th>
                                                    <th class="col-2 text-center">Item Description With Fabrication & HS Code</th>
                                                    <th class="col-2 text-center">Quantity(Set) 1 Set = 2 Pcs</th>
                                                    <th class="col-2 text-center">Unit Price</th>
                                                    <th class="col-1 text-center">Ground Price</th>
                                                    <th class="col-1 text-center">Selling Price</th>
                                                    <th class="col-2 text-center">Total Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($shipment->shipmentDetails as $detail)
                                                <tr>
                                                    <td class="col-1 text-center">{{ $loop->iteration }}</td>
                                                    <td class="col-1 text-center"></td>
                                                    <td class="col-2">
                                                        {{ $detail->product->name ?? '' }} 
                                                        ({{ $detail->size ?? '' }} {{ $detail->color ?? '' }} {{ $detail->type->name ?? '' }})
                                                        @if($detail->product->isZip())
                                                            (Zip: {{ $detail->zip == 1 ? 'Yes' : 'No' }})
                                                        @endif
                                                    </td>
                                                    <td class="col-2 text-center">{{ $detail->quantity / 2 }} ({{ $detail->quantity }} Pcs)</td>
                                                    <td class="col-2 text-center">£{{ number_format($detail->price_per_unit, 2) }}</td>
                                                    <td class="col-1 text-center">£{{ number_format($detail->ground_price_per_unit, 2) }}</td>
                                                    <td class="col-1 text-center">£{{ number_format($detail->selling_price, 2) }}</td>
                                                    <td class="col-2 text-center">£{{ number_format($detail->price_per_unit * $detail->quantity, 2) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr class="font-weight-bold">
                                                    <td class="col-1 text-center">Total</td>
                                                    <td class="col-1 text-center"></td>
                                                    <td class="col-2 text-center"></td>
                                                    <td class="col-2 text-center">{{ $shipment->shipmentDetails->sum('quantity') / 2 }}Set ({{ $shipment->shipmentDetails->sum('quantity') }} Pcs)</td>
                                                    <td class="col-2 text-center"></td>
                                                    <td class="col-1 text-center"></td>
                                                    <td class="col-1 text-center"></td>
                                                    <td class="col-2 text-center">£{{ number_format($shipment->shipmentDetails->sum(function($detail) { return $detail->price_per_unit * $detail->quantity; }), 2) }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
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
/* ========== STICKY TABLE WRAPPER ========== */
.table-wrapper {
    max-height: 500px;
    overflow-y: auto;
    overflow-x: auto;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    position: relative;
}

/* ========== TABLE STYLING ========== */
.table-wrapper table {
    margin-bottom: 0;
    width: 100%;
    border-collapse: collapse;
}

/* ========== TABLE HEADER LIGHT (NO DARK BACKGROUND) ========== */
.table-header-light {
    position: sticky;
    top: 0;
    background-color: #f8f9fa;  /* Light background */
    color: #333;                /* Dark text */
    z-index: 10;
    border-bottom: 2px solid #dee2e6;
}

.table-header-light th {
    background-color: #f8f9fa;  /* Light background */
    color: #333;                /* Dark text */
    font-weight: bold;
    padding: 12px 8px;
    border-color: #dee2e6;
    white-space: nowrap;
    text-align: center;
    vertical-align: middle;
}

/* ========== TABLE BODY STYLING ========== */
.table-wrapper tbody tr {
    border-bottom: 1px solid #dee2e6;
}

.table-wrapper tbody tr:hover {
    background-color: #f5f5f5;
}

.table-wrapper td {
    padding: 10px 8px;
    vertical-align: middle;
}

/* ========== TABLE FOOTER STYLING ========== */
.table-wrapper tfoot tr {
    background-color: #f8f9fa;
    font-weight: bold;
    border-top: 2px solid #dee2e6;
}

.table-wrapper tfoot td {
    padding: 10px 8px;
    text-align: center;
    vertical-align: middle;
}

/* ========== SCROLLBAR STYLING (WEBKIT) ========== */
.table-wrapper::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

.table-wrapper::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.table-wrapper::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.table-wrapper::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* ========== PRINT STYLES ========== */
@media print {
    /* Show all content without scroll */
    .table-wrapper {
        max-height: none !important;
        height: auto !important;
        overflow: visible !important;
        border: none !important;
        page-break-inside: avoid;
    }

    /* Remove sticky positioning for print */
    .table-header-light {
        position: static !important;
        background-color: #f8f9fa !important;
        color: #333 !important;
        border-bottom: 2px solid #dee2e6 !important;
    }

    .table-header-light th {
        background-color: #f8f9fa !important;
        color: #333 !important;
        padding: 12px 8px !important;
        border-color: #dee2e6 !important;
    }

    /* Show full table */
    .table-wrapper table {
        width: 100% !important;
        page-break-inside: avoid !important;
    }

    .table-wrapper tbody {
        display: table-row-group !important;
    }

    .table-wrapper tr {
        page-break-inside: avoid !important;
        page-break-after: auto !important;
    }

    /* Body print settings */
    body {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        font-size: 12px !important;
    }

    /* Hide navigation elements */
    .no-print {
        display: none !important;
    }

    /* Ensure table doesn't overflow */
    table {
        page-break-inside: auto !important;
    }
    
    tr {
        page-break-inside: avoid !important;
        page-break-after: auto !important;
    }
    
    td, th {
        page-break-inside: avoid !important;
    }

    /* Force table to show all rows */
    .table-wrapper,
    .table-wrapper * {
        overflow: visible !important;
        height: auto !important;
        max-height: none !important;
    }
}

/* ========== RESPONSIVE ADJUSTMENTS ========== */
@media (max-width: 1024px) {
    .table-wrapper {
        max-height: 400px;
    }

    .table-header-light th {
        padding: 10px 6px;
        font-size: 0.9rem;
    }

    .table-wrapper td {
        padding: 8px 6px;
        font-size: 0.9rem;
    }
}

@media (max-width: 768px) {
    .table-wrapper {
        max-height: 300px;
        overflow-x: auto;
    }

    .table-header-light th {
        padding: 8px 4px;
        font-size: 0.85rem;
    }

    .table-wrapper td {
        padding: 6px 4px;
        font-size: 0.85rem;
    }
}

@media (max-width: 576px) {
    .table-wrapper {
        max-height: 250px;
    }

    .table-header-light th {
        padding: 6px 3px;
        font-size: 0.75rem;
    }

    .table-wrapper td {
        padding: 4px 3px;
        font-size: 0.75rem;
    }
}

/* ========== PRINT SPECIFIC ========== */
@media print {
    /* Optimize for print */
    .table-wrapper {
        page-break-after: auto;
        max-height: none !important;
        height: auto !important;
        overflow: visible !important;
    }

    .table-wrapper table {
        display: table !important;
        width: 100% !important;
    }

    .table-wrapper tbody {
        display: table-row-group !important;
    }

    .table-wrapper tr {
        display: table-row !important;
    }

    .table-wrapper td, 
    .table-wrapper th {
        display: table-cell !important;
    }

    /* Light header in print */
    .table-header-light,
    .table-header-light th {
        background-color: #f8f9fa !important;
        color: #333 !important;
        border-color: #dee2e6 !important;
        position: static !important;
    }

    /* Remove hover effects for print */
    .table-wrapper tbody tr:hover {
        background-color: inherit !important;
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

    // Optional: Add beforeprint and afterprint event handlers
    window.addEventListener('beforeprint', function() {
        // Force table to show all content before printing
        $('.table-wrapper').addClass('print-mode');
    });

    window.addEventListener('afterprint', function() {
        // Restore normal view after printing
        $('.table-wrapper').removeClass('print-mode');
    });
</script>
@endsection