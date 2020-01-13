
<table>
    <thead>
        <tr>
            <th>{{$titulo}}</th>
        </tr>
        <tr>
            <th>Concepto</th>
            <th>Debe</th>
            <th>Haber</th>
        </tr>
    </thead>
    <tbody>
    @foreach($data as $item)
        <tr>
            <td>{{ $item['concepto']}}</td>
            @if($item['dh']=='D')
                <td>{{ $item['importe']}}</td>
            @else
                <td></td>
            @endif
            @if($item['dh']=='H')
                <td>{{ $item['importe']}}</td>
            @else
                <td></td>
            @endif
        </tr>
    @endforeach
    </tbody>
</table>
