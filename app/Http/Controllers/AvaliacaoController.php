<?php

namespace App\Http\Controllers;

use DB;
use Excel;
use PDF;
use Mail;
use App\Utils;
use App\Ods;
use App\Avaliacao;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;

class AvaliacaoController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index()
    {
        return view('dados/upload');
    }

    public function avaliacoes()
    {
        $sql = "SELECT * 
            FROM capes_teses_dissertacoes_ctd t1
            JOIN documento_ods t2 ON t2.id_producao_intelectual = t1.id_producao_intelectual 
            JOIN avaliacao t3 ON t3.id_documento = t1.id_producao_intelectual 
            JOIN ods t4 ON t4.id = t2.ods";

        $dados = DB::connection('pgsql')->select($sql);
        
        return view('avaliacao/avaliacoes', compact('dados'));
    }
}