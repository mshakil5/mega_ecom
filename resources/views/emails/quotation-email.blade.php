@component('mail::message')
# Hello {{ $order->user->name }},

@component('mail::button', ['url' => $downloadLink])
Download Quotation
@endcomponent

Thanks for choosing us!

Regards,<br>
{{ config('app.name') }}
@endcomponent