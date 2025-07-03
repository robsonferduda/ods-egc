<?php

namespace App\Http\Controllers;

use App\Utils;
use App\Dimensao;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;

class DimensaoController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index()
    {
        
    }

    public function tiposPorDimensao($apelido)
    {
        $dimensao = Dimensao::where('apelido', $apelido)->first();
        $tipos = [];
        if ($dimensao) {
            $tipos = $dimensao->tiposDocumentos()->pluck('ds_tipo_documento', 'id_tipo_documento'); 
        }
        return response()->json($tipos);
    }

}