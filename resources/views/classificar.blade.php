@extends('layouts.guest')
@section('content')
<div class="row">
    <div class="col-md-12">
       <div class="header-text">
            <h3><i class="fa fa-users"></i> ODS EGC Classificar</h3>
            <div class="cabecalho">
                <h5 class="mb-0">Classifique os textos de acordo com a ODS identificada na leitura</h5>
                <p>A classificação manual auxilia no processo de melhoria da qualidade da classificação dos modelos de Inteligência Artificial</p>
            </div>
       </div>
    </div>
    <div class="col-md-12">
        <div class="card card-plain">
            <div class="content">
                <div class="row">
                    <div class="col-md-12">
                        <h6>{{ $texto->nm_producao }}</h6>
                        <p>
                            {{ $texto->ds_resumo }}
                        </p>
                    </div>
                    <div class="col-md-1 mt-1">
                        <img src="http://localhost/ods-egc/public/img/ods-icone/ods_{{ $ods }}.png" class="img-fluid" alt="ODS 6" style="border-radius: 0px;">
                    </div>
                    <div class="col-md-9">
                        <p class="mb-0">Você concorda com a classificação deste texto como <strong>ODS {{ $ods }}</strong>?</p>
                        <button type="button" class="btn btn-fill btn-danger btn-wd"><i class="fa fa-ban"></i> Discordo</button>
                        <button type="button" class="btn btn-fill btn-warning btn-wd"><i class="fa fa-question-circle"></i> Não sei</button>
                        <button type="button" class="btn btn-fill btn-success btn-wd"><i class="fa fa-check"></i> Concordo</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
 </div>
@endsection