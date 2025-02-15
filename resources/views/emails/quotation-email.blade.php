@component('mail::message')
# Hello {{ $order->user->name }},

{!! $body !!}

@component('mail::button', ['url' => $downloadLink])
Download Quotation
@endcomponent

Thanks for choosing us!

Regards,<br>
{{ config('app.name') }}
@endcomponent