<?php

namespace App\Http\Controllers;

use App\Ods;
use App\Log;
use App\Dimensao;
use App\Certificado;
use App\Participante;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index()
    {
        $eventos = Evento::count();
        $certificados = Certificado::count();
        $participantes = Participante::count();
        return view('home', compact('participantes','eventos','certificados'));
    }

    public function dashboard(Request $request)
    {
        $ods = Ods::orderBy('cod')->get();
        $dimensoes = Dimensao::all();

        /*
        $data = \Location::get($request->ip());    

        $acesso = array("ip" => $request->ip(),
                        "cidade" => ($data) ? $data->cityName : "Não Definido",
                        "uf" => ($data) ? $data->areaCode : "Não Definido");*/

        Log::create($acesso);

        return view('dashboard', compact('ods','dimensoes'));
    }

    public function sobre()
    {
        return view('sobre');
    }
}