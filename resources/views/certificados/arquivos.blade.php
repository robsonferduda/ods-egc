@extends('layouts.app')
@section('content')
<div class="col-md-12">
    {!! Form::open(['id' => 'RegisterValidation', 'url' => 'certificado/create/arquivo', 'files' => true, 'novalidate' => 'novalidate']) !!}
        <div class="card ">
            <div class="card-header ">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="card-title"><i class="nc-icon nc-single-copy-04"></i> Certificados</h4>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ url('certificados') }}" class="btn btn-warning pull-right" style="margin-right: 12px;"><i class="fa fa-table"></i> Certificados</a>
                    </div>
                </div>
                @include('layouts.mensagens')
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <label for="dt_inicio_eve">Arquivo de Dados</label>
                        <div class="input-group mb-3">
                            <div class="custom-file">
                                <input type="file" name="arquivo" class="custom-file-input" id="arquivo"/>
                                <label class="custom-file-label" for="arquivo">Selecionar Arquivo</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="dt_inicio_eve">Modelo de Certificado</label>
                            <select class="form-control select2" name="id_modelo_certificado_moc" id="id_modelo_certificado_moc">
                                <option value="0">Selecione um modelo</option>
                                @foreach($modelos as $m)
                                    <option value="{{ $m->id_modelo_certificado_moc }}">{{ $m->evento->nm_evento_eve }} - {{ $m->ds_descricao_moc }}</option>
                                @endforeach
                            </select>
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