<!DOCTYPE html>
<html lang="en">

<head>
    @php
    $company = \App\Models\CompanyDetails::first();
    use Carbon\Carbon;
    @endphp

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $company->company_name }} - Quotation</title>
    <link crossorigin="anonymous" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 100%;
            padding: 0 10px;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid black !important;
        }

        .table th,
        .table td {
            padding: 0.5rem;
            vertical-align: middle;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .font-weight-bold {
            font-weight: bold;
        }

        .mt-4 {
            margin-top: 1rem;
        }

        .mb-4 {
            margin-bottom: 1rem;
        }

        .table img {
            width: 100px;
            height: 150px;
            object-fit: cover;
        }

        .logo img {
            max-width: 120px;
            margin-bottom: 10px;
        }

        .header {
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .footer {
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .signature {
            margin-top: 10px;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid black;
            width: 150px;
            margin: 0 auto;
        }
    </style>
</head>

<body>
    <div class="container mt-4 mb-4">
        <div class="header d-flex justify-content-between align-items-center">
            <div class="logo">
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/company/'.$company->company_logo))) }}" alt="Company Logo">
            </div>
            <div>
                <strong>Quotation #: </strong> {{ $order->invoice }}<br>
                <strong>Date: </strong> {{ Carbon::parse($order->purchase_date)->format('d F Y') }}
            </div>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr style="background-color: #233969; color: white;">
                    <th colspan="6" class="text-center">
                        {{ $order->remarks }}
                    </th>
                </tr>
                <tr>
                    <td colspan="6"></td>
                </tr>
                <tr style="background-color: skyblue; color: black;">
                    <th class="text-center">Style Name</th>
                    <th class="text-center">Item Description</th>
                    <th class="text-center">Size / Color</th>
                    <th class="text-center">Quantity</th>
                    <th class="text-center">Unit Selling Price ({{ $currency }})</th>
                    <th class="text-center">Total Selling Price ({{ $currency }})</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->orderDetails->groupBy('product_id') as $productId => $details)
                <tr>
                    <td class="text-center" rowspan="{{ $details->count() }}">
                        {{ $details->first()->product->product_code }} - {{ $details->first()->product->name }}
                    </td>
                    <td class="text-center" rowspan="{{ $details->count() }}">
                        @php
                        $imagePath = public_path('images/products/' . $details->first()->product->feature_image);
                        $base64Image = file_exists($imagePath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($imagePath)) : null;
                        @endphp
                        @if ($base64Image)
                        <x-image-with-loader
                            src="{{ $base64Image }}"
                            alt="{{ $details->first()->product->name }}"
                            class="product-image" />
                        @else
                        <span>No Image Available</span>
                        @endif
                    </td>

                    @foreach ($details as $index => $detail)
                    @if ($index > 0)
                <tr>
                    @endif
                    <td class="text-center">{{ $detail->size }} / {{ $detail->color }}</td>
                    <td class="text-center">{{ $detail->quantity }}</td>
                    <td class="text-center">{{ $currency }} {{ number_format($detail->price_per_unit, 2) }}</td>
                    <td class="text-center">{{ $currency }} {{ number_format($detail->total_price, 2) }}</td>
                    @if ($index > 0)
                </tr>
                @endif
                @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            <table class="table table-bordered">
                <tr>
                    <td class="font-weight-bold" colspan="3">Total Quantity</td>
                    <td class="text-center font-weight-bold">{{ $order->orderDetails->sum('quantity') }}</td>
                </tr>
                <tr>
                    <td class="font-weight-bold" colspan="3">Grand Total</td>
                    <td class="text-center font-weight-bold">{{ $currency }} {{ number_format($order->net_amount, 2) }}</td>
                </tr>

                <tr>
                    @php
                    use Rmunate\Utilities\SpellNumber;

                    $inWords = SpellNumber::value($order->net_amount)
                    ->locale('en')
                    ->currency('euros')
                    ->toMoney();
                    @endphp

                    <td colspan="4">
                        <strong>In Words:</strong>
                        {{ ucfirst($inWords) }} only.
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer mt-4">
            <div class="container">
                <div class="row">
                    <div class="col-6">
                        <div class="text-center">
                            <div><strong>Payment Terms are through Bank Transfer Only:</strong></div>
                            <div>SAPPHIRE TRADELINKS</div>
                            <div>Acc no: 48302821</div>
                            <div>Sort Code: 56-00-70</div>
                            <div>IBAN: GB44NWBK56007048302821</div>
                            <div>SWIFT: NWBKGb2L</div>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <div class="text-center">
                        <div>Feel free to contact us for further queries.</div>
                    </div>

                    <div class="signature text-center mt-3">
                        <div class="signature-line"></div>
                        <div>ISMAIL MIAH</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>