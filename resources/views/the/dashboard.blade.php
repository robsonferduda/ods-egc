@extends('layouts.guest')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="header-text">
            <h3>
                {{ config('app.name') }} <i class="fa fa-angle-double-right"></i> THE Impact Rankings
                @include('layouts.nivel')
            </h3>
            <div class="cabecalho">
                <h5 class="mb-0">Preparação para THE Impact Rankings</h5>
                <p>Análise de prontidão institucional para submissão ao ranking</p>
            </div>
        </div>
    </div>

    @if(Auth::user())
        <div class="col-md-12 mb-3">
            @include('layouts/menu-logado')
        </div>
    @endif

    <!-- Cards de Resumo -->
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Evidências Mapeadas
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalEvidencias, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Centros Engajados
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $centrosEngajados }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-university fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Docentes Ativos
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($docentesAtivos, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumo de Status -->
    <div class="col-md-12 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Distribuição por Nível de Prontidão</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="border-left-success p-3">
                            <h2 class="text-success">{{ $estatisticas['forte'] }}</h2>
                            <p class="mb-0"><strong>🟢 FORTE</strong><br>Prontos para submeter</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border-left-warning p-3">
                            <h2 class="text-warning">{{ $estatisticas['medio'] }}</h2>
                            <p class="mb-0"><strong>🟡 MÉDIO</strong><br>Reforçar evidências</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border-left-danger p-3">
                            <h2 class="text-danger">{{ $estatisticas['fraco'] }}</h2>
                            <p class="mb-0"><strong>🔴 FRACO</strong><br>Buscar evidências</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela de Status por ODS -->
    <div class="col-md-12 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Status de Preparação por ODS</h6>
                <div>
                    <a href="{{ url('the/gaps') }}" class="btn btn-sm btn-warning">
                        <i class="fas fa-exclamation-triangle"></i> Análise de Gaps
                    </a>
                    <a href="{{ url('the/matriz') }}" class="btn btn-sm btn-info">
                        <i class="fas fa-table"></i> Matriz de Alinhamento
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="tabelaOds">
                        <thead class="thead-light">
                            <tr>
                                <th width="60">ODS</th>
                                <th>Nome</th>
                                <th width="120" class="text-center">Evidências</th>
                                <th width="150" class="text-center">Cobertura</th>
                                <th width="100" class="text-center">Status</th>
                                <th width="120" class="text-center">Ação</th>
                                <th width="100" class="text-center">Prioridade</th>
                                <th width="100" class="text-center">Detalhes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($statusOds as $ods)
                            <tr class="ods-row" data-nivel="{{ $ods['nivel'] }}">
                                <td class="text-center">
                                    <img src="{{ asset('img/ods-icone/ods_'.$ods['numero'].'.png') }}" 
                                         alt="ODS {{ $ods['numero'] }}" 
                                         style="width: 40px; height: 40px;">
                                </td>
                                <td><strong>{{ $ods['nome'] }}</strong></td>
                                <td class="text-center">
                                    <span class="badge badge-secondary badge-pill" style="font-size: 14px;">
                                        {{ $ods['evidencias'] }}
                                    </span>
                                </td>
                                <td>
                                    <div class="progress" style="height: 25px;">
                                        <div class="progress-bar 
                                            @if($ods['nivel'] == 'forte') bg-success
                                            @elseif($ods['nivel'] == 'medio') bg-warning
                                            @else bg-danger
                                            @endif" 
                                            role="progressbar" 
                                            style="width: {{ $ods['cobertura'] }}%">
                                            {{ $ods['cobertura'] }}%
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($ods['nivel'] == 'forte')
                                        <span class="badge badge-success">🟢 FORTE</span>
                                    @elseif($ods['nivel'] == 'medio')
                                        <span class="badge badge-warning">🟡 MÉDIO</span>
                                    @else
                                        <span class="badge badge-danger">🔴 FRACO</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <strong>{{ $ods['acao'] }}</strong>
                                </td>
                                <td class="text-center">
                                    @if($ods['prioridade'] == 'alta')
                                        <span class="badge badge-danger">Alta</span>
                                    @elseif($ods['prioridade'] == 'media')
                                        <span class="badge badge-warning">Média</span>
                                    @else
                                        <span class="badge badge-secondary">Baixa</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ url('the/ods/'.$ods['numero'].'/evidencias') }}" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-search"></i> Ver
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

    <!-- Próximas Ações -->
    <div class="col-md-12 mb-4">
        <div class="card shadow border-left-warning">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-warning">
                    <i class="fas fa-tasks"></i> Próximas Ações Recomendadas
                </h6>
            </div>
            <div class="card-body">
                <ol class="mb-0">
                    @php
                        $odsFortes = collect($statusOds)->where('nivel', 'forte')->take(3);
                        $odsFracos = collect($statusOds)->where('nivel', 'fraco')->take(3);
                    @endphp
                    
                    @if($odsFortes->count() > 0)
                        <li class="mb-2">
                            <strong>Priorizar submissão dos ODSs fortes:</strong> 
                            {{ $odsFortes->pluck('numero')->implode(', ') }}
                        </li>
                    @endif
                    
                    @if($odsFracos->count() > 0)
                        <li class="mb-2">
                            <strong>Buscar evidências para ODSs fracos:</strong> 
                            {{ $odsFracos->pluck('numero')->implode(', ') }}
                        </li>
                    @endif
                    
                    <li class="mb-2">
                        <strong>Revisar</strong> <a href="{{ url('the/gaps') }}">análise de gaps</a> 
                        para identificar departamentos a contactar
                    </li>
                    
                    <li class="mb-0">
                        <strong>Exportar evidências</strong> dos ODSs prontos para preparar submissão
                    </li>
                </ol>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary { border-left: 4px solid #4e73df !important; }
.border-left-success { border-left: 4px solid #1cc88a !important; }
.border-left-info { border-left: 4px solid #36b9cc !important; }
.border-left-warning { border-left: 4px solid #f6c23e !important; }
.border-left-danger { border-left: 4px solid #e74a3b !important; }

.ods-row:hover {
    background-color: #f8f9fc;
    cursor: pointer;
}
</style>

@endsection

@section('script')
<script>
$(document).ready(function() {
    $('#tabelaOds').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json"
        },
        "order": [[2, "desc"]], // Ordenar por evidências
        "pageLength": 17,
        "paging": false,
        "info": false
    });
});
</script>
@endsection
