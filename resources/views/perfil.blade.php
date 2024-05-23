@extends('layouts.guest')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-plain">
            <div class="content">
                <div class="row">
                    <div class="col-md-6 col-sm-6 mt-3">
                        <canvas id="myChart" width="200" height="200"></canvas>   
                        <canvas id="chartjs-3" class="chartjs" width="undefined" height="undefined"></canvas>
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

            var token = $('meta[name="csrf-token"]').attr('content');
            var host =  $('meta[name="base-url"]').attr('content');

            alert("host");

            $.ajax({
                url: host+'/api/emissora/buscarEmissoras',
                type: 'GET',
                beforeSend: function() {
                    $('.content').loader('show');
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
                        let option = new Option(element.text, element.id);
                        $('#emissora').append(option);
                    });
                },
                complete: function(){
                    $('.content').loader('hide');
                }
            });

            new Chart(document.getElementById("chartjs-3"), {
                "type": "radar",
                "data": {
                    "labels": ["Ensino", "Pesquisa", "Extensão", "Inovação"],
                    "datasets": [ {
                        "label": "Dimensões",
                        "data": [ 48, 40, 35, 100],
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
                    }
                }
            });

                               
        });
    </script>
@endsection