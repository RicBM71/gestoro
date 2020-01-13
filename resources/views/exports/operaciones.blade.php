
<table>
    <thead>
        <tr>
            <th>Empresa</th>
            <th>Concepto</th>
            <th>Estado</th>
            <th>Detalle</th>
            <th>Operaciones</th>
            <th>Importe</th>
            <th>Peso</th>
            <th>Imp/Gr.</th>
        </tr>
    </thead>
    <tbody>
    @foreach($data as $item)
        <tr>
            <td>{{ $item['empresa']}}</td>
            <td>{{ $item['tipo']}}</td>
            <td>{{ $item['fase']}}</td>
            <td>{{ $item['clase']}}</td>
            <td>{{ $item['operaciones']}}</td>
            <td>{{ $item['importe']}}</td>
            <td>{{ $item['peso']}}</td>
            <td>{{ $item['peso'] !=0 ? round($item['importe'] / $item['peso'],2) : ''}}</td>
        </tr>
    @endforeach
    </tbody>
</table>

