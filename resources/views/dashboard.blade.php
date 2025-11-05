@extends('layouts.guest')
@section('content')
<div class="row mt-3">
    <div class="col-md-12"> 
        <h5 class="mb-2" style="font-size: 14px !important;"><i class="fa fa-university" aria-hidden="true"></i> UNIVERSIDADE FEDERAL DE SANTA CATARINA</h5>
        <p><strong>Dimensão</strong>: <span class="dimensao-selecionada">Todas</span></p>
    </div>
</div>
<div class="row">
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
                            <option value="{{ $dimensao->apelido }}">{{ $dimensao->nome }}</option>
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
                    <select class="form-control" name="ano_inicio" id="ano_inicio" aria-label="Default select example">
                    </select>
                </div>   
            </div>    
            <div class="col-md-6">
                <div class="form-group">
                    <select class="form-control" name="ano_fim" id="ano_fim" aria-label="Default select example">
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
                <button type="button" class="btn btn-fill btn-warning btn-limpar ml-2"><i class="fa fa-eraser"></i> Limpar</button>
                <button type="button" class="btn btn-fill btn-primary btn-filtrar"><i class="fa fa-filter"></i> Filtrar</button>
            </div>
        </div>  
    </div>
    <div class="col-md-9">

        <!-- Visualização geral -->
        <div class="row mt-3 mb-5" id="dados-geral">            
            
            <div class="col-md-8 painel">            
                <div class="col-md-12">
                    <h6 class="center">Totais de documentos por ODS</h6>
                    <canvas id="myChart" width="400" height="365"></canvas>  
                    <h6 class="center mb-0">ODS</h6>
                    <p class="mb-0 mt-0">Total de <strong id="total_documentos"></strong> documentos</p>
                    <p>
                        <span class="badge badge-pill" style="background: #ef8157; border-radius: 5px;"><i class="fa fa-filter"></i> Filtros aplicados</span><span class="filtros"></span>
                    </p>
                </div>
            </div>

            <div class="col-md-4 top-ods"> 
                <h6 class="">ODS MAIS FREQUENTES</h6>
                <div class="lista-ods"></div>
            </div>

            <div class="col-md-12 box-centro d-none"> 
                <h6 class="">Estatísticas do Centro</h6>
                <div class="row">
                    <!--<div class="col-md-4" id="card-dimensao-centro"></div>-->
                    <div class="col-md-4" id="card-dimensao-ods"></div>
                    <div class="col-md-4" id="card-ics"></div>
                    <div class="col-md-4" id="card-ies"></div>
                    <div class="col-md-12" id="card-pesquisador-centro"></div>
                </div>
            </div>

            <div class="col-md-12 painel-icones mt-8 mb-0">
                <h6 class="mt-3"><i class="fa fa-share-alt" aria-hidden="true"></i> RESULTADOS E ESTATÍSTICAS</h6>
                <span class="text-success excel-download mr-3" style="color: #15954e !important;"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Baixar Planilha</span>
                <span class="text-danger pdf-download" style="color: #dc3545 !important;"><i class="fa fa-file-pdf" aria-hidden="true"></i> Baixar PDF</span>
            </div>
            
            <div class="col-md-8 mt-3 mb-3">
                <h6>
                    <i class="fa fa-line-chart" aria-hidden="true"></i> EVOLUÇÃO POR ODS 
                </h6>
                <div class="box-evolucao">
                    <canvas id="chart" width="400" height="380"></canvas>
                </div>
                <p class="text-danger center" style="color: #ef1000 !important;">Clique na legenda para habilitar/desabiliar cada ODS</p>
            </div>

            <div class="col-md-4 painel-icones mt-8 mb-0">
                <h6 class="mt-8" style="margin-top: 50px;">ODS IDENTIFICADOS</h6>
                <div class="row perfil-ods mt-8"></div>
            </div>

            <div class="col-sm-12 col-md-9 painel mb-5">        
                <h6 class="mb-1"><i class="fa fa-file-text" aria-hidden="true"></i> DOCUMENTOS ANALISADOS</h6>
                <span class="text-danger">São listados apenas alguns documentos. <a id="" style="font-weight: 500;" href="#" class="text-primary mb-5 ver-todos">Clique aqui </a>para ver todos</span>
                <div class="mt-2 mb-1" id="lista_documentos"></div>
            </div>

            <div class="col-sm-6 col-md-3"> 
                <h6 class="mb-3">DOCUMENTOS POR DIMENSÃO</h6>
                @foreach($dimensoes_ies as $key => $dimensao)
                    <div class="row box-dimensao box-dimensao-{{ $dimensao->apelido }}">
                        <div class="col-sm-2 col-md-4 px-0 py-0"> 
                            <img src="{{ asset('img/icones-dimensao/'.$dimensao->img) }}" class="img-fluid">
                        </div>
                        <div class="col-sm-10 col-md-8"> 
                            <p class="mt-3 total_dimensao" data-dimensao="{{ $dimensao->id }}">{{ $dimensao->total_dimensao }}</p>
                            <p><strong>Documentos</strong></p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Visualização de docente -->
        <div class="row mt-3 d-none" id="perfil-docente">
               
            <div class="col-md-4 center">
                <img src="" style="height: 195px;" class="img-fluid rounded-circle w-75 foto-perfil">            
            </div>

            <!--<canvas id="chartjs-3" class="chartjs"></canvas>-->
                
            <div class="col-md-8">
                <h5 class="mb-0 mt-3" id="nm_docente"></h5>
                <span id="nm_ppg"></span>
                <p class="mt-2">Impacto Multidimensional: <strong class="impacto_multidimensional"></strong></p>
                <p class="mt-1">Índice de Colaboração Acadêmica: <strong class="indice_colaboracao"></strong></p>
            </div>

            <div class="col-md-12">
                <div class="card card-chart">
                    <div class="card-header">
                        <h5 class="card-category" style="text-transform: uppercase; color: black;">Total de Produções Associadas aos ODS: <span id="total_documentos_docente"></span></h5>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
              <div class="card">
                <div class="card-header">
                  <h5 class="card-category" style="margin-bottom: 0px; color: black; font-weight: bold;">Produção Acadêmica</h5>
                  <p style="text-transform: uppercase; margin-top: 3px;" class="card-title">Distribuição por Dimensão</p>
                </div>
                <div class="card-body">
                  <canvas id="chartDimensao"></canvas>
                </div>
              </div>
            </div>

               

                <!--
                <div class="col-md-2 center">
                    <div class="row">
                        <div class="col-md-12 center">
                            <h1 class="mb-0">125</h1>
                            <p>SCORE</p>
                        </div>
                        <div class="col-md-12 center">
                            <h1 class="mb-0"><span class="ranking"></span><span class="ranking_geral" style="font-size: 14px; font-weight: 400;"></span></h1>
                            <p>RANKING</p>
                        </div>
                    </div>
                </div>
            -->
            <div class="col-md-12"><hr/></div>
           
           <!--
            <div class="col-md-8 mt-3 mb-5 box-evolucao">
                <h6>EVOLUÇÃO POR ODS</h6>
                <canvas id="evolucaoDocente" width="400" height="380"></canvas>
                <p class="text-danger center">Clique na legenda para habilitar/desabiliar cada ODS</p>
            </div>
            -->

            <div class="col-sm-12 col-md-8 mb-5">        
                <h6 class="mb-1">DOCUMENTOS ANALISADOS</h6>
                <span class="text-danger">São listados apenas alguns documentos. <a id="" style="font-weight: 500;" href="#" class="text-primary mb-5 ver-todos">Clique aqui </a>para ver todos</span>
                <div class="mt-3 mb-1" id="lista_documentos_docente"></div>
            </div>

            <div class="col-md-4 painel-icones mt-8 mb-0">
                <h6 class="mt-8" style="margin-top: 50px;">ODS IDENTIFICADOS</h6>
                <div class="row perfil-ods"></div>                    
            </div>

            <div class="col-md-12 mb-5">
                <h6><i class="fa fa-users" aria-hidden="true"></i> Rede de Relacionamentos</h6>
                <div id="cy" style="width: 100%; height: 400px; border: 1px solid #8080801c; border-radius: 8px; background: #8080800a;"></div>
            </div>

            

                       
        </div>  
    </div>
