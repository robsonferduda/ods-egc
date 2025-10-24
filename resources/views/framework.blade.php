@extends('layouts.guest')
@section('content')
<div class="row mb-5">
    <div class="row mb-5 mt-3">
        <div class="col-md-2 col-sm-2">
            <img src="{{ asset('img/logo-egc.png') }}" class="img-fluid mt-1" alt="Logo EGC">
        </div>
        <div class="col-md-10 col-sm-10">
            <h6 class="mb-0">Universidade Federal de Santa catarina (UFSC)</h6>
            <h6 class="mb-0 mt-1">Programa de Pós-graduação em Engenharia, Gestão e Mídia do Conhecimento (PPGEGC)</h6>
            <h6 class="mb-0 mt-1">Engenharia do Conhecimento/Teoria e prática em Engenharia do Conhecimento</h6>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <p class="mb-3 center"><strong>AI4SDG-Gov: Framework de apoio a governança dos ODS em IES baseado em Inteligência Artificial e Gestão do Conhecimento</strong></p> 
            <p class="mb-1"><strong>Programa de Pós-Graduação em Engenharia, Gestão e Mídia do Conhecimento</strong></p>
            <p class="mb-1"><strong>Área de Concentração</strong>: Engenharia do Conhecimento</p>
            <p class="mb-1"><strong>Linha de Pesquisa</strong>: Teoria e prática em Engenharia do Conhecimento </p>
            <p class="mb-1"><strong>Nível</strong>: Doutorado </p>
            <p class="mb-1"><strong>Aluno</strong>: Robson Fernando Duda </p>
            <p class="mb-1"><strong>Orientador</strong>: Prof. Dr. Fernando Álvaro Ostuni Gauthier </p>
            <p class="mb-1"><strong>Coorientador</strong>: Prof. Dr. Roberto Carlos dos Santos Pacheco </p>  
            <p class="mb-1 mt-3">
                <strong>AI4SDG-GOV</strong> é um assistente de apoio a identificação da aderência das instituições de ensino superior (IES) aos Objetivos de Desenvolvimento Sustentável (ODS), a partir da análise da sua base documental. É uma ferramenta resultante de uma pesquisa de doutorado em andamento que tem como objetivo o mapeamento do processo de identificação da aderência de objetivos ODS com documentos de natureza heterogênea, produzidos pelas instituições de ensino superior em suas atividades de ensino, pesquisa, extensão, gestão e inovação.
            </p>                      
        </div>
    </div> 
    <div class="row mb-5">
        <div class="col-md-12 col-sm-12"> 
            <h6 class="center mt-3">Representação Conceitual</h6> 
            <p class="center text-danger">Etapas do framework e sua relação com a Governança dos ODS e da Gestão do Conhecimento</p>
            <img src="{{ asset('img/framework.png') }}" class="img-fluid mt-1" alt="Framework">  
            <h6 class="center mt-3">Processos</h6>  
            <p class="center text-danger">Apresentação das etapas e duas definições e objetivos</p>
            <div>
                <p class="mb-1 mt-3">
                    <strong>Modelagem</strong>
                    mapeamento e reconhecimento das fontes de conhecimento relevantes e representação do contexto universitário por meio de modelos conceituais.
                </p> 
            </div>    
            <div>
                <p class="mb-1 mt-3">
                    <strong>Análise</strong> 
                    utilização de métodos e técnicas de inteligência artificial para análise dos documentos institucionais e identificação da aderência aos ODS.
                </p> 
            </div>  
            <div>
                <p class="mb-1 mt-3">
                    <strong>Avaliação</strong>
                    produção de indicadores a partir da análise de dados institucionais, revelando o impacto da universidade sobre os ODS. 
                </p> 
            </div>   
            <div>
                <p class="mb-1 mt-3">
                    <strong>Participação</strong> 
                    intereração entre os diversos atores institucionais para validação dos resultados e tomada de decisão. Neste processo, entra o conceito de gamificação para engajamento dos atores.
                </p> 
            </div>   
            <div>
                <p class="mb-1 mt-3">
                    <strong>Acompanhamento</strong> 
                    aplicação do conhecimento produzido para monitoramento contínuo dos ODS na instituição, permitindo sua utilização estratégica no planejamento, nas políticas universitárias e no engajamento com a sociedade.
                </p> 
            </div>  
            <div>
                <p class="mb-1 mt-3">
                    <strong>Compartilhamento</strong>
                    compartilhar resultados por meio de repositórios abertos, relatórios e painéis de visualização para apoiar a transparência e prestação de contas.
                </p> 
            </div> 
            <div class="center">
                <h6 class="mt-3">Arquitetura do Framework</h6>
                <p class="text-danger">Representação das etapas e processos que estruturam o framework</p>
                <img src="{{ asset('img/arquitetura.png') }}" class="img-fluid mt-1" alt="Arquitetura do Framework"> 
            </div>       
        </div>
    </div>  
</div>
@endsection