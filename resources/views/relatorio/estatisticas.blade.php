<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório ODS</title>
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 12px;
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
			margin-bottom: 5px;
			padding: 3px 2px;
			border: 1px solid #e0e0e0;
		}
		.card-title {
			font-size: 1.0rem;
			font-weight: bold;
			margin-bottom: 3px;
			margin-top: 3px;
		}
		.card-body {
			text-align: center;
		}
		.display-4 {
			font-size: 1.8rem;
			font-weight: bold;
		}
		.badge {
			display: inline-block;
			padding: 0.15em 0.4em;
			font-size: 0.3em;
			font-weight: 600;
			border-radius: 0.5em;
			vertical-align: middle;
			margin-left: 8px;
		}
		.badge-danger {
			background: #dc3545;
			color: #fff;
		}
		.badge-success {
			background: #28a745;
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
			width: 31%;
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
		
    </style>
</head>
<body>
    <h3 style="text-align: center; margin-bottom: 0px; font-weight: 700; text-transform: uppercase;" class="center">AI4SDG-GOV - Relatório de Diagnóstico</h3>
    <p style="" class="center">{{ $periodo }}</p>
	<h4 style="margin-bottom: 3px; margin-top: 3px; padding-top: 0px;"><strong>Unidade Administrativa</strong>: Centro {{ $centro }}</h4>
	<h4 style="margin-bottom: 3px; margin-top: 5px; padding-top: 0px;"><strong>Dados Quantitativos</strong></h4>
    <p style="margin-bottom: 3px; margin-top: 3px; padding-top: 0px;"><strong>Total de ODS Detectados:</strong> {{ $total_ods_detectados }}</p>
	<p style="margin-bottom: 3px; margin-top: 3px; padding-top: 0px;"><strong>Total de Documentos Analisados:</strong> {{ number_format($total_documentos, 0, ',', '.') }}</p>
	<p style="margin-bottom: 3px; margin-top: 3px; padding-top: 0px;"><strong>Total de Documentos sem ODS:</strong> {{ number_format($documentos_sem_ods, 0, ',', '.') }}</p>
	<p style="margin-bottom: 8px; margin-top: 3px; padding-top: 0px;"><strong>Total de Documentos com ODS:</strong> {{ number_format($documentos_com_ods, 0, ',', '.') }}</p>
	<h4><strong>Indicadores</strong></h4>
	<div class="row">   
        @if($dimensao_predominante)
        <div class="col-md-4" id="card-dimensao-ods">
            <div class="card shadow-sm mb-2" style="background: #f3f3f3;">
                <div class="card-body text-center">
                    <h5 class="card-title mb-0">{{ $dimensao_predominante }}</h5>
                    <p class="card-text mb-1">
                        <span class="display-4 font-weight-bold">{{ $dimensao_predominante_percentual }}%</span>
                    </p>
                    <small class="text-muted">Dimensão Predominante</small>
                </div>
            </div>
        </div>
        @endif
        
        @if($ics_valor)
        <div class="col-md-4" id="card-ics">
            <div class="card shadow-sm mb-2" style="background: #f3f3f3;">
                <div class="card-body text-center">
                    <h5 class="card-title mb-0">
                        ICS
                        <span class="pull-right badge badge-pill badge-{{ $ics_badge }}" style="font-size: 10px; vertical-align: top; margin-left: 8px;">{{ $ics_nivel }}</span>                                        
                    </h5>
                    <p class="card-text mb-1">
                        <span class="display-4 font-weight-bold">{{ $ics_valor }}</span>    
						
                    </p>
                    <small class="text-muted">Índice de Crescimento Sustentável</small>
                </div>
            </div>
        </div>
        @endif
        
        @if($ies_valor)
        <div class="col-md-4" id="card-ies">
            <div class="card shadow-sm mb-2" style="background: #f3f3f3;">
                <div class="card-body text-center">
                    <h5 class="card-title mb-0">
                        IES
                        <span class="pull-right badge badge-pill badge-{{ $ies_badge }}" style="font-size: 10px; vertical-align: top; margin-left: 8px;">{{ $ies_nivel }}</span>                                       
                    </h5>
                    <p class="card-text mb-1">
                        <span class="display-4 font-weight-bold">{{ $ies_valor }}</span>    
						
                    </p>
                    <small class="text-muted">Índice de Engajamento Sustentável</small>
                </div>
            </div>
        </div>
        @endif
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

    @if(!empty($tabela_ods) && !empty($anos))
    <h4 style="margin-top: 20px"><strong>Documentos por ODS e Ano</strong>:</h4>
    <table class="table-dados" style="width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 10px;">
        <thead>
            <tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                <th style="padding: 8px; text-align: center; border: 1px solid #dee2e6;">ODS</th>
                @foreach($anos as $ano)
                    <th style="padding: 8px; text-align: center; border: 1px solid #dee2e6;">{{ $ano }}</th>
                @endforeach
                <th style="padding: 8px; text-align: center; border: 1px solid #dee2e6; background-color: #e9ecef;"><strong>Total</strong></th>
            </tr>
        </thead>
        <tbody>
            @foreach($tabela_ods as $ods => $valores_anos)
            <tr>
                <td style="padding: 8px; text-align: center; border: 1px solid #dee2e6; font-weight: bold;">
                    @if($ods == 0)
                        Sem ODS
                    @else
                        ODS {{ $ods }}
                    @endif
                </td>
                @php $total_linha = 0; @endphp
                @foreach($anos as $ano)
                    <td style="padding: 8px; text-align: center; border: 1px solid #dee2e6;">
                        {{ $valores_anos[$ano] ?? 0 }}
                        @php $total_linha += ($valores_anos[$ano] ?? 0); @endphp
                    </td>
                @endforeach
                <td style="padding: 8px; text-align: center; border: 1px solid #dee2e6; background-color: #f8f9fa; font-weight: bold;">
                    {{ $total_linha }}
                </td>
            </tr>
            @endforeach
            <tr style="background-color: #e9ecef; font-weight: bold;">
                <td style="padding: 8px; text-align: center; border: 1px solid #dee2e6;"><strong>Total</strong></td>
                @foreach($anos as $ano)
                    @php 
                        $total_coluna = 0;
                        foreach($tabela_ods as $valores_anos){
                            $total_coluna += ($valores_anos[$ano] ?? 0);
                        }
                    @endphp
                    <td style="padding: 8px; text-align: center; border: 1px solid #dee2e6;">{{ $total_coluna }}</td>
                @endforeach
                <td style="padding: 8px; text-align: center; border: 1px solid #dee2e6; background-color: #dee2e6;">
                    <strong>{{ $total_documentos }}</strong>
                </td>
            </tr>
        </tbody>
    </table>
    @endif
    
</body>
</html>