@extends('layouts.guest')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="header-text">
            <h3>
                <a href="{{ url('the/dashboard') }}">THE Impact Rankings</a> 
                <i class="fa fa-angle-double-right"></i> 
                ODS {{ $numero }} - {{ $nomeOds }}
            </h3>
            <div class="cabecalho">
                <h5 class="mb-0">Evidências Disponíveis</h5>
                <p>Documentos classificados para este ODS</p>
            </div>
        </div>
    </div>

    @if(Auth::user())
        <div class="col-md-12 mb-3">
            @include('layouts/menu-logado')
        </div>
    @endif

    <!-- Estatísticas Gerais -->
    <div class="col-md-12 mb-4">
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="card border-left-primary shadow h-100">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total de Evidências
                        </div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($stats['total'], 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card border-left-success shadow h-100">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Centros Participantes
                        </div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">
                            {{ $stats['por_centro']->count() }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card border-left-info shadow h-100">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Dimensões Cobertas
                        </div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">
                            {{ $stats['por_dimensao']->count() }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card border-left-warning shadow h-100">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Anos Recentes (5 anos)
                        </div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">
                            {{ $stats['por_ano']->sum('total') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="col-md-6 mb-4" style="max-height: 200px;">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Distribuição por Centro</h6>
            </div>
            <div class="card-body">
                <canvas id="chartCentros"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4" style="max-height: 200px;">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Distribuição por Dimensão</h6>
            </div>
            <div class="card-body">
                <canvas id="chartDimensoes"></canvas>
            </div>
        </div>
    </div>

    <!-- Evolução Temporal -->
    <div class="col-md-12 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Evolução Temporal (Últimos 5 Anos)</h6>
            </div>
            <div class="card-body">
                <canvas id="chartTemporal"></canvas>
            </div>
        </div>
    </div>

    <!-- Tabela de Evidências -->
    <div class="col-md-12 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Lista de Evidências</h6>
                <a href="{{ url('the/exportar/'.$numero) }}" class="btn btn-success btn-sm" target="_blank">
                    <i class="fas fa-file-excel"></i> Exportar para Excel
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="tabelaEvidencias">
                        <thead class="thead-light">
                            <tr>
                                <th width="50">Ano</th>
                                <th>Título</th>
                                <th width="120">Autor</th>
                                <th width="80">Centro</th>
                                <th width="100">Dimensão</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($evidencias as $evidencia)
                            <tr>
                                <td class="text-center">{{ $evidencia->ano }}</td>
                                <td>
                                    <strong>{{ $evidencia->titulo }}</strong>
                                    @if($evidencia->resumo)
                                        <br><small class="text-muted">{{ Str::limit($evidencia->resumo, 150) }}</small>
                                    @endif
                                </td>
                                <td>{{ $evidencia->autor ?? 'N/A' }}</td>
                                <td>{{ $evidencia->centro ?? 'N/A' }}</td>
                                <td>{{ $evidencia->dimensao ?? 'N/A' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $evidencias->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary { border-left: 4px solid #4e73df !important; }
.border-left-success { border-left: 4px solid #1cc88a !important; }
.border-left-info { border-left: 4px solid #36b9cc !important; }
.border-left-warning { border-left: 4px solid #f6c23e !important; }
</style>

@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
$(document).ready(function() {
    
    // Gráfico por Centro
    var ctxCentros = document.getElementById('chartCentros').getContext('2d');
    var chartCentros = new Chart(ctxCentros, {
        type: 'bar',
        data: {
            labels: {!! json_encode($stats['por_centro']->pluck('centro')) !!},
            datasets: [{
                label: 'Documentos',
                data: {!! json_encode($stats['por_centro']->pluck('total')) !!},
                backgroundColor: 'rgba(78, 115, 223, 0.5)',
                borderColor: 'rgba(78, 115, 223, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Gráfico por Dimensão
    var ctxDimensoes = document.getElementById('chartDimensoes').getContext('2d');
    var chartDimensoes = new Chart(ctxDimensoes, {
        type: 'pie',
        data: {
            labels: {!! json_encode($stats['por_dimensao']->pluck('dimensao')) !!},
            datasets: [{
                data: {!! json_encode($stats['por_dimensao']->pluck('total')) !!},
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Gráfico Temporal
    var ctxTemporal = document.getElementById('chartTemporal').getContext('2d');
    var chartTemporal = new Chart(ctxTemporal, {
        type: 'line',
        data: {
            labels: {!! json_encode($stats['por_ano']->pluck('ano')) !!},
            datasets: [{
                label: 'Documentos por Ano',
                data: {!! json_encode($stats['por_ano']->pluck('total')) !!},
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                borderColor: 'rgba(28, 200, 138, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
@endsection
