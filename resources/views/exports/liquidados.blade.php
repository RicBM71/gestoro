<table>
    <thead>
        <tr>
            <th>{{$titulo}}</th>
        </tr>
        <tr>
            <th>Empresa</th>
            <th>Clase</th>
            <th>Importe</th>
            <th>Peso Gr.</th>
        </tr>
    </thead>
    <tbody>
    @foreach($data as $item)
        @if($item['empresa']!="")
            <tr>
                <td>{{ $item['empresa']}}</td>
                @if($item['quilates'] > 0)
                    <td>{{ $item['clase'].' '.$item['quilates'].'K'}}</td>
                @else
                    <td>{{ $item['clase']}}</td>
                @endif
                <td>{{ $item['importe']}}</td>
                <td>{{ $item['peso_gr']}}</td>
            </tr>
        @endif
    @endforeach
    </tbody>
</table>
