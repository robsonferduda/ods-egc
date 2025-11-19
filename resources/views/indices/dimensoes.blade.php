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
        <div class="card mb-4" style="border-left: 5px solid #6c757d;">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2 text-center">
                        <i class="fa fa-balance-scale" style="font-size: 80px; color: #6c757d;"></i>
                    </div>
                    <div class="col-md-10">
                        <h3 style="color: #6c757d;">{{ $centro->ds_sigla_cen }} - {{ $centro->ds_nome_cen }}</h3>
                        <p class="lead">Análise de Dimensões: IES e ODS</p>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card text-center" style="background: #f8f9fa;">
                                    <div class="card-body">
                                        <h5 class="mb-0" style="color: #6c757d;">
                                            @if(count($dimensoes_ies) > 0)
                                                {{ $dimensoes_ies[0]->nm_dim_ies }}
                                            @else
                                                N/A
                                            @endif
                                        </h5>
                                        <small>Dimensão IES Predominante</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card text-center" style="background: #f8f9fa;">
                                    <div class="card-body">
                                        <h5 class="mb-0" style="color: #6c757d;">
                                            @if(count($dimensoes_ods) > 0)
                                                {{ $dimensoes_ods[0]->nm_dim_ods }}
                                            @else
                                                N/A
                                            @endif
                                        </h5>
                                        <small>Dimensão ODS Predominante</small>
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
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header" style="background: #6c757d; color: white;">
                <h6 class="mb-0"><i class="fa fa-graduation-cap"></i> Dimensões IES</h6>
            </div>
            <div class="card-body">
                <canvas id="chartDimensoesIES" height="250"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header" style="background: #6c757d; color: white;">
                <h6 class="mb-0"><i class="fa fa-globe"></i> Dimensões ODS</h6>
            </div>
            <div class="card-body">
                <canvas id="chartDimensoesODS" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header" style="background: #6c757d; color: white;">
                <h6 class="mb-0"><i class="fa fa-table"></i> Distribuição ODS por Dimensão</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>ODS</th>
                                <th>Objetivo</th>
                                <th>Dimensão ODS</th>
                                <th class="text-right">Total Documentos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $odsAtual = null;
                            @endphp
                            @foreach($ods_por_dimensao as $item)
                                @if($odsAtual != $item->ods)
                                    @php $odsAtual = $item->ods; @endphp
                                    <tr style="background: {{ $item->cor }}22;">
                                        <td rowspan="{{ collect($ods_por_dimensao)->where('ods', $item->ods)->count() }}" style="vertical-align: middle; font-weight: bold;">
                                            <span class="badge badge-pill" style="background: {{ $item->cor }}">{{ $item->ods }}</span>
                                        </td>
                                        <td rowspan="{{ collect($ods_por_dimensao)->where('ods', $item->ods)->count() }}" style="vertical-align: middle;">
                                            {{ $item->objetivo }}
                                        </td>
                                        <td>{{ $item->ds_dimensao }}</td>
                                        <td class="text-right"><strong>{{ $item->total_docs }}</strong></td>
                                    </tr>
                                @else
                                    <tr style="background: {{ $item->cor }}22;">
                                        <td>{{ $item->ds_dimensao }}</td>
                                        <td class="text-right"><strong>{{ $item->total_docs }}</strong></td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header" style="background: #6c757d; color: white;">
                <h6 class="mb-0"><i class="fa fa-info-circle"></i> Sobre as Dimensões</h6>
            </div>
            <div class="card-body">
                <h6 class="mb-2">Dimensões IES (Instituição de Ensino Superior)</h6>
                <ul>
                    <li><strong>Ensino</strong>: Atividades de graduação e formação acadêmica</li>
                    <li><strong>Pesquisa</strong>: Produção científica e desenvolvimento tecnológico</li>
                    <li><strong>Extensão</strong>: Integração com a comunidade e sociedade</li>
                    <li><strong>Gestão</strong>: Administração e governança institucional</li>
                    <li><strong>Ambiental</strong>: Sustentabilidade e meio ambiente no campus</li>
                </ul>

                <h6 class="mb-2 mt-3">Dimensões ODS (Objetivos de Desenvolvimento Sustentável)</h6>
                <ul>
                    <li><strong>Ambiental</strong>: ODS relacionados ao meio ambiente (6, 7, 12, 13, 14, 15)</li>
                    <li><strong>Social</strong>: ODS focados em desenvolvimento social (1, 2, 3, 4, 5, 10)</li>
                    <li><strong>Econômica</strong>: ODS sobre crescimento econômico (8, 9, 11)</li>
                    <li><strong>Institucional</strong>: ODS sobre governança e parcerias (16, 17)</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    // Gráfico Dimensões IES
    const ctxIES = document.getElementById('chartDimensoesIES').getContext('2d');
    const chartIES = new Chart(ctxIES, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(array_column($dimensoes_ies, 'nm_dim_ies')) !!},
            datasets: [{
                data: {!! json_encode(array_column($dimensoes_ies, 'total_docs')) !!},
                backgroundColor: [
                    '#007bff',
                    '#28a745',
                    '#ffc107',
                    '#dc3545',
                    '#6c757d'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.raw / total) * 100).toFixed(1);
                            return context.label + ': ' + context.raw + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });

    // Gráfico Dimensões ODS
    const ctxODS = document.getElementById('chartDimensoesODS').getContext('2d');
    const chartODS = new Chart(ctxODS, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_column($dimensoes_ods, 'nm_dim_ods')) !!},
            datasets: [{
                label: 'Documentos',
                data: {!! json_encode(array_column($dimensoes_ods, 'total_docs')) !!},
                backgroundColor: [
                    '#28a745',
                    '#007bff',
                    '#ffc107',
                    '#dc3545'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Número de Documentos'
                    }
                }
            }
        }
    });
</script>
@endsection