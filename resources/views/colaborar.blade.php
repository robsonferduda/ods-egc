@extends('layouts.guest')
@section('content')
<div class="row">
    <div class="col-md-12">
       <div class="header-text">
            <h3>Perfil ODS <i class="fa fa-angle-double-right" aria-hidden="true"></i> Colaborar</h3>
            <div class="cabecalho">
                <h5 class="mb-0">Registre-se e ajude a aumentar nossa base de conhecimento</h5>
                <p>A classificação manual auxilia no processo de melhoria da qualidade da classificação dos modelos de Inteligência Artificial</p>
            </div>
       </div>
    </div>
    <div class="col-md-6">
        @include('layouts.mensagens')
        {!! Form::open(['id' => 'frm', 'url' => ['colaborador']]) !!}
            <div class="card card-plain">
                <div class="content">
                    <h5 class="mb-0">Preencha seus dados</h5>
                    <span>Os participantes não serão identificados *</span>
                    <div class="form-group mt-2">
                    <input type="email" name="email" placeholder="Email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <select name="cd_estado" id="cd_estado" class="form-control" required>
                            <option value="">Estado</option>
                            @foreach($estados as $estado)
                                <option value="{{ $estado->cd_estado }}">{{ $estado->nm_estado }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <select name="cd_cidade" id="cd_cidade" class="form-control" required>
                            <option value="">Cidade</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <select name="sexo" class="form-control" required>
                                    <option value="">Sexo</option>
                                    <option value="F">Feminino</option>
                                    <option value="M">Masculino</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" name="dt_nascimento" id="dt_nascimento" class="form-control data" placeholder="__/__/____" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" id="password" placeholder="Senha" class="form-control" required>
                        <div class="view-eye">
                            <i class="fa fa-eye view-password" data-target="password"></i>  
                        </div> 
                    </div>
                    <div class="form-group">
                        <input type="password" name="repeat_password" id="repeat_password" placeholder="Repita a Senha" class="form-control" required>
                        <div class="view-eye">
                            <i class="fa fa-eye view-password" data-target="repeat_password"></i>  
                        </div> 
                    </div>
                    <div class="col-md-12">
                        * Os dados serão utilizados somente para o acesso do participante e para a geração de estatísticas de distribuição por regiões, sexo e faixa etária
                    </div>
                    <div class="center">
                        <button type="submit" class="btn btn-fill btn-success btn-wd"><i class="fa fa-user"></i> Criar Conta</button>
                        <br/>
                        <a class="btn-link mb-3 mt-5" href="{{ route('password.request') }}">
                            <span class="forget-password">{{ __('esqueceu sua senha?') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        {!! Form::close() !!} 
    </div>
    <div class="col-md-6">
        <div class="row mt-5">
            <div class="col-md-2 center">
                <div class="icon icon-danger mt-3">
                    <i class="fa fa-tags" style="font-size: 3em"></i>
                </div>
            </div>
            <div class="col-md-10">
                <h5 class="mb-0">Classificação de ODS</h5>
                Documentos são classificados pelos colaboradores, de forma manual, para aumentar a qualidade das amostras para testes.
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-2 center">
                <div class="icon icon-info mt-3">
                    <i class="fa fa-database" style="font-size: 3em"></i>
                </div>
            </div>
            <div class="col-md-10">
                <h5 class="mb-0">Base da Dados Colaborativa</h5>
                As avaliações auxiliam no processo de criação de uma base de dados com maior índice de assertividade.
            </div>
        </div>
    </div>
 </div>
@endsection