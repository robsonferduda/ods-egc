@extends('layouts.guest')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="header-text">
            <h3 class="mb-0">
                Perfil ODS <i class="fa fa-angle-double-right" aria-hidden="true"></i> Meu Perfil
                @include('layouts.nivel')
            </h3>
        </div>
    </div>
    <div class="col-md-12 mb-3">
        @include('layouts/menu-logado')
    </div>
    <div class="col-md-12">
        @if(Auth::user()->dt_nascimento == null or Auth::user()->sexo == null)
            <p class="mb-2 mt-0"><strong class="text-danger">Perfil Incompleto</strong><a href="{{ url('perfil/atualizar') }}"> Clique aqui</a> para atualizar e subir de nível</p>
        @endif
    </div>
    <div class="col-md-12">
        @if(count($avaliacoes))
            <p><i class="fa fa-users mr-2"></i> Você colaborou na avaliação de <a style="color: black; font-weight: bold;" href="{{ url('minhas-avaliacoes') }}">{{ count($avaliacoes) }}</a> documentos.</p>
        @else
            <p class="text-danger"><i class="fas fa-handshake me-1"></i> Você não colaborou na revisão de nenhum documento. <a href="{{ url('classificar') }}">Clique aqui</a> para colaborar!</p>
        @endif
    </div>   
    <div class="col-md-12">
        @if(count($analises))
            <p><i class="fa fa-files-o"></i> Você realizou <a style="color: black; font-weight: bold;" href="{{ url('minhas-analises') }}">{{ count($analises) }}</a> análises de documentos.</p>
        @else
            <p class="text-danger"><i class="fas fa-file-alt me-1"></i> Você não realizou nenhuma análise de documento. <a href="{{ url('analisar') }}">Clique aqui</a> para analisar!</p>
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