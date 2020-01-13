@component('mail::message', ['data' => $data])
# Documentación adjunta

Adjunto remitimos albarán/factura.
{{--
@component('mail::button', ['url' => 'url'])
View Order
@endcomponent --}}

{{ $data['msg'] }}

Saludos.<br>
{{-- {{ config('app.name') }} --}}

@endcomponent
