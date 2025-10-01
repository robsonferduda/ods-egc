<table>
    <thead>
    <tr>
        <td colspan="18" style="text-align: center;"><strong>Relatório de Evolução por ODS/Ano</strong></td>
    </tr>
    <tr>
        <td colspan="18"><strong>Filtros</strong>: {{ $dados[count($dados)-1]['filtros'] }}</td>
    </tr>
    <tr>
        <th style="font-weight: bold; text-align: center;">Ano</th>
        <th style="font-weight: bold; text-align: center;">ODS 1</th>
        <th style="font-weight: bold; text-align: center;">ODS 2</th>
        <th style="font-weight: bold; text-align: center;">ODS 3</th>
        <th style="font-weight: bold; text-align: center;">ODS 4</th>
        <th style="font-weight: bold; text-align: center;">ODS 5</th>
        <th style="font-weight: bold; text-align: center;">ODS 6</th>
        <th style="font-weight: bold; text-align: center;">ODS 7</th>
        <th style="font-weight: bold; text-align: center;">ODS 8</th>
        <th style="font-weight: bold; text-align: center;">ODS 9</th>
        <th style="font-weight: bold; text-align: center;">ODS 10</th>
        <th style="font-weight: bold; text-align: center;">ODS 11</th>
        <th style="font-weight: bold; text-align: center;">ODS 12</th>
        <th style="font-weight: bold; text-align: center;">ODS 13</th>
        <th style="font-weight: bold; text-align: center;">ODS 14</th>
        <th style="font-weight: bold; text-align: center;">ODS 15</th>
        <th style="font-weight: bold; text-align: center;">ODS 16</th>
        <th style="font-weight: bold; text-align: center;">Total Geral</th>
    </tr>
    </thead>
    <tbody>
        @foreach($dados as $key => $d)
            @if($key < (count($dados) - 1))
                <tr>
                    <td style="text-align: center;">{{ $d['ano'] }}</td>
                    <td style="text-align: center;">{{ $d['1'] }}</td>
                    <td style="text-align: center;">{{ $d['2'] }}</td>
                    <td style="text-align: center;">{{ $d['3'] }}</td>
                    <td style="text-align: center;">{{ $d['4'] }}</td>
                    <td style="text-align: center;">{{ $d['5'] }}</td>
                    <td style="text-align: center;">{{ $d['6'] }}</td>
                    <td style="text-align: center;">{{ $d['7'] }}</td>
                    <td style="text-align: center;">{{ $d['8'] }}</td>
                    <td style="text-align: center;">{{ $d['9'] }}</td>
                    <td style="text-align: center;">{{ $d['10'] }}</td>
                    <td style="text-align: center;">{{ $d['11'] }}</td>
                    <td style="text-align: center;">{{ $d['12'] }}</td>
                    <td style="text-align: center;">{{ $d['13'] }}</td>
                    <td style="text-align: center;">{{ $d['14'] }}</td>
                    <td style="text-align: center;">{{ $d['15'] }}</td>
                    <td style="text-align: center;">{{ $d['16'] }}</td>
                    <td style="text-align: center;">{{ $d['total'] }}</td>
                </tr>
            @endif
        @endforeach
        <tr>
            <td style="font-weight: bold; text-align: center;">Totais</td>
            <td style="font-weight: bold; text-align: center;">{{ $dados[count($dados)-1]['soma'] }}</td>
            <td style="font-weight: bold; text-align: center;"></td>
            <td style="font-weight: bold; text-align: center;"></td>
            <td style="font-weight: bold; text-align: center;"></td>
            <td style="font-weight: bold; text-align: center;"></td>
            <td style="font-weight: bold; text-align: center;"></td>
            <td style="font-weight: bold; text-align: center;"></td>
            <td style="font-weight: bold; text-align: center;"></td>
            <td style="font-weight: bold; text-align: center;"></td>
            <td style="font-weight: bold; text-align: center;"></td>
            <td style="font-weight: bold; text-align: center;"></td>
            <td style="font-weight: bold; text-align: center;"></td>
            <td style="font-weight: bold; text-align: center;"></td>
            <td style="font-weight: bold; text-align: center;"></td>
            <td style="font-weight: bold; text-align: center;"></td>
            <td style="font-weight: bold; text-align: center;"></td>
            <td style="font-weight: bold; text-align: center;"></td>
        </tr>
        <tr>
            <td style="font-weight: bold; text-align: center;">Percentual</td>
            <td style="font-weight: bold; text-align: center;"></td>
            <td style="font-weight: bold; text-align: center;"></td>
            <td style="font-weight: bold; text-align: center;"></td>
            <td style="font-weight: bold; text-align: center;"></td>
            <td style="font-weight: bold; text-align: center;"></td>
            <td style="font-weight: bold; text-align: center;"></td>
            <td style="font-weight: bold; text-align: center;"></td>
            <td style="font-weight: bold; text-align: center;"></td>
            <td style="font-weight: bold; text-align: center;"></td>
            <td style="font-weight: bold; text-align: center;"></td>
            <td style="font-weight: bold; text-align: center;"></td>
            <td style="font-weight: bold; text-align: center;"></td>
            <td style="font-weight: bold; text-align: center;"></td>
            <td style="font-weight: bold; text-align: center;"></td>
            <td style="font-weight: bold; text-align: center;"></td>
            <td style="font-weight: bold; text-align: center;"></td>
            <td style="font-weight: bold; text-align: center;"></td>
        </tr>
    </tbody>
</table>