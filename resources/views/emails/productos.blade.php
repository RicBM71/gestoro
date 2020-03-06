@component('mail::message')
# Productos modificados:

{{--
@component('mail::button', ['url' => 'url'])
View Order
@endcomponent --}}

@component('mail::table', ['data' => $data])

|Referencia |Producto |Estado                               |
|-----------|---------|-------------------------------------|
@foreach($data['albaranes'] as $item)
|{{$item->referencia}}|{{$item->nombre}}|{{$item->estado}}
@endforeach

@endcomponent


@endcomponent

