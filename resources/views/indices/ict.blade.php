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
            <p class="mb-1"><strong>Índice de Colaboração Temática nos ODS (ICT-ODS)</strong></p>    
        </div>
        <div class="col-md-12 col-sm-6">
            <p>
                O ídice foi desenvolvido com o propósito de mensurar a diversidade temática
                das contribuições de unidades acadêmicas (como centros, departamentos ou
                programas de pós-graduação), ou mesmo de docentes individualmente, em relação
                aos ODS. Enquanto o IVC-ODS mede a intensidade temporal da produção
                relacionada aos ODS, o ICT-ODS visa captar sua abrangência temática, refletindo a
                capacidade da unidade ou ator institucional em atuar de forma transversal nos
                diferentes ODS.
            </p>  
        </div>
        <div class="col-md-12 col-sm-6 center">
            <img src="{{ asset('img/ict.png') }}" class="img-fluid mt-1 w-20" style="width: 30%;" alt="Índice de Colaboração Temática nos ODS (ICT-ODS)">
        </div>
    </div>  
</div>
@endsection