@extends('layouts.guest')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><strong>{{ __('2ª Via dos Certificados') }}</strong></div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <span>Informe o CPF ou Número de Passaporte</span>
                    <form method="POST" class="form-inline" action="{{ url('certificado/emitir') }}">
                        @csrf
                        <div class="form-group">
                            <input id="chave" type="text" class="form-control @error('chave') is-invalid @enderror" name="chave" value="{{ old('chave') }}" placeholder="CPF ou Passaporte" required autofocus>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-file-pdf"></i> {{ __('Emitir') }}
                            </button>
                        </div>
                    </form>
                    @include('layouts.mensagens')
                    <div>
                        @if(Session::get('dados'))

                            @if(count(Session::get('dados')) == 0)
                                <p>Não foram encontrados registros para o documento informado</p>
                            @elseif(count(Session::get('dados')) == 1)
                                <p>Foi encontrado <strong>1</strong> registro de certificado para o documento informado</p>
                            @else
                                <p>Foram encontrados <strong>{{ count(Session::get('dados')) }}</strong> registros de certificado para o documento informado</p>
                            @endif
                            
                            @foreach(Session::get('dados') as $key => $dados)

                                @if($key > 0)
                                    <hr/>
                                @endif

                                <div>
                                    <div><strong>Evento</strong>: {!! $dados->getEvento($dados->certificado->id_modelo_certificado) !!}</div>
                                    <div><strong>Emitido para</strong>: {!! $dados->nm_participante !!}</div>
                                    <div><strong>Data de liberação</strong>: {!! date('d/m/Y H:i:s', strtotime($dados->certificado->dt_inclusao)) !!}</div> 
                                    <div><a href="{{ url('certificado/gerar/'.$dados->certificado->de_hash) }}"><i class="fa fa-file-pdf-o text-danger"></i> Emitir Certificado</a></div>
                                </div>
                                
                            @endforeach
                            <br/>

                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
