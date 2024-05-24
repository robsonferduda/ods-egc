<?php

use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/', function () { return view('welcome'); });
Route::get('perfil', function () { return view('perfil'); });
Route::get('dashboard', function () { return view('dashboard'); });
Route::get('classificar', 'ODSController@classificar');
Route::get('colaborar', function () { return view('colaborar'); });
Route::get('descobrir', function () { return view('descobrir'); });
Route::post('ods/descobrir', 'ODSController@descobrir');

Route::get('dados/ano', 'ODSController@getAno');
Route::get('dados/documentos', 'ODSController@getDocumentos');
Route::get('dados/geral', 'ODSController@getTotalGeral');
Route::get('dados/ppg/{ies}', 'ODSController@getPPG');
Route::get('dados/ppg/docentes/{ppg}', 'ODSController@getDocente');
Route::get('dados/ppg/{ppg}/docente/{docente}/ods', 'ODSController@getODS');

Route::get('ods/{ods}', 'ODSController@getDadosOds');