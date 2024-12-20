<!DOCTYPE html>
<html lang="en">
<head>
@php
    $company = \App\Models\CompanyDetails::select('company_name', 'company_logo', 'address1', 'address2', 'phone1')->first();
    use Carbon\Carbon;
@endphp 
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $company->company_name }} - Invoice</title>
    <style>
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 14px;
            line-height: 24px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }

        .sub-total {
            line-height: 1.3;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }

        /** RTL **/
        .invoice-box.rtl {
            direction: rtl;
            font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        }

        .invoice-box.rtl table {
            text-align: right;
        }

        .invoice-box.rtl table tr td:nth-child(2) {
            text-align: left;
        }

        .line {
            border-top: 1px solid #eee;
        }

        .sub-total td:first-child,
        .total td:first-child {
            width: 80%;
            padding-left: 70%;
            text-align: left;
        }

        .right-align {
            text-align: right;
            width: 50%;
        }

    </style>
</head>

<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/company/'.$company->company_logo))) }}" style="width: 100%; max-width: 150px" />
                            </td>
                            <td>
                                Order #: {{ $order->invoice }}<br />
                                Purchase Date: {{ \Carbon\Carbon::parse($order->purchase_date)->format('F d, Y') }}<br />
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                {{ $company->address1 }} <br />
                                {{ $company->address2 }} <br />
                                {{ $company->phone1 }}
                            </td>
                            <td>
                                {{ $order->name }}<br />
                                {{ $order->email }}<br />
                                {{ $order->phone }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="heading">
                <td>Payment Method</td>
                <td>Amount</td>
            </tr>

            <tr class="details">
                <td>
                    @if($order->payment_method === 'paypal')
                        PayPal
                    @elseif($order->payment_method === 'stripe')
                        Stripe
                    @elseif($order->payment_method === 'cashOnDelivery')
                        Cash On Delivery
                    @else
                        {{ $order->payment_method }}
                    @endif
                </td>
                <td>{{ $currency }} {{ $order->net_amount }}</td>
            </tr>

            <tr class="heading">
                <td>Item</td>
                <td>Price</td>
            </tr>

            @foreach ($order->orderDetails as $detail)
                @php
                    $productName = '';

                    if ($detail->product_id) {
                        $product = \App\Models\Product::find($detail->product_id);
                        $productName = $product ? $product->name : 'Unknown Product';
                    } else {
                        $productName = $bundleProduct ? $bundleProduct->name : 'Unknown Bundle Product';
                    }

                    $imagePath = public_path('images/products/' . $detail->product->feature_image);
                    $base64Image = file_exists($imagePath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($imagePath)) : null;
                @endphp
                <tr class="item {{ $loop->last ? 'last' : '' }}">
                    <td style="padding-top: 15px;">
                        @if($base64Image)
                            <img src="{{ $base64Image }}" alt="{{ $productName }}" style="width: 50px; height: 50px; margin-right: 10px;">
                        @else
                            <span>No Image Available</span>
                        @endif
                        <span style="display: inline-block; vertical-align: middle;">{{ $productName }} {{ $detail->size }} - {{ $detail->color }} ({{ $detail->quantity }} x {{ $currency }} {{ $detail->price_per_unit }})</span>
                    </td>
                    <td style="padding-top: 15px;">{{ $currency }} {{ $detail->total_price }}</td>
                </tr>
            @endforeach

            <tr>
                <td colspan="2">
                    <div class="line"></div>
                </td>
            </tr>

            @if($order->vat_amount > 0)
            <tr class="sub-total">
                <td class="text-left fixed-width">Vat:</td>
                <td class="right-align">{{ $currency }} {{ number_format($order->vat_amount ?? 0.00, 2) }}</td>
            </tr>
            @endif

            @if($order->shipping_amount > 0)
            <tr class="sub-total">
                <td class="text-left fixed-width">Shipping:</td>
                <td class="right-align">{{ $currency }} {{ number_format($order->shipping_amount ?? 0.00, 2) }}</td>
            </tr>
            @endif

            @if($order->discount_amount > 0)
            <tr class="sub-total">
                <td class="text-left fixed-width">Discount Amount:</td>
                <td class="right-align">{{ $currency }} {{ number_format($order->discount_amount ?? 0.00, 2) }}</td>
            </tr>
            @endif

            
            <tr class="sub-total">
                <td class="text-left fixed-width">Sub Total:</td>
                <td class="right-align">{{ $currency }} {{ number_format($order->subtotal_amount ?? 0.00, 2) }}</td>
            </tr>

            <tr class="total">
                <td class="text-left fixed-width" style="font-weight: bold;">Total</td>
                <td class="right-align">{{ $currency }} {{ $order->net_amount }} </td>
            </tr>
        </table>
    </div>
</body>
</html>