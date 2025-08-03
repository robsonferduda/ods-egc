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

    public function getODS($id){

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