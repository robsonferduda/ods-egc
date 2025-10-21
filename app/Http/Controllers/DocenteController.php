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
                  WHERE dop.id_pessoa_pes = $id
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
                    ->select('ods.cod', 'ods.nome', DB::raw('count(distinct doc.id) as total'))
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

    public function impactoMultidimensional($id)
    {

        $dimensoes = DB::table('documento_ods as d')
        ->join('documento_pessoa_dop as dp', 'dp.id_documento_ods', '=', 'd.id')
        ->where('dp.id_pessoa_pes', $id)
        ->select('d.id_dimensao', DB::raw('count(distinct d.id) as total'))
        ->groupBy('d.id_dimensao')
        ->pluck('total', 'id_dimensao')
        ->toArray();

        $totalDimensoes = 4;
        $dimensoesAlcançadas = count($dimensoes);
        $indice = $dimensoesAlcançadas / $totalDimensoes;

        return response()->json([
            'indice' => round($indice, 2),
            'dimensoes' => $dimensoes
        ]);
    }

    public function indiceColaboracao($idDocente)
    {
        $indice = DB::selectOne("
            SELECT ROUND(AVG(sub.coautores), 2) AS indice_colaboracao
            FROM (
                SELECT d1.id AS id_documento,
                       COUNT(DISTINCT dp2.id_pessoa_pes) AS coautores
                FROM documento_ods d1
                JOIN documento_pessoa_dop dp1 ON dp1.id_documento_ods = d1.id
                JOIN documento_pessoa_dop dp2 ON dp2.id_documento_ods = d1.id
                     AND dp2.id_pessoa_pes != dp1.id_pessoa_pes
                     AND dp2.id_funcao_fun IN (1, 3, 4)
                WHERE dp1.id_pessoa_pes = ?
                  AND dp1.id_funcao_fun IN (1, 3, 4)
                GROUP BY d1.id
            ) sub
        ", [$idDocente]);

        return response()->json(['indice' => $indice->indice_colaboracao ?? 0]);
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