@extends('layouts.guest')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="header-text">
            <div class="header-text">
            <h3 class="mb-0">
                {{ config('app.name') }} <i class="fa fa-angle-double-right" aria-hidden="true"></i> Colaborar
                @include('layouts.nivel')
            </h3>
        </div>
        </div>
    </div>
    @if(Auth::user())
      <div class="col-md-12 mb-3">
           @include('layouts/menu-logado')
       </div>
    @endif
    <div class="col-md-12">
        <div class="cabecalho">
              <h5 class="mb-0">Classifique os textos de acordo com o ODS identificado na leitura</h5>
              <p>A classificação manual auxilia no processo de melhoria da qualidade da classificação dos modelos de Inteligência Artificial</p>
            </div>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
        <div class="card card-plain">
            <div class="content">
                <div class="row">
                    <div class="col-md-12">
                        @include('layouts.mensagens')
                    </div>
                    <div class="col-md-12">
                        <p class="text-info" style="padding: 3px 8px; background: #f3f3f3; border-radius: 5px;"><i class="fa fa-exclamation-circle"></i> Neste processo, fragmentos de textos de documentos classificados por modelos de IA são mostrados para análise e avaliação. Clique em <strong>Texto Completo</strong> caso queira entender melhor o contexto do texto.</p>
                    </div>
                    <div class="col-md-12">
                        <h6>Texto Para Análise</h6>
                        <p>
                            {{ $texto_avaliacao }} 
                        </p>
                    </div>
                    <div class="col-md-12">
                        <div id="accordion" role="tablist" aria-multiselectable="true" class="card-collapsed">
                            <div class="card card-plain">
                                <div class="card-header" style="padding: 0px !important;" role="tab" id="heading">
                                    <a class="collapsed show-text" data-toggle="collapse" data-parent="#accordion" href="#collapse" aria-expanded="false" aria-controls="collapse">
                                    Texto Completo
                                        <i class="nc-icon nc-minimal-down"></i>
                                    </a>
                                </div>
                                <div id="collapse" class="collapse" role="tabpanel" aria-labelledby="heading}">
                                <div class="card-body">
                                    <h6>{{ $texto->nm_producao }}</h6>
                                    {{ ucfirst(mb_strtolower($texto->ds_resumo, 'UTF-8')) }}
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 mt-1">
                        <img src="{{ asset('img/ods-icone/ods_'.$ods.'.png') }}" class="img-fluid" alt="Imagem ODS {{ $ods }}" style="border-radius: 0px;">
                    </div>
                    <div class="col-md-10">
                        <p class="mb-0">Você concorda com a classificação deste texto como <strong class="ods-classificacao" data-ods="{{ $ods }}">ODS {{ $ods }}</strong>?</p>
                        <a href="{{ url('documento/'.$texto->id.'/classificar/negativo') }}" class="btn btn-fill btn-danger btn-wd"><i class="fa fa-ban"></i> Discordo</a>
                        <a href="{{ url('documento/'.$texto->id.'/classificar/neutro') }}" class="btn btn-fill btn-warning btn-wd"><i class="fa fa-question-circle"></i> Não sei</a>
                        <a href="{{ url('documento/'.$texto->id.'/classificar/positivo') }}" class="btn btn-fill btn-success btn-wd"><i class="fa fa-check"></i> Concordo</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-sm-12 mb-5">
                        <div class="row">
                            <div class="col-md-12 mb-3 ods-header d-none">
                                <h6></h6>
                                <div class="barra"></div>
                            </div>
                            <div class="col-md-12">
                                <p class="mb-2"><strong class="ods-objetivo text-uppercase"></strong></p>
                                <p><strong class="ods-descricao"></strong></p>
                                <div class="clearfix ods-metas">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() { 

            var host =  $('meta[name="base-url"]').attr('content');
            var token = $('meta[name="csrf-token"]').attr('content');
            var ods = $('.ods-classificacao').data("ods");

            $.ajax({
                url: host+'/ods/'+ods,
                type: 'GET',
                beforeSend: function() {
                    
                },
                success: function(data) {
                    
                    $(".ods-header").addClass("d-block");
                    $(".ods-objetivo").html('OBJETIVO '+data.cod+' - '+data.objetivo);
                    $(".ods-descricao").html(data.descricao);
                    $(".ods-metas").html(data.metas);
                    $(".ods-img").html('<img src="'+host+'/img/ods-icone/ods_'+data.cod+'.png" class="img-fluid" alt="Imagem ODS '+data.cod+'"  style="border-radius: 0px;">');
                }
            });      
        });
    </script>
@endsection