@extends('layouts.guest')
@section('content')
<div class="row">
    <div class="col-md-6">
    <p>Rede de Colaboração</p>
    <div id="chartdiv"></div>  
    </div>  
 </div>
@endsection
@section('script')
    <script>

        $(document).ready(function() { 

            am4core.useTheme(am4themes_animated);

            // Create chart
            var chart = am4core.create("chartdiv", am4plugins_forceDirected.ForceDirectedTree);

            // Create series
            var series = chart.series.push(new am4plugins_forceDirected.ForceDirectedSeries())

            // Set data
            series.data = [{
                "name": "Giustino Tribuzi",
                "value": 100, 
                "children": [{
                    "name": " Maria Manuela Camino Feltes", "value": 75,
                    "link": ["Marília Miotto","Silvani Verruck"]
                }]
            }, {
                "name": "Marília Miotto", 
                "value": 100,
                "children": [{
                "name": " Katia Rezzadori", "value": 25
                }]
            }, {
                "name": "Silvani Verruck", 
                "value": 100,
                "children": [{
                    "name": " Renata Dias de Mello Castanho Amboni", "value": 25
                },{
                    "name": "Juliano de Dea Lindner", "value": 25
                }]

            }];

            series.dataFields.value = "value";
            series.dataFields.name = "name";
            series.dataFields.children = "children";
            series.dataFields.id = "name";
            series.dataFields.linkWith = "link";


            series.nodes.template.label.text = "{name}";
            series.nodes.template.tooltipText = "{name}: [bold]{value}[/]";
            series.fontSize = 10;
            series.minRadius = 15;
            series.maxRadius = 40;
            series.centerStrength = 0.5;
        });

    </script>
@endsection