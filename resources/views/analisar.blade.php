@extends('layouts.guest')
@section('content')
<div class="row">
    <div class="col-md-12">
       <div class="header-text">
         <div class="header-text">
            <h3>
                {{ config('app.name') }} <i class="fa fa-angle-double-right" aria-hidden="true"></i> Analisar Documentos
                @include('layouts.nivel')
            </h3>
            <div class="cabecalho">
               <h5 class="mb-0">Classificação de Textos de acordo com os ODS</h5>
               <p>Informe seu texto para classificação automática</p>
            </div>
        </div>
       </div>
    </div>  
    @if(Auth::user())
         <div class="col-md-12 mb-3">
            @include('layouts/menu-logado')
         </div>
    @endif
    <div class="col-md-12">
            
      @include('layouts.mensagens')
       <form method="POST" if="frmODS" action="{{ url('ods/descobrir/salvar') }}">
        @csrf
         
         <div class="content">
            <h5 class="mb-0">Insira o texto para análise</h5>
            @if(!Auth::user())
               <p class="text-danger mb-0 mt-0">Faça seu cadastro <a href="{{ url('colaborar') }}" class="text-danger fw-bold" style="font-weight: bold;">aqui</a> para classificar e salvar suas classificações para uso posterior.</p>
            @endif
            <div class="form-group mt-2">
               <label class="mb-1"><strong>Selecione um Modelo de Classificação</strong></label>
               <select class="form-control mt-0" name="modelo" id="modelo">
                  <option value="ods-roberta" >ODS-RoBERTa</option>
               </select>
            </div>
                                
            <div class="form-group mt-2 texto_ods">
               <textarea name="texto" id="texto_ods" rows="10" style="height: 300px !important; max-height: 800px !important;" placeholder="Insira seu texto aqui. Ele deve ter no mínimo 50 palavras e no máximo 500. Para a classificação de documentos em lote, crie uma conta e utilize as ferramentas avançadas do sistema." class="form-control texto_ods" required="required"></textarea>
            </div>

            <div class="row">
               <div class="col-md-12 col-sm-12 resultado">
                  
               </div>
            </div>
            <div class="row">
               <div class="col-md-12 col-sm-12">
                  @if(Auth::user() and Auth::user()->nivel->id < 3)
                     <p class="mb-1 text-center">Realize a avaliação de textos já classificados, na opção "<a href="{{ url('classificar') }}">Colaborar</a>" para ganhar pontos, subir de nível e ter permissão para salvar suas análises.</p>
                  @endif
               </div>
            </div>
            <div class="center">
               <button type="button" class="btn btn-fill btn-primary btn-wd btn-discovery"><i class="fa fa-cogs"></i> Classificar</button>
               @if(Auth::user())
                  @if(Auth::user()->nivel->id >= 3)
                     <button type="submit" class="btn btn-fill btn-success btn-wd btn-salvar"><i class="fa fa-save"></i> Salvar Classificação</button>
                  @else
                     <button type="button" class="btn btn-fill btn-success btn-wd" disabled="disabled"><i class="fa fa-save"></i> Salvar Classificação</button>
                  @endif
               @endif
            </div>
            <div class="row ods-result d-none">
               <div class="col-md-12 col-sm-12">
                  <h6>ODS Identificados</h6>
               </div>
            </div>
            <div class="row img-ods">

            </div>
            <div class="row">
               <div class="col-md-12 col-sm-12 mt-3">
                  <h6 class="label-info d-none">Texto Analisado</h6>
                  <p id="mytext"></p>
               </div>
            </div>
            <div class="row">
               <div class="col-md-12 mt-3 mb-5">
                  <h6 class="label-info d-none">Distribuição das probabilidades de relação por ODS</h6>
                  <canvas id="resultado-ods" class="chartjs"></canvas>
               </div>
            </div>
         </div>         
          
       </form>
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

         $(".btn-salvar").click(function() {
            $('.texto_ods').loader('show');
         })

            $(".btn-discovery").click(function(){

               $.ajax({
                  url: host+'/ods/descobrir',
                  type: 'POST',
                  data: {
                        "_token": token,
                        "texto": $("#texto_ods").val()
                  },
                  beforeSend: function() {
                    $(".img-ods").empty();
                    $('.texto_ods').loader('show');
                    
                    $(".ods-result").removeClass("d-block");
                    $(".ods-result").addClass("d-none");

                    $(".label-info").addClass("d-none");

                    $("#mytext").empty();

                    let Grafico = null;
                           let graphareaGrafico = document.getElementById("resultado-ods").getContext("2d");
                           let chartStatusGrafico = Chart.getChart("resultado-ods"); // <canvas> id
                                       
                           if (chartStatusGrafico != undefined) {
                              chartStatusGrafico.destroy();
                           }

                  },
                  success: function(data) {

                     $(".resultado").html("");

                     if(data.resultado.length){

                        var cores = ["#FF3A21","#FF3A21","#FF3A21","#FF3A21","#FF3A21","#FF3A21","#FF3A21","#FF3A21","#FF3A21","#FF3A21","#FF3A21","#FF3A21","#FF3A21","#FF3A21","#FF3A21","#FF3A21","#FF3A21"];

                        $(".ods-result").removeClass("d-none");
                        $(".ods-result").addClass("d-block");

                        

                         $(".label-info").removeClass("d-none");

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

                           let Grafico = null;
                           let graphareaGrafico = document.getElementById("resultado-ods").getContext("2d");
                           let chartStatusGrafico = Chart.getChart("resultado-ods"); // <canvas> id
                                       
                           if (chartStatusGrafico != undefined) {
                              chartStatusGrafico.destroy();
                           }

                           new Chart(document.getElementById("resultado-ods"), {
                                 "type": "bar",
                                    "data": {
                                          "labels": ["ODS 1", "ODS 2", "ODS 3", "ODS 4","ODS 5","ODS 6","ODS 7","ODS 8","ODS 9","ODS 10","ODS 11","ODS 12","ODS 13","ODS 14","ODS 15","ODS 16","ODS 17"],
                                          "datasets": [{
                                                         label: false,
                                                         data: percentuais,
                                                         backgroundColor: ["#e5243b","#DDA63A","#4C9F38","#C5192D","#FF3A21","#26BDE2","#FCC30B","#A21942","#FD6925","#DD1367","#FD9D24","#BF8B2E","#3F7E44","#0A97D9","#56C02B","#00689D","#19486A"]
                                                      }]
                                    },
                                    "options": {   
                                       
                                       plugins: { 
                                        title: { 
                                            display: false, 
                                            text: 'Distribuição de total por dimensão' 
                                        }, 
                                        legend: {
                                            display: false
                                        }
                                    },

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