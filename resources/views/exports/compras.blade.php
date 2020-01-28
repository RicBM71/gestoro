
<table>
    <thead>
        <tr>
            <th>Cliente</th>
            <th>Razón</th>
            <th>Compra</th>
            <th>Operación</th>
            <th>F. Compra</th>
            <th>F. Recuperación</th>
            <th>Factura</th>
            <th>Importe</th>
            <th>Fase</th>
            <th>Retraso</th>
        </tr>
    </thead>
    <tbody>
    @foreach($data as $item)
        <tr>
            <td>{{ $item['cliente']['dni']}}</td>
            <td>{{ $item['cliente']['razon']}}</td>
            <td>{{ $item['serie_com'].$item['albaran']}}</td>
            <td>{{ $item['tipo']['nombre']}}</td>
            <td>{{ getFecha($item['fecha_compra'])}}</td>
            @if($item['fase_id']==5)
                <td>{{ getFecha($item['fecha_recogida'])}}</td>
            @else
                <td>-</td>
            @endif
            <td>{{ $item['serie_fac'].$item['factura']}}</td>
            <td>{{ $item['importe']}}</td>
            <td>{{ $item['fase']['nombre']}}</td>
            <td>{{ $item['retraso']}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