</div>
@endsection
@section('script')
    <script src="https://unpkg.com/cytoscape@3.26.0/dist/cytoscape.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/cytoscape-cose-bilkent@4.0.0/cytoscape-cose-bilkent.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
        
            var host =  $('meta[name="base-url"]').attr('content');

            $(document).on('click', '.pdf-download', function() {

                var canvas = document.getElementById('myChart');
                var imgBase64 = canvas.toDataURL('image/png');

                var canvas_evolucao = document.getElementById('chart');
                var evolucaoBase64 = canvas_evolucao.toDataURL('image/png');

                var ies = $("#ies").val();
                var dimensao = $("#dimensao").val();
                var tipo = $("#tipo").val();
                var ano_inicio = $("#ano_inicio").val();
                var ano_fim = $("#ano_fim").val();
                var centro = $("#centro").val();
                var departamento = $("#departamento").val();
                var ppg = $("#ppg").val();
                var docente = $("#docente").val();

                $.post(host+'/gerar-pdf', 
                    { grafico: imgBase64, 
                        grafico_evolucao: evolucaoBase64,
                        _token: '{{ csrf_token() }}',
                        dimensao: dimensao,
                        tipo: tipo,
                        centro: centro,
                        departamento: departamento,
                        ppg: ppg,
                        ano_inicio: ano_inicio,
                        ano_fim: ano_fim,
                        tipo: tipo,
                        docente: docente
                    }, 
                    function(resposta){
                        window.open(resposta.url, '_blank');
                    });
            });

            $(document).on('click', '.btn-limpar', function() {
                // Reseta selects para o valor padrão
                $('#ies').val('ufsc');
                $('#dimensao').val('0');
                $('#tipo').val('todos');
                $('#ano_inicio option').prop('selected', false).first().prop('selected', true);
                $('#ano_fim option').prop('selected', false).last().prop('selected', true);
                $('#centro').val('');
                $('#departamento').val('');
                $('#ppg').val('');
                $('#docente').val('');

                // Atualiza visualização e dispara o filtro padrão
                $(".btn-filtrar").trigger("click");
            });

            $('.ver-todos').on('click', function(e) {
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
                    tipo: tipo,
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

            $(document).on('change', '#docente', function() {

                var id = $(this).val();

                $.ajax({
                    url: host+'/docente/grafo/'+id,
                    type: 'GET',
                    success: function(data) {

                        const nodes = data.nodes;
                        const edges = data.edges;

                        const cy = cytoscape({
                          container: document.getElementById('cy'),
                          elements: {
                            nodes: nodes.map(n => ({
                              data: {
                                id: n.id,
                                label: n.label,
                                color: n.color ?? '#007bff', // azul padrão
                                title: n.label // tooltip
                              }
                            })),
                            edges: edges.map(e => ({
                              data: {
                                id: `edge-${e.from}-${e.to}`,
                                source: e.from,
                                target: e.to,
                                value: e.value
                              }
                            }))
                          },
                          style: [
                            {
                              selector: 'node',
                              style: {
                                'shape': 'round-rectangle',
                                'label': 'data(label)',
                                'width': 'label',
                                'padding': '6px',
                                'height': 'label',
                                'font-size': '11px',
                                'background-color': 'data(color)',
                                'text-valign': 'center',
                                'text-halign': 'center',
                                'color': '#fff',
                                'text-outline-color': '#444',
                                'text-outline-width': 2
                              }
                            },
                            {
                              selector: 'edge',
                              style: {
                                'width': 'mapData(value, 1, 10, 1, 5)',
                                'line-color': '#bbb',
                                'target-arrow-color': '#999',
                                'target-arrow-shape': 'triangle',
                                'curve-style': 'bezier'
                              }
                            }
                          ],
                          layout: {
                            name: 'cose-bilkent',
                            animate: true,
                            fit: true,
                            padding: 3,
                            idealEdgeLength: 180,
                            nodeRepulsion: 10000,
                            edgeElasticity: 0.45, // ajuste fino da "mola"
                            gravity: 0.25
                          }
                        });

                        // Tooltip nativo
                        cy.nodes().forEach(node => {
                          node.qtip({
                            content: node.data('title'),
                            show: { event: 'mouseover' },
                            hide: { event: 'mouseout' },
                            position: { my: 'top center', at: 'bottom center' },
                            style: {
                              classes: 'qtip-bootstrap',
                              tip: { width: 16, height: 8 }
                            }
                          });
                        });                       
                    }
                });
            });
        });
    </script>
    <script>

        $(document).ready(function() { 

            var host =  $('meta[name="base-url"]').attr('content');
            var token = $('meta[name="csrf-token"]').attr('content');

            //Carregar Centros
            $.ajax({
                url: host+'/dados/centros',
                type: 'GET',
                success: function(data) {
                    $('#centro').empty().append('<option value="">Todos</option>');
                    data.forEach(function(centro) {
                        $('#centro').append(`<option value="${centro.cd_centro_cen}">${centro.ds_sigla_cen} - ${centro.ds_nome_cen}</option>`);
                    });
                }
            });

            // Departamentos
            $.ajax({
                url: host + '/dados/departamentos',
                type: 'GET',
                success: function(data) {
                    $('#departamento').empty().append('<option value="">Todos</option>');
                    data.forEach(function(dep) {
                        $('#departamento').append(`<option value="${dep.id_departamento_dep}">${dep.ds_sigla_dep} - ${dep.ds_departamento_dep}</option>`);
                    });
                }
            });

            // PPGs
            $.ajax({
                url: host + '/dados/ppgs/todos',
                type: 'GET',
                success: function(data) {
                    $('#ppg').empty().append('<option value="">Todos</option>');
                    data.forEach(function(ppg) {
                        $('#ppg').append(`<option value="${ppg.id_ppg}">${ppg.nm_curso_cur}</option>`);
                    });
                }
            });

            // Docentes
            $('#docente').select2({
             placeholder: 'Selecione um docente',
             allowClear: true,
             width: '100%',
              language: {
                 noResults: function() { return "Nenhum docente encontrado"; },
                  searching: function() { return "Buscando..."; }
             }
          });

            $.ajax({
                url: host + '/dados/docentes',
                type: 'GET',
                success: function(data) {
                    $('#docente').empty().append('<option value="">Selecione um docente</option>');
                    data.forEach(function(docente) {
                        $('#docente').append(`<option value="${docente.id_pessoa_pes}">${docente.ds_nome_pessoa}</option>`);
                    });
                  // atualiza Select2
                  $('#docente').val('').trigger('change.select2');
                }
            });

            $('#centro').change(function() {

                var centroId = $(this).val();

                $.ajax({
                    url: host + '/dados/departamentos/centro/'+centroId,
                    type: 'GET',
                    data: { centro: centroId },
                    success: function(data) {
                        $('#departamento').empty().append('<option value="">Todos</option>');
                        data.forEach(function(dep) {
                            $('#departamento').append(`<option value="${dep.id_departamento_dep}">${dep.ds_sigla_dep} - ${dep.ds_departamento_dep}</option>`);
                        });
                    }
                });

                $.ajax({
                    url: host + '/dados/ppgs/'+centroId,
                    type: 'GET',
                    data: { centro: centroId },
                    success: function(data) {
                        $('#ppg').empty().append('<option value="">Todos</option>');
                        data.forEach(function(ppg) {
                            $('#ppg').append(`<option value="${ppg.id_ppg}">${ppg.nm_curso_cur}</option>`);
                        });
                    }
                });

                // Limpa PPG e Docente ao trocar centro
                $('#ppg').empty().append('<option value="">Todos</option>');
                $('#departamento').empty().append('<option value="">Todos</option>');            

            });

           

            $('#departamento').change(function() {

                var departamentoId = $(this).val();
                $('#ppg').val("");

            });

            $(document).on('change', '#dimensao', function() {
                buscarTiposPorDimensao();
            });

            function buscarTiposPorDimensao() {

                var apelido = $('#dimensao').val();
                $.ajax({
                    url: host+'/dimensao/' + apelido + '/tipos',
                    type: 'GET',
                    beforeSend: function() {
                        $('#tipo').html('<option>Carregando...</option>');
                    },
                    success: function(data) {
                        let html = '<option value="todos">Todos</option>';
                        $.each(data, function(codigo, nome) {
                            html += `<option value="${codigo}">${nome}</option>`;
                        });
                        $('#tipo').html(html);
                    },
                    error: function() {
                        $('#tipo').html('<option value="todos">Todos</option>');
                    }
                });
            }

            //Carrega combo Período

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
                        $('#ano_inicio').append(option);
                    });

                    data.forEach(function(value, i) {
                        let option = new Option(value.ano, value.ano);
                        if(i == (data.length -1)) option.setAttribute('selected', true);
                        $('#ano_fim').append(option);
                    });

                    $('#ano_inicio').val(2012);
                    $('#ano_fim').val(2025);

                    $(".btn-filtrar").trigger("click");
                },
                complete: function(){
                    $('.painel').loader('hide');
                }
            });           

            $(document).on('click', '.btn-filtrar', function() {

                var ies = $("#ies").val();
                var dimensao = $("#dimensao").val();
                var tipo = $("#tipo").val();
                var ano_inicial = $("#ano_inicio").val();
                var ano_fim = $("#ano_fim").val();
                var centro = $("#centro").val();
                var departamento = $("#departamento").val();
                var ppg = $("#ppg").val();
                var docente = $("#docente").val();
                var host =  $('meta[name="base-url"]').attr('content');

                // Atualiza label dimensão
                if(centro){
                    $(".box-centro").removeClass("d-none");

                    $.get(host + '/centro/ics/' + centro +'/ano/' + ano_fim, function(data){
                        if(data.length > 0) {

                            var ics = parseFloat(data[0].ics_norm_0_100);
                            var nivel = '';

                            if(ics > 50) {
                                nivel = 'Crescimento';
                            } else if(ics == 50) {
                                nivel = 'Estável';
                            } else if(ics < 50) {
                                nivel = 'Queda';
                            }
                            
                            var html = `
                                <div class="card shadow-sm mb-2" style="background: #f3f3f3;">
                                    <div class="card-body text-center">
                                        <h5 class="card-title mb-0">
                                            ICS
                                            <span class="pull-right badge badge-pill badge-${nivel === 'Crescimento' ? 'success' : (nivel === 'Estável' ? 'warning' : 'danger')}" style="font-size: 0.7rem; vertical-align: top; margin-left: 8px; position: absolute; right: 9px;">${nivel}</span>
                                        </h5>
                                        <p class="card-text mb-1">
                                            <span class="display-4 font-weight-bold">${ics}</span>                                            
                                        </p>
                                        <a class="link_indices" href="${host}/indices/crescimento/${centro}" target="_blank"><small class="">Índice de Crescimento Sustentável <i class="fa fa-question-circle" aria-hidden="true"></i></small></a>
                                    </div>
                                </div>
                            `;
                            $('#card-ics').html(html);
                        } else {
                            $('#card-ics').html('<div class="alert alert-warning">Nenhum IES encontrado.</div>');
                        }
                    });

                    $.get(host + '/centro/ies/' + centro, function(data){
                        if(data.length > 0) {

                            var sec_index = parseFloat(data[0].sec_index);
                            var nivel = '';
                            var descricao = '';

                            if(sec_index >= 70) {
                                nivel = 'Alto';
                                descricao = 'Centro com alto volume, temática diversa, presença em várias dimensões IES, colaboração robusta e/ou crescimento recente.';
                            } else if(sec_index >= 40) {
                                nivel = 'Médio';
                                descricao = 'Bom volume, mas pode faltar diversidade temática, transversalidade ou colaboração.';
                            } else {
                                nivel = 'Baixo';
                                descricao = 'Nichado, pouco transversal e/ou produção baixa/estável — pede estímulo programático.';
                            }
                            
                            var html = `
                                <div class="card shadow-sm mb-2" style="background: #f3f3f3;">
                                    <div class="card-body text-center">
                                        <h5 class="card-title mb-0">
                                            IES
                                            <span class="pull-right badge badge-pill badge-${nivel === 'Alto' ? 'success' : (nivel === 'Médio' ? 'warning' : 'danger')}" style="font-size: 0.7rem; vertical-align: top; margin-left: 8px; position: absolute; right: 9px;">${nivel}</span>
                                        </h5>
                                        <p class="card-text mb-1">
                                            <span class="display-4 font-weight-bold">${sec_index}</span>                                           
                                        </p>
                                        <a class="link_indices" href="${host}/indices/engajamento/${centro}" target="_blank"><small class="">Índice de Engajamento Sustentável <i class="fa fa-question-circle" aria-hidden="true"></i></small></a>
                                    </div>
                                </div>
                            `;
                            $('#card-ies').html(html);
                        } else {
                            $('#card-ies').html('<div class="alert alert-warning">Nenhum IES encontrado.</div>');
                        }
                    });

                    $.get(host + '/centro/dimensao/' + centro, function(data){
                        if(data.length > 0) {
                            var destaque = data.find(function(item){ return item.rk === 1 || item.rk == "1"; });
                            var totalDocs = data.reduce(function(sum, item){ return sum + parseInt(item.total_docs); }, 0);
                            var percentual = totalDocs > 0 ? ((destaque.total_docs / totalDocs) * 100).toFixed(1) : 0;

                            var html = `
                                <div class="card shadow-sm mb-2" style="background: #f3f3f3;">
                                    <div class="card-body text-center">
                                        <h5 class="card-title mb-0">${destaque.nm_dim_ies}</h5>
                                        <p class="card-text mb-1">
                                            <span class="display-4 font-weight-bold">${percentual}%</span>
                                        </p>
                                        <a class="link_indices" href="${host}/indices/dimensoes/${centro}" target="_blank"><small class="">Dimensão IES mais destacada <i class="fa fa-question-circle" aria-hidden="true"></i></small></a>
                                    </div>
                                </div>
                            `;
                            $('#card-dimensao-centro').html(html);
                        } else {
                            $('#card-dimensao-centro').html('<div class="alert alert-warning">Nenhuma dimensão encontrada.</div>');
                        }
                    });

                    $.get(host + '/centro/dimensao-ods/' + centro, function(data){
                        if(data.length > 0) {
                            var destaque = data.find(function(item){ return item.rk === 1 || item.rk == "1"; });
                            var totalDocs = data.reduce(function(sum, item){ return sum + parseInt(item.total_docs); }, 0);
                            var percentual = totalDocs > 0 ? ((destaque.total_docs / totalDocs) * 100).toFixed(1) : 0;

                            var html = `
                                <div class="card shadow-sm mb-2" style="background: #f3f3f3;">
                                    <div class="card-body text-center">
                                        <h5 class="card-title mb-0">${destaque.nm_dim_ods}</h5>
                                        <p class="card-text mb-1">
                                            <span class="display-4 font-weight-bold">${percentual}%</span>
                                        </p>
                                        
                                        <a class="link_indices" href="${host}/indices/dimensoes/${centro}" target="_blank"><small class="">Dimensão Predominante <i class="fa fa-question-circle" aria-hidden="true"></i></small></a>
                                    </div>
                                </div>
                            `;
                            $('#card-dimensao-ods').html(html);
                        } else {
                            $('#card-dimensao-ods').html('<div class="alert alert-warning">Nenhuma dimensão ODS encontrada.</div>');
                        }
                    });

                    /*
                    $.get('/centro/pesquisador/' + centro, function(data){
                        if(data.length > 0) {
                            var pesquisador = data[0];
                            var html = `
                                <div class="card shadow-sm mb-2 mt-3" style="background: #f3f3f3;">
                                    <div class="card-body text-center">
                                        <h5 class="card-title mb-0">${pesquisador.nome_pessoa}</h5>
                                        <p class="card-text mb-1">
                                            <span class="display-4 font-weight-bold">${pesquisador.total_docs}</span>
                                        </p>
                                        <small class="text-muted">Pesquisador(a) com mais documentos no centro ${pesquisador.sigla_centro}</small>
                                    </div>
                                </div>
                            `;
                            $('#card-pesquisador-centro').html(html);
                        } else {
                            $('#card-pesquisador-centro').html('<div class="alert alert-warning">Nenhum pesquisador encontrado.</div>');
                        }
                    });*/

                }else{
                    $(".box-centro").addClass("d-none");
                }

                if(docente){

                    carregaDocente(ppg, docente); 
                    //getFrequencia("evolucaoDocente", dimensao, tipo, ano_inicial, ano_fim, centro, departamento, ppg, docente);
                    documentosAnalisados("#lista_documentos_docente", dimensao, tipo, ano_inicial, ano_fim, centro, departamento, ppg, docente); 
                    carregarGraficoDimensao(docente);

                    // Total de Documentos ODS por docente
                    $.ajax({
                        url: host + '/docentes/total-documentos/'+docente,
                        type: 'GET',
                        success: function(data) {
                            $("#total_documentos_docente").text(data.total);
                        }
                    });   

                    // Impacto Multidimensional
                    $.ajax({
                        url: host + '/docentes/impacto-multidimensional/'+docente,
                        type: 'GET',
                        success: function(data) {
                            $(".impacto_multidimensional").text(data.indice);
                        }
                    });  

                    // Colaboração Acadêmica
                    $.ajax({
                        url: host + '/docentes/colaboracao-academica/'+docente,
                        type: 'GET',
                        success: function(data) {
                            $(".indice_colaboracao").text(data.indice);
                        }
                    }); 
                    
                }else{

                    $(".filtros").empty();

                    if(ies != "todas")
                        $(".filtros").append('<span class="badge badge-pill">'+$("#ies option:selected" ).text()+'</span>');

                    if(dimensao != "todas")
                        $(".filtros").append('<span class="badge badge-pill">'+$("#dimensao option:selected" ).text()+'</span>');

                    if(tipo != "todos")
                        $(".filtros").append('<span class="badge badge-pill">'+$("#tipo option:selected" ).text()+'</span>');

                    if(centro != "")
                        $(".filtros").append('<span class="badge badge-pill">'+$("#centro option:selected" ).text()+'</span>');

                    if(departamento != "")
                        $(".filtros").append('<span class="badge badge-pill">'+$("#departamento option:selected" ).text()+'</span>');

                    if(ppg != "")
                        $(".filtros").append('<span class="badge badge-pill">'+$("#ppg option:selected" ).text()+'</span>');

                    if(docente != "")
                        $(".filtros").append('<span class="badge badge-pill">'+$("#docente option:selected" ).text()+'</span>');
                
                    $(".filtros").append('<span class="badge badge-pill">'+$("#ano_inicio option:selected" ).text()+' - '+$("#ano_fim option:selected" ).text()+'</span>');

                    //Atualiza label dimensão
                    var dimensao_selecionada = $("#dimensao option:selected" ).text();
                    $(".dimensao-selecionada").text(dimensao_selecionada);

                    graficoDistribuicaoBarras(dimensao, tipo, ano_inicial, ano_fim, centro, departamento, ppg, docente);  
                    documentosAnalisados("#lista_documentos", dimensao, tipo, ano_inicial, ano_fim, centro, departamento, ppg, docente); 
                    graficoTopOds(dimensao, tipo, ano_inicial, ano_fim, centro, departamento, ppg, docente);
                    painelODS(dimensao, tipo, ano_inicial, ano_fim, centro, departamento, ppg, docente);
                    getFrequencia("chart", dimensao, tipo, ano_inicial, ano_fim, centro, departamento, ppg, docente);
                    atualizarTotaisDimensoes(dimensao, tipo, ano_inicial, ano_fim, centro, departamento, ppg, docente);

                    $("#dados-geral").removeClass("d-none");
                    $("#perfil-docente").addClass("d-none");
                }

            });           

            $.ajax({
                url: host+'/docentes',
                type: 'GET',
                beforeSend: function() {
                    $('.top-docentes').loader('show');                    
                },
                success: function(data) {

                    var foto = "";
        
                    data.forEach(element => {

                        if(element.chave){
                            foto = host+'/img/docentes/'+element.chave+'.jpg';
                        }else{
                            foto = host+'/img/docentes/user.png';
                        }
                        
                        $('.lista-docentes').append('<div class="row mt-3 perfil-docente-mostrar" data-docente="'+element.nm_orientador+'"><div class="col-md-2 center"><img src="'+foto+'" class="img-fluid rounded-circle w-100"></div><div class="col-md-10 pl-1"><p class="mb-0"><strong>'+element.nm_orientador+'</strong></p><span id="nm_ppg">'+element.total+' Documentos</span></div></div>');
                    });
                },
                complete: function(){
                    $('.top-docentes').loader('hide');
                }
            });           

            $(document).on('click', '.perfil-docente-mostrar', function() {
                
                var docente = $(this).data("docente");

                carregaDocente("Todos", docente);
                
            });

            $(document).on('click', '.detalhes-documento', function() {
                
                var id = $(this).data("id");
                var dimensao = $(this).data("dimensao");

                window.location.href = host+"/documentos/dimensao/"+dimensao+"/detalhes/"+id;
                
            });

            $("#ppg").change(function(){

                var ppg = $(this).val();

                $('#departamento').val("");                               

            });

            $("#docente").change(function(){

                
                var ppg = $("#ppg").val();
                var docente = $(this).val();

                if(docente){
                    //carregaDocente(ppg, docente); 
                }         

            });

            $(".excel-download").click(function(){

                var ies = $("#ies").val();
                var dimensao = $("#dimensao").val();
                var ano_inicial = $("#ano_inicio").val();
                var ano_fim = $("#ano_fim").val();
                var ppg = $("#ppg").val();
                var docente = $("#docente").val();
                var tipo = $("#tipo").val();

                // informa usuário que o arquivo está sendo gerado
                Swal.fire({
                    title: 'Gerando planilha',
                    html: '<p class="center">Aguarde! O arquivo está sendo preparado para download.</p>',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: host+'/dados/excel',
                    type: 'POST',
                    xhrFields:{
                        responseType: 'blob'
                    },
                    data: {
                            "_token": token,
                            "dimensao": dimensao,
                            "ppg": ppg,
                            "ano_inicial": ano_inicial,
                            "ano_fim": ano_fim,
                            "tipo": tipo,
                            "docente": docente
                    },
                    success: function(data) {

                        var blob = data;
                        var downloadUrl = URL.createObjectURL(blob);
                        var a = document.createElement("a");
                        a.href = downloadUrl;

                        // timestamp no formato YYYYMMDD_HHMMSS
                        var now = new Date();
                        var pad = (n) => n.toString().padStart(2, '0');
                        var timestamp = now.getFullYear().toString()
                                        + pad(now.getMonth() + 1)
                                        + pad(now.getDate())
                                        + '_'
                                        + pad(now.getHours())
                                        + pad(now.getMinutes())
                                        + pad(now.getSeconds());

                        a.download = "dados_evolucao_" + timestamp + ".xlsx";
                        document.body.appendChild(a);

                        // fecha o modal de progresso antes de iniciar o download
                        Swal.close();

                        a.click();

                        // libera recursos e remove o elemento
                        setTimeout(function(){
                            URL.revokeObjectURL(downloadUrl);
                            a.remove();
                        }, 100);
                    },
                    error: function(xhr){
                        // fecha modal de progresso e mostra erro
                        Swal.close();
                        var msg = 'Erro ao gerar planilha. Tente novamente.';
                        if(xhr && xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                        Swal.fire({
                            icon: 'error',
                            title: 'Falha',
                            text: msg
                        });
                    },
                    complete: function(){
                    
                    }
                });

            });

            //Totais de documentos por ODS
            function graficoDistribuicaoBarras(dimensao, tipo, ano_inicial, ano_fim, centro, departamento, ppg, docente) {

                $.ajax({
                    url: host+'/dados/geral',
                    type: 'POST',
                    data: {
                            "_token": token,
                            "dimensao": dimensao,
                            "ano_inicial": ano_inicial,
                            "ano_fim": ano_fim,
                            "tipo": tipo,
                            "centro": centro,
                            "departamento": departamento,
                            "ppg": ppg,
                            "docente": docente
                    },
                    beforeSend: function() {
                        $('.painel').loader('show');
                    },
                    success: function(data) {

                        var soma_documentos = 0;

                        data.total.forEach(element => {
                            soma_documentos += element;
                        });

                        $("#total_documentos").text(soma_documentos);

                        $('.total_dimensao').each(function(index, element) {
                            
                            if($(this).data("dimensao") == 5){
                                $(this).text(soma_documentos);
                            }
                            
                        });

                        let GraficoGeral = null;
                        let graphareaGeral = document.getElementById("myChart").getContext("2d");
                        let chartStatusGeral = Chart.getChart("myChart"); // <canvas> id
                                    
                        if (chartStatusGeral != undefined) {
                            chartStatusGeral.destroy();
                        }

                        GraficoGeral = new Chart(graphareaGeral, {
                            type: 'bar',
                            data: {
                                labels: data.ods,
                                datasets: [{
                                    label: false,
                                    data: data.total,
                                    backgroundColor: data.cor,
                                    borderWidth: 0
                                }]
                            },
                            options: {
                                
                                plugins: { 
                                    // configura ChartDataLabels
                                        datalabels: {
                                            anchor: 'end',
                                            align: 'end',
                                            color: '#FFFFFF',
                                            font: {
                                                weight: 'bold',
                                                size: 11
                                            },
                                            formatter: function(value, context) {
                                                // mostra inteiro quando for número ou valor formatado
                                                if (typeof value === 'number') {
                                                    return value;
                                                }
                                                return value;
                                            }
                                        },
                                        title: { 
                                            display: false, 
                                            text: 'Distribuição de total por dimensão' 
                                        }, 
                                        legend: {
                                            display: false
                                        }
                                    }, 
                                scales: {
                                    yAxes: [{
                                        ticks: {
                                            beginAtZero:true
                                        }
                                    }]
                                }
                            }
                        });
                        
                    },
                    complete: function(){
                        $('.painel').loader('hide'); 
                    }
                });

            }

            //ODS MAIS FREQUENTES
            function graficoTopOds(dimensao, tipo, ano_inicial, ano_fim, centro, departamento, ppg, docente){

                $.ajax({
                    url: host+'/documento/ranking/ods',
                    type: 'POST',
                    data: {
                            "_token": token,
                            "dimensao": dimensao,
                            "ano_inicial": ano_inicial,
                            "ano_fim": ano_fim,
                            "tipo": tipo,
                            "ppg": ppg,
                            "centro": centro,
                            "departamento": departamento,
                            "docente": docente
                    },
                    beforeSend: function() {
                        $('.top-ods').loader('show');                    
                    },
                    success: function(data) {

                        var foto = "";

                        $('.lista-ods').empty();            
                        data.forEach(element => {

                            foto = host+'/img/ods-icone/ods_'+element.ods+'.png';
                            
                            $('.lista-ods').append('<div class="row mt-3 perfil-docente-mostrar-off" data-docente="'+element.ods+'"><div class="col-sm-2 col-md-4 center"><img src="'+foto+'" class="img-fluid w-100"></div><div class="col-sm-10 col-md-8 pl-1"><p class="mb-0"><strong>ODS '+element.ods+'</strong></p><p class="mb-0">'+element.objetivo+'</p><span id="nm_ppg"><strong>'+element.total+' Documentos</strong></span></div></div>');
                        });
                    },
                    complete: function(){
                        $('.top-ods').loader('hide');
                    }
                });
            }

            //EVOLUÇÃO POR ODS
            function getFrequencia(elemento, dimensao, tipo, ano_inicial, ano_fim, centro, departamento, docente, ppg) {

                let chx = document.getElementById(elemento).getContext('2d');

                $.ajax({
                    url: host+'/dados/geral/frequencia',
                    type: 'POST',
                    data: {
                            "_token": token,
                            "dimensao": dimensao,
                            "ano_inicial": ano_inicial,
                            "ano_fim": ano_fim,
                            "tipo": tipo,
                            "centro": centro,
                            "departamento": departamento,
                            "ppg": ppg,
                            "docente": docente
                    },
                    beforeSend: function() {
                        $('.box-evolucao').loader('show');
                    },
                    success: function(data) {

                        datasets = [];

                        data.frequencias.forEach(function(value, i) {

                            dict = {};

                            dict.data = value.totais;
                            dict.label = "ODS "+value.ods;
                            dict.borderColor = value.cor;
                            dict.fill = false;

                            datasets.push(dict);
                        });                        
                       
                        let config = {
                            type : 'line',
                            data : {
                                labels : data.sequencia,
                                datasets : datasets
                            },
                            options : {
                                title : {
                                    display : true,
                                    text : 'Chart JS Multiple Lines Example'
                                },
                                plugins: { 
                                        title: { 
                                            display: false, 
                                            text: 'Distribuição de total por dimensão' 
                                        }, 
                                        legend: {
                                            display: true,
                                            position: 'bottom'
                                        }
                                }, 
                            }
                        };

                        let GraficoEvolucao = null;
                        let graphareaEvolucao = document.getElementById(elemento).getContext("2d");
                        let chartStatusEvolucao = Chart.getChart(elemento); // <canvas> id
                                    
                        if (chartStatusEvolucao != undefined) {
                            chartStatusEvolucao.destroy();
                        }

                        var grafico = new Chart(chx, config);

                    },
                    complete: function(){
                        $('.box-evolucao').loader('hide');
                    }
                });
            } 

            //ODS IDENTIFICADOS
            function painelODS(dimensao, tipo, ano_inicial, ano_fim, centro, departamento, ppg, docente){

                $.ajax({
                    url: host+'/dados/geral',
                    type: 'POST',
                    data: {
                            "_token": token,
                            "dimensao": dimensao,
                            "centro": centro,
                            "departamento": departamento,
                            "ppg": ppg,
                            "docente": docente,
                            "ano_inicial": ano_inicial,
                            "ano_fim": ano_fim,
                            "tipo": tipo
                    },
                    beforeSend: function() {
                                
                    },
                    success: function(data) {

                        $(".perfil-ods").empty();

                        for (let i=0; i < 17; i++)  {
                            var ods = i+1;
                            if(data.total[i] > 0 ){
                                $(".perfil-ods").append('<div class="col-md-3 col-sm-4 mb-2 px-1"><img src="'+host+'/img/ods-icone/ods_'+ods+'.png" class="img-fluid img-ods" alt="ODS"></div>');
                            }else{
                                $(".perfil-ods").append('<div class="col-md-3 col-sm-4 mb-2 px-1"><img src="'+host+'/img/ods_icone_pb/ods_'+ods+'.png" class="img-fluid img-ods" alt="ODS"></div>');
                            }
                        }
                    },
                    complete: function(){
                        
                    }
                });

            }

            //DOCUMENTOS ANALISADOS
            function documentosAnalisados(elementoPai, dimensao, tipo, anoInicial, anoFim, centro, departamento, ppg, docente) {
              const url = `${host}/dados/documentos`;

              const payload = {
                "_token": token,
                "dimensao": dimensao,
                "ano_inicial": anoInicial,
                "ano_fim": anoFim,
                "tipo": tipo,
                "ppg": ppg,
                "centro": centro,
                "departamento": departamento,
                "docente": docente
              };

              $.ajax({
                url: url,
                type: 'POST',
                data: payload,

                beforeSend: function () {
                  // Pode exibir loader aqui, se desejar
                    $(elementoPai).loader('show');
                },

                success: function (data) {
                  const container = $(elementoPai);
                  container.empty();

                  data.forEach(doc => {
                    const html = `
                      <div class="box-documento">
                        <p class="mb-0"><strong>Título</strong>: ${doc.titulo}</p>
                        <p class="mt-1 mb-0"><strong>${doc.nome} - ${doc.ds_tipo_documento}</strong></p>
                        <p class="mt-0">
                            <span class="badge badge-pill" style="background: ${doc.cor}">ODS ${doc.ods}</span>
                            <span class="badge badge-pill" style="background: black;">${doc.ano}</span>
                              <a href="${host}/documentos/dimensao/${doc.id_dimensao}/detalhes/${doc.id}" target="_blank">
                                <span class="badge badge-pill detalhes-documento_off" data-dimensao="${doc.id_dimensao}" data-id="${doc.id}" style="background: #adadad;">
                                  <i class="fa fa-bar-chart"></i> Detalhes
                                </span>
                              </a>
                        </p>
                      </div>
                    `;
                    container.append(html);
                  });

                  $("#lista_documentos_docente").loader('hide');
                },

                complete: function () {
                  // Pode remover loader aqui
                  $(elementoPai).loader('hide');
                   
                  
                }
              });
            }

            //DOCUMENTOS POR DIMENSÃO
            function atualizarTotaisDimensoes(dimensao, tipo, ano_inicial, ano_fim, centro, departamento, ppg, docente) {

                var host = $('meta[name="base-url"]').attr('content');
                var token = $('meta[name="csrf-token"]').attr('content');

                // Limpa os totais antes de atualizar
                $('.total_dimensao').each(function() {
                    $(this).text('-');
                });

                $.ajax({
                    url: host + '/dados/total/dimensao',
                    type: 'POST',
                    data: {
                        "_token": token,
                        "dimensao": dimensao,
                        "tipo": tipo,
                        "ano_inicial": ano_inicial,
                        "ano_fim": ano_fim,
                        "centro": centro,
                        "departamento": departamento,
                        "ppg": ppg,
                        "docente": docente
                    },
                    success: function(data) {
                        $('.total_dimensao').each(function() {
                            var id = $(this).data('dimensao');
                            if (data.total && data.total[id] !== undefined) {
                                $(this).text(data.total[id]);
                            } else {
                                $(this).text('-');
                            }
                        });
                    }
                });
            }

            function carregaDocente(ppg, docente){
                
                $("#dados-geral").addClass("d-none");
                $("#perfil-docente").removeClass("d-none");

                $.ajax({
                    url: host+'/docentes/foto/'+docente,
                    type: 'GET',
                    beforeSend: function() {
                        $('.foto-perfil').loader('show');
                    },
                    success: function(data) {

                        foto = host+'/img/docentes/user.png';
                        $("#nm_docente").text(data.nome);
                                
                        if(data.chave){
                            foto = host+'/img/docentes/'+data.chave+'.jpg';
                        }

                        $(".foto-perfil").attr('src', foto);
                    },
                    complete: function(){
                        $('.foto-perfil').loader('hide');
                    }
                });
                
                $.ajax({
                    url: host+'/docentes/ods/'+docente,
                    type: 'GET',
                    beforeSend: function() {
                        $('.painel').loader('show');
                        $(".perfil-ods").empty();
                        $(".lista-ods").empty();
                    },
                    success: function(data) {

                        ods = [];
                        totais = [];
                        cores = [];
                        total = 0;
                        totalExtensao = 4;
                        data.forEach(element => {    
                            ods.push(element.ods);
                            totais[element.ods] = element.total;
                            cores[element.ods] = element.cor;
                            total += element.total;
                        });

                        for (let i=1; i<=17; i++)  {
                            if(ods.includes(i)){

                                var percentual = (totais[i]*100)/total;
                                var float = percentual + 30;
                                var label = (totais[i] > 1) ? 'Documentos' : 'Documento';

                                $(".perfil-ods").append('<div class="col-md-3 col-sm-4 mb-2 px-1"><img src="'+host+'/img/ods-icone/ods_'+i+'.png" class="img-fluid img-ods" alt="ODS"></div>');

                                $(".lista-ods").append('<div class="row mb-2 ml-1 mr-1"><div class="col-md-3"><img src="'+host+'/img/ods-icone/ods_'+i+'.png" class="img-fluid img-ods" alt="ODS"></div>'+
                                                        '<div class="col-md-9"><h6 class="progresso-title mb-0">ODS '+i+'</h6><p>'+totais[i]+' '+label+'</p>'+
                                                        '<div class="progresso ods-'+i+'"><div class="progresso-bar" style="width:'+percentual.toFixed(1)+'%; background:'+cores[i]+';"><div class="progresso-value" style="left: '+float.toFixed(1)+'%;">'+percentual.toFixed(1)+'%</div></div></div></div></div>');

                            }else{
                                $(".perfil-ods").append('<div class="col-md-3 col-sm-4 mb-2 px-1"><img src="'+host+'/img/ods_icone_pb/ods_'+i+'.png" class="img-fluid img-ods" alt="ODS"></div>');
                            }
                            
                        }

                        $(".perfil-ods").append('<div class="col-md-3 col-sm-4 mb-2"><img src="'+host+'/img/ods-icone/ods.png" class="img-fluid img-ods" alt="ODS"></div>');

                        let myChart = null;
                        let grapharea = document.getElementById("chartjs-3").getContext("2d");

                        let chartStatus = Chart.getChart("chartjs-3"); // <canvas> id
                        if (chartStatus != undefined) {
                        chartStatus.destroy();
                        }

                        myChart = new Chart(grapharea, {
                            "type": "bar",
                            showTooltips: false,
                            data: { 
                                labels: ["Ensino", "Pesquisa", "Extensão","Inovação", "Gestão"], 
                                datasets: [
                                    { 
                                        label: 'ODS 1', 
                                        backgroundColor: cores[1], 
                                        data: [0, ((totais[1]*100)/total).toFixed(2), 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 2', 
                                        backgroundColor: cores[2], 
                                        data: [0, ((totais[2]*100)/total).toFixed(2), 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 3', 
                                        backgroundColor: cores[3], 
                                        data: [0, ((totais[3]*100)/total).toFixed(2), 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 4', 
                                        backgroundColor: cores[4], 
                                        data: [0, ((totais[4]*100)/total).toFixed(2), 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 5', 
                                        backgroundColor: cores[5], 
                                        data: [0, ((totais[5]*100)/total).toFixed(2), 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 6', 
                                        backgroundColor: cores[6], 
                                        data: [0, ((totais[6]*100)/total).toFixed(2), 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 7', 
                                        backgroundColor: cores[7], 
                                        data: [0, ((totais[7]*100)/total).toFixed(2), 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 8', 
                                        backgroundColor: cores[8], 
                                        data: [0, ((totais[8]*100)/total).toFixed(2), 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 9', 
                                        backgroundColor: cores[9], 
                                        data: [0, ((totais[9]*100)/total).toFixed(2), 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 10', 
                                        backgroundColor: cores[10], 
                                        data: [0, ((totais[10]*100)/total).toFixed(2), 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 11', 
                                        backgroundColor: cores[11], 
                                        data: [0, ((totais[11]*100)/total).toFixed(2), 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 12', 
                                        backgroundColor: cores[12], 
                                        data: [0, ((totais[12]*100)/total).toFixed(2), 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 13', 
                                        backgroundColor: cores[13], 
                                        data: [0, ((totais[13]*100)/total).toFixed(2), 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 14', 
                                        backgroundColor: cores[14], 
                                        data: [0, ((totais[14]*100)/total).toFixed(2), 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 15', 
                                        backgroundColor: cores[15], 
                                        data: [0, ((totais[15]*100)/total).toFixed(12), 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 16', 
                                        backgroundColor: cores[16], 
                                        data: [0, ((totais[16]*100)/total).toFixed(2), 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 17', 
                                        backgroundColor: cores[17], 
                                        data: [0, ((totais[17]*100)/total).toFixed(2), 0, 0, 0], 
                                        stack: 'Stack 1',
                                    },                                     
                                ], 
                            }, 

                            options: { 
                                tooltips: {
                                callbacks: {
                                    title: function(chart, data) {
                                        return data.labels[chart[0].datasetIndex];
                                        }
                                    }
                                },
                                plugins: { 
                                    title: { 
                                        display: false, 
                                        text: 'Distribuição de total por dimensão' 
                                    }, 
                                    legend: {
                                        display: false,
                                        labels: {
                                            color: 'rgb(255, 99, 132)'
                                        }
                                    },
                                    datalabels: {
                                        color: 'black',
                                        anchor: 'end',
                                        align: 'end',
                                        offset: 15,
                                        formatter: (val, ctx) => (ctx.chart.data.labels[ctx.dataIndex])
                                    }
                                }, 
                                
                                scales: { 
                                    x: { 
                                        stacked: true
                                    }, 
                                    y: { 
                                        stacked: true,
                                        title: {
                                            display: true
                                        },
                                        ticks: {
                                            beginAtZero: true,
                                            min: 0,
                                            max: 100,
                                            stepSize: 5
                                        } 
                                    } 
                                } 
                            } 
                        });
                    }
                });

                /*
                $.ajax({
                    url: host+'/dados/ppg/'+ppg+'/docente/'+docente+'/ods',
                    type: 'GET',
                    beforeSend: function() {
                        $('.painel').loader('show');
                        $(".perfil-ods").empty();
                        $(".lista-ods").empty();
                    },
                    success: function(data) {

                        if(!data) {
                            Swal.fire({
                                text: 'Não foi possível buscar as emissoras. Entre em contato com o suporte.',
                                type: "warning",
                                icon: "warning",
                            });
                            return;
                        }

                        $.ajax({
                            url: host+'/docentes/ranking/'+docente,
                            type: 'GET',
                            beforeSend: function() {
                                
                            },
                            success: function(data) {
                                $(".ranking").html(data.rank_number);
                            },
                            complete: function(){
                                
                            }
                        });

                        $.ajax({
                            url: host+'/docentes/foto/'+docente,
                            type: 'GET',
                            beforeSend: function() {
                                $('.foto-perfil').loader('show');
                            },
                            success: function(data) {

                                foto = host+'/img/docentes/user.png';
                                
                                if(data.chave){
                                    foto = host+'/img/docentes/'+data.chave+'.jpg';
                                }

                                $(".foto-perfil").attr('src', foto);
                            },
                            complete: function(){
                                $('.foto-perfil').loader('hide');
                            }
                        });

                        $.ajax({
                            url: host+'/docentes/max-ranking',
                            type: 'GET',
                            beforeSend: function() {
                                
                            },
                            success: function(data) {
                                $(".ranking_geral").html('/'+data.total);
                            },
                            complete: function(){
                                
                            }
                        });

                        $("#dados-geral").addClass("d-none");
                        $("#perfil-docente").removeClass("d-none");
                        
                        ods = [];
                        totais = [];
                        cores = [];
                        total = 0;
                        totalExtensao = 4;
                        data.forEach(element => {    
                            ods.push(element.ods);
                            totais[element.ods] = element.total;
                            cores[element.ods] = element.cor;
                            total += element.total;
                        });

                        for (let i=1; i<=17; i++)  {
                            if(ods.includes(i)){

                                var percentual = (totais[i]*100)/total;
                                var float = percentual + 30;
                                var label = (totais[i] > 1) ? 'Documentos' : 'Documento';

                                $(".perfil-ods").append('<div class="col-md-3 col-sm-4 mb-2 px-1"><img src="'+host+'/img/ods-icone/ods_'+i+'.png" class="img-fluid img-ods" alt="ODS"></div>');

                                $(".lista-ods").append('<div class="row mb-2 ml-1 mr-1"><div class="col-md-3"><img src="'+host+'/img/ods-icone/ods_'+i+'.png" class="img-fluid img-ods" alt="ODS"></div>'+
                                                        '<div class="col-md-9"><h6 class="progresso-title mb-0">ODS '+i+'</h6><p>'+totais[i]+' '+label+'</p>'+
                                                        '<div class="progresso ods-'+i+'"><div class="progresso-bar" style="width:'+percentual.toFixed(1)+'%; background:'+cores[i]+';"><div class="progresso-value" style="left: '+float.toFixed(1)+'%;">'+percentual.toFixed(1)+'%</div></div></div></div></div>');

                            }else{
                                $(".perfil-ods").append('<div class="col-md-3 col-sm-4 mb-2 px-1"><img src="'+host+'/img/ods_icone_pb/ods_'+i+'.png" class="img-fluid img-ods" alt="ODS"></div>');
                            }
                            
                        }
                        $(".perfil-ods").append('<div class="col-md-3 col-sm-4 mb-2"><img src="'+host+'/img/ods-icone/ods.png" class="img-fluid img-ods" alt="ODS"></div>');
                            //$(".perfil-ods").append('<div class="col-md-2 col-sm-2"><img src="'+host+'/img/ods-icone/ods_'+element.ods+'.png" class="img-fluid img-ods" alt="ODS"></div>');
                        $("#nm_docente").text(docente);
                        $("#nm_ppg").text(ppg);

                        let myChart = null;
                        let grapharea = document.getElementById("chartjs-3").getContext("2d");

                        let chartStatus = Chart.getChart("chartjs-3"); // <canvas> id
                        if (chartStatus != undefined) {
                        chartStatus.destroy();
                        }

                        myChart = new Chart(grapharea, {
                            "type": "bar",
                            showTooltips: false,
                            data: { 
                                labels: ["Ensino", "Pesquisa", "Extensão","Inovação", "Gestão"], 
                                datasets: [
                                    { 
                                        label: 'ODS 1', 
                                        backgroundColor: cores[1], 
                                        data: [0, ((totais[1]*100)/total).toFixed(2), 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 2', 
                                        backgroundColor: cores[2], 
                                        data: [0, ((totais[2]*100)/total).toFixed(2), 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 3', 
                                        backgroundColor: cores[3], 
                                        data: [0, ((totais[3]*100)/total).toFixed(2), 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 4', 
                                        backgroundColor: cores[4], 
                                        data: [0, ((totais[4]*100)/total).toFixed(2), 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 5', 
                                        backgroundColor: cores[5], 
                                        data: [0, ((totais[5]*100)/total).toFixed(2), 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 6', 
                                        backgroundColor: cores[6], 
                                        data: [0, ((totais[6]*100)/total).toFixed(2), 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 7', 
                                        backgroundColor: cores[7], 
                                        data: [0, ((totais[7]*100)/total).toFixed(2), 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 8', 
                                        backgroundColor: cores[8], 
                                        data: [0, ((totais[8]*100)/total).toFixed(2), 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 9', 
                                        backgroundColor: cores[9], 
                                        data: [0, ((totais[9]*100)/total).toFixed(2), 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 10', 
                                        backgroundColor: cores[10], 
                                        data: [0, ((totais[10]*100)/total).toFixed(2), 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 11', 
                                        backgroundColor: cores[11], 
                                        data: [0, ((totais[11]*100)/total).toFixed(2), 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 12', 
                                        backgroundColor: cores[12], 
                                        data: [0, ((totais[12]*100)/total).toFixed(2), 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 13', 
                                        backgroundColor: cores[13], 
                                        data: [0, ((totais[13]*100)/total).toFixed(2), 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 14', 
                                        backgroundColor: cores[14], 
                                        data: [0, ((totais[14]*100)/total).toFixed(2), 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 15', 
                                        backgroundColor: cores[15], 
                                        data: [0, ((totais[15]*100)/total).toFixed(12), 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 16', 
                                        backgroundColor: cores[16], 
                                        data: [0, ((totais[16]*100)/total).toFixed(2), 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 17', 
                                        backgroundColor: cores[17], 
                                        data: [0, ((totais[17]*100)/total).toFixed(2), 0, 0, 0], 
                                        stack: 'Stack 1',
                                    },                                     
                                ], 
                            }, 

                            options: { 
                                tooltips: {
                                callbacks: {
                                    title: function(chart, data) {
                                        return data.labels[chart[0].datasetIndex];
                                        }
                                    }
                                },
                                plugins: { 
                                    title: { 
                                        display: false, 
                                        text: 'Distribuição de total por dimensão' 
                                    }, 
                                    legend: {
                                        display: false,
                                        labels: {
                                            color: 'rgb(255, 99, 132)'
                                        }
                                    },
                                    datalabels: {
                                        color: 'black',
                                        anchor: 'end',
                                        align: 'end',
                                        offset: 15,
                                        formatter: (val, ctx) => (ctx.chart.data.labels[ctx.dataIndex])
                                    }
                                }, 
                                
                                scales: { 
                                    x: { 
                                        stacked: true
                                    }, 
                                    y: { 
                                        stacked: true,
                                        title: {
                                            display: true
                                        },
                                        ticks: {
                                            beginAtZero: true,
                                            min: 0,
                                            max: 100,
                                            stepSize: 5
                                        } 
                                    } 
                                } 
                            } 
                        });
                    },
                    complete: function(){
                        $('.painel').loader('hide');
                    }
                });*/

            }

            function carregarGraficoDimensao(docente) {
 
                var host =  $('meta[name="base-url"]').attr('content');          
                var url = host+'/docentes/dimensao/'+docente;

                let ctx = document.getElementById('chartDimensao').getContext('2d');

                        fetch(url)
                            .then(response => response.json())
                            .then(data => {
                                const dimensoes = [...new Set(data.map(d => d.dimensao))]; // Eixo Y
                                const odsList = [...new Set(data.map(d => d.ods))];

                                const cores = {};
                                data.forEach(item => {
                                    cores[item.ods] = item.cor;
                                });

                                // Montar datasets por ODS
                                const datasets = odsList.map(ods => {
                                    return {
                                        label: ods,
                                        data: dimensoes.map(dim => {
                                            const found = data.find(d => d.ods === ods && d.dimensao === dim);
                                            return found ? found.total : 0;
                                        }),
                                        backgroundColor: cores[ods]
                                    };
                                });

                                let chartStatusEvolucao = Chart.getChart('chartDimensao'); // <canvas> id
                                            
                                if (chartStatusEvolucao != undefined) {
                                    chartStatusEvolucao.destroy();
                                }

                                chartDimensaoOds = new Chart(ctx, {
                                    type: 'bar',
                                    data: {
                                        labels: dimensoes,
                                        datasets: datasets
                                    },
                                    options: {
                                        responsive: true,
                                        indexAxis: 'y',
                                        plugins: {
                                            tooltip: { mode: 'index', intersect: false },
                                            title: { 
                                                display: false, 
                                                text: 'Distribuição de total por dimensão' 
                                            }, 
                                            legend: {
                                                display: false,
                                                labels: {
                                                    color: 'rgb(255, 99, 132)'
                                                }
                                            },
                                        },
                                        scales: {
                                            x: { stacked: true },
                                            y: { stacked: true }
                                        }
                                    }
                                });
                            })
                            .catch(error => console.error('Erro ao carregar gráfico:', error));
                    }

               
      
});
    </script>
@endsection