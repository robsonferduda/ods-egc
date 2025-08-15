@extends('layouts.guest')
@section('content')
<div class="row">
    <div class="col-md-12">
        <h3 class="text-center">Repositório de Documentos</h3>
        <h6>UNIVERSIDADE FEDERAL DE SANTA CATARINA</h6>
    </div>
</div>
<div class="row mt-3">
    <div class="col-md-3">
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
                        <option value="0">Todas</option>
                        @foreach($dimensoes_ies as $key => $dimensao)
                            <option value="{{ $dimensao->apelido }}">{{ $dimensao->nome }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>Ano Inicial</label>
                    <select class="form-control" name="ano_inicial" id="ano_inicial" aria-label="Default select example">
                        <option>Todos</option>
                    </select>
                </div>   
            </div>    
            <div class="col-md-2">
                <div class="form-group">
                    <label>Ano Final</label>
                    <select class="form-control" name="ano_final" id="ano_final" aria-label="Default select example">
                        <option>Todos</option>
                    </select>
                </div>   
            </div>   
            <div class="col-md-4"> 
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
    <div class="col-md-9">
        <div class="row">
            <div class="col-md-12" style="display: grid; justify-content: center !important;">
                {{ $documentos->appends(request()->query())->links() }}
            </div>
            <div class="col-md-12"> 
                <div class="mb-1" id="lista_documentos">
                    @foreach($documentos as $doc)
                        <div class="box-documento">
                            <p class="mb-0"><strong>Título</strong>: {{ $doc->titulo }}</p>
                            <p class="mt-1 mb-0"><strong>{{ $doc->dimensao->nome }} - {{ $doc->tipo->ds_tipo_documento }}</strong></p>
                            <p class="mt-0">
                                <span class="badge badge-pill" style="background: {{ $doc->classificacao->cor }}">ODS {{ $doc->classificacao->cod }}</span>
                                <span class="badge badge-pill" style="background: black;">{{ $doc->ano }}</span>
                                <a href="{{ url('documentos/dimensao/'.$doc->id_dimensao.'/detalhes/'.$doc->id) }}" target="_blank">
                                    <span class="badge badge-pill detalhes-documento_off" data-dimensao="{{ $doc->id_dimensao }}" data-id="{{ $doc->id }}" style="background: #adadad;">
                                        <i class="fa fa-bar-chart"></i> Detalhes
                                    </span>
                                </a>
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-md-12 mb-5" style="display: grid; justify-content: center !important;">
                {{ $documentos->appends(request()->query())->links() }}
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

            $.ajax({
                url: host+'/dados/ano',
                type: 'GET',
                dataType: "json",
                beforeSend: function() {
                    $('.painel').loader('show');
                },
                success: function(data) {
                    if(!data) {
                        Swal.fire({
                            text: 'Erro ao carregar períodos',
                            type: "warning",
                            icon: "warning",
                        });
                        return;
                    }

                    data.forEach(function(value, i) {
                        let option = new Option(value.ano, value.ano);
                        if(i == 0) option.setAttribute('selected', true);
                        $('#ano_inicial').append(option);
                    });

                    data.forEach(function(value, i) {
                        let option = new Option(value.ano, value.ano);
                        if(i == (data.length -1)) option.setAttribute('selected', true);
                        $('#ano_final').append(option);
                    });

                    $(".btn-filtrar").trigger("click");
                },
                complete: function(){
                    $('.painel').loader('hide');
                }
            }); 

        });
    </script>
@endsection