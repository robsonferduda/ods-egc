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
            <p class="mb-1"><strong>Índice de Crescimento Sustentável (ICS) </strong></p>  
            <p></p>  
        </div>
        <div class="col-md-12 col-sm-12">
            <h6>{{ $ics[0]->nome_centro }}/{{ $ics[0]->sigla_centro }}</h6>
            <p>Valores do ICS por Ano</p>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Ano</th>
                        <th>Documentos por Ano</th>
                        <th>Documentos Atuais</th>
                        <th>Documentos Anteriores</th>
                        <th>ICS</th>
                        <th>Siatuação</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ics as $item)
                    <tr>
                        <td>{{ $item->ano }}</td>
                        <td>{{ $item->docs_ano }}</td>
                        <td>{{ $item->docs_janela_atual_3a }}</td>
                        <td>{{ $item->docs_janela_prev_3a }}</td>
                        <td>{{ number_format($item->ics_norm_0_100, 4, ',', '.') }}</td>
                        <td>
                            @if($item->ics_norm_0_100 > 50) 
                                @php $nivel = 'Crescimento'; @endphp
                            @else if($item->ics_norm_0_100 == 50) 
                                @php $nivel = 'Estável'; @endphp
                            @else if($item->ics_norm_0_100 < 50) 
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