<!DOCTYPE html>
<html lang="en">
<head>
    @php
        $company = \App\Models\CompanyDetails::first();
        use Carbon\Carbon;
    @endphp

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $company->company_name }} - Invoice</title>
    <link crossorigin="anonymous" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .table-bordered th, .table-bordered td {
            border: 1px solid black !important;
        }
        .table th, .table td {
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
            margin-top: 1.5rem;
        }
        .mb-4 {
            margin-bottom: 1.5rem;
        }
        .table img {
            width: 100px;
            height: 150px;
            object-fit: cover;
        }
        .logo img {
            max-width: 150px;
            margin-bottom: 10px;
        }
        .header {
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .footer {
            margin-top: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container mt-4 mb-4">
    <!-- Header Section -->
    <div class="header d-flex justify-content-between align-items-center">
        <div class="logo">
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/company/'.$company->company_logo))) }}" alt="Company Logo">
        </div>
        <div>
            <strong>Invoice #: </strong> {{ $order->invoice }}<br>
            <strong>Purchase Date: </strong> {{ Carbon::parse($order->purchase_date)->format('F d, Y') }}
        </div>
    </div>

    <!-- Product Table -->
    <table class="table table-bordered">
        <thead>
        <tr>
            <th class="text-center">Style Name</th>
            <th class="text-center">Item Description</th>
            <th class="text-center">Size</th>
            <th class="text-center">Quantity</th>
            <th class="text-center">Unit Selling Price ({{ $currency }})</th>
            <th class="text-center">Total Selling Price ({{ $currency }})</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($order->orderDetails as $detail)
            <tr>
                <td class="text-center">{{ $detail->product->name }}</td>
                <td class="text-center">
                    
                </td>
                <td class="text-center">
                </td>
                <td class="text-center">{{ $detail->quantity }}</td>
                <td class="text-center">{{ $currency }} {{ number_format($detail->price_per_unit, 2) }}</td>
                <td class="text-center">{{ $currency }} {{ number_format($detail->total_price, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <!-- Grand Total Section -->
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
    function numberToWords($num) {
        $ones = [
            0 => 'zero', 1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six', 
            7 => 'seven', 8 => 'eight', 9 => 'nine', 10 => 'ten', 11 => 'eleven', 12 => 'twelve', 
            13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen', 16 => 'sixteen', 17 => 'seventeen', 
            18 => 'eighteen', 19 => 'nineteen'
        ];
        $tens = [
            2 => 'twenty', 3 => 'thirty', 4 => 'forty', 5 => 'fifty', 6 => 'sixty', 7 => 'seventy', 
            8 => 'eighty', 9 => 'ninety'
        ];
        $suffixes = [
            100 => 'hundred', 1000 => 'thousand', 1000000 => 'million', 1000000000 => 'billion'
        ];
        
        if ($num < 20) return $ones[$num];
        
        if ($num < 100) {
            return $tens[intval($num / 10)] . ' ' . $ones[$num % 10];
        }
        
        foreach (array_reverse($suffixes, true) as $limit => $suffix) {
            if ($num >= $limit) {
                $left = intval($num / $limit);
                $right = $num % $limit;
                return numberToWords($left) . ' ' . $suffix . ($right ? ' ' . numberToWords($right) : '');
            }
        }
    }

    $inWords = ucfirst(numberToWords($order->net_amount));
@endphp

<td colspan="4">
    <strong>In Words:</strong> 
    {{ $inWords }} {{ strtolower($currency) }} only.
</td>

            </tr>
        </table>
    </div>

    <!-- Payment Details -->
    <div class="footer">
        <strong>Payment Method:</strong> {{ $order->payment_method }}<br>
        @if ($company->bank_details)
            <strong>Bank Details:</strong><br>
            {!! nl2br(e($company->bank_details)) !!}
        @endif
    </div>
</div>
</body>
</html>