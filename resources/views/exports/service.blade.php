<table>
    <thead>
        <tr>
            <th>{{$titulo}}</th>
        </tr>
        <tr>
            <th>Albarán</th>
            <th>Fecha</th>
            <th>Pieza</th>
            <th>Servicio</th>
            <th>Coste</th>
        </tr>
    </thead>
    <tbody>
    @foreach($data as $item)
        <tr>
            <td>{{ $item['serie'].$item['albaran']}}</td>
            <td>{{ getFecha($item['fecha'])}}</td>
            <td>{{ $item['notas_ext']}}</td>
            <td>{{ $item['producto']}}</td>
            <td>{{ $item['precio_coste']}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
