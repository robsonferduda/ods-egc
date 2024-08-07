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
                    <select class="form-control" aria-label="Default select example">
                        <option value="todas">Todas</option>
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
            <div class="col-md-3"> 
                <div class="form-group">
                    <label>PPG</label>
                    <select class="form-control" name="ppg" id="ppg" aria-label="Default select example">
                        <option>Todos</option>
                    </select>
                </div> 
            </div>
            <div class="col-md-3">       
                <div class="form-group">
                    <label>Docente</label>
                    <select class="form-control" name="docente" id="docente" aria-label="Default select example">
                        <option>Todos</option>
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
</div>
<div class="row mt-3" id="dados-geral">
    <div class="col-md-6 painel">            
        <div class="col-md-12">
            <canvas id="myChart" width="400" height="400"></canvas>  
        </div>
    </div>
    <div class="col-md-5 ml-3 top-docentes"> 
        <div class="row">
            <h6>RANKING DOCENTES</h6>
        </div>
        <div class="lista-docentes">           
            
        </div>
    </div>
    <div class="col-md-12 painel-icones mt-3 mb-0 mr-4 ml-4">
        <div class="row perfil-ods"> 

        </div>
        
    </div>
    <div class="col-md-12 mt-5 mb-5 painel">
        <h6>DOCUMENTOS ANALISADOS</h6>
        <div class="mb-1" id="lista_documentos"></div>
        <a href="#" class="mb-5">VER TODOS</a>
    </div>
</div>
    <div class="col-md-12 painel">
        <div class="row mt-3 d-none" id="perfil-docente">
            <div class="col-md-3 center">
                <img src="" class="img-fluid rounded-circle w-75 foto-perfil">            
                <h5 class="mb-0 mt-3" id="nm_docente"></h5>
                <span id="nm_ppg"></span>
            </div>
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
            <div class="col-md-7">
                <canvas id="chartjs-3" class="chartjs"></canvas>
            </div>
            <div class="col-md-12 painel-icones mt-3 mb-5">
                <div class="row perfil-ods"> 

                </div>
                
            </div>
            <div class="row lista-ods mb-5">
                
            </div>            
        </div>  
   </div>

