<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Estado;
use App\Colaborador;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;

class ColaboradorController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index()
    {
        
    }

   public function colaborar() 
   { 
        $estados = Estado::orderBy('nm_estado')->get();

        return view('colaborar', compact('estados'));
   }

    public function store(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if($user){

            Flash::info('<p class="mb-1"><strong>Já existe um cadastro com o email informado.</strong></p> Use seu usuário e senha para acessar ou redefina sua senha utilizando a recuperação de senha');
            return redirect('colaborar')->withInput();

        }else{

            $nome = 'usuario-'.str_pad(User::max('id'), 6, "0", STR_PAD_LEFT);
            $nome = $request->nome;

            $request->merge(['name' => $nome]);
            $request->merge(['password' => \Hash::make($request->password)]);
            $user = User::create($request->all());

            Auth::loginUsingId($user->id);

            return redirect('meu-perfil');
        }
    }
}