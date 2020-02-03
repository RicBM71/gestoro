
<table>
    <thead>
        <tr>
            <th>{{$titulo}}</th>
        </tr>
        <tr>
            <th>Concepto</th>
            <th>Clase</th>
            <th>Importe</th>
            <th>Importe Venta</th>
            <th>Márgen</th>
            <th>Peso</th>
        </tr>
    </thead>
    <tbody>
    @foreach($data as $item)
        @if($item['nom_com']!="")
            <tr>
                <td>{{ $item['nom_com']}}</td>
                @if($item['quilates']>0)
                    <td>{{ $item['clase']." ".$item['quilates']." K"}}</td>
                @else
                    <td>{{ $item['clase']}}</td>
                @endif
                <td>{{ $item['importe']}}</td>
                <td>{{ $item['importe_venta']}}</td>
                <td>{{ $item['importe_venta'] - $item['importe']}}</td>
                <td>{{ $item['peso_gr']}}</td>
            </tr>
        @endif
    @endforeach
    </tbody>
</table>
