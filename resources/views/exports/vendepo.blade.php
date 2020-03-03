<table>
    <thead>
        <tr>
            <th colspan="8">{{$titulo}}</th>
        </tr>
        <tr>
            <th>EMPRESA DE VENTA</th>
            <th>Origen/Dest</th>
            <th>FACT/ALB</th>
            <th>FECHA</th>
            <th>REFERENCIA</th>
            <th>PRODUCTO</th>
            <th>P. VENTA</th>
            <th>P. COSTE</th>
            <th>MARGEN</th>
        </tr>
    </thead>
    <tbody>
    @foreach($data as $item)
        <tr>
            <td>{{ $item['empresa']}}</td>
            <td>{{ $item['sigla']}}/{{$item['procede']}}</td>
            <td>{{ $item['serie'].$item['numero']}}</td>
            <td>{{ getFecha($item['fecha'])}}</td>
            <td>{{ $item['referencia']}}</td>
            <td>{{ $item['producto']}}</td>
            <td>{{ $item['importe_venta']}}</td>
            <td>{{ $item['precio_coste']}}</td>
            <td>{{ $item['importe_venta'] - $item['precio_coste']}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
