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
                t1.cor, 
                t0.id,
                t0.id_dimensao,
                titulo,
                t2.nome,
                t3.ds_tipo_documento,
                t0.texto  
                FROM documento_ods t0
                JOIN ods t1 ON t1.cod = t0.ods 
                JOIN dimensao_ies t2 ON t2.id = t0.id_dimensao 
                JOIN tipo_documento t3 ON t3.id_tipo_documento = t0.id_tipo_documento 
               	AND t0.id = $id";



        $documento = Documento::find($id);

        return view('detalhes', compact('documento'));
    }

}