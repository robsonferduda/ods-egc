<table>
    <thead>
    <tr>
        <th>ODS</th>
        <th>Ano</th>
        <th>Quantidade</th>
    </tr>
    </thead>
    <tbody>
        @foreach($dados as $d)
            <tr>
                <td>{{ $d['ods'] }}</td>
                <td>{{ $d['ano'] }}</td>
                <td>{{ $d['total'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>