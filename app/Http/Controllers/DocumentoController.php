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
        $documento = Documento::with('probabilidades')->find($id);

        $resultado = collect((array) $documento->probabilidades)
                ->filter(function($v, $k) {
                    return strpos($k, 'probabilidade_ods_') === 0;
                })
                ->sortDesc()
                ->take(2);

        dd($resultado);

        return view('detalhes', compact('documento','resultado'));
    }

}