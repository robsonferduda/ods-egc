@extends('layouts.guest')
@section('content')
<div class="row">
    <div class="col-md-12">
       <div class="header-text">
          <h3><i class="fa fa-files-o"></i> ODS EGC Descobrir</h3>
          <div class="cabecalho">
            <h5 class="mb-0">Classificação de Textos de acordo com a ODS</h5>
            <p>Informe seu texto para classificação</p>
          </div>
       </div>
    </div>  

    <div class="col-md-12">
       <form method="POST" action="{{ url('ods/discovery') }}">
        @csrf
         
         <div class="content">
            <h5 class="mb-0">Insira o texto para análise</h5>
            <div class="form-group mt-2 texto_ods">
               <textarea id="texto_ods" rows="10" style="height: 300px !important; max-height: 800px !important;" placeholder="Insira seu texto aqui. Ele deve ter no mínimo 50 palavras e no máximo 500. Para a classificação de documentos em lote, crie uma conta e utilize as ferramentas avançadas do sistema." class="form-control texto_ods"></textarea>
            </div>
            <div class="row">
               <div class="col-md-12 col-sm-12 resultado">
                  
               </div>
            </div>
            <div class="center">
               <button type="button" class="btn btn-fill btn-primary btn-wd btn-discovery"><i class="fa fa-cogs"></i> Classificar</button>
            </div>
            <div class="row ods-result d-none">
               <div class="col-md-12 col-sm-12">
                  <h6>ODS Identificadas</h6>
               </div>
            </div>
            <div class="row img-ods">

            </div>
            <div class="row">
               <div class="col-md-12 col-sm-12 mt-3">
                  <p id="mytext"></p>
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
                  },
                  success: function(data) {

                     $(".resultado").html("");

                     if(data.length){

                        $(".ods-result").removeClass("d-none");
                        $(".ods-result").addClass("d-block");

                        $.each(data, function(i, item) {
                              $(".img-ods").append('<div class="col-md-2 col-sm-12"><img src="'+host+'/img/ods-icone/ods_'+item.ods+'.png" class="img-fluid img-ods" alt="ODS '+item.ods+'"></div>');
                        });

                        $("#mytext").text($("#texto_ods").val());

                           var context = document.querySelector("#mytext"); 

                           $.each(data, function(i, item) {

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

                              instance_ods.mark(lista_ods[item.ods], options); 

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