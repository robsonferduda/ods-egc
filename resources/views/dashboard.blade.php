@extends('layouts.guest')
@section('content')
<div class="row">
    <div class="col-md-3">
        <h6 class="mb-2"><i class="fa fa-filter"></i> Filtros</h6>
        <div class="form-group">
            <select class="form-control" name="ies" id="ies" aria-label="Default select example">
                <option>IES</option>
                <option>UFSC</option>
            </select>
        </div>
        <div class="form-group">
            <select class="form-control" name="ano" id="ano" aria-label="Default select example">
                <option>Ano</option>
            </select>
        </div>
        <div class="form-group">
            <select class="form-control" aria-label="Default select example">
                <option>Dimensão</option>
                <option>Ensino</option>
                <option>Pesquisa</option>
                <option>Extensão</option>
                <option>Inovação</option>
            </select>
        </div>
        <div class="form-group">
            <select class="form-control" name="ppg" id="ppg" aria-label="Default select example">
                <option>PPG</option>
            </select>
        </div>        
        <div class="form-group">
            <select class="form-control" name="docente" id="docente" aria-label="Default select example">
                <option>Docente</option>
            </select>
        </div>       
   </div>
   <div class="col-md-9 painel">
        <div class="row mt-3" id="dados-geral">
            <p><strong>Dimensão</strong>: Pesquisa > Distribuição de ODS</p>
            <canvas id="myChart" width="400" height="400"></canvas>
            <h6>DOCUMENTOS ANALISADOS</h6>
            <div class="mb-1" id="lista_documentos"></div>
            <a href="#" class="mb-5">VER TODOS</a>
        </div>
        <div class="row mt-3 d-none" id="perfil-docente">
            <div class="col-md-4 center">
                <img src="{{ asset('img/user.png') }}" class="img-fluid rounded-circle w-75">
            
                <h5 class="mb-0 mt-3" id="nm_docente"></h5>
                <span id="nm_ppg"></span>
            </div>
            <div class="col-md-8 mt-3">
                <canvas id="chartjs-3" class="chartjs"></canvas>
            </div>
            <div class="col-md-12 painel mt-3 mb-5">
                <div class="row perfil-ods"> 

                </div>
                
            </div>
            <div class="row lista-ods mb-5">
                
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

            $("#ppg").click(function(){

                var ppg = $(this).val();
                
                $.ajax({
                    url: host+'/dados/ppg/docentes/'+ppg,
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
                        
                        $('#docente').empty();
                        $('#docente').append('<option value="">Docente</option>').val('');
                        data.forEach(element => {
                            let option = new Option(element.nm_orientador, element.nm_orientador);
                            $('#docente').append(option);
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

                        

                        new Chart(document.getElementById("chartjs-3"), {
                            "type": "bar",
                            data: { 
                                labels: ["Ensino", "Pesquisa", "Extensão","Inovação", "Gestão"], 
                                datasets: [
                                    { 
                                        label: 'ODS 1', 
                                        backgroundColor: cores[1], 
                                        data: [0, totais[1], 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 2', 
                                        backgroundColor: cores[2], 
                                        data: [0, totais[2], 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 3', 
                                        backgroundColor: cores[3], 
                                        data: [0, totais[3], 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 4', 
                                        backgroundColor: cores[4], 
                                        data: [0, totais[4], 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 5', 
                                        backgroundColor: cores[5], 
                                        data: [0, totais[5], 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 6', 
                                        backgroundColor: cores[6], 
                                        data: [0, totais[6], 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 7', 
                                        backgroundColor: cores[7], 
                                        data: [0, totais[7], 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 8', 
                                        backgroundColor: cores[8], 
                                        data: [0, totais[8], 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 9', 
                                        backgroundColor: cores[9], 
                                        data: [0, totais[9], 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 10', 
                                        backgroundColor: cores[10], 
                                        data: [0, totais[10], 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 11', 
                                        backgroundColor: cores[11], 
                                        data: [0, totais[11], 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 12', 
                                        backgroundColor: cores[12], 
                                        data: [0, totais[12], 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 13', 
                                        backgroundColor: cores[13], 
                                        data: [0, totais[13], 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 14', 
                                        backgroundColor: cores[14], 
                                        data: [0, totais[14], 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 15', 
                                        backgroundColor: cores[15], 
                                        data: [0, totais[15], 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 16', 
                                        backgroundColor: cores[16], 
                                        data: [0, totais[16], 0, 0, 0], 
                                        stack: 'Stack 1',
                                    }, 
                                    { 
                                        label: 'ODS 17', 
                                        backgroundColor: cores[17], 
                                        data: [0, totais[17], 0, 0, 0], 
                                        stack: 'Stack 1',
                                    },                                     
                                ], 
                            }, 

                            options: { 
                                plugins: { 
                                    title: { 
                                        display: true, 
                                        text: 'Stacked Bar chart for pollution status' 
                                    }, 
                                }, 
                                legend: {
                                    display: false
                                },
                                scales: { 
                                    x: { 
                                        stacked: true, 
                                    }, 
                                    y: { 
                                        stacked: true 
                                    } 
                                } 
                            } 
                        });

                        /*
                        new Chart(document.getElementById("chartjs-3"), {
                            "type": "radar",
                            "data": {
                                "labels": ["ENSINO", "PESQUISA", "EXTENSÃO", "INOVAÇÃO","GESTÃO"],
                                "datasets": [ {
                                    "label": "Radar ODS",
                                    "data": [ 48, 40, 50, 65, 54],
                                    "fill": false,
                                    "backgroundColor": "rgba(54, 162, 235, 0.2)",
                                    "borderColor": "rgb(54, 162, 235)",
                                    "pointBackgroundColor": "rgb(54, 162, 235)",
                                    "pointBorderColor": "#fff",
                                    "pointHoverBackgroundColor": "#fff",
                                    "pointHoverBorderColor": "rgb(54, 162, 235)"
                                }]
                            },
                            "options": {
                                "elements": {
                                    "line": {
                                        "tension": 0,
                                        "borderWidth": 3
                                    }
                                },
                                legend: {
                                    display: false
                                },
                                scale: {
                                    pointLabels: {
                                    fontSize: 16
                                    }
                                }
                            }
                        });*/
                    },
                    complete: function(){
                        $('.painel').loader('hide');
                    }
                });

            });

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

            $.ajax({
                url: host+'/dados/geral',
                type: 'GET',
                beforeSend: function() {
                    
                },
                success: function(data) {

                    var ctx = document.getElementById("myChart").getContext('2d');
                    var myChart = new Chart(ctx, {
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
                            legend: {
                                display: false
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

        });
    </script>
@endsection