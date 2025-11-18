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
        <div class="card mb-4" style="border-left: 5px solid {{ $ods->cor }};">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2 text-center">
                        <img src="https://ai4sdg-gov.org/img/ods-icone/ods_{{ $ods->cod }}.png" 
                             class="img-fluid" 
                             alt="ODS {{ $ods->cod }}" 
                             style="max-width: 150px;">
                    </div>
                    <div class="col-md-10">
                        <h3 style="color: {{ $ods->cor }};">ODS {{ $ods->cod }}: {{ $ods->objetivo }}</h3>
                        <p class="lead">{{ $ods->descricao }}</p>
                        <hr>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card text-center" style="background: #f8f9fa;">
                                    <div class="card-body">
                                        <h2 class="mb-0" style="color: {{ $ods->cor }};">
                                            {{ $total_documentos[0]->total_documentos ?? 0 }}
                                        </h2>
                                        <small>Documentos (últimos 5 anos)</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-center" style="background: #f8f9fa;">
                                    <div class="card-body">
                                        <h2 class="mb-0" style="color: {{ $ods->cor }};">
                                            {{ count($dimensoes) }}
                                        </h2>
                                        <small>Dimensões IES ativas</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-center" style="background: #f8f9fa;">
                                    <div class="card-body">
                                        <h2 class="mb-0" style="color: {{ $ods->cor }};">
                                            {{ count($evolucao) }}
                                        </h2>
                                        <small>Anos com registros</small>
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
            <div class="card-header" style="background: {{ $ods->cor }}; color: white;">
                <h5 class="mb-0"><i class="fa fa-line-chart"></i> Evolução Anual</h5>
            </div>
            <div class="card-body">
                <canvas id="chartEvolucao" height="100"></canvas>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header" style="background: {{ $ods->cor }}; color: white;">
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
                                </p>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center">Nenhum documento encontrado para esta ODS.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header" style="background: {{ $ods->cor }}; color: white;">
                <h5 class="mb-0"><i class="fa fa-pie-chart"></i> Distribuição por Dimensão</h5>
            </div>
            <div class="card-body">
                <canvas id="chartDimensao" height="250"></canvas>
            </div>
        </div>

        <div class="card">
            <div class="card-header" style="background: {{ $ods->cor }}; color: white;">
                <h5 class="mb-0"><i class="fa fa-list"></i> Dimensões IES</h5>
            </div>
            <div class="card-body">
                @if(count($dimensoes) > 0)
                    <ul class="list-group">
                        @foreach($dimensoes as $dim)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $dim->nome }}
                                <span class="badge badge-primary badge-pill">{{ $dim->total }}</span>
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
    // Gráfico de Evolução Anual
    const ctxEvolucao = document.getElementById('chartEvolucao').getContext('2d');
    const chartEvolucao = new Chart(ctxEvolucao, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($evolucao, 'ano')) !!},
            datasets: [{
                label: 'Documentos por Ano',
                data: {!! json_encode(array_column($evolucao, 'total')) !!},
                backgroundColor: 'rgba({{ hexdec(substr($ods->cor, 1, 2)) }}, {{ hexdec(substr($ods->cor, 3, 2)) }}, {{ hexdec(substr($ods->cor, 5, 2)) }}, 0.2)',
                borderColor: '{{ $ods->cor }}',
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

    // Gráfico de Dimensões
    const ctxDimensao = document.getElementById('chartDimensao').getContext('2d');
    const chartDimensao = new Chart(ctxDimensao, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(array_column($dimensoes, 'nome')) !!},
            datasets: [{
                data: {!! json_encode(array_column($dimensoes, 'total')) !!},
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF',
                    '#FF9F40'
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
</script>
@endsection
