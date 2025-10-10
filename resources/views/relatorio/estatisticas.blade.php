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
		<h4><strong>Centro</strong>: {{ $centro }}</h4>
	</div>
	<h4><strong>Indicadores</strong></h4>
	<h5 style="margin-bottom: 3px; margin-top: 3px; padding-top: 0px;">Total de Documentos Analisados: {{ $total_documentos }}</h5>
	<h5 style="margin-bottom: 3px; margin-top: 3px; padding-top: 0px;">Total de Documentos sem ODS: {{ $documentos_sem_ods }}</h5>
	<h5 style="margin-bottom: 3px; margin-top: 3px; padding-top: 0px;">Total de Documentos com ODS: {{ $documentos_com_ods }}</h5>
	<h5 style="margin-bottom: 3px; margin-top: 3px; padding-top: 0px;">Dimensão Predominante: {{ $dimensao_predominante }}</h5>
	<h5 style="margin-bottom: 3px; margin-top: 3px; padding-top: 0px;">Índice de Crescimento Sustentável: {{ $indice_crescimento_sustentavel }}</h5>
	<h5 style="margin-bottom: 3px; margin-top: 3px; padding-top: 0px;">Índice de Engajamento Sustentável: {{ $indice_engajamento_sustentavel }}</h5>
	<h5 style="margin-bottom: 3px; margin-top: 3px; padding-top: 0px;">Docente Destaque: {{ $docente_destaque }}</h5>
	
	<div class="row">
                    <!--<div class="col-md-4" id="card-dimensao-centro"></div>-->
                    <div class="col-md-4" id="card-dimensao-ods">
                                <div class="card shadow-sm mb-2" style="background: #f3f3f3;">
                                    <div class="card-body text-center">
                                        <h5 class="card-title mb-0">Institucional</h5>
                                        <p class="card-text mb-1">
                                            <span class="display-4 font-weight-bold">35.3%</span>
                                        </p>
                                        <small class="text-muted">Dimensão ODS mais destacada no CFH</small>
                                    </div>
                                </div>
                            </div>
                    <div class="col-md-4" id="card-ics">
                                <div class="card shadow-sm mb-2" style="background: #f3f3f3;">
                                    <div class="card-body text-center">
                                        <h5 class="card-title mb-0">
                                            ICS
                                            <span class="pull-right badge badge-pill badge-danger" style="font-size: 1rem; vertical-align: top; margin-left: 8px;">Queda</span>
                                        </h5>
                                        <p class="card-text mb-1">
                                            <span class="display-4 font-weight-bold">23.3</span>                                            
                                        </p>
                                        <small class="text-muted">Índice de Crescimento Sustentável</small>
                                    </div>
                                </div>
                            </div>
                    <div class="col-md-4" id="card-ies">
                                <div class="card shadow-sm mb-2" style="background: #f3f3f3;">
                                    <div class="card-body text-center">
                                        <h5 class="card-title mb-0">
                                            IES
                                            <span class="pull-right badge badge-pill badge-warning" style="font-size: 1rem; vertical-align: top; margin-left: 8px;">Médio</span>
                                        </h5>
                                        <p class="card-text mb-1">
                                            <span class="display-4 font-weight-bold">60.7</span>                                           
                                        </p>
                                        <small class="text-muted">Índice de Engajamento Sustentável</small>
                                    </div>
                                </div>
                            </div>
                    <div class="col-md-12" id="card-pesquisador-centro">
                                <div class="card shadow-sm mb-2 mt-3" style="background: #f3f3f3;">
                                    <div class="card-body text-center">
                                        <h5 class="card-title mb-0">ANDRÉIA ISABEL GIACOMOZZI</h5>
                                        <p class="card-text mb-1">
                                            <span class="display-4 font-weight-bold">22</span>
                                        </p>
                                        <small class="text-muted">Pesquisador(a) com mais documentos no centro CFH</small>
                                    </div>
                                </div>
                            </div>
                </div>
				
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