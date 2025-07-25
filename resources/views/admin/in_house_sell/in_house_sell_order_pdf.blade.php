<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    @php
        $company = \App\Models\CompanyDetails::select('company_name', 'company_logo', 'address1', 'email1', 'phone1', 'website', 'company_reg_number', 'vat_number')->first();
        use Carbon\Carbon;
    @endphp

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta http-equiv="Content-Type" content="text/html">
    <title>{{ $company->company_name }} - Invoice</title>
    <style>
        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }
    </style>

</head>

<body>


    <section class="invoice">
        <div class="container-fluid p-0">
            <div class="invoice-body py-5 position-relative">
                <div style="max-width: 1170px; margin: 20px auto;">


                    <table style="width: 100%;">
                        <tbody>
                            <tr>
                                <td colspan="2" class="" style="border :0px solid #dee2e6;width:50%;">
                                    <div class="col-lg-2" style="flex: 2; text-align: left;">
                                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/company/'.$company->company_logo))) }}" width="120px" style="display:inline-block;" />
                                    </div>
                                </td>
                                <td colspan="2" class="" style="border :0px solid #dee2e6 ;width:50%;"></td>
                                <td colspan="2" class="" style="border :0px solid #dee2e6 ;">
                                    <div class="col-lg-2" style="flex: 2; text-align: right;">
                                        <h1 style="font-size: 30px; color:blue">INVOICE</h1>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="" style="border :0px solid #dee2e6;width:25%;">
                                </td>
                                <td colspan="2" class="" style="border :0px solid #dee2e6 ;width:50%;"></td>
                                <td colspan="2" class="" style="border :0px solid #dee2e6 ;">
                                </td>
                            </tr>
                        </tbody>

                    </table>

                    <br><br>

                    <table style="width: 100%;font-family: Arial, Helvetica;font-size: 12px;">
                        <tbody>

                            <tr>
                                <td colspan="2" class="" style="border :0px solid #828283 ;width:40%;">
                                    <div class="col-lg-2 text-end" style="flex: 2; text-align: right;">
                                        <h5 style="font-size: 12px; margin : 5px;text-align: left; line-height: 10px;">Invoice To</h5>
                                        @if($order->user->surname)
                                        <p style="font-size: 12px; margin : 5px;text-align: left; line-height: 10px;">{{ $order->user->surname }}</p>
                                        @endif
                                        <p style="font-size: 12px; margin : 5px;text-align: left; line-height: 10px;">{{ $order->user->name }}</p>
                                        <p style="font-size: 12px; margin : 5px;text-align: left; line-height: 10px;">{{ $order->user->email }}</p>
                                        <p style="font-size: 12px; margin : 5px;text-align: left; line-height: 10px;">{{ $order->user->phone }}</p>
                                        @if($order->user->address)
                                        <p style="font-size: 12px; margin: 5px; text-align: left; line-height: 10px;">
                                            {{ $order->user->address }}
                                        </p>
                                        @endif
                                    </div>
                                </td>

                                <td colspan="2" class="" style="border :0px solid #dee2e6;width:30%;"></td>
                                <td colspan="2" class="" style="border :0px solid #dee2e6 ;">
                                    <div class="col-lg-2 text-end" style="flex: 2; text-align: right;">
                                        <p style="font-size: 12px; margin : 5px;text-align: right;line-height: 10px;">Invoice No: {{ $order->invoice }}</p>
                                        <p style="font-size: 12px; margin : 5px;text-align: right;line-height: 10px;">Date: {{ \Carbon\Carbon::parse($order->purchase_date)->format('d/m/Y') }}</p>
                                        <p style="font-size: 12px; margin : 5px;text-align: right;line-height: 10px;">Payment Method: 
                                        @if($order->payment_method == 'cashOnDelivery')
                                            Cash On Delivery
                                        @elseif($order->payment_method == 'stripe')
                                            Stripe
                                        @elseif($order->payment_method == 'paypal')
                                            PayPal
                                        @else
                                            {{ ucfirst($order->payment_method) }}
                                        @endif
                                        </p>
                                        <p style="font-size: 12px; margin : 5px;text-align: right;line-height: 10px;">Order Type: 
                                            {{ 
                                                $order->order_type == 0 ? 'Website' : 
                                                ($order->order_type == 1 ? 'In House' : 
                                                ($order->order_type == 2 ? 'Quotation' : ''))
                                            }}
                                        </p>
                                    </div>
                                </td>
                            </tr>

                        </tbody>

                    </table>
                    <br>

                    <div class="row overflow" style="font-family: Arial, Helvetica;font-size: 12px;">
                        <table style="width: 100%;border-collapse: collapse;" class="table">
                            <thead>
                                <tr>
                                    <td style="border: 1px solid #dee2e6!important; padding: 0 10px 0 10;text-align:center;"><b>Style Name</b></td>
                                    <td style="border: 1px solid #dee2e6!important; padding: 0 10px 0 10;text-align:center;"><b>Qty</b></td>
                                    <td style="border: 1px solid #dee2e6!important; padding: 0 10px 0 10;text-align:center;"><b>Unit Price</b></td>
                                    <td style="border: 1px solid #dee2e6!important; padding: 0 10px 0 10;text-align:right;"><b>Total</b></td>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($order->orderDetails as $key => $detail )      
                                <tr style="border-bottom:1px solid #dee2e6 ; border-right:1px solid #dee2e6 ; border-left:1px solid #dee2e6 ;">
                                    <td style="border: 0px solid #ffffff!important; padding: 1px 10px;">{{$detail->product->product_code}} - {{ $detail->product->name }} - {{ $detail->size }} - {{ $detail->color }} 
                                      @if($detail->product->isZip())
                                        (Zip: {{ $detail->zip == 1 ? 'Yes' : 'No' }})
                                      @endif
                                      @if ($detail->type_id) - {{ $detail->type->name }} @endif
                                    </td>
                                    <td style="border: 0px solid #ffffff!important; padding: 1px 10px;text-align:center;width: 10%">{{$detail->quantity}} </td>
                                    <td style="border: 0px solid #ffffff!important; padding: 1px 10px;text-align:center;width: 10%">£{{ number_format($detail->price_per_unit, 2) }}</td>
                                    <td style="border: 0px solid #ffffff!important; padding: 1px 1px;text-align:right;width: 20%">£{{ number_format($detail->total_price , 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>


                        <table style="width: 100%;border-collapse: collapse;">
                            <tbody>
                                <tr>
                                    <td style="width: 20%">&nbsp;</td>
                                    <td style="width: 25%">&nbsp;</td>
                                    <td style="width: 25%">&nbsp;</td>
                                    <td>Subtotal</td>
                                    <td style="text-align:right">£{{ number_format($order->subtotal_amount, 2) }}</td>
                                </tr>
                                @if($order->discount_amount > 0)
                                <tr>
                                    <td style="width: 20%">&nbsp;</td>
                                    <td style="width: 25%">&nbsp;</td>
                                    <td style="width: 25%">&nbsp;</td>
                                    <td>Discount</td>
                                    <td style="text-align:right">£{{ number_format($order->discount_amount, 2) }}</td>
                                </tr>
                                @endif
                                @if($order->shipping_amount > 0)
                                <tr>
                                    <td style="width: 20%">&nbsp;</td>
                                    <td style="width: 25%">&nbsp;</td>
                                    <td style="width: 25%">&nbsp;</td>
                                    <td>Delivery Charge</td>
                                    <td style="text-align:right">£{{ number_format($order->shipping_amount, 2) }}</td>
                                </tr>
                                @endif

                                @if($order->vat_amount > 0)
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>Vat @if($order->vat_percent) ({{ $order->vat_percent }}%) @endif</td>
                                    <td style="text-align:right">£{{ number_format($order->vat_amount, 2) }}</td>
                                </tr>
                                @endif

                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td>&nbsp;</td>
                                    <td style="background-color: #f2f2f2">Total</td>
                                    <td style="text-align:right; background-color: #f2f2f2">£{{ number_format($order->net_amount, 2) }}</td>
                                </tr>

                            </tbody>
                            <tfoot style="border :0px solid #dee2e6 ; width: 100%; ">

                            </tfoot>
                        </table>
                    </div>

                    <br><br>

                    <div class="row overflow" style="position:fixed; bottom:0; width:100%;font-family: Arial, Helvetica;font-size: 12px; ">
                        <hr>
                        <table style="width:100%; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th style="width: 50%;"></th>
                                    <th style="width: 50%;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="width: 50%; text-align:left;" colspan="1"><b>{{ $company->company_name }}</b></td>
                                    <td style="width: 50%; text-align:right;" colspan="1"><b>Contact Information</b></td>
                                </tr>
                                <tr>
                                    <td>
                                        Registration Number: {{ $company->company_reg_number }} <br>
                                        Vat Number: {{ $company->vat_number }} <br>
                                        {{ $company->address1 }} <br>
                                    </td>
                                    <td style="width: 50%; text-align:right;">
                                        {{ $company->phone1 }} <br>
                                        {{ $company->email1 }} <br>
                                        {{ $company->website }} <br>

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>