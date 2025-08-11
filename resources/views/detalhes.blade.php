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
            <p class="mb-1"><strong>Centro</strong>: {{ ($documento->centro) ? $documento->centro->ds_nome_cen : 'Não Informado' }} </p>
            <p class="mb-1"><strong>Departamento</strong>: {{ ($documento->departamento) ? $documento->departamento->ds_departamento_dep.'/'.$documento->departamento->ds_sigla_dep : 'Não Informado' }} </p>
            <p class="mb-1"><strong>Dimensão Institucional</strong>: {{ $documento->dimensao->nome }} </p>
            <p class="mb-1"><strong>Dimensão ODS</strong>: {{ $documento->dimensaoOds->ds_dimensao }} </p>
            <p class="mb-1"><strong>Tipo do Documento</strong>: {{ $documento->tipo->ds_tipo_documento }} </p>            
            <p class="mb-1"><strong>Título</strong>: {{ $documento->titulo }} </p>  

            @php
              // Mapeamento de rótulos por id_funcao_fun
              $rotulos = [
                1 => 'Orientador',
                2 => 'Aluno',
                3 => 'Coordenador',
                4 => 'Inventor',
                5 => 'Participante',
              ];

              // Iniciais do vínculo (vêm de pessoa_pes -> vinculo_vin.ds_vinculo_vin)
              $iniciaisVinculo = [
                'Docente'        => 'D',
                'Discente'       => 'Di',
                'Administrativo' => 'A',
                'Externo'        => 'E',
              ];

              // Agrupar por função para renderizar por blocos
              $porFuncao = $participantes->groupBy('id_funcao_fun');

              /**
               * Renderiza uma <ul> de pessoas por função, colocando iniciais de vínculo
               * apenas quando a função for "Participante" (id 5).
               */
              function listaPorFuncaoComIniciais($porFuncao, $idFuncao, $rotulos, $iniciaisVinculo) {
                  if (!isset($porFuncao[$idFuncao]) || $porFuncao[$idFuncao]->isEmpty()) return '';

                  $html = '<h6 class="mt-3">'.e($rotulos[$idFuncao]).'</h6><ul>';
                  foreach ($porFuncao[$idFuncao] as $p) {
                      $nome  = e($p->ds_nome_pessoa);
                      // Se for Participante (id 5), anexa a inicial do vínculo, se houver
                      if ((int)$idFuncao === 5) {
                          $sigla = isset($p->ds_vinculo_vin) && isset($iniciaisVinculo[$p->ds_vinculo_vin])
                              ? $iniciaisVinculo[$p->ds_vinculo_vin]
                              : null;
                          $html .= '<li>'.$nome.($sigla ? ' ('.e($sigla).')' : '').'</li>';
                      } else {
                          $html .= '<li>'.$nome.'</li>';
                      }
                  }
                  $html .= '</ul>';
                  return $html;
              }
            @endphp

            @switch($documento->id_tipo_documento)
              @case(1) {{-- Tese --}}
              @case(2) {{-- Dissertação --}}
                {!! listaPorFuncaoComIniciais($porFuncao, 1, $rotulos, $iniciaisVinculo) !!} {{-- Orientador --}}
                {!! listaPorFuncaoComIniciais($porFuncao, 2, $rotulos, $iniciaisVinculo) !!} {{-- Aluno --}}
                @break

              @case(4) {{-- Projeto de Extensão --}}
                {!! listaPorFuncaoComIniciais($porFuncao, 3, $rotulos, $iniciaisVinculo) !!} {{-- Coordenador --}}
                {!! listaPorFuncaoComIniciais($porFuncao, 5, $rotulos, $iniciaisVinculo) !!} {{-- Participante (com iniciais) --}}
                @break

              @case(5) {{-- Patente --}}
                {!! listaPorFuncaoComIniciais($porFuncao, 4, $rotulos, $iniciaisVinculo) !!} {{-- Inventor --}}
                @break

              @default
                {{-- Fallback: mostra tudo que houver, participantes com iniciais --}}
                @foreach($porFuncao as $idFunc => $colecao)
                  {!! listaPorFuncaoComIniciais($porFuncao, $idFunc, $rotulos, $iniciaisVinculo) !!}
                @endforeach
            @endswitch

            <p class="mb-1"><strong>Conteúdo</strong></p>
            <div class="documento-conteudo">{{ ucfirst(mb_strtolower($documento->texto, 'UTF-8')) }} </div>
            <p class="mb-1 mt-2"><strong>Índice de Shannon</strong>: {{ $documento->probabilidades->shannon }}</p>
            <p class="mb-1"><strong>Índice de Gini</strong>: {{ $documento->probabilidades->gini }}</p>
            <table class="table table-bordered" id="tabela-ods">
              <thead>
                <tr>
                  @for ($i = 1; $i <= 16; $i++)
                    <th class="center">ODS {{ $i }}</th>
                  @endfor
                </tr>
              </thead>
              <tbody>
                <tr>
                 @for ($i = 1; $i <= 16; $i++)
                  @php
                     $valor = $documento->probabilidades->{'probabilidade_ods_' . $i};
                     $ods[$i] = $valor;
                  @endphp
                  <td class="center" data-valor="{{ $valor }}">
                     {{ number_format($valor * 100, 2, ',', '.') }}%
                  </td>
                  @endfor
                </tr>
              </tbody>
            </table>
            <div class="row ods-result d-none mt-5">
                <div class="col-md-12 col-sm-12">
                   <h6>ODS Predominates</h6>
                </div>
             </div>
             <div class="row img-ods">

               @php
                  // Encontra o índice do maior valor (ODS)
                  $maxIndex = 1;
                  $maxValor = $ods[1] ?? 0;
                  for ($i = 2; $i <= 16; $i++) {
                     if (($ods[$i] ?? 0) > $maxValor) {
                           $maxValor = $ods[$i];
                           $maxIndex = $i;
                     }
                  }
               @endphp

               <div class="col-md-2 col-sm-3">
                  <img src="{{ asset('img/ods-icone/ods_'.$maxIndex.'.png') }}" class="img-fluid img-ods ods-predominante" alt="ODS {{ $maxIndex }}">
               </div>

               @for ($i = 1; $i <= 16; $i++)
                  <div class="col-md-1 col-sm-3">
                     @if ($i == $maxIndex)
                        <img src="{{ asset('img/ods-icone/ods_'.$i.'.png') }}" class="img-fluid img-ods ods-predominante" alt="ODS {{ $i }}">
                     @else
                        <img src="{{ asset('img/ods_icone_pb/ods_'.$i.'.png') }}" class="img-fluid img-ods ods-nao-predominante" alt="ODS {{ $i }}">  
                     @endif      
                     <p class="center">{{ number_format($ods[$i] * 100, 2, ',', '.') }}%</p>
                  </div>
               @endfor                  
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

          
               const cells = document.querySelectorAll("#tabela-ods tbody tr td");
               const min = 0.026;
               const max = 0.9;

               cells.forEach(cell => {
                  const valor = parseFloat(cell.getAttribute("data-valor"));

                  // normaliza o valor para [0, 1]
                  const peso = (valor - min) / (max - min);
                  const cor = calcularCor(peso);

                  console.log(`Valor: ${valor}, Peso: ${peso}, Cor: ${cor}`);

                  // aplica a cor ao fundo da célula                  
                  cell.style.backgroundColor = cor;
                  cell.style.color = peso > 0.5 ? "white" : "black"; // contraste dinâmico
               });

               function calcularCor(peso) {
                 // de azul claro (#B2EBF2 → rgb(178, 235, 242))
                 // até azul petróleo (#004D40 → rgb(0, 77, 64))

                 const r = Math.round(178 - peso * (178 - 0));   // 178 → 0
                 const g = Math.round(235 - peso * (235 - 77));  // 235 → 77
                 const b = Math.round(242 - peso * (242 - 64));  // 242 → 64

                 return `rgb(${r}, ${g}, ${b})`;
               }
            

        

         /*
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

            });*/

        });
    </script>
@endsection