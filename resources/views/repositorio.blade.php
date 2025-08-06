@extends('layouts.guest')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12">
                <h6 class="mb-2"><i class="fa fa-filter"></i> Filtros</h6>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>Instituição</label>
                    <select class="form-control" name="ies" id="ies" aria-label="Default select example">
                        <option>Todas</option>
                        <option value="ufsc" selected>UFSC</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>Dimensão</label>
                    <select class="form-control" name="dimensao" id="dimensao" aria-label="Selecione a dimensão">
                        <option value="todas">Todas</option>
                        <option value="extensao">Extensão</option>
                        <option value="pesquisa">Pesquisa</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>Ano</label>
                    <select class="form-control" name="ano" id="ano" aria-label="Default select example">
                        <option>Todos</option>
                    </select>
                </div>   
            </div>    
            <div class="col-md-6"> 
                <div class="form-group">
                    <label>ODS</label>
                    <select class="form-control" name="ods" id="ods" aria-label="Default select example">
                        <option value="0">Todos</option>
                        @foreach($ods as $key => $o)
                            <option value="{{ $o->cod }}">ODS {{ $o->cod }} - {{ $o->objetivo }}</option>
                        @endforeach
                    </select>
                </div> 
            </div>
        </div>  
    </div>
</div>
<div class="row mt-3">
    <div class="col-md-12"> 
        <h6>UNIVERSIDADE FEDERAL DE SANTA CATARINA</h6>
        <p><strong>Dimensão</strong>: <span class="dimensao-selecionada">Todas</span></p>
    </div>
    <div class="col-md-12"> 
        <div class="mb-1" id="lista_documentos">
            @foreach($documentos as $doc)
                <div class="box-documento">
                    <p class="mb-0"><strong>Título</strong>: {{ $doc->titulo }}</p>
                    <p class="mt-1 mb-0"><strong>{{ $doc->dimensao->nome }} - {{ $doc->tipo->ds_tipo_documento }}</strong></p>
                    <p class="mt-0">
                        <span class="badge badge-pill" style="background: {{ $doc->classificacao->cor }}">ODS {{ $doc->ods }}</span>
                        <a href="{{ url('documentos/dimensao/'.$doc->id_dimensao.'/detalhes/'.$doc->id) }}" target="_blank">
                            <span class="badge badge-pill detalhes-documento_off" data-dimensao="{{ $doc->id_dimensao }}" data-id="{{ $doc->id }}" style="background: #adadad;">
                                <i class="fa fa-bar-chart"></i> Detalhes
                            </span>
                        </a>
                    </p>
                </div>
            @endforeach

            <div class="mt-3" style="text-align:center;">
                {{ $documentos->appends(request()->query())->links() }}
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
            var dimensao = $("#dimensao").val();
            var ods = $("#ods").val();

            documentosAnalisados(dimensao, ods); 

            $(document).on('change', '#dimensao', function() {
                
                var dimensao = $(this).val();
                documentosAnalisados(dimensao, ods);
                
            });

            $(document).on('change', '#ods', function() {
                
                var dimensao = $("#dimensao").val();
                var ods = $(this).val();
                documentosAnalisados(dimensao, ods);
                
            });

            function documentosAnalisados(dimensao, ods){

                $.ajax({
                    url: host+'/dados/documentos/'+dimensao+'/ods/'+ods,
                    type: 'GET',
                    beforeSend: function() {
                        
                    },
                    success: function(data) {

                        $('#lista_documentos').empty();

                        data.forEach(element => {
                            
                            $('#lista_documentos').append('<p class="mb-0"><strong>Título</strong>: '+element.titulo+'</p><p class="mt-1 mb-0"><strong> '+element.complemento+'</strong></p><p class="mt-0"><span class="badge badge-pill" style="background: '+element.cor+'"> ODS '+element.ods+'</span></p>');
                        });
                    },
                    complete: function(){
                        
                    }
                });

            }

        });
    </script>
@endsection