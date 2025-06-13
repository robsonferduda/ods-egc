@extends('layouts.guest')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="header-text">
            <h3>Perfil ODS <i class="fa fa-angle-double-right" aria-hidden="true"></i> Classificar</h3>
         </div>
    </div>
    <div class="col-md-12 mb-3">
        @include('layouts/menu-logado')
    </div>
    <div class="col-md-12 mb-5">
        <h5>Minhas Análises</h5>
        @forelse($dados as $key => $documento)
            @if($documento->odsDetectado)
                <span class="badge badge-pill" style="background: {{ $documento->odsDetectado->cor }}"> ODS {{ $documento->odsDetectado->cod }}</span>
                <span> {{ $documento->odsDetectado->objetivo }} definido com probabilidade de <strong>{{ $documento->probabilidade }} %</strong></span>
            @else
                <p class="text-danger mb-0 mt-0">Não foi possível determinar uma ODS vinculada ao texto informado</p>
            @endif
            <p class="mb-1">Modelo de Classificação: <strong>{{ $documento->modelo->ds_modelo }}</strong></p>
            <p class="mb-1 text-muted">Documento enviado para classificação em {{ \Carbon\Carbon::parse($documento->created_at)->format('d/m/Y') }}</p>
            <div id="accordion_{{ $documento->id }}" role="tablist" aria-multiselectable="true" class="card-collapsed">
                <div class="card card-plain">
                    <div class="card-header" style="padding: 0px !important;" role="tab" id="heading_{{ $documento->id }}">
                        <a class="collapsed show-text" data-toggle="collapse" data-parent="#accordion_{{ $documento->id }}" href="#collapse_{{ $documento->id }}" aria-expanded="false" aria-controls="collapse_{{ $documento->id }}">
                        Texto Analisado
                            <i class="nc-icon nc-minimal-down"></i>
                        </a>
                    </div>
                    <div id="collapse_{{ $documento->id }}" class="collapse" role="tabpanel" aria-labelledby="heading_{{ $documento->id }}">
                      <div class="card-body">
                            {{ $documento->texto }}
                      </div>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-danger">Você não realizou nenhuma análise de documento</p>
        @endforelse
    </div>
 </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() { 

            var host =  $('meta[name="base-url"]').attr('content');
            var token = $('meta[name="csrf-token"]').attr('content');
           
        });
    </script>
@endsection