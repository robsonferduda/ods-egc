<?php

namespace App\Http\Controllers;

use DB;
use App\Ods;
use App\Log;
use App\Dimensao;
use App\Certificado;
use App\Participante;
use Illuminate\Http\Request;

class GrafoController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index()
    {
        
    }

    public function ods()
{
    $relacoes = DB::select('
        SELECT dp1.id_pessoa_pes as p1, dp2.id_pessoa_pes as p2, COUNT(*) as peso
        FROM documento_pessoa_dop dp1
        JOIN documento_pessoa_dop dp2
          ON dp1.id_documento_ods = dp2.id_documento_ods
         AND dp1.id_pessoa_pes < dp2.id_pessoa_pes
          WHERE dp1.id_pessoa_pes = 5532
        GROUP BY dp1.id_pessoa_pes, dp2.id_pessoa_pes
    ');

    // Calcula o grau (número de conexões) para cada pessoa
    $graus = [];
    foreach ($relacoes as $r) {
        if (!isset($graus[$r->p1])) $graus[$r->p1] = 0;
        if (!isset($graus[$r->p2])) $graus[$r->p2] = 0;
        $graus[$r->p1]++;
        $graus[$r->p2]++;
    }

    // Pega os nomes
    $ids = array_unique(array_merge(
        array_map(function ($r) { return $r->p1; }, $relacoes),
        array_map(function ($r) { return $r->p2; }, $relacoes)
    ));

    $pessoas = DB::table('pessoa_pes')
        ->whereIn('id_pessoa_pes', $ids)
        ->get()
        ->keyBy('id_pessoa_pes');

    $nodes = [];
    $edges = [];

    foreach ($ids as $id) {
        $nodes[] = [
            'data' => [
                'id' => $id,
                'label' => $pessoas[$id]->ds_nome_pessoa ?? "ID:{$id}",
                'grau' => $graus[$id] ?? 1
            ]
        ];
    }

    foreach ($relacoes as $r) {
        $edges[] = [
            'data' => [
                'source' => $r->p1,
                'target' => $r->p2,
                'value' => $r->peso
            ]
        ];
    }

    return view('grafo', compact('nodes', 'edges'));
}

}