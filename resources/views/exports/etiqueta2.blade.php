<table>
    <tbody>
        <tr><th>Referencia</th><th>PVP</th></tr>
        @foreach($data as $item)
            <tr>
                <td>{{ $item['referencia']}}</td>
                <td>{{ $item['nombre']}}</td>
                <td>{{ $item['pvp']}}</td>
            </tr>
        @endforeach
    </tbody>
</table>
