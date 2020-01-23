
<table>
    <thead>
        <tr>
            <th>Cliente</th>
            <th>Razón</th>
            <th>Compra</th>
            <th>Fecha</th>
            <th>Tipo</th>
            <th>Importe</th>
            <th>Retraso</th>
            <th>Fase</th>
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
            <td>{{ $item['importe']}}</td>
            <td>{{ $item['retraso']}}</td>
            <td>{{ $item['fase']['nombre']}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
