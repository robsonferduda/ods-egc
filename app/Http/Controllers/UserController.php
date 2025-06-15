<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Estado;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('usuarios/index');
    }

    public function perfil()
    {
        $user = User::find(Auth::user()->id);

        $avaliacoes = $user->avaliacao;
        $analises = $user->analise;

        return view('perfil', compact('avaliacoes','analises'));
    }

    public function atualizarPerfil()
    {
        $user = User::find(Auth::user()->id);
        $estados = Estado::orderBy('nm_estado')->get();

        return view('usuarios/atualizar-perfil', compact('user','estados'));
    }
}