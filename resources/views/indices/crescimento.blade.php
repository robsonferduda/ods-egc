@extends('layouts.guest')
@section('content')
<div class="row">
    <div class="row mb-5 mt-3">
        <div class="col-md-2 col-sm-2">
            <img src="{{ asset('img/logo-egc.png') }}" class="img-fluid mt-1" alt="Responsive image">
        </div>
        <div class="col-md-10 col-sm-10">
            <h6 class="mb-0">Universidade Federal de Santa catarina (UFSC)</h6>
            <h6 class="mb-0 mt-1">Programa de Pós-graduação em Engenharia, Gestão e Mídia do Conhecimento (PPGEGC)</h6>
            <h6 class="mb-0 mt-1">Engenharia do Conhecimento/Teoria e prática em Engenharia do Conhecimento</h6>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <p class="mb-1 center"><strong>Índice de Crescimento Sustentável (ICS) </strong></p>              
        </div>
        <div class="col-md-12 col-sm-6">
            <p>O Índice de Crescimento Sustentável (ICS) foi desenvolvido com o propósito de mensurar a tendência evolutiva da produção institucional vinculada aos Objetivos de Desenvolvimento Sustentável (ODS), em especial quando observada sob a ótica dos centros universitários. Trata-se de um indicador de natureza longitudinal, fundamentado na comparação entre janelas móveis de tempo, buscando identificar padrões de crescimento, estagnação ou retração ao longo de séries históricas.</p>  
        </div>
        <div class="col-md-12 col-sm-6 center">
            <img src="{{ asset('img/ics.png') }}" class="img-fluid mt-1 w-20" style="width: 30%;" alt="Índice de Crescimento Sustentável (ICS)">
        </div>
        <div class="col-md-12 col-sm-12">
            <h6>CENTRO {{ $ics[0]->nome_centro }}/{{ $ics[0]->sigla_centro }}</h6>
            <p class="mb-1">Valores do ICS por Ano</p>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class="center">Ano</th>
                        <th class="center">Documentos por Ano</th>
                        <th class="center">Documentos Atuais</th>
                        <th class="center">Documentos Anteriores</th>
                        <th class="center">ICS</th>
                        <th class="center">Situação</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ics as $item)
                    <tr>
                        <td class="center">{{ $item->ano }}</td>
                        <td class="center">{{ $item->docs_ano }}</td>
                        <td class="center">{{ $item->docs_janela_atual_3a }}</td>
                        <td class="center">{{ $item->docs_janela_prev_3a }}</td>
                        <td class="center">{{ number_format($item->ics_norm_0_100, 2, ',', '.') }}</td>
                        <td class="center">
                            @if($item->ics_norm_0_100 > 50) 
                                @php $nivel = 'Crescimento'; @endphp
                            @elseif($item->ics_norm_0_100 == 50) 
                                @php $nivel = 'Estável'; @endphp
                            @elseif($item->ics_norm_0_100 < 50) 
                                @php $nivel = 'Queda'; @endphp
                            @endif

                            @if($nivel == 'Crescimento')
                                <span class="badge badge-success">{{ $nivel }}</span>
                            @elseif($nivel == 'Estável')
                                <span class="badge badge-warning">{{ $nivel }}</span>
                            @else
                                <span class="badge badge-danger">{{ $nivel }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>  
</div>
@endsection