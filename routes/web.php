<?php

use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/', 'HomeController@dashboard');
Route::get('home', 'HomeController@dashboard');
Route::get('sobre', 'HomeController@sobre');
Route::get('equipe', 'HomeController@equipe');
Route::get('ods', function () { return view('welcome'); });
Route::get('classificar', 'ODSController@classificar');
Route::get('analisar', function () { return view('analisar'); });
Route::post('ods/descobrir', 'ODSController@descobrir');
Route::post('ods/descobrir/salvar', 'ODSController@descobrirSalvar');

Route::get('dashboard', 'HomeController@dashboard');
Route::post('dados/matriz/ods-dimensoes', 'HomeController@matrizOdsDimensoes');

Route::get('repositorio', 'ODSController@repositorio');

Route::get('centro/dimensao/{centro}', 'CentroController@dimensao');
Route::get('centro/dimensao-ods/{centro}', 'CentroController@dimensaoODS');
Route::get('centro/pesquisador/{centro}', 'CentroController@pesquisador');
Route::get('centro/ies/{centro}', 'CentroController@calcularIES');
Route::get('centro/ics/{centro}/ano/{ano}', 'CentroController@calcularICS');

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

Route::get('dados/centros', 'DashboardController@centros');
Route::get('dados/departamentos', 'DashboardController@departamentos');
Route::get('dados/departamentos/centro/{id}', 'DashboardController@departamentosPorCentro');
Route::get('dados/ppgs/{centro}', 'DashboardController@ppgs');
Route::get('dados/docentes', 'DashboardController@docentes');

Route::get('dimensao/{apelido}/tipos', 'DimensaoController@tiposPorDimensao');
Route::post('dados/total/dimensao', 'DimensaoController@totalPorDimensao');

Route::get('documento/{id}/classificar/{classificacao}', 'ODSController@classificarManual');
Route::post('documento/ranking/ods', 'ODSController@getTotalDimensaoODS');
Route::get('documentos/dimensao/{dimensao}/detalhes/{id}', 'DocumentoController@detalhes');

Route::get('estado/{estado}/cidades', 'DadosController@cidades');

Route::get('dados/extensao/exportar', 'ExtensaoController@importar');
Route::get('dados/extensao/coordenador', 'ExtensaoController@mapCoordenador');
Route::get('dados/extensao/participantes', 'ExtensaoController@mapParticipantes');
Route::get('dados/extensao/grafo', 'ExtensaoController@grafo');
Route::get('dados/extensao/relacoes', 'ExtensaoController@getRelacoes');

Route::get('dados/inovacao/extracao', 'InovacaoController@extracao');
Route::get('dados/extensao/extracao', 'ExtensaoController@extracao');

Route::get('docente/grafo/{id}', 'DadosController@grafo');
Route::get('docentes/foto/{docente}', 'DocenteController@getImagem');
Route::get('docentes/ods/{id}', 'DocenteController@getODS');
Route::get('docentes/dimensao/{id}', 'DocenteController@getTotalDimensao');
Route::get('docentes/total-documentos/{id}', 'DocenteController@getTotalDocumentos');
Route::get('docentes/impacto-multidimensional/{id}', 'DocenteController@impactoMultidimensional');
Route::get('docentes/colaboracao-academica/{id}', 'DocenteController@indiceColaboracao');

Route::get('docentes', 'ODSController@getTotalProfessores');
Route::get('docentes/ppg/{ppg}', 'ODSController@getTotalProfessoresPPG');
Route::get('docentes/ranking/{nome}', 'ODSController@getRanking');
Route::get('docentes/max-ranking', 'ODSController@getMaxRanking');

Route::get('indices/dimensoes/{centro}', 'CentroController@indiceDimensao')->name('indices.dimensoes');
Route::get('indices/crescimento/{centro}', 'CentroController@indiceCrescimento')->name('indices.crescimento');
Route::get('indices/engajamento/{centro}', 'CentroController@indiceEngajamento')->name('indices.engajamento');

Route::get('panorama/centro/{id}', 'CentroController@panoramaCentro')->name('panorama.centro');
Route::get('panorama/departamento/{id}', 'CentroController@panoramaDepartamento')->name('panorama.departamento');
Route::get('panorama/ppg/{id}', 'CentroController@panoramaPPG')->name('panorama.ppg');

Route::get('grafo', 'GrafoController@ods');

Route::get('avaliacoes', 'AvaliacaoController@avaliacoes');

Route::get('minhas-avaliacoes', 'ODSController@avaliacoes');
Route::get('minhas-analises', 'ODSController@analises');

Route::get('ods/{numero}', 'ODSController@panorama');
Route::get('ods/{ods}/dados', 'ODSController@getDadosOds');

Route::get('meu-perfil', 'UserController@perfil');
Route::get('perfil/atualizar', 'UserController@atualizarPerfil');

Route::get('colaborar', 'ColaboradorController@colaborar');
Route::resource('colaborador', 'ColaboradorController');

Route::post('gerar-pdf', 'RelatorioController@gerarPdf');