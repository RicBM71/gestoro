<table>
    <thead>
    <tr>
        <th>FACTURA</th>
        <th>F. FACTURA</th>
        <th>F. COMPRA</th>
        <th>DNI/PASAPORTE</th>
        <th>NOMBRE</th>
        <th>PVP</th>
        <th>COSTE</th>
        <th>BENEFICIO</th>
        <th>BASE</th>
        <th>IVA</th>
        <th>%IVA</th>
        <th>REBU</th>
        <th>COMPRA</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $item)
        <tr>
            <td>{{ $item['facser']}}</td>
            <td>{{ getFecha($item['fecha_factura']) }}</td>
            <td>{{ getFecha($item['fecha_compra']) }}</td>
            <td>{{ $item['dni']}}</td>
            <td>{{ $item['razon']}}</td>
            <td>{{ $item['pvp']}}</td>
            <td>{{ $item['coste']}}</td>
            <td>{{ $item['bene']}}</td>
            <td>{{ $item['base']}}</td>
            <td>{{ $item['iva']}}</td>
            <td>{{ $item['por_iva']}}</td>
            <td>{{ $item['rebu']}}</td>
            <td>{{ $item['alb_ser']}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
