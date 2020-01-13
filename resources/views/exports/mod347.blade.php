<table>
    <thead>
        <tr>
            <th>RAZON SOCIAL</th>
            <th>CIF</th>
            <th>TRIMESTRE 1</th>
            <th>TRIMESTRE 2</th>
            <th>TRIMESTRE 3</th>
            <th>TRIMESTRE 4</th>
            <th>TOTAL</th>
        </tr>
    </thead>
    <tbody>
    @foreach($data as $item)
        <tr>
            <td>{{ $item->razon}}</td>
            <td>{{ $item->dni}}</td>
            <td>{{ $item->rimptri1}}</td>
            <td>{{ $item->rimptri2}}</td>
            <td>{{ $item->rimptri3}}</td>
            <td>{{ $item->rimptri4}}</td>
            <td>{{ $item->rimptot}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
