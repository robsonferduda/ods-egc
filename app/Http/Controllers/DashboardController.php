<?php

namespace App\Http\Controllers;

use App\Centro;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function centros()
    {
        $centros = \App\Centro::orderBy('ds_nome_cen')->get(['ds_sigla_cen', 'ds_nome_cen']);
        return response()->json($centros);
    }

    public function departamentos()
    {
        $departamentos = \App\Departamento::orderBy('ds_departamento_dep')->get(['ds_sigla_dep', 'ds_departamento_dep']);
        return response()->json($departamentos);
    }

    public function departamentosPorCentro($sigla)
    {
        $departamentos = \App\Departamento::where('ds_sigla_cen', $sigla)
            ->orderBy('ds_departamento_dep')
            ->get(['ds_sigla_dep', 'ds_departamento_dep']);

        return response()->json($departamentos);
    }

    public function ppgs()
    {
        $departamentos = \App\PPG::orderBy('nm_curso_cur')->get(['id_ppg', 'nm_curso_cur']);
        return response()->json($departamentos);
    }

    public function docentes()
    {
        $docentes = \App\Pessoa::where('id_vinculo_vin', 2)->orderBy('ds_nome_pessoa')->get(['id_pessoa_pes', 'ds_nome_pessoa']);
        return response()->json($docentes);
    }    
}