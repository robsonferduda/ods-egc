@extends('layouts.guest')
@section('content')
<div class="row mt-3">
    <div class="col-md-12"> 
        <h5 class="mb-2" style="font-size: 14px !important;"><i class="fa fa-university" aria-hidden="true"></i> UNIVERSIDADE FEDERAL DE SANTA CATARINA</h5>
        <h5 class="mb-2" style="font-size: 14px !important;">Repositório de Documentos</h5>
        <p><strong>Dimensão</strong>: <span class="dimensao-selecionada">Todas</span></p>
    </div>
</div>
<div class="row mt-3">
    <div class="col-md-3">
        <div class="row">
            <div class="col-md-12">
                <h6 class="mb-2"><i class="fa fa-filter"></i> Filtros</h6>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>Instituição</label>
                    <select class="form-control" name="ies" id="ies" aria-label="Default select example">
                        <option>Todas</option>
                        <option value="ufsc" selected>UFSC</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>Dimensão</label>
                    <select class="form-control" name="dimensao" id="dimensao" aria-label="Selecione a dimensão">
                        <option value="0">Todas</option>
                        @foreach($dimensoes_ies as $key => $dimensao)
                            <option value="{{ $dimensao->apelido }}" {{ $dimensao->apelido == request('dimensao') ? 'selected' : '' }}>{{ $dimensao->nome }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>Documentos Analisados</label>
                    <select class="form-control" name="tipo" id="tipo" aria-label="Selecione o tipo">
                        <option value="todos">Todos</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <label>Período</label>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <select class="form-control" name="ano_inicial" id="ano_inicial" aria-label="Default select example">
                        <option>Todos</option>
                    </select>
                </div>   
            </div>    
            <div class="col-md-6">
                <div class="form-group">
                    <select class="form-control" name="ano_final" id="ano_final" aria-label="Default select example">
                        <option>Todos</option>
                    </select>
                </div>   
            </div>   
            <div class="col-md-12">       
                <div class="form-group">
                    <label>Centro</label>
                    <select class="form-control" name="centro" id="centro" aria-label="Default select example">
                        <option value="">Todos</option>
                    </select>
                </div> 
            </div> 

            <div class="col-md-12">       
                <div class="form-group">
                    <label>Departamento</label>
                    <select class="form-control" name="departamento" id="departamento" aria-label="Default select example">
                        <option value="">Todos</option>
                    </select>
                </div> 
            </div> 

            <div class="col-md-12">       
                <div class="form-group">
                    <label>Programa de Pós-Graduação</label>
                    <select class="form-control" name="ppg" id="ppg" aria-label="Default select example">
                        <option value="">Todos</option>
                    </select>
                </div> 
            </div> 
            
            <div class="col-md-12">       
                <div class="form-group">
                    <label>Docente</label>
                    <select class="form-control" name="docente" id="docente" aria-label="Default select example">
                        <option value="">Selecione um docente</option>
                    </select>
                </div> 
            </div>  
            <div class="col-md-12 center"> 
                <button type="button" class="btn btn-fill btn-primary btn-wd btn-filtrar"><i class="fa fa-filter"></i> Filtrar</button>
            </div>
        </div>  
    </div>
    <div class="col-md-9">
        <div class="row">
            <div class="col-md-12">
                <h6 class="mb-2"><i class="fa fa-files-o"></i> Documentos Filtrados</h6>
            </div>
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

            $('.btn-filtrar').on('click', function(e) {
                e.preventDefault();

                // Pegue os valores dos filtros
                var ies = $("#ies").val();
                var dimensao = $("#dimensao").val();
                var tipo = $("#tipo").val();
                var ano_inicio = $("#ano_inicio").val();
                var ano_fim = $("#ano_fim").val();
                var centro = $("#centro").val();
                var departamento = $("#departamento").val();
                var ppg = $("#ppg").val();
                var docente = $("#docente").val();

                // Monte a query string
                var params = $.param({
                    ies: ies,
                    dimensao: dimensao,
                    tipo: tipo
                    ano_inicio: ano_inicio,
                    ano_fim: ano_fim,
                    centro: centro,
                    departamento: departamento,
                    ppg: ppg,
                    docente: docente                    
                });

                // Redirecione para a página de destino com os filtros
                window.location.href = "{{ url('repositorio') }}" + "?" + params;
            });

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