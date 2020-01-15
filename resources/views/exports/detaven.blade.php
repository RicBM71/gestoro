<table>
    <thead>
        <tr>
            <th>{{$titulo}}</th>
        </tr>
        <tr>
            <th>Albarán</th>
            <th>Fecha</th>
            <th>Referencia</th>
            <th>Producto</th>
            <th>Clase</th>
            <th>Coste</th>
            <th>Importe</th>
            <th>Margen</th>
            <th>Efectivo</th>
            <th>Banco</th>
        </tr>
    </thead>
    <tbody>
    @foreach($data as $item)
        <tr>
            <td>{{ $item['serie_albaran'].$item['albaran']}}</td>
            <td>{{ getFecha($item['fecha'])}}</td>
            <td>{{ $item['referencia']}}</td>
            <td>{{ $item['producto']}}</td>
            <td>{{ $item['clase']}}</td>
            <td>{{ $item['precio_coste']}}</td>
            <td>{{ $item['importe_venta']}}</td>
            <td>{{ $item['margen']}}</td>
            <td>{{ $item['efectivo']}}</td>
            <td>{{ $item['banco']}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
