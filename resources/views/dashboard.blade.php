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
        <div class="row mt-3">
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

            $("#docente").click(function(){

                var ppg = $("#ppg").val();
                var docente = $(this).val();

                $.ajax({
                    url: host+'/dados/ppg/'+ppg+'/docente/'+docente+'/ods',
                    type: 'GET',
                    beforeSend: function() {
                        $('.painel').loader('show');
                        $(".perfil-ods").empty();
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
                        
                        ods = [];
                        data.forEach(element => {    
                            ods.push(element.ods);
                        });

                        for (let i=1; i<=17; i++)  {
                            if(ods.includes(i)){
                                $(".perfil-ods").append('<div class="col-md-2 col-sm-2 mb-2 px-1"><img src="'+host+'/img/ods-icone/ods_'+i+'.png" class="img-fluid img-ods" alt="ODS"></div>');
                            }else{
                                $(".perfil-ods").append('<div class="col-md-2 col-sm-2 mb-2 px-1"><img src="'+host+'/img/ods_icone_pb/ods_'+i+'.png" class="img-fluid img-ods" alt="ODS"></div>');
                            }
                        }
                        $(".perfil-ods").append('<div class="col-md-2 col-sm-2 mb-2"><img src="'+host+'/img/ods-icone/ods.png" class="img-fluid img-ods" alt="ODS"></div>');
                            //$(".perfil-ods").append('<div class="col-md-2 col-sm-2"><img src="'+host+'/img/ods-icone/ods_'+element.ods+'.png" class="img-fluid img-ods" alt="ODS"></div>');
                        $("#nm_docente").text(docente);
                        $("#nm_ppg").text(ppg);

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
                        });
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

        });
    </script>
@endsection