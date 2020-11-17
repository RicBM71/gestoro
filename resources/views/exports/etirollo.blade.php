<table>
    <tbody>
        <tr><th>Referencia</th>
            <th>Nombre</th>
            <th>PVP</th></tr>
        @foreach($data as $item)
            <tr>
                <td>{{ $item->referencia}}</td>
                <td>{{ $item->nombre}}</td>
                <td>{{ getDecimalExcel($item->precio_venta, 0)}}</td>
            </tr>
        @endforeach
    </tbody>
</table>
