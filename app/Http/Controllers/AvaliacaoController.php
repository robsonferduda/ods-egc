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

        $dados = Avaliacao::all();
        
        return view('avaliacao/avaliacoes', compact('dados'));
    }
}