<table>
    <thead>
    <tr>
        <th>Fecha</th>
        <th>DH</th>
        <th>Concepto</th>
        <th>Importe</th>
        <th>T</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $item)
        <tr>
            <td>{{ getFecha($item['fecha']) }}</td>
            <td>{{ $item['dh'] }}</td>
            <td>{{ $item['nombre']}}</td>
            <td>{{ getDecimalExcel($item['importe']) }}</td>
            <td>{{ $item['manual']}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
