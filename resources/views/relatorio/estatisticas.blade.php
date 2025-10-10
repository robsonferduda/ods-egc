<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório ODS</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            color: #222;
            margin: 20px;
        }
        h3, h4 {
            margin-top: 10px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            margin-bottom: 18px;
        }
        td, th {
            padding: 0px;
            vertical-align: top;
        }
        img {
            max-width: 100%;
            height: auto;
            margin-bottom: 8px;
        }
        .center { text-align: center; }
    </style>
</head>
<body>
    <h3 style="text-align: center; margin-bottom: 0px; font-weight: 700; text-transform: uppercase;" class="center">Perfil ODS - Relatório de Diagnóstico</h3>
    <p style="" class="center">{{ $periodo }}</p>
	<div style="margin-bottom: 10px; background-color: #f3f3f3; padding: 10px; border-radius: 5px;">
		<h4><strong>Centro</strong>:</h4>
	</div>
	<h4><strong>Indicadores</strong></h4>
	<h5 style="margin-bottom: 3px; margin-top: 3px; padding-top: 0px;">Total de Documentos Analisados: {{ $total_documentos }}</h5>
	<h5 style="margin-bottom: 3px; margin-top: 3px; padding-top: 0px;">Total de Documentos sem ODS: {{ $documentos_sem_ods }}</h5>
	<h5 style="margin-bottom: 3px; margin-top: 3px; padding-top: 0px;">Total de Documentos com ODS: {{ $documentos_com_ods }}</h5>
	<h5 style="margin-bottom: 3px; margin-top: 3px; padding-top: 0px;">Dimensão Predominante: {{ $dimensao_predominante }}</h5>
	<h5 style="margin-bottom: 3px; margin-top: 3px; padding-top: 0px;">Índice de Crescimento Sustentável: {{ $indice_crescimento_sustentavel }}</h5>
	<h5 style="margin-bottom: 3px; margin-top: 3px; padding-top: 0px;">Índice de Engajamento Sustentável: {{ $indice_engajamento_sustentavel }}</h5>
	<h5 style="margin-bottom: 3px; margin-top: 3px; padding-top: 0px;">Docente Destaque: {{ $docente_destaque }}</h5>
	
	<table>
		<thead>
		<tr>
			<td colspan="18" style="text-align: center;"><strong>Relatório de Evolução por ODS/Ano</strong></td>
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
			@foreach($lista as $key => $d)
				@if($key < (count($lista)))
				@php
					$formatacao = ($key > (count($lista) - 3)) ? 'font-weight: bold;' : '';
				@endphp
					<tr>
						<td style="{{ $formatacao }} text-align: center;">{{ $d['ano'] }}</td>
						<td style="{{ $formatacao }} text-align: center;">{{ $d['1'] }}</td>
						<td style="{{ $formatacao }} text-align: center;">{{ $d['2'] }}</td>
						<td style="{{ $formatacao }} text-align: center;">{{ $d['3'] }}</td>
						<td style="{{ $formatacao }} text-align: center;">{{ $d['4'] }}</td>
						<td style="{{ $formatacao }} text-align: center;">{{ $d['5'] }}</td>
						<td style="{{ $formatacao }} text-align: center;">{{ $d['6'] }}</td>
						<td style="{{ $formatacao }} text-align: center;">{{ $d['7'] }}</td>
						<td style="{{ $formatacao }} text-align: center;">{{ $d['8'] }}</td>
						<td style="{{ $formatacao }} text-align: center;">{{ $d['9'] }}</td>
						<td style="{{ $formatacao }} text-align: center;">{{ $d['10'] }}</td>
						<td style="{{ $formatacao }} text-align: center;">{{ $d['11'] }}</td>
						<td style="{{ $formatacao }} text-align: center;">{{ $d['12'] }}</td>
						<td style="{{ $formatacao }} text-align: center;">{{ $d['13'] }}</td>
						<td style="{{ $formatacao }} text-align: center;">{{ $d['14'] }}</td>
						<td style="{{ $formatacao }} text-align: center;">{{ $d['15'] }}</td>
						<td style="{{ $formatacao }} text-align: center;">{{ $d['16'] }}</td>
						<td style="{{ $formatacao }} text-align: center;">{{ $d['total'] }}</td>
					</tr>
				@endif
			@endforeach
		</tbody>
	</table>
	
	<h4 style="margin-top: 10px"><strong>Gráficos de Desempenho</strong>:</h4>
    <table>
        <tr>
            <td>
                <p class="center">Evolução por ODS</p>
                <img src="{{ $grafico_evolucao }}" alt="Gráfico Evolução ODS" />
            </td>
            <td>
                <p class="center">Totais de documentos por ODS</p>
                <img src="{{ $grafico_total }}" alt="Gráfico Total ODS" />
            </td>
        </tr>
    </table>
    <!-- Adicione mais conteúdo conforme necessário -->
</body>
</html>