
<table>
    <thead>
        <tr>
            <th>Empresa</th>
            <th>Concepto</th>
            <th>Operaciones</th>
            <th>Importe</th>
        </tr>
    </thead>
    <tbody>
    @foreach($data as $item)
        <tr>
            <td>{{ $item['empresa']}}</td>
            <td>{{ $item['concepto']}}</td>
            <td>{{ $item['operaciones']}}</td>
            <td>{{ $item['importe']}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
