<table>
    <tbody>
    <tr><td>01</td><td>{{date('d/m/Y H:i:s')}}</td></tr>
    @foreach($data as $item)
        <tr>
            <td>{{ $item['reg']}}</td>
            <td>{{ $item['rfid']}}</td>
            <td>{{ $item['check']}}</td>
            <td>{{ $item['nombre']}}</td>
            <td>{{ $item['referencia']}}</td>
            <td>{{ $item['pvp']}}</td>
            <td>{{ $item['clase']}}</td>
            <td>{{ $item['coste']}}</td>
        </tr>
    @endforeach
    <tr><td>99</td><td>{{count($data)}}</td></tr>
    </tbody>
</table>
