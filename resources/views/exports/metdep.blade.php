<table>
    <thead>
        <tr>
            <th colspan="8">{{$titulo}}</th>
        </tr>
        <tr>
            <th>Empresa</th>
            <th>Clase</th>
            <th>Peso</th>
            <th>Importe</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
            <tr>
                <td>{{$item['empresa']}}</td>
                <td>{{ $item['clase'] }}</td>
                <td class="text-xs-right">{{ $item['peso_gr'] }}</td>
                <td class="text-xs-right">{{ $item['importe'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
