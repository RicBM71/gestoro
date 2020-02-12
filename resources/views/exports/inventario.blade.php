<table>
    <thead>
        <tr>
            <th colspan="8">{{$titulo}}</th>
        </tr>
        <tr>
            <th>REFERENCIA</th>
            <th>NOMBRE</th>
            <th>CLASE</th>
            <th>PESO</th>
            <th>P. VENTA</th>
            <th>P. COSTE</th>
            <th>REF. POL</th>
            <th>Estado</th>
            <th>PROVEEDOR</th>
            <th>Notas</th>
            <th>Interno</th>
            <th>Borrado</th>
        </tr>
    </thead>
    <tbody>
    @foreach($data as $item)
        <tr>
            <td>{{ $item['referencia']}}</td>
            <td>{{ $item['nombre']}}</td>
            @if($item['quilates'] > 0)
                <td>{{ $item['clase']['nombre'].$item['quilates'].'K'}}</td>
            @else
                <td>{{ $item['clase']['nombre']}}</td>
            @endif
            <td>{{ $item['peso_gr']}}</td>
            <td>{{ $item['precio_venta']}}</td>
            <td>{{ $item['precio_coste']}}</td>
            <td>{{ $item['ref_pol']}}</td>
            <td>{{ $item['estado']['nombre']}}</td>
            @if($item['cliente_id'] > 0)
            <td>{{ $item['cliente']['razon']}}</td>
            @else
            <td>-</td>
            @endif
            <td>{{ $item['notas']}}</td>
            <td>{{ $item['compra_id'] > 0 ? 'Si' : 'No'}}
            <td>{{ $item['deleted_at'] != null ? 'Anulado '.$item['username'] : ''}}
        </tr>
    @endforeach
    </tbody>
</table>
