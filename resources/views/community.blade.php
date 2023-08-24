@extends('layouts.guest')
@section('content')
<div class="row">
    <div class="col-md-12">
       <div class="header-text">
          <h3><i class="fa fa-users"></i> ODS EGC Community</h3>
          <h5 class="mb-0">Registre-se e ajude a aumentar nossa base de conhecimento</h5>
          <p>A classificação manual auxiliar no processo de melhoria da qualidade de classificação dos modelos de Inteligência Artificial desenvolvidos</p>
          <hr>
       </div>
    </div>
    <div class="col-md-6">
        <div class="row">
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
                As avaliações auxiliam no processo de criação de uma base de dados com mais índice de assetividade.
            </div>
        </div>
    </div>
    <div class="col-md-6">
       <form method="#" action="#">
          <div class="card card-plain">
             <div class="content">
                <h5 class="mb-0">Preencha seus dados</h5>
                <span>Os participantes não são identificados</span>
                <div class="form-group mt-2">
                   <input type="email" placeholder="Email" class="form-control">
                </div>
                <div class="form-group">
                    <select class="form-control" aria-label="Default select example">
                        <option>Estado</option>
                    </select>
                </div>
                <div class="form-group">
                    <select class="form-control" aria-label="Default select example">
                        <option>Cidade</option>
                    </select>
                </div>
                <div class="form-group">
                    <select class="form-control" aria-label="Default select example">
                        <option>Sexo</option>
                    </select>
                </div>
                <div class="form-group mt-2">
                    <input type="text" placeholder="Data de Nascimento" class="form-control">
                 </div>
                <div class="center">
                    <button type="submit" class="btn btn-fill btn-success btn-wd"><i class="fa fa-user"></i> Criar Conta</button>
                </div>
             </div>
          </div>
       </form>
    </div>
 </div>
@endsection