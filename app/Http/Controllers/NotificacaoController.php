<?php

namespace App\Http\Controllers;

use Mail;
use App\Evento;
use App\Certificado;
use App\Participante;
use Illuminate\Http\Request;

class NotificacaoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('notificacoes/index');
    }

    public function enviar()
    {

        $to_name = "Robson Fernando Duda";
        $to_email = 'robsonferduda@gmail.com';
        $hash = 'AGJ66dasd'; 

        $data = array("name"=> $to_name, 'hash' => $hash);

        Mail::send('email.notificacao', $data, function($message) use ($to_name, $to_email, $hash) {
        $message->to($to_email, $to_name)
        ->subject('X ciKi - Certificado de avaliador disponível');
        $message->from('nao.responda.studiokem@gmail.com','Não responda');
        });
    }
}