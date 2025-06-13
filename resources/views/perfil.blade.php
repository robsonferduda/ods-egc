@extends('layouts.guest')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="header-text">
            <h3>Perfil ODS <i class="fa fa-angle-double-right" aria-hidden="true"></i> Meu Perfil</h3>
        </div>
    </div>  
    <div class="col-md-12">
        <p class="mb-2">Olá <strong>{{ Auth::user()->name }}</strong>! O que deseja fazer hoje?</p>
    </div>
    <div class="col-md-12 mb-3">
        @include('layouts/menu-logado')
    </div>
    <div class="col-md-12">
        @if(count($avaliacoes))
            <p><i class="fa fa-users mr-2"></i> Você colaborou na avaliação de <a style="color: black; font-weight: bold;" href="{{ url('minhas-avaliacoes') }}">{{ count($avaliacoes) }}</a> documentos.</p>
        @endif
    </div>   
    <div class="col-md-12">
        @if(count($analises))
            <p><i class="fa fa-files-o"></i> Você realizou <a style="color: black; font-weight: bold;" href="{{ url('minhas-analises') }}">{{ count($analises) }}</a> análises de documentos.</p>
        @endif
    </div>    
 </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() { 

            var token = $('meta[name="csrf-token"]').attr('content');
            var host =  $('meta[name="base-url"]').attr('content');

        });
    </script>
@endsection