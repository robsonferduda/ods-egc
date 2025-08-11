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
        $dimensao = $request->input('dimensao');
        $tipo = $request->input('tipo');
        $ppg = $request->input('ppg');
        $ano_inicial = $request->input('ano_inicial');
        $ano_fim = $request->input('ano_fim');

        $query = Documento::query();

        if ($dimensao && $dimensao != '0') {

            switch ($request->dimensao) {
            
                case 'pos-graduacao':
                    $dimensao =    array(6);
                    break;

                case 'pesquisa':
                    $dimensao =    array(5);
                    break;

                case 'extensao':
                    $dimensao =    array(2);
                    break;            
                
                case 'gestao':
                    $dimensao =    array(3);
                    break;

                case 'inovacao':
                    $dimensao =    array(4);
                    break;

                case 'ensino':
                    $dimensao =    array(1);
                    break;
                    
                default:
                    $dimensao =    array(1,2,3,4,5,6);
                    break;
            }

            $query->where('id_dimensao', $dimensao);
        }

        if ($tipo && $tipo != 'todos') {
            $query->where('id_tipo_documento', $tipo);
        }

        if ($ppg && $ppg != '0') {
            $query->where('id_ppg', $ppg);
        }

        // Filtro de ano como intervalo (between)
        if ($ano_inicial && $ano_inicial != 'Todos' && $ano_fim && $ano_fim != 'Todos') {
            $query->whereBetween('ano', [$ano_inicial, $ano_fim]);
        } elseif ($ano_inicial && $ano_inicial != 'Todos') {
            $query->where('ano', '>=', $ano_inicial);
        } elseif ($ano_fim && $ano_fim != 'Todos') {
            $query->where('ano', '<=', $ano_fim);
        }

        $totais = $query
            ->selectRaw('id_dimensao, count(*) as total')
            ->groupBy('id_dimensao')
            ->pluck('total', 'id_dimensao')
            ->toArray();

        return response()->json(['total' => $totais]);
    }
}