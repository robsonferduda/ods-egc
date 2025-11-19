@extends('layouts.guest')

@section('content')
<div class="row mt-3">
    <div class="col-md-12">
        <a href="{{ url('/') }}" class="btn btn-sm btn-secondary mb-3">
            <i class="fa fa-arrow-left"></i> Voltar ao Dashboard
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4" style="border-left: 5px solid {{ $cor_predominante }};">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2 text-center">
                        <i class="fa fa-building" style="font-size: 80px; color: {{ $cor_predominante }};"></i>
                    </div>
                    <div class="col-md-10">
                        <h3 style="color: {{ $cor_predominante }};">{{ $centro->ds_sigla_cen }} - {{ $centro->ds_nome_cen }}</h3>
                        <p class="lead">Panorama geral de produção acadêmica e engajamento com os ODS</p>
                        <hr>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card text-center" style="background: #f8f9fa;">
                                    <div class="card-body">
                                        <h2 class="mb-0" style="color: {{ $cor_predominante }};">
                                            {{ $total_documentos[0]->total_documentos ?? 0 }}
                                        </h2>
                                        <small>Documentos (últimos 5 anos)</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-center" style="background: #f8f9fa;">
                                    <div class="card-body">
                                        <h2 class="mb-0" style="color: {{ $cor_predominante }};">
                                            {{ count($ods_distribuicao) }}
                                        </h2>
                                        <small>ODS identificados</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-center" style="background: #f8f9fa;">
                                    <div class="card-body">
                                        <h2 class="mb-0" style="color: {{ $cor_predominante }};">
                                            {{ count($dimensoes) }}
                                        </h2>
                                        <small>Dimensões IES ativas</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header" style="background: {{ $cor_predominante }}; color: white;">
                <h5 class="mb-0"><i class="fa fa-line-chart"></i> Evolução Anual</h5>
            </div>
            <div class="card-body">
                <canvas id="chartEvolucao" height="100"></canvas>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header" style="background: {{ $cor_predominante }}; color: white;">
                <h5 class="mb-0"><i class="fa fa-file-text"></i> Documentos Recentes</h5>
            </div>
            <div class="card-body">
                @if(count($documentos_recentes) > 0)
                    <div class="list-group">
                        @foreach($documentos_recentes as $doc)
                            <a href="{{ url('documentos/dimensao/'.$doc->id.'/detalhes/'.$doc->id) }}" 
                               class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $doc->titulo }}</h6>
                                    <small class="text-muted">{{ $doc->ano }}</small>
                                </div>
                                <p class="mb-1">
                                    <span class="badge badge-info">{{ $doc->dimensao }}</span>
                                    <span class="badge badge-secondary">{{ $doc->tipo }}</span>
                                    <span class="badge badge-success">ODS {{ $doc->ods }}</span>
                                </p>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center">Nenhum documento encontrado para este centro.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header" style="background: {{ $cor_predominante }}; color: white;">
                <h5 class="mb-0"><i class="fa fa-pie-chart"></i> Top 10 ODS</h5>
            </div>
            <div class="card-body">
                <canvas id="chartODS" height="250"></canvas>
            </div>
        </div>

        <div class="card">
            <div class="card-header" style="background: {{ $cor_predominante }}; color: white;">
                <h5 class="mb-0"><i class="fa fa-list"></i> Dimensões IES</h5>
            </div>
            <div class="card-body">
                @if(count($dimensoes) > 0)
                    <ul class="list-group">
                        @foreach($dimensoes as $dim)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $dim->nome }}
                                <span class="badge badge-pill" style="background: {{ $cor_predominante }};">{{ $dim->total }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted text-center">Nenhuma dimensão registrada.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    const corPredominante = '{{ $cor_predominante }}';
    
    // Gráfico de Evolução Anual
    const ctxEvolucao = document.getElementById('chartEvolucao').getContext('2d');
    const chartEvolucao = new Chart(ctxEvolucao, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($evolucao, 'ano')) !!},
            datasets: [{
                label: 'Documentos por Ano',
                data: {!! json_encode(array_column($evolucao, 'total')) !!},
                backgroundColor: corPredominante + '33',
                borderColor: corPredominante,
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Gráfico de ODS
    const ctxODS = document.getElementById('chartODS').getContext('2d');
    const chartODS = new Chart(ctxODS, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_map(function($item) { return 'ODS ' . $item->ods; }, $ods_distribuicao)) !!},
            datasets: [{
                label: 'Documentos',
                data: {!! json_encode(array_column($ods_distribuicao, 'total')) !!},
                backgroundColor: {!! json_encode(array_column($ods_distribuicao, 'cor')) !!}
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endsection
