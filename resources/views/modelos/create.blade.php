@extends('layouts.app')
@section('content')
<div class="col-md-12">
    {!! Form::open(['id' => 'RegisterValidation', 'url' => ['eventos'], 'method' => 'patch', 'novalidate' => 'novalidate']) !!}
        <div class="card ">
            <div class="card-header ">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="card-title"><i class="nc-icon nc-tag-content"></i> Certificado</h4>
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
                       <h6> </h6>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="dt_inicio_eve">Tipo de Participação</label>
                            <select class="form-control" name="id_tipo_participacao_tip" id="id_tipo_participacao_tip">
                                <option value="0">Selecione um tipo</option>
                                @foreach($tipos as $t)
                                    <option value="{{ $t->id_tipo_participacao_tip }}">{{ $t->ds_tipo_participacao_tip }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="dt_final_eve">Carga Horária (Não obrigatória)</label>
                            <input class="form-control" name="total_horas_cer" id="total_horas_cer" type="name" />
                        </div>
                    </div>
                    <div class="col-md-12">
                      <div class="form-check">
                        <label class="form-check-label">
                          <input class="form-check-input" type="checkbox">
                          <span class="form-check-sign"></span>
                            Certificado possui informação adicional (Esse campo pode ser complementado com dados no arquivo de participantes)
                        </label>
                      </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="complemento_cer">Complemento textual (Preencha somente se houver)</label>
                            <input class="form-control" name="complemento_cer" id="complemento_cer" type="name" />
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="ds_conteudo-ser">Programação ou Conteúdo (Preencha somente se houver, informação aparecerá no verso do certificado)</label>
                            <textarea class="form-control" name="ds_conteudo_cer" id="ds_conteudo_cer"></textarea>
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
<div class="col-md-6">
    <div class="card ">
        <div class="card-body">
            <div class="row" style="min-height: 300px;">
                <div class="col-md-12">
                    <h5 class="center">CERTIFICADO</h5>
                    <p class="center">CERTIFICAMOS QUE</p>
                    <p class="center"><strong>FULANO DE TAL</strong></p>
                    <p class="center">
                        participou como <strong id="tipo_participacao">TIPO DE PARTICIPAÇÃO</strong>
                        do <strong>/strong>, 
                       
                            que ocorreu do dia .
                       
                            que ocorreu entre os dias .
                       
                        <span id="complemento"></span>
                    </p>
                    <p class="carga_horaria" style="display: none;">Carga horária de <strong id="carga_horaria">00</strong> horas e 100% de frequência.<p>
                    <p class="center">Florianópolis, {{ date('d/m/Y') }}</p>
                </div>
                <div class="col-md-12">
                    <p style="font-size: 10px;">A autenticidade do documento pode ser verificada no site: http://sistema/certificados/validar, informando a chave: 9999-99AA-AAAA-AA99</p>
                </div>
            </div>
        </div>
    </div> 
</div> 
<div class="col-md-6">
    <div class="card ">
        <div class="card-body">
            <div class="row" style="min-height: 300px;">
                <div class="col-md-12">
                    <div id="conteudo">
                       
                    </div>
                </div>
            </div>
        </div>
    </div> 
</div> 
@endsection