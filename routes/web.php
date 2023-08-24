<?php

use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/', function () { return view('welcome'); });
Route::get('community', function () { return view('community'); });
Route::get('discovery', function () { return view('discovery'); });
Route::post('ods/discovery', 'ODSController@discovery');