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
        <div class="pull-left">
            <a href="{{ url('analisar') }}">
                <span class="badge badge-pill badge-default">Analisar DOcumentos</span>
            </a>
            <a href="{{ url('classificar') }}">
                <span class="badge badge-pill badge-default">COLABORAR</span>
            </a>
            <a href="{{ url('minhas-avaliacoes') }}">
                <span class="badge badge-pill badge-default">Minhas Avaliações</span>
            </a>
            <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                <span class="badge badge-pill badge-danger">Sair</span>
            </a>
        </div>
    </div>
    <div class="col-md-12">
       
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