<!DOCTYPE html>
<html lang="en">

<head>
    @php
    $company = \App\Models\CompanyDetails::select('company_name','company_logo', 'email1', 'company_reg_number', 'vat_number')->first();
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
            border: 1px solid lightgray !important;
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
            /* text-align: center; */
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
            <div class="row">
                <div class="col-md-6">
                    <div>
                        <strong>Quotation #: </strong> {{ $order->invoice }}<br>
                        <strong>Date: </strong> {{ Carbon::parse($order->purchase_date)->format('d F Y') }}
                    </div>
                </div>

                <div class="col-md-6" style="margin-top: -50px; padding-bottom: 15px">
                    <div class="text-end">
                        @if($order->user->surname) <strong></strong> {{ $order->user->surname }}<br> @endif
                        @if($order->user->name) <strong></strong> {{ $order->user->name }}<br> @endif
                        @if($order->user->email) <strong></strong> {{ $order->user->email }}<br> @endif
                        @if($order->user->phone) <strong></strong> {{ $order->user->phone }} <br> @endif
                        @if($order->user->address) <strong></strong> {{ $order->user->address }} <br> @endif
                    </div>
                </div>
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
                <tr style="background-color: rgb(228, 235, 253); color: black;">
                    <th class="text-center">Style Name</th>
                    <th class="text-center">Item Description</th>
                    <th class="text-center">Size / Color</th>
                    <th class="text-center">Quantity</th>
                    <th class="text-center">Unit Selling Price({{ $currency }})</th>
                    <th class="text-center">Total Selling Price({{ $currency }})</th>
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
                    <td class="text-center">{{ $currency }}{{ number_format($detail->price_per_unit, 2) }}</td>
                    <td class="text-center">{{ $currency }}{{ number_format($detail->total_price, 2) }}</td>
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
                    <td class="font-weight-bold" colspan="3" style="background-color: rgb(228, 235, 253); color: black;">Grand Total</td>
                    <td class="text-center font-weight-bold" style="background-color: rgb(228, 235, 253); color: black;">{{ $currency }}{{ number_format($order->net_amount, 2) }}</td>
                </tr>

                <tr>
                    @php
                    use Rmunate\Utilities\SpellNumber;

                    $inWords = SpellNumber::value($order->net_amount)
                    ->locale('en')
                    ->currency('Pounds')
                    ->fraction('Pence')
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
                    <div class="col-5">
                        <div>
                            {{$company->company_name}}<br>
                            {{$company->email1}}<br>
                            Company Reg. No. {{$company->company_reg_number}}<br>
                            VAT Reg. No. {{$company->vat_number}}
                        </div>
                    </div>

                    <div class="col-7 text-end" style="margin-top: -150px">
                        <div>
                            Feel free to contact us.
                        </div>
                        <div class="signature mt-3">
                            <div style="border-top: 1px solid #000; width: 100px; margin: 10px 0; display: inline-block;"></div>
                            <div><strong>Team Sapphire</strong></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</body>

</html>