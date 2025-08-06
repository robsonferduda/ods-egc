<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Ods;
use App\Avaliacao;
use App\Documento;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DocumentoController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index()
    {
        
    }

    public function detalhes($dimensao, $id)
    {
        $documento = Documento::with('probabilidades')->find($id);

        $resultado = collect((array) $documento->probabilidades)
                ->filter(function($v, $k) {
                    return strpos($k, 'probabilidade_ods_') === 0;
                })
                ->sortDesc()
                ->take(2);

        // Participantes + função + vínculo
        $participantes = DB::table('documento_pessoa_dop as dop')
            ->join('pessoa_pes as p', 'p.id_pessoa_pes', '=', 'dop.id_pessoa_pes')
            ->leftJoin('vinculo_vin as v', 'v.id_vinculo_vin', '=', 'p.id_vinculo_vin')
            ->leftJoin('funcao_fun as f', 'f.id_funcao_fun', '=', 'dop.id_funcao_fun')
            ->where('dop.id_documento_ods', $id)
            ->select(
                'p.id_pessoa_pes',
                'p.ds_nome_pessoa',
                'dop.id_funcao_fun',
                'f.ds_funcao_fun',
                'v.ds_vinculo_vin'
            )
            ->orderBy('dop.id_funcao_fun')
            ->orderBy('p.ds_nome_pessoa')
            ->get();

        return view('detalhes', compact('documento','resultado','participantes'));
    }

}