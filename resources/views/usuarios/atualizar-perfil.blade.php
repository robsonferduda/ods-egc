@extends('layouts.guest')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="header-text">
            <h3>Perfil ODS <i class="fa fa-angle-double-right" aria-hidden="true"></i> Meu Perfil</h3>
        </div>
    </div>  
    <div class="col-md-12">
        <p class="mb-2">Olá <strong>{{ Auth::user()->name }}</strong>! O que deseja fazer hoje?</p>
    </div>
    <div class="col-md-12 mb-3">
        @include('layouts/menu-logado')
    </div>
    <div class="col-md-12">
        @if(Auth::user()->dt_nascimento == null and Auth::user()->sexo == null)
            <p class="mb-2 mt-0"><strong class="text-danger">Perfil Incompleto</strong><a href="{{ url('perfil/atualizar') }}"> Clique aqui</a> para atualizar e subir de nível</p>
        @endif
    </div>
    <div class="col-md-12">
       @include('layouts.mensagens')
        {!! Form::open(['id' => 'frm_user', 'url' => ['colaborador', $user->id], 'method' => 'patch']) !!}
            <div class="card card-plain">
                <div class="content">
                    <h5 class="mb-0">Atualização de Dados <small class="text-muted">COMPLETE SEU PERFIL</small></h5>
                    <span>Os participantes não serão identificados *</span>
                    <div class="form-group mt-2">
                        <input type="hidden" name="id" value="{{ $user->id }}">
                        <input type="nome" name="nome" placeholder="Nome" class="form-control" value="{{ $user->name }}" required>
                    </div>
                    <div class="form-group mt-2">
                        <input type="email" name="email" placeholder="Email" class="form-control" value="{{ $user->email }}" required>
                    </div>
                    <div class="form-group">
                        <select name="cd_estado" id="cd_estado" class="form-control">
                            <option value="">Estado</option>
                            @foreach($estados as $estado)
                                <option value="{{ $estado->cd_estado }}" {{ ($user->cd_estado == $estado->cd_estado) ? 'selected' : '' }}>{{ $estado->nm_estado }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <select name="cd_cidade" id="cd_cidade" class="form-control">
                            <option value="">Cidade</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <select name="sexo" class="form-control">
                                    <option value="">Sexo</option>
                                    <option value="F" {{ ($user->sexo == 'F') ? 'selected' : '' }}>Feminino</option>
                                    <option value="M" {{ ($user->sexo == 'M') ? 'selected' : '' }}>Masculino</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" 
                                name="dt_nascimento" 
                                id="dt_nascimento" 
                                class="form-control data" 
                                value="{{ ($user and $user->dt_nascimento) ? \Carbon\Carbon::parse($user->dt_nascimento)->format('d/m/Y') : '' }}" 
                                placeholder="__/__/____">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        * Os dados serão utilizados somente para o acesso do participante e para a geração de estatísticas de distribuição por regiões, sexo e faixa etária
                    </div>
                    <div class="center">
                        <button type="submit" class="btn btn-fill btn-success btn-wd"><i class="fa fa-refresh"></i> Atualizar Dados</button>
                    </div>
                </div>
            </div>
        {!! Form::close() !!}
    </div>    
 </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() { 

            var token = $('meta[name="csrf-token"]').attr('content');
            var host =  $('meta[name="base-url"]').attr('content');

        });
    </script>
@endsection