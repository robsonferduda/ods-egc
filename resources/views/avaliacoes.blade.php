@extends('layouts.guest')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="header-text">
            <h3>Perfil ODS <i class="fa fa-angle-double-right" aria-hidden="true"></i> Classificar</h3>
         </div>
    </div>
    <div class="col-md-12 mb-3">
        <div class="pull-left">
            <a href="{{ url('classificar') }}">
                <span class="badge badge-pill badge-warning">COLABORAR</span>
            </a>
            <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                <span class="badge badge-pill badge-danger">Sair</span>
            </a>
        </div>
    </div>
    <div class="col-md-12">
        <h5>Minhas Avaliações</h5>
        @foreach($dados as $key => $documento)
            <p class="mb-0">
                <strong>Título</strong>: {{ $documento->nm_producao }}</p><p class="mt-1 mb-0">
                <strong> {{ $documento->nm_programa }} </strong></p><p class="mt-0">
                <span class="badge badge-pill" style="background: {{ $documento->cor }}"> ODS {{ $documento->cod }}</span>
            
                @switch($documento->voto)
                    @case(-1)
                        <span class="badge badge-pill text-danger"><i class="fa fa-ban"></i> Discordo</span>
                        @break
                    @case(0)
                        <span class="badge badge-pill text-warning"><i class="fa fa-question-circle"></i> Não Sei</span>
                        @break

                    @case(1)
                        <span class="badge badge-pill text-success"><i class="fa fa-check"></i> Concordo</span>
                        @break
                    @default
                        
                @endswitch
            </p>
            <hr/>
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