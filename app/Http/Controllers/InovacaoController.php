<?php

namespace App\Http\Controllers;

use App\Inovacao;
use Illuminate\Http\Request;

class InovacaoController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index()
    {
        
    }

    public function extracao()
    {
        $inovacoes = Inovacao::all();

        foreach ($inovacoes as $key => $inovacao) {
            
            $nomes = array_map('trim', explode('/', $inovacao->participantes));

            dd($nomes);

        }
    }
}