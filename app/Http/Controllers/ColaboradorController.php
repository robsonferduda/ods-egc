<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Estado;
use App\Pontuacao;
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
            $request->merge(['id_nivel' => 1]);
            $request->merge(['dt_nascimento' => $dt_nascimento]);
            $request->merge(['password' => \Hash::make($request->password)]);

            $user = User::create($request->all());

            Auth::loginUsingId($user->id);

            return redirect('meu-perfil');
        }
    }

    public function update(Request $request)
    {
        $user = User::where('id', $request->id)->first();
        $pontuacao = Pontuacao::where('id_usuario', $request->id)->where('acao','atualizar-perfil')->first();

        $dt_nascimento = ($request->dt_nascimento) ? $this->carbon->createFromFormat('d/m/Y', $request->dt_nascimento)->format('Y-m-d') : date("Y-m-d");
        $request->merge(['dt_nascimento' => $dt_nascimento]);

        $user->update($request->all());

        if(!$pontuacao){

            $dados = array('id_usuario' => $request->id,
                            'acao' => 'atualizar-perfil',
                            'total_pts' => 50);

            Pontuacao::create($dados);

            $user->pts += 50;
            $user->id_nivel = 2;
            $user->save(); 
        }

        return redirect('meu-perfil');
    }
}