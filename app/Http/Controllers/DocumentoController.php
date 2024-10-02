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
        $sql = "SELECT t0.ods, 
	            t3.cor, 
	            t1.nm_programa,
                t1.nm_discente,
                t1.nm_orientador,
                t0.id,
                t2.coordenador,
                t0.id_dimensao as dimensao,
                CASE 
                    WHEN t1.nm_subtipo_producao IS NOT NULL THEN t1.nm_subtipo_producao
                    WHEN t1.nm_subtipo_producao IS NULL THEN 'Projeto de Extensão'
                END AS complemento,
                CASE 
                    WHEN t1.nm_producao IS NOT NULL THEN t1.nm_producao
                    WHEN t2.titulo IS NOT NULL THEN t2.titulo
                END AS titulo,
                CASE 
                    WHEN t1.ds_resumo IS NOT NULL THEN t1.ds_resumo
                    WHEN t2.resumo IS NOT NULL THEN t2.resumo
                END AS resumo
                FROM documento_ods t0
                LEFT JOIN capes_teses_dissertacoes_ctd t1 ON t1.id_producao_intelectual = t0.id_producao_intelectual 
                LEFT JOIN extensao t2 ON t2.id = t0.id_producao_intelectual 
                JOIN ods t3 ON t3.cod = t0.ods    
               	WHERE t0.id = $id
               	AND t0.id_dimensao = $dimensao";


        $documento = DB::connection('pgsql')->select($sql)[0];

        return view('detalhes', compact('documento'));
    }

}