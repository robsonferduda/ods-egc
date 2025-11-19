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
            <p class="mb-1"><strong>Índice de Variação de Contribuição com ODS (IVC-ODS)</strong></p>    
        </div>
        <div class="col-md-12 col-sm-6">
            <p>
                O índice foi desenvolvido com o objetivo de mensurar a evolução anual da
                produção institucional relacionada aos ODS, considerando somente documentos
                relacionados aos ODS. Este indicador permite identificar se houve aumento,
                estabilidade ou redução na quantidade de documentos vinculados aos ODS em
                determinado período.
            </p>
            <p>
                <strong>Cálculo:</strong> O índice compara a produção de documentos relacionados aos ODS
                entre o ano atual e o ano anterior, expressando a variação em percentual.
            </p>
            <p>
                <strong>Interpretação:</strong>
            </p>
            <ul>
                <li><strong>IVC > 0</strong>: Crescimento na produção relacionada aos ODS</li>
                <li><strong>IVC = 0</strong>: Produção estável</li>
                <li><strong>IVC < 0</strong>: Redução na produção relacionada aos ODS</li>
            </ul>
        </div>
        <div class="col-md-12 col-sm-6 center">
            <img src="{{ asset('img/ivc.png') }}" class="img-fluid mt-1 w-20" style="width: 30%;" alt="Índice de Variação de Contribuição com ODS (IVC-ODS)">
        </div>
    </div>  
</div>
@endsection