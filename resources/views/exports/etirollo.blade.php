<table>
    <tbody>
        <tr><th>Referencia</th>
            <th>Nombre</th>
            <th>PVP/Peso</th>
            <th>Características</th>
        </tr>
        @foreach($data as $item)
            <tr>
                <td>{{ $item->referencia}}</td>
                <td>{{ $item->nombre}}</td>
                @if ($item->clase_id == 1)
                    <td>{{ getDecimalExcel($item->peso_gr, 2)}}</td>
                    <td>{{ $item->quilates.' K'}}</td>
                @else
                    <td>{{ getDecimalExcel($item->precio_venta, 0)}}</td>
                    <td>{{ $item->caracteristicas}}</td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>
