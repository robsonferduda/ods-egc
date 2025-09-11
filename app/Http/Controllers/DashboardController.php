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
        $centros = \App\Centro::orderBy('ds_nome_cen')->get(['cd_centro_cen', 'ds_sigla_cen', 'ds_nome_cen']);
        return response()->json($centros);
    }

    public function departamentos()
    {
        $departamentos = \App\Departamento::orderBy('ds_sigla_dep')
                                        ->get(['id_departamento_dep','ds_sigla_dep', 'ds_departamento_dep']);
        
        return response()->json($departamentos);
    }

    public function departamentosPorCentro($centro)
    {
        $centro = Centro::where('cd_centro_cen', $centro)->first();

        $departamentos = \App\Departamento::where('ds_sigla_cen', $centro->ds_sigla_cen)
            ->orderBy('ds_sigla_dep')
            ->get(['id_departamento_dep','ds_sigla_dep', 'ds_departamento_dep']);

        return response()->json($departamentos);
    }

    public function ppgs($centro)
    {
        if ($centro == 'todos') {
            $ppgs = \App\PPG::orderBy('nm_curso_cur')->get(['id_ppg', 'nm_curso_cur']);
        }else{
            $centro = Centro::where('cd_centro_cen', $centro)->first();
            $ppgs = \App\PPG::orderBy('nm_curso_cur')->where('cd_sigla_cen', $centro->ds_sigla_cen)->get(['id_ppg', 'nm_curso_cur']);
        }        

        return response()->json($ppgs);
    }

    public function docentes()
    {
        $docentes = \App\Pessoa::where('id_vinculo_vin', 2)->orderBy('ds_nome_pessoa')->get(['id_pessoa_pes', 'ds_nome_pessoa']);
        return response()->json($docentes);
    }    
}