@extends('layouts.guest')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="header-text">
            <h3>
                <a href="{{ url('the/dashboard') }}">THE Impact Rankings</a> 
                <i class="fa fa-angle-double-right"></i> 
                Matriz de Alinhamento
            </h3>
            <div class="cabecalho">
                <h5 class="mb-0">Dimensões IES × ODSs THE</h5>
                <p>Mapeamento da contribuição de cada dimensão para os ODSs</p>
            </div>
        </div>
    </div>

    @if(Auth::user())
        <div class="col-md-12 mb-3">
            @include('layouts/menu-logado')
        </div>
    @endif

    <!-- Matriz -->
    <div class="col-md-12 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-table"></i> Matriz de Alinhamento
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm text-center" style="font-size: 12px;">
                        <thead class="thead-dark">
                            <tr>
                                <th width="150" class="align-middle">Dimensão</th>
                                @for($i = 1; $i <= 17; $i++)
                                    <th width="50" class="align-middle">
                                        <img src="{{ asset('img/ods-icone/ods_'.$i.'.png') }}" 
                                             alt="ODS {{ $i }}" 
                                             style="width: 35px; height: 35px;"
                                             title="ODS {{ $i }}">
                                    </th>
                                @endfor
                                <th width="80" class="align-middle">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totaisPorOds = array_fill(1, 17, 0);
                            @endphp
                            
                            @foreach($matriz as $linha)
                                <tr>
                                    <td class="text-left font-weight-bold">{{ $linha['dimensao'] }}</td>
                                    @php
                                        $totalLinha = 0;
                                    @endphp
                                    @for($i = 1; $i <= 17; $i++)
                                        @php
                                            $valor = $linha["ods_{$i}"] ?? 0;
                                            $totalLinha += $valor;
                                            $totaisPorOds[$i] += $valor;
                                            
                                            // Definir cor baseada no valor
                                            if ($valor == 0) {
                                                $cor = 'background-color: #f8f9fa;';
                                                $texto = '';
                                            } elseif ($valor < 10) {
                                                $cor = 'background-color: #fff3cd;';
                                                $texto = $valor;
                                            } elseif ($valor < 50) {
                                                $cor = 'background-color: #d1ecf1;';
                                                $texto = '<strong>'.$valor.'</strong>';
                                            } elseif ($valor < 100) {
                                                $cor = 'background-color: #d4edda;';
                                                $texto = '<strong>'.$valor.'</strong>';
                                            } else {
                                                $cor = 'background-color: #c3e6cb;';
                                                $texto = '<strong>'.$valor.'</strong>';
                                            }
                                        @endphp
                                        <td style="{{ $cor }}">{!! $texto !!}</td>
                                    @endfor
                                    <td class="font-weight-bold bg-light">{{ $totalLinha }}</td>
                                </tr>
                            @endforeach
                            
                            <!-- Linha de totais -->
                            <tr class="table-dark font-weight-bold">
                                <td>TOTAL</td>
                                @for($i = 1; $i <= 17; $i++)
                                    <td>{{ $totaisPorOds[$i] }}</td>
                                @endfor
                                <td>{{ array_sum($totaisPorOds) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Legenda -->
                <div class="mt-3">
                    <h6 class="font-weight-bold">Legenda:</h6>
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <div class="d-flex align-items-center">
                                <div style="width: 30px; height: 20px; background-color: #f8f9fa; border: 1px solid #dee2e6; margin-right: 10px;"></div>
                                <span>Sem evidências</span>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="d-flex align-items-center">
                                <div style="width: 30px; height: 20px; background-color: #fff3cd; border: 1px solid #dee2e6; margin-right: 10px;"></div>
                                <span>Fraco (1-9)</span>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="d-flex align-items-center">
                                <div style="width: 30px; height: 20px; background-color: #d1ecf1; border: 1px solid #dee2e6; margin-right: 10px;"></div>
                                <span>Médio (10-49)</span>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="d-flex align-items-center">
                                <div style="width: 30px; height: 20px; background-color: #d4edda; border: 1px solid #dee2e6; margin-right: 10px;"></div>
                                <span>Forte (50-99)</span>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="d-flex align-items-center">
                                <div style="width: 30px; height: 20px; background-color: #c3e6cb; border: 1px solid #dee2e6; margin-right: 10px;"></div>
                                <span>Muito Forte (100+)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Análise por Dimensão -->
    <div class="col-md-12 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-bar"></i> Contribuição por Dimensão
                </h6>
            </div>
            <div class="card-body">
                <canvas id="chartDimensoes" height="80"></canvas>
            </div>
        </div>
    </div>

    <!-- Análise por ODS -->
    <div class="col-md-12 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-line"></i> Distribuição por ODS
                </h6>
            </div>
            <div class="card-body">
                <canvas id="chartOds" height="80"></canvas>
            </div>
        </div>
    </div>

    <!-- Insights -->
    <div class="col-md-12 mb-4">
        <div class="card shadow border-left-info">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">
                    <i class="fas fa-lightbulb"></i> Insights Estratégicos
                </h6>
            </div>
            <div class="card-body">
                @php
                    // Identificar dimensão mais produtiva
                    $dimensaoMaisForte = collect($matriz)->sortByDesc(function($linha) {
                        $total = 0;
                        for($i = 1; $i <= 17; $i++) {
                            $total += $linha["ods_{$i}"] ?? 0;
                        }
                        return $total;
                    })->first();
                    
                    // Identificar ODS mais coberto
                    $odsMaisCoberto = array_keys($totaisPorOds, max($totaisPorOds))[0] ?? 1;
                    
                    // Identificar gaps
                    $odsComGaps = array_filter($totaisPorOds, function($total) {
                        return $total < 20;
                    });
                @endphp

                <div class="row">
                    <div class="col-md-4">
                        <div class="alert alert-success">
                            <h6 class="alert-heading">
                                <i class="fas fa-trophy"></i> Dimensão Mais Forte
                            </h6>
                            <p class="mb-0">
                                <strong>{{ $dimensaoMaisForte['dimensao'] }}</strong> é a dimensão com maior contribuição para os ODSs.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="alert alert-primary">
                            <h6 class="alert-heading">
                                <i class="fas fa-bullseye"></i> ODS Mais Coberto
                            </h6>
                            <p class="mb-0">
                                <strong>ODS {{ $odsMaisCoberto }}</strong> possui a maior cobertura entre todas as dimensões 
                                ({{ $totaisPorOds[$odsMaisCoberto] }} documentos).
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="alert alert-warning">
                            <h6 class="alert-heading">
                                <i class="fas fa-exclamation-triangle"></i> ODSs com Gaps
                            </h6>
                            <p class="mb-0">
                                <strong>{{ count($odsComGaps) }} ODSs</strong> possuem menos de 20 documentos e necessitam atenção.
                            </p>
                        </div>
                    </div>
                </div>

                <hr>

                <h6 class="font-weight-bold text-dark mb-3">Recomendações Baseadas na Matriz:</h6>
                <ul class="mb-0">
                    <li class="mb-2">
                        <strong>Aproveitar Pontos Fortes:</strong> Focar submissão THE nos ODSs com mais de 50 documentos
                    </li>
                    <li class="mb-2">
                        <strong>Diversificar Dimensões:</strong> Incentivar dimensões menos produtivas a contribuírem
                    </li>
                    <li class="mb-2">
                        <strong>Preencher Gaps:</strong> Priorizar mapeamento de evidências nos {{ count($odsComGaps) }} ODSs fracos
                    </li>
                    <li class="mb-0">
                        <strong>Integração Transversal:</strong> Identificar projetos que contribuem para múltiplos ODSs
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-info { border-left: 4px solid #36b9cc !important; }
</style>

@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
$(document).ready(function() {
    // Preparar dados para gráfico de dimensões
    var dimensoes = {!! json_encode(array_column($matriz, 'dimensao')) !!};
    var totaisDimensoes = [];
    
    @foreach($matriz as $linha)
        var total = 0;
        @for($i = 1; $i <= 17; $i++)
            total += {{ $linha["ods_{$i}"] ?? 0 }};
        @endfor
        totaisDimensoes.push(total);
    @endforeach

    // Gráfico por Dimensão
    var ctxDimensoes = document.getElementById('chartDimensoes').getContext('2d');
    new Chart(ctxDimensoes, {
        type: 'bar',
        data: {
            labels: dimensoes,
            datasets: [{
                label: 'Total de Documentos',
                data: totaisDimensoes,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
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
            }
        }
    });

    // Gráfico por ODS
    var totaisOds = {!! json_encode(array_values($totaisPorOds)) !!};
    var labelsOds = [];
    for(var i = 1; i <= 17; i++) {
        labelsOds.push('ODS ' + i);
    }

    var ctxOds = document.getElementById('chartOds').getContext('2d');
    new Chart(ctxOds, {
        type: 'line',
        data: {
            labels: labelsOds,
            datasets: [{
                label: 'Total de Documentos',
                data: totaisOds,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
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
