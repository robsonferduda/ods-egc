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
        $sql = "SELECT d.nome AS dimensao, o.objetivo AS ods, COUNT(*) AS total, o.cor
                FROM documento_ods doc
                JOIN dimensao_ies d ON d.id = doc.id_dimensao
                JOIN ods o ON o.cod = doc.ods
                JOIN documento_pessoa_dop dop ON dop.id_documento_ods = doc.id
                WHERE dop.id_pessoa_pes = :docente_id
                GROUP BY d.nome, o.objetivo, o.cor
                ORDER BY d.nome, o.cod";

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