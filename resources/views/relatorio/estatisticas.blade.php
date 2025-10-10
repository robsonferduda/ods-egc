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
        
		.card {
			background: #f3f3f3;
			border-radius: 6px;
			box-shadow: 0 2px 6px #0001;
			margin-bottom: 18px;
			padding: 18px 12px;
			border: 1px solid #e0e0e0;
		}
		.card-title {
			font-size: 1.1rem;
			font-weight: bold;
			margin-bottom: 6px;
		}
		.card-body {
			text-align: center;
		}
		.display-4 {
			font-size: 2.2rem;
			font-weight: bold;
		}
		.badge {
			display: inline-block;
			padding: 0.35em 0.7em;
			font-size: 0.95em;
			font-weight: 600;
			border-radius: 1em;
			vertical-align: middle;
			margin-left: 8px;
		}
		.badge-danger {
			background: #dc3545;
			color: #fff;
		}
		.badge-warning {
			background: #ffc107;
			color: #222;
		}
		.badge-pill {
			border-radius: 1em;
		}
		.text-muted {
			color: #6c757d;
			font-size: 0.95em;
		}
		.mt-3 { margin-top: 1.2em; }
		.mb-2 { margin-bottom: 0.8em; }
		.mb-1 { margin-bottom: 0.4em; }
		.center { text-align: center; }
		.row {
			display: flex;
			flex-wrap: wrap;
			margin-left: -8px;
			margin-right: -8px;
		}
		.col-md-4 {
			width: 32%;
			margin: 0 1%;
			min-width: 200px;
			box-sizing: border-box;
			display: inline-block;
			vertical-align: top;
		}
		.col-md-12 {
			width: 98%;
			margin: 0 1%;
			box-sizing: border-box;
			display: block;
		}
		@media (max-width: 900px) {
			.col-md-4, .col-md-12 {
				width: 100% !important;
				margin: 0 0 12px 0;
			}
		}
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