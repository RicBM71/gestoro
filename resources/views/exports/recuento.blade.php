<table>
    <thead>
        <tr>
            <th colspan="5">{{$titulo}}</th>
        </tr>
        <tr>
            <th>REFERENCIA</th>
            <th>NOMBRE</th>
            <th>ESTADO</th>
            <th>SITUACION</th>
            <th>Notas</th>
        </tr>
    </thead>
    <tbody>
    @foreach($data as $item)
        <tr>
            @if($item['producto'] != null)
                <td>{{ $item['producto']['referencia']}}</td>
                <td>{{ $item['producto']['nombre']}}</td>
                <td>{{ $item['estado']['nombre']}}</td>
                <td>{{ $item['rfid']['nombre']}}</td>
                <td>{{ $item['producto']['notas']}}</td>
            @else
                <td>{{ $item['producto_id']}}</td>
                <td>n/d</td>
                <td>n/d</td>
                <td>{{ $item['rfid']['nombre']}}</td>
                <td></td>
            @endif
        </tr>
    @endforeach
    </tbody>
</table>
