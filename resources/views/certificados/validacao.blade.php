@extends('layouts.guest')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><strong>{{ __('Validação de Certificados') }}</strong></div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div style="margin-bottom: 10px;"><span>Informe o código de autenticação do certificado no campo abaixo</span></div>
                    @include('layouts.mensagens')
                    <form style="margin-top: -8px;" method="POST" class="form-inline" action="{{ url('certificados/validacao') }}">
                        @csrf
                        <div class="form-group">
                            <input id="chave" type="text" class="form-control @error('chave') is-invalid @enderror" name="chave" value="{{ old('chave') }}" placeholder="Chave de validação" required autofocus>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-check"></i> {{ __('Validar') }}
                            </button>
                        </div>
                    </form>                    
                    <div>
                        @if(Session::get('certificado'))
                            <div><strong>Chave</strong>: {!! Session::get('certificado')->ds_hash_cer !!}</div>
                            <div><strong>Evento</strong>: {{ Session::get('certificado')->modelo->evento->nm_evento_eve }}</div>
                            <div><strong>Emitido para</strong>: {!! Session::get('certificado')->participante->ds_nome_par !!}</div>
                            <div><strong>Data de liberação</strong>: {!! date('d/m/Y H:i:s', strtotime(Session::get('certificado')->created_at)) !!}</div>  
                            <div><strong>Tipo do certificado</strong>: {!! Session::get('certificado')->modelo->tipo->ds_tipo_participacao_tip !!}</div>   
                            @if(Session::get('certificado')->modelo->tipo->id_tipo_participacao_tip == 4)
                                <div><strong>Tipo do trabalho</strong>: {{ Session::get('certificado')->metadados->where('label_metadado_cem','#titulo')->first()->valor_metadado_cem }}</div> 
                            @endif                      
                            <div><strong>Situação</strong>: <span class="badge badge-{{ Session::get('certificado')->situacao->ds_color_sit }}">{{ Session::get('certificado')->situacao->ds_situacao_sit }}</span></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
