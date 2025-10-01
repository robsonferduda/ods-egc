<table>
    <thead>
    <tr>
        <td colspan="18"><strong>Relatório de Evolução por ODS/Ano</strong></td>
    </tr>
    <tr>
        <th style="font-weight: bold;">Ano</th>
        <th>ODS 1</th>
        <th>ODS 2</th>
        <th>ODS 3</th>
        <th>ODS 4</th>
        <th>ODS 5</th>
        <th>ODS 6</th>
        <th>ODS 7</th>
        <th>ODS 8</th>
        <th>ODS 9</th>
        <th>ODS 10</th>
        <th>ODS 11</th>
        <th>ODS 12</th>
        <th>ODS 13</th>
        <th>ODS 14</th>
        <th>ODS 15</th>
        <th>ODS 16</th>
        <th>Total Geral</th>
    </tr>
    </thead>
    <tbody>
        @foreach($dados as $d)
            <tr>
                <td>{{ $d['ano'] }}</td>
                <td>{{ $d['1'] }}</td>
                <td>{{ $d['2'] }}</td>
                <td>{{ $d['3'] }}</td>
                <td>{{ $d['4'] }}</td>
                <td>{{ $d['5'] }}</td>
                <td>{{ $d['6'] }}</td>
                <td>{{ $d['7'] }}</td>
                <td>{{ $d['8'] }}</td>
                <td>{{ $d['9'] }}</td>
                <td>{{ $d['10'] }}</td>
                <td>{{ $d['11'] }}</td>
                <td>{{ $d['12'] }}</td>
                <td>{{ $d['13'] }}</td>
                <td>{{ $d['14'] }}</td>
                <td>{{ $d['15'] }}</td>
                <td>{{ $d['16'] }}</td>
                <td>{{ $d['total'] }}</td>
            </tr>
        @endforeach
        <tr>
            <td>Totais</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>Percentual</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </tbody>
</table>