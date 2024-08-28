<?php

namespace App\Http\Controllers;

use App\Ods;
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

    public function dashboard()
    {
        $ods = Ods::orderBy('cod')->get();

        return view('dashboard', compact('ods'));
    }

    public function sobre()
    {
        return view('sobre');
    }
}