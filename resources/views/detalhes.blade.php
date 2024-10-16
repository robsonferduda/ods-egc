@extends('layouts.guest')
@section('content')
<div class="row">
    <div class="row mb-5">
        <div class="col-md-2 col-sm-2">
            <img src="{{ asset('img/logo-egc.png') }}" class="img-fluid" alt="Responsive image">
        </div>
        <div class="col-md-10 col-sm-10">
            <h6>Universidade Federal de Santa catarina (UFSC)</h6>
            <h6>Programa de Pós-graduação em Engenharia, Gestão e Mídia do Conhecimento (PPGEGC)</h6>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-sm-12 mt-3">
            <h6 class="mb-1"><strong>Detalhes do Documento Analisado</strong></h6>

            <p class="mb-1"><strong>Dimensão</strong>: {{ ($documento->dimensao == 1) ? 'Pesquisa' : 'Extensão' }} </p>
            <p class="mb-1"><strong>Natureza do Documento</strong>: {{ $documento->complemento }} </p>

            @if($documento->dimensao == 1)
               <p class="mb-1"><strong>Programa de Pós-Graduação</strong>: {{ $documento->nm_programa }} </p> 
               <p class="mb-1"><strong>Autor</strong>: {{ $documento->nm_discente }} </p> 
               <p class="mb-1"><strong>Orientador</strong>: {{ $documento->nm_orientador }} </p> 
            @endif
            @if($documento->dimensao == 2)
               <p class="mb-1"><strong>Coordenador</strong>: {{ $documento->coordenador }} </p> 
            @endif
            
            <p class="mb-1"><strong>Título</strong>: {{ $documento->titulo }} </p>           
            <p class="mb-1"><strong>Conteúdo</strong></p>
            <div class="documento-conteudo">{{ ucfirst(mb_strtolower($documento->resumo, 'UTF-8')) }} </div>

            <div class="row ods-result d-none mt-5">
                <div class="col-md-12 col-sm-12">
                   <h6>ODS Identificados</h6>
                </div>
             </div>
             <div class="row img-ods">
 
             </div>
             <div class="row">
                <div class="col-md-12 mt-3 mb-5">
                   <canvas id="resultado-ods" class="chartjs"></canvas>
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
            
            var lista_ods = [
               [],
               ["pobreza","pobre","privação","criança","renda","doméstico","social","proteção social","os pobres","família","vivendo","pensão","proteção","transferir","beneficiar"],
               ["comida","agrícola","agricultor","preço","fazenda","agricultura","arroz","cortar","Produção","gado","mercadoria","produtor","comida segura","importar"],
               ["saúde","Cuidado","paciente","hospital","medicamento","doença","mortalidade","médico","álcool","morte","tratamento","mental","da Saúde","Câncer","farmacêutico"],
               ["Educação","escola","estudante","professor","aprendizado","habilidade","ensino","ensino superior","universidade","educacional","ecec","currículo","pisa","criança","sistema de educação"],
               ["mulher","gênero","fêmea","garota","homens","família","violência","de mulher","casado","igualdade","certo","igualdade de gênero","dela","trabalhar","masculino"],
               ["água","águas subterrâneas","rio","águas residuais","irrigação","bacia","poluição","aquífero","recursos hídricos","saneamento","qualidade da água","gestão","abastecimento de água"],
               ["energia","eletricidade","poder","tecnologia","vento","gás","combustível","custo","grade","plantar","renovável","solar","nuclear","geração","capacidade"],
               ["emprego","trabalho","trabalhador","trabalho","juventude","turismo","desemprego","trabalhar","treinamento","jovem","empreendimento","crescimento","trabalhando","economia","empregador"],
               ["inovação","infraestrutura","tecnologia","banda larga","Internet","digital","TIC","blockchain","móvel","rede","plataforma","ainda","terceiro","empresa","pesquisa"],
               ["desigualdade","renda","trabalhador","beneficiar","imposto","social","remuneração","trabalho","troca","país","distribuição","remessa","desemprego","redistribuição","transferir"],
               ["cidade","urbano","habitação","transporte","estrada","planejamento","tráfego","local","veículo","município","plano","transporte público","espacial","governo","terra"],
               ["desperdício","reciclando","material","produtos","químico","lixo eletrônico","ambiental","consumo","sustentável","empresa","recurso","eficiência de recursos","coleção","Produção","consumidor"],
               ["clima","adaptação","emissão","finança","das Alterações Climáticas","ah","mitigação","verde","financiamento climático","mudar","desastre","perigo","Etiópia","relacionado ao clima","temperatura"],
               ["pesca","marinho","peixe","pescaria","oceano","aquicultura","pegar","navio","mar","pescador","espécie","costeiro","estoque","gerenciamento","frutos do mar"],
               ["floresta","biodiversidade","espécie","ecossistema","área","protegido","área protegida","silvicultura","conservação","madeira","terra","habitat","serviço ecossistêmico","paisagem","desmatamento"],
               ["lei","artigo","certo","político","corrupção","Este artigo","tribunal","público","governança","internacional","direito humano","justiça","humano","responsabilidade"],
               []
        ];

        jQuery(document).ready(function(){

               $.ajax({
                  url: host+'/ods/descobrir',
                  type: 'POST',
                  data: {
                        "_token": token,
                        "texto": $(".documento-conteudo").text()
                  },
                  beforeSend: function() {
                    $(".img-ods").empty();
                    $('.texto_ods').loader('show');
                    
                    $(".ods-result").removeClass("d-block");
                    $(".ods-result").addClass("d-none");

                    $('.ods-result').loader('show');
                    $('.img-ods').loader('show');
                  },
                  success: function(data) {

                     $(".resultado").html("");

                     if(data.resultado.length){

                        var cores = ["#FF3A21","#FF3A21","#FF3A21","#FF3A21","#FF3A21","#FF3A21","#FF3A21","#FF3A21","#FF3A21","#FF3A21","#FF3A21","#FF3A21","#FF3A21","#FF3A21","#FF3A21","#FF3A21","#FF3A21"];

                        $(".ods-result").removeClass("d-none");
                        $(".ods-result").addClass("d-block");

                        $.each(data.resultado, function(i, item) {
                              $(".img-ods").append('<div class="col-md-2 col-sm-12"><img src="'+host+'/img/ods-icone/ods_'+item.ods+'.png" class="img-fluid img-ods" alt="ODS '+item.ods+'"><p class="result-proba">'+Math.trunc(item.probabilidade*100)+'%</p></div>');
                        });

                        $("#mytext").text($("#texto_ods").val());

                           var context = document.querySelector("#mytext"); 

                           $.each(data.resultado, function(i, item) {

                              var instance_ods = new Mark(context);
                              var marcador = "mark"+item.ods;

                              var options = {
                                 "element": marcador,
                                 "separateWordSearch": false,
                                 "accuracy": {
                                    "value": "exactly",
                                    "limiters": [",", "."]
                                 },
                                 "diacritics": true
                              };

                              //instance_ods.mark(lista_ods[item.ods], options); 

                           });

                           var percentuais = [];
                           $.each(data.probabilidades, function(i, item) {
                              percentuais.push(Math.trunc(item.probabilidade*100));
                           });

                           new Chart(document.getElementById("resultado-ods"), {
                           "type": "bar",
                              "data": {
                                    "labels": ["ODS 1", "ODS 2", "ODS 3", "ODS 4","ODS 5","ODS 6","ODS 7","ODS 8","ODS 9","ODS 10","ODS 11","ODS 12","ODS 13","ODS 14","ODS 15","ODS 16","ODS 17"],
                                    "datasets": [{
                                                   "label": "Distribuição da probabilidade de relação com os ODS",
                                                   "data": percentuais,
                                                   "backgroundColor": ["#e5243b","#DDA63A","#4C9F38","#C5192D","#FF3A21","#26BDE2","#FCC30B","#A21942","#FD6925","#DD1367","#FD9D24","#BF8B2E","#3F7E44","#0A97D9","#56C02B","#00689D","#19486A"]
                                                }]
                              },
                              "options": {                                
                                 legend: {
                                    display: false
                                 }
                              }      
                           });

                     }else{

                        $(".resultado").html('<p class="center text-danger">Não foram encontrados ODS no texto informado.</p>');

                     }
                     
                  },
                  error: function(){
                     $('.texto_ods').loader('hide');
                     $('.ods-result').loader('hide');
                     $('.img-ods').loader('hide');
                  },
                  complete: function(){
                     $('.texto_ods').loader('hide');
                     $('.ods-result').loader('hide');
                     $('.img-ods').loader('hide');
                  }
               }); 

            });

        });
    </script>
@endsection