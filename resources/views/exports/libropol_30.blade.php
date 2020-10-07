<table>
    <thead>
    <tr>
        <th>CODIGO TIENDA</th>
        <th>ESTABLECIMIENTO</th>
        <th>Nº ORDEN</th>
        <th>F. COMPRA</th>
        <th>APELLIDOS, NOMBRE</th>
        <th>DNI/PASAPORTE</th>
        <th>F. NACIMIENTO</th>
        <th>DOMICILIO</th>
        <th>LOCALIDAD</th>
        <th>PROVINCIA</th>
        <th>CLASE OBJETO</th>
        <th>PESO TOTAL</th>
        <th>METAL</th>
        <th>GRABACIONES</th>
        <th>CLASE PIEDRAS</th>
        <th>PRECIO</th>
        <th>PAPELETA</th>
        <th>FECHA VENTA</th>
        <th>IMÁGENES</th>
    </tr>
    </thead>
    <tbody>
    @foreach($compras as $item)
        <tr>
            <td>{{ $codigo_pol}}</td>
            <td>{{ $establecimiento}}</td>
            <td>{{ $item->albaran }}</td>
            <td>{{ getFecha($item->fecha_compra) }}</td>
            <td>{{ $item->apellidos.', '.$item->nombre }}</td>
            <td>{{ $item->dni }}</td>
            <td>{{ getFecha($item->fecha_nacimiento) }}</td>
            <td>{{ $item->direccion }}</td>
            <td>{{ $item->poblacion }}</td>
            <td>{{ $item->provincia }}</td>
            <td>{{ $item->concepto }}</td>
            <td>{{ getDecimal($item->peso_gr) }}</td>
            <td>{{ $item->clase }}</td>
            <td>{{ $item->grabaciones }}</td>
            <td>{{ getQuilates($item->quilates).' '.$item->colores}}</td>
            <td>{{ getDecimal($item->importe) }}</td>
            <td>{{ $item->papeleta }}</td>
            <td>-</td>
            <td>0</td>
        </tr>
    @endforeach
    </tbody>
</table>
