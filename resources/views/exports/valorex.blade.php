<table>
    <thead>
        <tr>
            <th colspan="9">{{$titulo}}</th>
        </tr>
        <tr>
            <th>EMPRESA</th>
            <th>CONCEPTO</th>
            <th>IMPORTE</th>
        </tr>
    </thead>
    <tbody>
    @foreach($data as $item)
        <tr>
            <td>{{ $item['nombre']}}</td>
            @if($item['detalle_id'] == 1)
                <td>Recompras</td>
            @endif
            @if($item['detalle_id'] == 2)
                <td>Compras</td>
            @endif
            @if($item['detalle_id'] == 3)
                <td>Inventariados</td>
            @endif
            <td>{{ round($item['importe'], 2)}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