@endsection
@section('script')
    <script>

        $(document).ready(function() { 

            var host =  $('meta[name="base-url"]').attr('content');
            var token = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: host+'/dados/geral',
                type: 'GET',
                beforeSend: function() {
                    
                },
                success: function(data) {

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
                        
                }
            });

            $.ajax({
                url: host+'/dados/documentos',
                type: 'GET',
                beforeSend: function() {
                    
                },
                success: function(data) {
        
                    data.forEach(element => {
                        
                        $('#lista_documentos').append('<p class="mb-0"><strong>Título</strong>: '+element.nm_producao+'</p><p class="mt-1 mb-0"><strong> '+element.nm_programa+'</strong></p><p class="mt-0"><span class="badge badge-pill" style="background: '+element.cor+'"> ODS '+element.ods+'</span></p>');
                    });
                },
                complete: function(){
                    
                }
            });

            $.ajax({
                url: host+'/docentes',
                type: 'GET',
                beforeSend: function() {
                    $('.top-docentes').loader('show');                    
                },
                success: function(data) {
        
                    data.forEach(element => {
                        
                        $('.lista-docentes').append('<div class="row mt-3 perfil-docente-mostrar" data-docente="'+element.nm_orientador+'"><div class="col-md-2 center"><img src="'+host+'/img/docentes/'+element.foto+'.jpg" class="img-fluid rounded-circle w-100"></div><div class="col-md-10 pl-1"><p class="mb-0"><strong>'+element.nm_orientador+'</strong></p><span id="nm_ppg">'+element.total+' Documentos</span></div></div>');
                    });
                },
                complete: function(){
                    $('.top-docentes').loader('hide');
                }
            });

            $.ajax({
                url: host+'/dados/ppg/ufsc',
                type: 'GET',
                beforeSend: function() {
                    $('.painel').loader('show');
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

                    data.forEach(element => {
                        let option = new Option(element.nm_programa, element.nm_programa);
                        $('#ppg').append(option);
                    });
                },
                complete: function(){
                    $('.painel').loader('hide');
                }
            });

            $.ajax({
                url: host+'/dados/ano',
                type: 'GET',
                beforeSend: function() {
                    $('.painel').loader('show');
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

                    data.forEach(element => {
                        let option = new Option(element.an_base, element.an_base);
                        $('#ano').append(option);
                    });
                },
                complete: function(){
                    $('.painel').loader('hide');
                }
            });

            $(document).on('click', '.perfil-docente-mostrar', function() {
                
                var docente = $(this).data("docente");

                carregaDocente("Todos", docente);
                
            });

            $("#ppg").click(function(){

                var ppg = $(this).val();
                
                $.ajax({
                    url: host+'/dados/ppg/docentes/'+ppg,
                    type: 'GET',
                    beforeSend: function() {
                        $('.painel').loader('show');
                    },
                    success: function(data) {

                        $("#dados-geral").removeClass("d-none");
                        $("#perfil-docente").addClass("d-none");

                        if(!data) {
                            Swal.fire({
                                text: 'Não foi possível buscar as emissoras. Entre em contato com o suporte.',
                                type: "warning",
                                icon: "warning",
                            });
                            return;
                        }
                        
                        $('#docente').empty();
                        $('#docente').append('<option value="">Todos</option>').val('');
                        data.forEach(element => {
                            let option = new Option(element.nm_orientador, element.nm_orientador);
                            $('#docente').append(option);
                        });

                        $.ajax({
                            url: host+'/docentes/ppg/'+ppg,
                            type: 'GET',
                            beforeSend: function() {
                                $('.top-docentes').loader('show');                    
                            },
                            success: function(data) {

                                $('.lista-docentes').empty();
                                data.forEach(element => {
                                    
                                    $('.lista-docentes').append('<div class="row mt-3"><div class="col-md-2 center"><img src="'+element.foto+'" class="img-fluid rounded-circle w-100"></div><div class="col-md-10 pl-0"><p class="mb-0"><strong>'+element.nm_orientador+'</strong></p><span id="nm_ppg">'+element.total+' Documentos</span></div></div>');
                                });
                            },
                            complete: function(){
                                $('.top-docentes').loader('hide');
                            }
                        });

                        $.ajax({
                            url: host+'/dados/geral/ppg/'+ppg,
                            type: 'GET',
                            beforeSend: function() {
                                
                            },
                            success: function(data) {

                                $(".perfil-ods").empty();

                                for (let i=1; i<=17; i++)  {
                                    if(data.ods.includes(i)){
                                        $(".perfil-ods").append('<div class="col-md-2 col-sm-2 mb-2 px-1"><img src="'+host+'/img/ods-icone/ods_'+i+'.png" class="img-fluid img-ods" alt="ODS"></div>');
                                    }else{
                                        $(".perfil-ods").append('<div class="col-md-2 col-sm-2 mb-2 px-1"><img src="'+host+'/img/ods_icone_pb/ods_'+i+'.png" class="img-fluid img-ods" alt="ODS"></div>');
                                    }
                                }

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
                                    
                            }
                        });

                    },
                    complete: function(){
                        $('.painel').loader('hide');
                    }
                });

            });

            $("#docente").change(function(){

                var ppg = $("#ppg").val();
                var docente = $(this).val();

                carregaDocente(ppg, docente);        

            });

            function carregaDocente(ppg, docente){

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
                                
                            },
                            success: function(data) {
                                $(".foto-perfil").attr('src', host+'/img/docentes/'+data.foto+'.jpg');
                            },
                            complete: function(){
                                
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
                        data.forEach(element => {    
                            ods.push(element.ods);
                            totais[element.ods] = element.total;
                            cores[element.ods] = element.cor;
                            total += element.total;
                        });

                        for (let i=1; i<=17; i++)  {
                            if(ods.includes(i)){

                                var percentual = (totais[i]*100)/total;
                                var label = (totais[i] > 1) ? 'Documentos' : 'Documento';

                                $(".perfil-ods").append('<div class="col-md-2 col-sm-2 mb-2 px-1"><img src="'+host+'/img/ods-icone/ods_'+i+'.png" class="img-fluid img-ods" alt="ODS"></div>');

                                $(".lista-ods").append('<div class="row mb-2 ml-1 mr-1"><div class="col-md-2"><img src="'+host+'/img/ods-icone/ods_'+i+'.png" class="img-fluid img-ods" alt="ODS"></div>'+
                                                        '<div class="col-md-10"><h6 class="progresso-title">ODS '+i+'</h6><p>'+totais[i]+' '+label+'</p>'+
                                                        '<div class="progresso ods-'+i+'"><div class="progresso-bar" style="width:'+percentual.toFixed(1)+'%; background:'+cores[i]+';"><div class="progresso-value">'+percentual.toFixed(1)+'%</div></div></div></div></div>');

                            }else{
                                $(".perfil-ods").append('<div class="col-md-2 col-sm-2 mb-2 px-1"><img src="'+host+'/img/ods_icone_pb/ods_'+i+'.png" class="img-fluid img-ods" alt="ODS"></div>');
                            }
                            
                        }
                        $(".perfil-ods").append('<div class="col-md-2 col-sm-2 mb-2"><img src="'+host+'/img/ods-icone/ods.png" class="img-fluid img-ods" alt="ODS"></div>');
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
                                        data: [0, ((totais[1]*100)/total).toFixed(3), 0, 0, 0], 
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
                });

            }

            $(".btn-discovery").click(function(){

               $.ajax({
                  url: host+'/ods/discovery',
                  type: 'POST',
                  data: {
                        "_token": token
                  },
                  beforeSend: function() {
                     $(".ods-result").removeClass("d-none");
                     $(".ods-result").addClass("d-block");
                     $(".img-ods").empty();
                     $('.texto_ods').loader('show');
                  },
                  success: function(data) {

                     $.each(data, function(i, item) {
                        $(".img-ods").append('<div class="col-md-4 col-sm-4"><img src="'+host+'/img/ods-icone/ods_'+item.ods+'.png" class="img-fluid img-ods" alt="ODS"></div>');
                     });
                     
                  },
                  error: function(){
                     $('.texto_ods').loader('hide');
                  },
                  complete: function(){
                     $('.texto_ods').loader('hide');
                  }
               }); 
               
               $.notify({
                  icon: 'fa fa-bell',
                  message: "<b>Mensagem do Sistema</b><br/> O texto foi enviado para o servidor, aguarde o processamento."
               },{
                  type: 'info',
                  timer: 1500
               });

            });

        });
    </script>
@endsection