<?php

namespace App\Http\Controllers;

use App\Utils;
use App\Dimensao;
use App\Documento;
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

    public function totalPorDimensao(Request $request)
    {
        // Recupere os filtros enviados via AJAX, se necessário
        $dimensao = $request->input('dimensao');
        $tipo = $request->input('tipo');
        $ppg = $request->input('ppg');
        $ano_inicial = $request->input('ano_inicial');
        $ano_fim = $request->input('ano_fim');

        // Busque os totais por dimensão (ajuste conforme sua lógica/model)
        // Exemplo: retorna um array associativo [id_dimensao => total]
        $totais = Documento::query()
            // ->filtros($dimensao, $tipo, $ppg, $ano_inicial, $ano_fim) // se houver
            ->selectRaw('id_dimensao, count(*) as total')
            ->groupBy('id_dimensao')
            ->pluck('total', 'id_dimensao')
            ->toArray();

        return response()->json(['total' => $totais]);
    }
}