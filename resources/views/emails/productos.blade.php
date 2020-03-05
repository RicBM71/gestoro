@component('mail::message', ['data' => $data])
# Productos modificados:

{{--
@component('mail::button', ['url' => 'url'])
View Order
@endcomponent --}}

|Referencia|Producto|Estado
|---|---|---|
@foreach($data['albaranes'] as $item)
|{{$item->referencia}}|{{$item->nombre}}|{{' Estado: '.$item->estado}}
@endforeach

Saludos.<br>
{{-- {{ config('app.name') }} --}}

@endcomponent
