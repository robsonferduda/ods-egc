@extends('layouts.guest')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><strong>{{ __('Segunda Via dos Certificados') }}</strong></div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <span>Informe o email utilizado para cadastro no evento</span>
                    <form method="POST" class="form-inline" action="{{ url('certificados/segunda-via') }}">
                        @csrf
                        <div class="form-group">
                            <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="Email" required autofocus>
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
                                <p>Não foram encontrados certificados para o email informado</p>
                            @elseif(count(Session::get('dados')) == 1)
                                <p>Foi encontrado <strong>1</strong> registro de certificado para o documento informado</p>
                            @else
                                <p>Foram encontrados <strong>{{ count(Session::get('dados')) }}</strong> registros de certificado para o documento informado</p>
                            @endif
                            
                            @foreach(Session::get('dados') as $key => $certificado)

                                @if($key > 0)
                                    <hr/>
                                @endif

                                <div>
                                    <div><strong>Evento</strong>: {{ $certificado->modelo->evento->nm_evento_eve }}</div>
                                    <div><strong>Emitido para</strong>: {!! $certificado->participante->ds_nome_par !!}</div>
                                    <div><strong>Data de liberação</strong>: {{ date('d/m/Y H:i:s', strtotime($certificado->created_at))  }}</div>  
                                    <div><strong>Tipo do certificado</strong>: {!! $certificado->modelo->tipo->ds_tipo_participacao_tip !!}</div> 
                                    @if($certificado->modelo->tipo->id_tipo_participacao_tip == 4)

                                        <div><strong>Tipo do trabalho</strong>: {{ $certificado->metadados->where('label_metadado_cem','#titulo')->first()->valor_metadado_cem }}</div> 

                                    @endif
                                    <div><a href="{{ url('certificados/gerar/'.$certificado->ds_hash_cer) }}"><i class="fa fa-file-pdf-o text-danger"></i> Emitir Certificado</a></div>
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
