@extends('layouts.app')
@section('content')
<div class="col-md-12">
    {!! Form::open(['id' => 'RegisterValidation', 'url' => 'eventos', 'novalidate' => 'novalidate']) !!}
        <div class="card ">
            <div class="card-header ">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="card-title"><i class="nc-icon nc-tag-content"></i> Eventos</h4>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ url('eventos') }}" class="btn btn-warning pull-right" style="margin-right: 12px;"><i class="fa fa-table"></i> Eventos</a>
                    </div>
                </div>
                @include('layouts.mensagens')
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group has-label">
                            <label for="nm_evento_eve">Nome do Evento <span class="text-danger">Obrigatório</span></label>
                            <input class="form-control" name="nm_evento_eve" id="nm_evento_eve" type="text" required="true" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="dt_inicio_eve">Data Inicial</label>
                            <input class="form-control datepicker" name="dt_inicio_eve" id="dt_inicio_eve" type="name" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="dt_final_eve">Data Final</label>
                            <input class="form-control datepicker" name="dt_final_eve" id="dt_final_eve" type="name" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-right">
                <a href="{{ url('eventos') }}" class="btn btn-danger"><i class="fa fa-times"></i> Cancelar</a>
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Salvar</button>
            </div>
        </div>
    {!! Form::close() !!} 
</div> 
@endsection