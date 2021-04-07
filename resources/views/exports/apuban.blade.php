<table>
    <thead>
        <tr>
            <th>Apuntes de Banco</th>
        </tr>
        <tr>
            <th>Fecha</th>
            <th>Concepto</th>
            <th>Cliente</th>
            <th>Importe</th>
            <th>Operación</th>
            <th>Serie</th>
            <th>Fecha</th>
            <th>Notas</th>
        </tr>
    </thead>
    <tbody>
    @foreach($data as $item)
        <tr>
            <td>{{ getFecha($item['fecha'])}}</td>
            <td>{{ $item['concepto']}}</td>
            <td>{{ $item['razon']}}</td>
            <td>{{ $item['importe']}}</td>
            <td>{{ $item['albaran']}}</td>
            <td>{{ $item['serie']}}</td>
            <td>{{ getFecha($item['fecha_op'])}}</td>
            <td>{{ $item['notas']}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
