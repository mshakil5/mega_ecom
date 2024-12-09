@component('mail::message')
# Hello {{ $order->user->name }},

Thank you for requesting a quotation from us. Please find the details of your quotation below.

@component('mail::button', ['url' => $downloadLink])
Download Quotation
@endcomponent

Thanks for choosing us!

Regards,<br>
{{ config('app.name') }}
@endcomponent