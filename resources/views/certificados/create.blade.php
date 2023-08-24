@extends('layouts.app')
@section('content')
<div class="col-md-12">
    {!! Form::open(['id' => 'RegisterValidation', 'url' => 'certificados', 'novalidate' => 'novalidate']) !!}
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
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="exampleFormControlSelect1">Participante</label>
                            <select class="form-control select2" name="id_participante_par" id="id_participante_par">
                                <option>Selecione um participante</option>
                                @foreach($participantes as $p)
                                    <option value="{{ $p->id_participante_par }}">{{ $p->ds_nome_par }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="dt_inicio_eve">Modelo de Certificado</label>
                            <select class="form-control select2" name="id_modelo_certificado_moc" id="id_modelo_certificado_moc">
                                <option>Selecione um modelo</option>
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