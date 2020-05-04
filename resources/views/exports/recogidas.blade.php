<table>
    <thead>
        <tr>
            <th colspan="6">RECOGIDAS</th>
        </tr>
        <tr>
            <th>EMPRESA</th>
            <th>COMPRA</th>
            <th>IMPORTE</th>
            <th>CLIENTE</th>
            <th>F. COMPRA</th>
            <th>F. RECOGIDA</th>
        </tr>
    </thead>
    <tbody>
    @foreach($data as $item)
        <tr>
            <td>{{ $item['empresa']}}</td>
            <td>{{ $item['serie_com'].$item['albaran']}}</td>
            <td>{{ $item['importe']}}</td>
            <td>{{ $item['razon']}}</td>
            <td>{{ getFecha($item['fecha_compra'])}}</td>
            <td>{{ getFecha($item['fecha_recogida'])}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
