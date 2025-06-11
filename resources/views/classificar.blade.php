@extends('layouts.guest')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="header-text">
            <h3>Perfil ODS <i class="fa fa-angle-double-right" aria-hidden="true"></i> Classificar</h3>
         </div>
    </div>
    @if(Auth::user())
      <div class="col-md-12 mb-3">
           <div class="pull-left">
               <a href="{{ url('analisar') }}">
                   <span class="badge badge-pill badge-default">Analisar DOcumentos</span>
               </a>
               <a href="{{ url('classificar') }}">
                   <span class="badge badge-pill badge-default">COLABORAR</span>
               </a>
               <a href="{{ url('minhas-avaliacoes') }}">
                   <span class="badge badge-pill badge-default">Minhas Avaliações</span>
               </a>
               <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                   <span class="badge badge-pill badge-danger">Sair</span>
               </a>
           </div>
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
                        <h6>{{ $texto->nm_producao }}</h6>
                        <p>
                            {{ ucfirst(mb_strtolower($texto->ds_resumo, 'UTF-8')) }} 
                        </p>
                    </div>
                    <div class="col-md-2 mt-1">
                        <img src="{{ asset('img/ods-icone/ods_'.$ods.'.png') }}" class="img-fluid" alt="Imagem ODS {{ $ods }}" style="border-radius: 0px;">
                    </div>
                    <div class="col-md-10">
                        <p class="mb-0">Você concorda com a classificação deste texto como <strong class="ods-classificacao" data-ods="{{ $ods }}">ODS {{ $ods }}</strong>?</p>
                        <a href="{{ url('documento/'.$texto->id_producao_intelectual.'/classificar/negativo') }}" class="btn btn-fill btn-danger btn-wd"><i class="fa fa-ban"></i> Discordo</a>
                        <a href="{{ url('documento/'.$texto->id_producao_intelectual.'/classificar/neutro') }}" class="btn btn-fill btn-warning btn-wd"><i class="fa fa-question-circle"></i> Não sei</a>
                        <a href="{{ url('documento/'.$texto->id_producao_intelectual.'/classificar/positivo') }}" class="btn btn-fill btn-success btn-wd"><i class="fa fa-check"></i> Concordo</a>
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