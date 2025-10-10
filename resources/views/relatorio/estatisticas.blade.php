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
	<div style="margin-bottom: 10px; background-color: #c4c4c4; padding: 10px; border-radius: 5px;">
		<h4><strong>Centro</strong>:</h4>
	</div>
	<h4><strong>Indicadores</strong></h4>
	<h5 style="margin-bottom: 3px;">Total de Documentos Analisados: {{ $total_documentos }}</h5>
	<h5 style="margin-bottom: 3px;">Total de Documentos sem ODS: {{ $documentos_sem_ods }}</h5>
	<h5 style="margin-bottom: 3px;">Total de Documentos com ODS: {{ $documentos_com_ods }}</h5>
	<h5 style="margin-bottom: 3px;">Dimensão Predominante: {{ $dimensao_predominante }}</h5>
	<h5 style="margin-bottom: 3px;">Índice de Crescimento Sustentável: {{ $indice_crescimento_sustentavel }}</h5>
	<h5 style="margin-bottom: 3px;">Índice de Engajamento Sustentável: {{ $indice_engajamento_sustentavel }}</h5>
	<h5 style="margin-bottom: 3px;">Docente Destaque: {{ $docente_destaque }}</h5>
	<h4><strong>Gráficos de Desempenho</strong>:</h4>
    <table>
        <tr>
            <td>
                <p>Evolução por ODS</p>
                <img src="{{ $grafico_evolucao }}" alt="Gráfico Evolução ODS" />
            </td>
            <td>
                <p>Totais de documentos por ODS</p>
                <img src="{{ $grafico_total }}" alt="Gráfico Total ODS" />
            </td>
        </tr>
    </table>
    <!-- Adicione mais conteúdo conforme necessário -->
</body>
</html>