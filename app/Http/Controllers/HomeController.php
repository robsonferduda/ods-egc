<?php

namespace App\Http\Controllers;

use App\Evento;
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

    public function sobre()
    {
        return view('sobre');
    }
}