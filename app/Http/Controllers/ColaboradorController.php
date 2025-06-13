<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Estado;
use App\Colaborador;
use Carbon\Carbon;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;

class ColaboradorController extends Controller
{
    public $carbon;

    public function __construct()
    {
        //$this->middleware('auth');
        $this->carbon = new Carbon();
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

            $nome = $request->nome;
            $dt_nascimento = ($request->dt_nascimento) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_nascimento)->format('Y-m-d') : date("Y-m-d");

            $request->merge(['name' => $nome]);
            $request->merge(['dt_nascimento' => $dt_nascimento]);
            $request->merge(['password' => \Hash::make($request->password)]);

            $user = User::create($request->all());

            Auth::loginUsingId($user->id);

            return redirect('meu-perfil');
        }
    }
}