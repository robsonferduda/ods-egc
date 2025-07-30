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
}