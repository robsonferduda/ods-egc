@extends('layouts.guest')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><strong>{{ __('Verificação de Certificados') }}</strong></div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <span>Informe o código de autenticação do certificado no campo abaixo</span>
                    <form method="POST" class="form-inline" action="{{ url('certificado/validar') }}">
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
                    @include('layouts.mensagens')
                    <div>
                        @if(Session::get('certificado'))
                            <div><strong>Chave</strong>: {!! Session::get('certificado')->de_hash !!}</div>
                            <div><strong>Emitido para</strong>: {!! Session::get('certificado')->participante->nm_participante !!}</div>
                            <div><strong>Data de liberação</strong>: {!! date('d/m/Y H:i:s', strtotime(Session::get('certificado')->dt_inclusao)) !!}</div>                           
                            <div><strong>Descrição</strong>: {!! strip_tags(htmlspecialchars_decode(Session::get('certificado')->de_texto_certificado)) !!}</div>
                            <div><strong>Situação</strong>: <span class="badge badge-{{ Session::get('certificado')->situacao->ds_color_sit }}">{{ Session::get('certificado')->situacao->ds_situacao_sit }}</span></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
