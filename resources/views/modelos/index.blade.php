@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title"><i class="fa fa-file-o"></i> Modelos</h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('modelos/create') }}" class="btn btn-info pull-right" style="margin-right: 12px;"><i class="fa fa-plus"></i> Cadastrar</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    @include('layouts.mensagens')
                </div>
            </div>
            <table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                      <th>Evento</th>
                      <th>Tipo</th>
                      <th class="disabled-sorting text-center">Ações</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Tipo</th>
                        <th>Finalizar</th>
                        <th class="disabled-sorting text-center">Ações</th>
                    </tr>
                </tfoot>
                <tbody>
                    @foreach($modelos as $e)
                        
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div> 
@endsection