@component('mail::message', ['data' => $data])
# Documentación adjunta

Adjunto remitimos documentación referente a la renovación del lote adjunto. En caso de duda o aclaración puede dirigirse a nostros por los medios indicados en dicho formulario.
{{--
@component('mail::button', ['url' => 'url'])
View Order
@endcomponent --}}

{{ $data['msg'] }}

Saludos.<br>
{{-- {{ config('app.name') }} --}}

@endcomponent
