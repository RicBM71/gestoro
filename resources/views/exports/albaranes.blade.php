
<table>
    <thead>
        <tr>
            <th>Cliente</th>
            <th>Razón</th>
            <th>Albarán</th>
            <th>F. Albaraán</th>
            <th>Tipo</th>
            <th>Importe</th>
            <th>F. Factura</th>
            <th>Factura</th>
            <th>Fase</th>
            <th>Notas</th>
        </tr>
    </thead>
    <tbody>
    @foreach($data as $item)
        <tr>
            <td>{{ $item['cliente']['dni']}}</td>
            <td>{{ $item['cliente']['razon']}}</td>
            <td>{{ $item['serie_albaran'].$item['albaran']}}</td>
            <td>{{ getFecha($item['fecha_albaran'])}}</td>
            <td>{{ $item['tipo']['abreviatura']}}</td>
            <td>{{ totalAlbalin($item['albalins'])}}</td>
            <td>{{ getFecha($item['fecha_factura'])}}</td>
            <td>{{ $item['serie_factura'].$item['factura']}}</td>
            <td>{{ $item['fase']['nombre']}}</td>
            <td>{{ $item['notas_int']}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
