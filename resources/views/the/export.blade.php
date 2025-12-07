<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>THE Impact Rankings - ODS {{ $ods }} - Evidências</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
        }
        h1 {
            color: #2c3e50;
            font-size: 18px;
            margin-bottom: 5px;
        }
        h2 {
            color: #34495e;
            font-size: 14px;
            margin-top: 20px;
            margin-bottom: 10px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 5px;
        }
        .header {
            background-color: #3498db;
            color: white;
            padding: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: white;
            margin: 0;
        }
        .summary {
            background-color: #ecf0f1;
            padding: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #3498db;
        }
        .summary p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }
        th {
            background-color: #34495e;
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 6px 8px;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 9px;
            color: #7f8c8d;
            text-align: center;
        }
        .prob-high {
            background-color: #d4edda;
            font-weight: bold;
        }
        .prob-medium {
            background-color: #fff3cd;
        }
        .prob-low {
            background-color: #f8d7da;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>THE Impact Rankings - Pacote de Evidências</h1>
        <p style="margin: 5px 0;">ODS {{ $ods }} - {{ $nomeOds }}</p>
        <p style="margin: 0; font-size: 10px;">Gerado em: {{ date('d/m/Y H:i:s') }}</p>
    </div>

    <div class="summary">
        <h2>Resumo Executivo</h2>
        <p><strong>Total de Evidências:</strong> {{ $evidencias->count() }} documentos</p>
        <p><strong>Período de Cobertura:</strong> 
            {{ $evidencias->min('ano') ?? 'N/A' }} - {{ $evidencias->max('ano') ?? 'N/A' }}
        </p>
        <p><strong>Status:</strong> 
            @if($evidencias->count() >= 50)
                <strong style="color: #27ae60;">✓ PRONTO PARA SUBMISSÃO</strong>
            @elseif($evidencias->count() >= 25)
                <strong style="color: #f39c12;">⚠ NECESSITA REFORÇO</strong>
            @else
                <strong style="color: #e74c3c;">✗ INSUFICIENTE</strong>
            @endif
        </p>
    </div>

    <h2>Lista Completa de Evidências</h2>
    <table>
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="5%">Ano</th>
                <th width="45%">Título</th>
                <th width="15%">Autor</th>
                <th width="10%">Centro</th>
                <th width="12%">Dimensão</th>
                <th width="8%">Prob.</th>
            </tr>
        </thead>
        <tbody>
            @foreach($evidencias as $index => $evidencia)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $evidencia->ano }}</td>
                <td>
                    <strong>{{ $evidencia->titulo }}</strong>
                    @if($evidencia->resumo)
                        <br><em style="font-size: 9px; color: #666;">
                            {{ Str::limit($evidencia->resumo, 200) }}
                        </em>
                    @endif
                </td>
                <td>{{ $evidencia->autor ?? 'N/A' }}</td>
                <td>{{ $evidencia->centro ?? 'N/A' }}</td>
                <td>{{ $evidencia->dimensao ?? 'N/A' }}</td>
                <td class="
                    @if($evidencia->probabilidade >= 0.7) prob-high
                    @elseif($evidencia->probabilidade >= 0.5) prob-medium
                    @else prob-low
                    @endif
                ">
                    {{ number_format($evidencia->probabilidade * 100, 1) }}%
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Distribuição das Evidências</h2>
    
    @php
        $porAno = $evidencias->groupBy('ano')->map->count()->sortKeysDesc();
        $porCentro = $evidencias->groupBy('centro')->map->count()->sortDesc();
        $porDimensao = $evidencias->groupBy('dimensao')->map->count()->sortDesc();
    @endphp

    <table>
        <tr>
            <td style="width: 33%; vertical-align: top; padding-right: 10px;">
                <strong>Por Ano:</strong>
                <table style="margin-top: 5px;">
                    <thead>
                        <tr>
                            <th>Ano</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($porAno->take(10) as $ano => $total)
                        <tr>
                            <td>{{ $ano }}</td>
                            <td>{{ $total }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
            <td style="width: 33%; vertical-align: top; padding: 0 5px;">
                <strong>Por Centro:</strong>
                <table style="margin-top: 5px;">
                    <thead>
                        <tr>
                            <th>Centro</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($porCentro->take(10) as $centro => $total)
                        <tr>
                            <td>{{ $centro ?: 'N/A' }}</td>
                            <td>{{ $total }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
            <td style="width: 33%; vertical-align: top; padding-left: 10px;">
                <strong>Por Dimensão:</strong>
                <table style="margin-top: 5px;">
                    <thead>
                        <tr>
                            <th>Dimensão</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($porDimensao as $dimensao => $total)
                        <tr>
                            <td>{{ $dimensao ?: 'N/A' }}</td>
                            <td>{{ $total }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <h2>Recomendações para Submissão THE</h2>
    <div style="background-color: #fff3cd; padding: 10px; border-left: 4px solid #f39c12;">
        <p><strong>Próximos Passos:</strong></p>
        <ol style="margin: 5px 0; padding-left: 20px;">
            <li>Revisar documentos com maior probabilidade (>70%)</li>
            <li>Validar informações com coordenadores de centro</li>
            <li>Preparar narrativa institucional alinhada ao THE</li>
            <li>Traduzir documentos-chave para inglês</li>
            <li>Complementar com dados administrativos quando aplicável</li>
        </ol>
    </div>

    <div class="footer">
        <p>
            <strong>UNIVERSIDADE FEDERAL DE SANTA CATARINA - UFSC</strong><br>
            Documento gerado pelo Sistema de Análise ODS-EGC<br>
            Para uso interno - THE Impact Rankings Preparation
        </p>
    </div>
</body>
</html>
