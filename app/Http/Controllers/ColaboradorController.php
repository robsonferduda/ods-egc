<?php

namespace App\Http\Controllers;

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
        $pessoa = Colaborador::create($request->all());

        $user = array('name' => $request->email,
                      'email' => $request->email,
                      'password' => \Hash::make($request->password),
                      'pessoa_id' => $pessoa->id);

        User::create($user);
    }

}