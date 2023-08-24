@extends('layouts.app')
@section('content')
<div class="col-md-12">
    {!! Form::open(['id' => 'RegisterValidation', 'url' => 'leitura', 'files' => true, 'novalidate' => 'novalidate']) !!}
        <div class="card ">
            <div class="card-header ">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="card-title"><i class="fa fa-group"></i> Inserir Dados</h4>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ url('participantes') }}" class="btn btn-warning pull-right" style="margin-right: 12px;"><i class="fa fa-table"></i> Participantes</a>
                    </div>
                </div>
                @include('layouts.mensagens')
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="input-group mb-3">
                            <div class="custom-file">
                                <input type="file" name="arquivo" class="custom-file-input" id="arquivo"/>
                                <label class="custom-file-label" for="arquivo">Selecionar Arquivo</label>
                            </div>
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