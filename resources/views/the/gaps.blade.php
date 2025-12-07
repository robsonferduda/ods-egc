@extends('layouts.guest')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="header-text">
            <h3>
                <a href="{{ url('the/dashboard') }}">THE Impact Rankings</a> 
                <i class="fa fa-angle-double-right"></i> 
                Análise de Gaps
            </h3>
            <div class="cabecalho">
                <h5 class="mb-0">Gaps e Oportunidades de Melhoria</h5>
                <p>Identificação de áreas que necessitam atenção para fortalecimento da submissão</p>
            </div>
        </div>
    </div>

    @if(Auth::user())
        <div class="col-md-12 mb-3">
            @include('layouts/menu-logado')
        </div>
    @endif

    <!-- Recomendações Gerais -->
    <div class="col-md-12 mb-4">
        <div class="card shadow border-left-info">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">
                    <i class="fas fa-lightbulb"></i> Recomendações Estratégicas
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold text-primary">Estratégia de Curto Prazo (3 meses)</h6>
                        <ol>
                            <li class="mb-2">
                                <strong>Submeter ODSs Fortes</strong> - Focar nos {{ $fortes->count() }} ODSs com evidências robustas
                            </li>
                            <li class="mb-2">
                                <strong>Mapear Gaps Críticos</strong> - Contactar departamentos para os {{ $criticos->count() }} ODSs fracos
                            </li>
                            <li class="mb-2">
                                <strong>Validar Dados</strong> - Confirmar informações com gestores institucionais
                            </li>
                            <li class="mb-0">
                                <strong>Preparar Traduções</strong> - Documentos chave em inglês para THE
                            </li>
                        </ol>
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold text-success">Estratégia de Médio Prazo (6-12 meses)</h6>
                        <ol>
                            <li class="mb-2">
                                <strong>Reforçar ODSs Médios</strong> - Ampliar evidências dos {{ $atencao->count() }} ODSs de atenção
                            </li>
                            <li class="mb-2">
                                <strong>Criar Indicadores</strong> - Desenvolver métricas alinhadas ao THE
                            </li>
                            <li class="mb-2">
                                <strong>Engajar Centros</strong> - Workshops sobre THE Impact Rankings
                            </li>
                            <li class="mb-0">
                                <strong>Monitoramento Contínuo</strong> - Sistema de atualização de evidências
                            </li>
                        </ol>
                    </div>
                </div>

                <hr class="my-4">

                <div class="alert alert-warning mb-0">
                    <h6 class="alert-heading">
                        <i class="fas fa-exclamation-triangle"></i> Atenção Especial
                    </h6>
                    <p class="mb-2">
                        <strong>ODSs Priorizados pelo THE Impact Rankings 2025:</strong>
                    </p>
                    <ul class="mb-0">
                        <li>ODS 3 (Saúde), 4 (Educação), 5 (Gênero) - Obrigatórios</li>
                        <li>ODS 8 (Trabalho), 11 (Cidades), 13 (Clima), 17 (Parcerias) - Recomendados</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- ODSs Críticos -->
    @if($criticos->count() > 0)
    <div class="col-md-12 mb-4">
        <div class="card shadow border-left-danger">
            <div class="card-header py-3 bg-danger text-white">
                <h6 class="m-0 font-weight-bold">
                    🔴 CRÍTICOS - Ação Imediata Necessária ({{ $criticos->count() }} ODSs)
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th width="80">ODS</th>
                                <th>Nome</th>
                                <th width="100" class="text-center">Evidências</th>
                                <th width="150">Contatos Sugeridos</th>
                                <th>Ação Recomendada</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($criticos as $ods)
                            <tr>
                                <td class="text-center">
                                    <img src="{{ asset('img/ods-icone/ods_'.$ods['numero'].'.png') }}" 
                                         alt="ODS {{ $ods['numero'] }}" 
                                         style="width: 50px;">
                                </td>
                                <td><strong>{{ $ods['nome'] }}</strong></td>
                                <td class="text-center">
                                    <span class="badge badge-danger badge-pill" style="font-size: 14px;">
                                        {{ $ods['evidencias'] }}
                                    </span>
                                </td>
                                <td>
                                    @if(isset($sugestoes[$ods['numero']]))
                                        <strong>{{ $sugestoes[$ods['numero']]['contato'] }}</strong>
                                    @else
                                        <em class="text-muted">A definir</em>
                                    @endif
                                </td>
                                <td>
                                    @if(isset($sugestoes[$ods['numero']]))
                                        {{ $sugestoes[$ods['numero']]['acao'] }}
                                    @else
                                        <em class="text-muted">Mapear ações institucionais</em>
                                    @endif
                                    <br>
                                    <a href="{{ url('the/ods/'.$ods['numero'].'/evidencias') }}" class="btn btn-sm btn-outline-primary mt-2">
                                        <i class="fas fa-search"></i> Ver Evidências Atuais
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- ODSs de Atenção -->
    @if($atencao->count() > 0)
    <div class="col-md-12 mb-4">
        <div class="card shadow border-left-warning">
            <div class="card-header py-3 bg-warning text-white">
                <h6 class="m-0 font-weight-bold">
                    🟡 ATENÇÃO - Reforçar Evidências ({{ $atencao->count() }} ODSs)
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th width="80">ODS</th>
                                <th>Nome</th>
                                <th width="100" class="text-center">Evidências</th>
                                <th width="150">Contatos Sugeridos</th>
                                <th>Ação Recomendada</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($atencao as $ods)
                            <tr>
                                <td class="text-center">
                                    <img src="{{ asset('img/ods-icone/ods_'.$ods['numero'].'.png') }}" 
                                         alt="ODS {{ $ods['numero'] }}" 
                                         style="width: 50px;">
                                </td>
                                <td><strong>{{ $ods['nome'] }}</strong></td>
                                <td class="text-center">
                                    <span class="badge badge-warning badge-pill" style="font-size: 14px;">
                                        {{ $ods['evidencias'] }}
                                    </span>
                                </td>
                                <td>
                                    @if(isset($sugestoes[$ods['numero']]))
                                        <strong>{{ $sugestoes[$ods['numero']]['contato'] }}</strong>
                                    @else
                                        <em class="text-muted">A definir</em>
                                    @endif
                                </td>
                                <td>
                                    @if(isset($sugestoes[$ods['numero']]))
                                        {{ $sugestoes[$ods['numero']]['acao'] }}
                                    @else
                                        <em class="text-muted">Ampliar documentação existente</em>
                                    @endif
                                    <br>
                                    <a href="{{ url('the/ods/'.$ods['numero'].'/evidencias') }}" class="btn btn-sm btn-outline-primary mt-2">
                                        <i class="fas fa-search"></i> Ver Evidências Atuais
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- ODSs Fortes -->
    @if($fortes->count() > 0)
    <div class="col-md-12 mb-4">
        <div class="card shadow border-left-success">
            <div class="card-header py-3 bg-success text-white">
                <h6 class="m-0 font-weight-bold">
                    🟢 FORTE - Priorizar para Submissão ({{ $fortes->count() }} ODSs)
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th width="80">ODS</th>
                                <th>Nome</th>
                                <th width="100" class="text-center">Evidências</th>
                                <th width="150">Status</th>
                                <th>Ação Recomendada</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fortes as $ods)
                            <tr>
                                <td class="text-center">
                                    <img src="{{ asset('img/ods-icone/ods_'.$ods['numero'].'.png') }}" 
                                         alt="ODS {{ $ods['numero'] }}" 
                                         style="width: 50px;">
                                </td>
                                <td><strong>{{ $ods['nome'] }}</strong></td>
                                <td class="text-center">
                                    <span class="badge badge-success badge-pill" style="font-size: 14px;">
                                        {{ $ods['evidencias'] }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-success">✓ Evidências Robustas</span>
                                    @if($ods['prioridade'] == 'alta')
                                        <br><span class="badge badge-danger mt-1">Prioridade Alta THE</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>Preparar para submissão imediata</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>Revisar documentos principais</li>
                                        <li>Preparar narrativa institucional</li>
                                        <li>Validar dados com gestores</li>
                                    </ul>
                                    <a href="{{ url('the/ods/'.$ods['numero'].'/evidencias') }}" class="btn btn-sm btn-success mt-2">
                                        <i class="fas fa-file-export"></i> Exportar Evidências
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    
</div>

<style>
.border-left-danger { border-left: 4px solid #e74a3b !important; }
.border-left-warning { border-left: 4px solid #f6c23e !important; }
.border-left-success { border-left: 4px solid #1cc88a !important; }
.border-left-info { border-left: 4px solid #36b9cc !important; }
</style>

@endsection
