<?php

namespace App\Http\Controllers;

use DB;
use Excel;
use App\Utils;
use App\Evento;
use App\ModeloCertificado;
use App\TipoParticipacao;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Imports\ParticipanteImport;

class ModeloController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $eventos = Evento::all();
        $modelos = ModeloCertificado::all();
        $tipos = TipoParticipacao::orderBy('ds_tipo_participacao_tip')->get();

        return view('modelos/index', compact('eventos','modelos','tipos'));
    }
}