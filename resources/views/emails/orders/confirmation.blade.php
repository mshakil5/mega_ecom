@component('mail::message')
# {{ $order->order_type === 2 ? 'Quotation Confirmation' : 'Order Confirmation' }}

Dear {{ $order->user_id ? $order->user->name : $order->name }},

Thank you for your {{ $order->order_type === 2 ? 'quotation' : 'order' }}. Below are the details of your {{ $order->order_type === 2 ? 'quotation' : 'purchase' }}:

- **{{ $order->order_type === 2 ? 'Quotation' : 'Order' }}**#: {{ $order->invoice }}
- **Purchase Date**: {{ \Carbon\Carbon::parse($order->purchase_date)->format('F d, Y') }}
@if($order->order_type === 0)
- **Payment Method**: {{ $order->payment_method === 'CashOnDelivery' ? 'Cash On Delivery' : ucfirst($order->payment_method) }}
@endif
- **Total Amount**: {{ $order->net_amount }}

@component('mail::button', ['url' => $pdfUrl])
    {{ $order->order_type === 2 ? 'Download Quotation' : 'Download Invoice' }}
@endcomponent

Thanks for choosing us!

Regards,<br>
{{ config('app.name') }}
@endcomponent