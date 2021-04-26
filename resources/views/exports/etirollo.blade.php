<table>
    <tbody>
        <tr><th>Referencia</th>
            <th>Nombre</th>
            <th>Carac/Peso</th>
            <th>PVP/KT</th>
        </tr>
        @foreach($data as $item)
            <tr>
                <td>{{ $item->referencia}}</td>
                <td>{{ $item->nombre}}</td>
                @if ($item->clase_id == 1)
                    <td>{{ getDecimalExcel($item->peso_gr, 2)}} g</td>
                    <td>{{ $item->quilates.' KT'}}</td>
                @else
                    <td>{{ $item->caracteristicas}}</td>
                    <td>{{ getDecimalExcel($item->precio_venta, 0)}} €</td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>
