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
    <div class="col-md-12">
        <h5>Minhas Análises</h5>
        @foreach($dados as $key => $documento)
            <span class="badge badge-pill" style="background: {{ $documento->odsDetectado->cor }}"> ODS {{ $documento->odsDetectado->cod }}</span>
            <span> {{ $documento->odsDetectado->objetivo }} definido com probabilidade de <strong>{{ $documento->probabilidade }} %</strong></span>
            <p class="mb-0">
                <strong>Texto</strong>: {{ $documento->texto }}</p><p class="mt-1 mb-0">
            </p>
        @endforeach
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