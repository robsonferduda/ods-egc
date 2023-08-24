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
          <div class="card card-plain">
             <div class="content">
                <h5 class="mb-0">Insira o texto</h5>
                <div class="form-group mt-2">
                   <textarea rows="10" style="height: 300px !important; max-height: 800px !important;" placeholder="Insira seu texto aqui. Ele deve ter no mínimo 50 palavras e no máximo 500. Para a classificação de documentos em lote, crie uma conta e utilize as ferramentas avançadas do sistema." class="form-control"></textarea>
                </div>
                <div class="center">
                    <button type="submit" class="btn btn-fill btn-primary btn-wd"><i class="fa fa-cogs"></i> Classificar</button>
                </div>
             </div>
          </div>
       </form>
    </div>
 </div>
@endsection