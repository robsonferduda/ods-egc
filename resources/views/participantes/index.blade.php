@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title"><i class="fa fa-group"></i> Participantes</h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('participantes/create') }}" class="btn btn-info pull-right" style="margin-right: 12px;"><i class="fa fa-plus"></i> Cadastrar</a>
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
                      <th>Nome</th>
                      <th>Email</th>
                      <th>Documento</th>
                      <th class="disabled-sorting text-center">Ações</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Documento</th>
                        <th class="disabled-sorting text-center">Ações</th>
                    </tr>
                </tfoot>
                <tbody>
                    @foreach($participantes as $p)
                        <tr>
                            <td>{{ $p->ds_nome_par }}</td>
                            <td>{{ $p->ds_email_par }}</td>
                            <td>{{ $p->nr_documento_par }}</td>
                            <td class="text-center">
                                <a href="{{ url('eventos.edit',$p->id_participante_per) }}" class="btn btn-primary btn-link btn-icon"><i class="fa fa-edit fa-2x"></i></a>
                                <a href="#" class="btn btn-danger btn-link btn-icon remove"><i class="fa fa-times fa-2x"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div> 
@endsection