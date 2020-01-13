<table>
    <thead>
        <tr>
            <th>{{$titulo}}</th>
        </tr>
        <tr>
            <th>Compra</th>
            <th>Fecha</th>
            <th>Concepto</th>
            <th>Grabaciones</th>
            <th>Importe</th>
            <th>Peso Gr.</th>
        </tr>
    </thead>
    <tbody>
    @foreach($data as $item)
        <tr>
            <td>{{ $item['serie_com'].$item['albaran']}}</td>
            <td>{{ getFecha($item['fecha_compra'])}}</td>
            <td>{{ $item['concepto']}}</td>
            <td>{{ $item['grabaciones']}}</td>
            @if($item['quilates'] > 0)
                <td>{{ $item['clase'].' '.$item['quilates'].'K'}}</td>
            @else
                <td>{{ $item['clase']}}</td>
            @endif
            <td>{{ $item['importe']}}</td>
            <td>{{ $item['peso_gr']}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
