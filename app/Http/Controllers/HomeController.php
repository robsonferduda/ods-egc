<?php

namespace App\Http\Controllers;

use DB;
use App\Ods;
use App\Log;
use App\Dimensao;
use App\Certificado;
use App\Participante;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index()
    {
        $eventos = Evento::count();
        $certificados = Certificado::count();
        $participantes = Participante::count();
        return view('home', compact('participantes','eventos','certificados'));
    }

    public function dashboard(Request $request)
    {
        $ods = Ods::orderBy('cod')->get();
        $data = \Location::get($request->ip());    

        $acesso = array("ip" => $request->ip(),
                        "cidade" => ($data) ? $data->cityName : "Não Definido",
                        "uf" => ($data) ? $data->areaCode : "Não Definido");

        $dimensoes_ies = Dimensao::orderBy('nome')->get();

        Log::create($acesso);

         // 1. Relações de coautoria (mesmo documento)
        $relacoes = DB::select('
            SELECT dp1.id_pessoa_pes as p1, dp2.id_pessoa_pes as p2, COUNT(*) as peso
            FROM documento_pessoa_dop dp1
            JOIN documento_pessoa_dop dp2
              ON dp1.id_documento_ods = dp2.id_documento_ods
             AND dp1.id_pessoa_pes < dp2.id_pessoa_pes
             WHERE dp1.id_pessoa_pes = ?
            GROUP BY dp1.id_pessoa_pes, dp2.id_pessoa_pes
        ', [4077]);

        // 2. Todas as pessoas com ID envolvido nas relações
        $ids = collect($relacoes)->flatMap(function($r) {
            return [$r->p1, $r->p2];
        })->unique()->values();

        // 3. Nomes das pessoas
        $pessoas = DB::table('pessoa_pes')
            ->whereIn('id_pessoa_pes', $ids)
            ->get()
            ->keyBy('id_pessoa_pes');

        // 4. Funções (uma por pessoa, com prioridade pela menor id_funcao_fun)
        $funcoes = DB::table('documento_pessoa_dop as dop')
                    ->join('funcao_fun as f', 'f.id_funcao_fun', '=', 'dop.id_funcao_fun')
                    ->select('dop.id_pessoa_pes', 'f.ds_funcao_fun')
                    ->whereIn('dop.id_pessoa_pes', $ids)
                    ->groupBy('dop.id_pessoa_pes', 'f.ds_funcao_fun', 'dop.id_funcao_fun') // adicionado aqui
                    ->orderBy('dop.id_pessoa_pes')
                    ->orderBy('dop.id_funcao_fun') // agora está OK
                    ->get()
                    ->groupBy('id_pessoa_pes')
                    ->map(function($grupo) {
                        return $grupo->first()->ds_funcao_fun;
                    });

        // 5. Montar nodes e edges
        $nodes = [];
        $edges = [];
        $idsAdicionados = [];

        foreach ($relacoes as $r) {
            foreach ([$r->p1, $r->p2] as $id) {
                if (!in_array($id, $idsAdicionados)) {

                    $funcao = $funcoes[$id] ?? 'Outro';
                    $cor = '#6c757d'; // cor padrão

                    if ($funcao === 'Orientador') {
                        $cor = '#007bff';
                    } elseif ($funcao === 'Participante') {
                        $cor = '#ffc107';
                    } elseif ($funcao === 'Inventor') {
                        $cor = '#28a745';
                    }

                    $nodes[] = [
                        'id' => $id,
                        'label' => $pessoas[$id]->ds_nome_pessoa ?? "ID:$id",
                        'color' => $cor
                    ];
                    $idsAdicionados[] = $id;
                }
            }

            $edges[] = [
                'from' => $r->p1,
                'to' => $r->p2,
                'value' => $r->peso
            ];
        }

        return view('dashboard', compact('ods','dimensoes_ies','nodes', 'edges'));
    }

    public function sobre()
    {
        return view('sobre');
    }
}