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
            GROUP BY dp1.id_pessoa_pes, dp2.id_pessoa_pes
        ');

        $pessoas = DB::table('pessoa_pes')->get()->keyBy('id_pessoa_pes');

        $nodes = [];
        $edges = [];

        $idsAdicionados = [];

        foreach ($relacoes as $r) {
            $nome1 = $pessoas[$r->p1]->ds_nome_pessoa ?? "ID:{$r->p1}";
            $nome2 = $pessoas[$r->p2]->ds_nome_pessoa ?? "ID:{$r->p2}";

            foreach ([$r->p1 => $nome1, $r->p2 => $nome2] as $id => $nome) {
                if (!in_array($id, $idsAdicionados)) {
                    $nodes[] = ['id' => $id, 'label' => $nome];
                    $idsAdicionados[] = $id;
                }
            }

            $edges[] = ['from' => $r->p1, 'to' => $r->p2, 'value' => $r->peso];
        }

        dd($nodes);

        return view('grafo', compact('nodes', 'edges', /* outros dados... */));
    }
}