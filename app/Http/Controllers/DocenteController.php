<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Pessoa;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DocenteController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index()
    {
        
    }

    public function getImagem($id)
    {
        $pessoa = Pessoa::find($id);

        $dados = ['nome' => $pessoa->ds_nome_pessoa, 'ds_foto' => $pessoa->ds_image_pes];
       
        return response()->json($dados);
    }

    public function getTotalDimensao($id)
    {
        $sql = "WITH todas_dimensoes AS (
                  SELECT id AS id_dimensao, nome FROM dimensao_ies
                ),
                dados_por_docente AS (
                  SELECT 
                    d.id,
                    o.cod AS ods,
                    o.cor,
                    COUNT(*) AS total
                  FROM documento_ods doc
                  JOIN documento_pessoa_dop dop ON dop.id_documento_ods = doc.id
                  JOIN ods o ON o.cod = doc.ods
                  JOIN dimensao_ies d ON d.id = doc.id_dimensao
                  WHERE dop.id_pessoa_pes = 5532
                  GROUP BY d.id, o.cod, o.cor
                )
                SELECT 
                  td.nome AS dimensao,
                  dp.ods,
                  dp.cor,
                  COALESCE(dp.total, 0) AS total
                FROM todas_dimensoes td
                LEFT JOIN dados_por_docente dp ON dp.id = td.id_dimensao
                ORDER BY td.nome, dp.ods";

        $dados = DB::connection('pgsql')->select($sql);

        return response()->json($dados);
    }

    public function getTotalDocumentos($id)
    {

        $documentos = DB::table('documento_pessoa_dop as dop')
            ->join('documento_ods as doc', 'doc.id', '=', 'dop.id_documento_ods')
            ->join('ods', 'ods.cod', '=', 'doc.ods')
            ->select('ods.cod', 'ods.nome', DB::raw('count(*) as total'))
            ->where('dop.id_pessoa_pes', $id)
            ->groupBy('ods.cod', 'ods.nome')
            ->orderBy('ods.cod')
            ->get();

        $labelsODS = $documentos->pluck('nome');
        $valoresODS = $documentos->pluck('total');
        $totalODS = $valoresODS->sum();

        $dados = array('total' => $totalODS);
       
        return response()->json($dados);
    }

    public function getODS($id)
    {

        $sql = "SELECT 
                    t2.ods, 
                    t3.cor, 
                    COUNT(*) AS total
                FROM 
                    pessoa_pes t0
                JOIN 
                    documento_pessoa_dop t1 ON t1.id_pessoa_pes = t0.id_pessoa_pes
                JOIN 
                    documento_ods t2 ON t2.id = t1.id_documento_ods
                JOIN 
                    ods t3 ON t3.cod = t2.ods
                WHERE 
                    t0.id_pessoa_pes = $id
                    AND t1.id_funcao_fun = 1 -- Orientador
                GROUP BY 
                    t2.ods, t3.cor
                ORDER BY 
                    t2.ods";

        $dados = DB::connection('pgsql')->select($sql);

        return response()->json($dados);
    }
}