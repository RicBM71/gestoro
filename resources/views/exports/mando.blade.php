<table>
    <thead>
        <tr>
            <th colspan="8">{{$titulo}}</th>
        </tr>
        <tr>
            <th>Concepto</th>
            <th>Clase</th>
            <th>Peso</th>
            <th>Importe</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data_com as $item)
            <tr>
                <td>COMPRAS</td>
                @if( $item['quilates'] > 0)
                    <td>{{ $item['clase']." ".$item['quilates']." K" }}</td>
                @else
                    <td>{{ $item['clase'] }}</td>
                @endif
                <td class="text-xs-right">{{ $item['peso_gr'] }}</td>
                <td class="text-xs-right">{{ $item['importe'] }}</td>
            </tr>
        @endforeach
        @foreach($data_liq as $item)
            <tr>
                <td>LIQUIDADOS BRUTO</td>
                @if( $item['quilates'] > 0)
                    <td>{{ $item['clase']." ".$item['quilates']." K" }}</td>
                @else
                    <td>{{ $item['clase'] }}</td>
                @endif
                <td class="text-xs-right">{{ $item['peso_gr'] }}</td>
                <td class="text-xs-right">{{ $item['importe'] }}</td>
            </tr>
        @endforeach
        @foreach($data_inv as $item)
            <tr>
                <td>INVENTARIADOS</td>
                @if( $item['quilates'] > 0)
                    <td>{{ $item['clase']." ".$item['quilates']." K" }}</td>
                @else
                    <td>{{ $item['clase'] }}</td>
                @endif
                <td class="text-xs-right">{{ $item['peso_gr'] }}</td>
                <td class="text-xs-right">{{ $item['importe'] }}</td>
            </tr>
        @endforeach
        @foreach($data_net as $item)
            <tr>
                <td>LIQUIDADOS NETO</td>
                @if( $item['quilates'] > 0)
                    <td>{{ $item['clase']." ".$item['quilates']." K" }}</td>
                @else
                    <td>{{ $item['clase'] }}</td>
                @endif
                <td class="text-xs-right">{{ $item['peso_gr'] }}</td>
                <td class="text-xs-right">{{ $item['importe'] }}</td>
            </tr>
        @endforeach
        @foreach($data_ven as $item)
            <tr>
                <td>{{$det_ven[$item['tipo_id']]}}</td>
                <td>{{ $item['clase'] }}</td>
                <td class="text-xs-right">{{ $item['peso_gr'] }}</td>
                <td class="text-xs-right">{{ $item['importe'] }}</td>
            </tr>
        @endforeach
        @foreach($data_dep as $item)
            <tr>
                <td>{{$det_com[$item['tipo_id']]}}</td>
                <td>{{ $item['clase'] }}</td>
                <td class="text-xs-right">{{ $item['peso_gr'] }}</td>
                <td class="text-xs-right">{{ $item['importe'] }}</td>
            </tr>
        @endforeach
        {@foreach($data_pro as $item)
            <tr>
                <td>INVENTARIO</td>

                <td>{{ $item['clase'] }}</td>
                <td class="text-xs-right">{{ $item['peso_gr'] }}</td>
                <td class="text-xs-right">{{ $item['importe'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
