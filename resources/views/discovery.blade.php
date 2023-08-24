@extends('layouts.guest')
@section('content')
<div class="row">
    <div class="col-md-12">
       <div class="header-text">
          <h3><i class="fa fa-files-o"></i> ODS EGC Discovery</h3>
          <h5 class="mb-0">Classidicação de ODS em Textos</h5>
          <p>Informe seu texto para classificação</p>
          <hr>
       </div>
    </div>
    <div class="col-md-12">
       <form method="POST" action="{{ url('ods/discovery') }}">
        @csrf
         
         <div class="content">
                <h5 class="mb-0">Insira o texto</h5>
                <div class="form-group mt-2 texto_ods">
                   <textarea rows="10" style="height: 300px !important; max-height: 800px !important;" placeholder="Insira seu texto aqui. Ele deve ter no mínimo 50 palavras e no máximo 500. Para a classificação de documentos em lote, crie uma conta e utilize as ferramentas avançadas do sistema." class="form-control texto_ods"></textarea>
                </div>
                <div class="center">
                    <button type="button" class="btn btn-fill btn-primary btn-wd btn-discovery"><i class="fa fa-cogs"></i> Classificar</button>
                </div>
                
            <div class="row">
                  <div class="col-md-2 col-sm-2">
                     <div class="">
                         <img src="http://ods-egc.localhost/public/img/ods-icone/ods_03.png" class="img-fluid img-ods" alt="ODS">
                     </div>
                  </div>
                  <div class="col-md-10 col-sm-10">
                     <h5>Palavras-chave</h5>
                     <p>ODS </p>
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

            $(".btn-discovery").click(function(){

               $.ajax({
                  url: host+'/ods/discovery',
                  type: 'POST',
                  data: {
                        "_token": token
                  },
                  beforeSend: function() {
                     $('.texto_ods').loader('show');
                  },
                  success: function(response) {

                     $(".img-ods").attr('src','http://ods-egc.localhost/public/img/ods-icone/ods_0'+response.ods+'.png');
                     
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