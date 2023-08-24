@extends('layouts.app')
@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title"><i class="nc-icon nc-tag-content"></i> Eventos</h4>
                </div>
                <div class="col-md-6">
                    <a href="{{ url('eventos/create') }}" class="btn btn-info pull-right" style="margin-right: 12px;"><i class="fa fa-plus"></i> Cadastrar</a>
                </div>
                <div class="col-md-12">
                    <span><i class="fa fa-info-circle" style="margin-left: 15px;"></i> Os eventos possuem um ou mais certificados. Utilize a opção certificados <i class="fa fa-file-pdf-o text-warning"></i> para visualizar e fazer o cadastro.</span>
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
                      <th>Início</th>
                      <th>Término</th>
                      <th class="text-center">Finalizar</th>
                      <th class="disabled-sorting text-center">Ações</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Evento</th>
                        <th>Início</th>
                        <th>Término</th>
                        <th>Finalizar</th>
                        <th class="disabled-sorting text-center">Ações</th>
                    </tr>
                </tfoot>
                <tbody>
                    @foreach($eventos as $e)
                        <tr>
                            <td>{{ $e->nm_evento_eve }}</td>
                            <td>{!! ($e->dt_inicio_eve) ? date('d/m/Y', strtotime($e->dt_inicio_eve)) : '<span class="text-danger">Não informada</span>' !!}</td>
                            <td>{!! ($e->dt_final_eve) ? date('d/m/Y', strtotime($e->dt_final_eve)) : '<span class="text-danger">Não informada</span>' !!}</td>
                            <td class="text-center">
                                @if($e->fl_finalizado_eve == 'S') 
                                    <span class="text-success">Finalizado</span>' 
                                @else
                                    <a href="{{ url('evento/finalizar/'.$e->cd_evento_eve) }}" class="btn btn-danger btn-link">Finalizar</a>
                                @endif        
                            </td>
                            <td class="text-center">
                                <a href="{{ url('eventos/'.$e->cd_evento_eve.'/certificados') }}" class="btn btn-warning btn-link btn-icon"><i class="fa fa-file-pdf-o fa-2x"></i></a>
                                <a href="{{ route('eventos.edit',$e->cd_evento_eve) }}" class="btn btn-primary btn-link btn-icon"><i class="fa fa-edit fa-2x"></i></a>
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