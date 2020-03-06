<table>
    <thead>
    <tr>
        <th>Fecha</th>
        <th>DH</th>
        <th>Concepto</th>
        <th>Debe</th>
        <th>Haber</th>
        <th>T</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $item)
        <tr>
            <td>{{ getFecha($item['fecha']) }}</td>
            <td>{{ $item['dh'] }}</td>
            <td>{{ $item['nombre']}}</td>
            @if($item['dh']=='D')
                <td>{{ getDecimalExcel($item['importe']) }}</td>
            @else
                <td></td>
            @endif
            @if($item['dh']=='H')
                <td>{{ getDecimalExcel($item['importe']) }}</td>
            @else
                <td></td>
            @endif
            <td>{{ $item['manual']}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
