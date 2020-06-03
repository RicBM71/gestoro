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
            <th>P. Coste</th>
            <th>Notas</th>
        </tr>
    </thead>
    <tbody>
    @foreach($data as $item)
        <tr>
            @if($item['deleted_at'] == null)
                <td>{{ $item['referencia']}}</td>
                <td>{{ $item['nombre']}}</td>
                <td>{{ $item['estado']}}</td>
                <td>{{ $item['rfid']}}</td>
                <td>{{ $item['precio_coste']}}</td>
                <td>{{ $item['notas']}}</td>
            @else
                <td>{{ $item['referencia']}}</td>
                <td>{{ $item['nombre']}}</td>
                <td>{{ $item['estado']}}</td>
                <td>BORRADO</td>
                <td>{{ $item['precio_coste']}}</td>
                <td>{{ $item['notas']}}</td>
                <td></td>
            @endif
        </tr>
    @endforeach
    </tbody>
</table>
