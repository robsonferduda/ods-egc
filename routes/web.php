<?php

use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/', 'HomeController@dashboard');
Route::get('ods', function () { return view('welcome'); });
Route::get('perfil', function () { return view('perfil'); });
Route::get('classificar', 'ODSController@classificar');
Route::get('analisar', function () { return view('descobrir'); });
Route::post('ods/descobrir', 'ODSController@descobrir');

Route::get('dashboard', 'HomeController@dashboard');

Route::get('repositorio', 'ODSController@repositorio');

Route::post('dados/excel', 'DadosController@excel');
Route::post('dados/geral', 'ODSController@getTotalGeral');
Route::post('dados/geral/frequencia', 'ODSController@getTotalGeralFrequencia');
Route::get('dados/ano', 'ODSController@getAno');
Route::post('dados/documentos', 'ODSController@getDocumentos');
Route::get('dados/geral/{dimensao}', 'ODSController@getTotalGeral');
Route::get('dados/geral/ppg/{ppg}', 'ODSController@getTotalGeralPPG');
Route::get('dados/ppg/{ies}', 'ODSController@getPPG');
Route::get('dados/ppg/docentes/{ppg}', 'ODSController@getDocente');
Route::get('dados/ppg/{ppg}/docente/{docente}/ods', 'ODSController@getODS');

Route::get('documento/{id}/classificar/{classificacao}', 'ODSController@classificarManual');
Route::post('documento/ranking/ods', 'ODSController@getTotalDimensaoODS');
Route::get('documentos/dimensao/{dimensao}/detalhes/{id}', 'DocumentoController@detalhes');

Route::get('estado/{estado}/cidades', 'DadosController@cidades');

Route::get('dados/extensao/exportar', 'ExtensaoController@importar');
Route::get('dados/extensao/coordenador', 'ExtensaoController@mapCoordenador');
Route::get('dados/extensao/participantes', 'ExtensaoController@mapParticipantes');
Route::get('dados/extensao/grafo', 'ExtensaoController@grafo');
Route::get('dados/extensao/relacoes', 'ExtensaoController@getRelacoes');

Route::get('docentes', 'ODSController@getTotalProfessores');
Route::get('docentes/ppg/{ppg}', 'ODSController@getTotalProfessoresPPG');
Route::get('docentes/ranking/{nome}', 'ODSController@getRanking');
Route::get('docentes/max-ranking', 'ODSController@getMaxRanking');
Route::get('docentes/foto/{docente}', 'ODSController@getImagem');

Route::get('sobre', 'HomeController@sobre');

Route::get('minhas-avaliacoes', 'ODSController@avaliacoes');

Route::get('ods/{ods}', 'ODSController@getDadosOds');

Route::get('colaborar', 'ColaboradorController@colaborar');
Route::resource('colaborador', 'ColaboradorController');