@component('mail::message', ['data' => $data])
# Productos modificados:

{{--
@component('mail::button', ['url' => 'url'])
View Order
@endcomponent --}}

@foreach($data['albaranes'] as $item)
<br>
    {{$item->referencia.' - '.$item->nombre.' Estado: '.$item->estado}}
@endforeach

Saludos.<br>
{{-- {{ config('app.name') }} --}}

@endcomponent
