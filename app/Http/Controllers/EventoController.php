<?php

namespace App\Http\Controllers;

use App\Utils;
use App\Evento;
use App\TipoParticipacao;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;

class EventoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $eventos = Evento::paginate(10);
        return view('eventos/index', compact('eventos'));
    }

    public function teste()
    {
        $redis=Redis::connect('127.0.0.1',6379);
        $visits = Redis::incr('visits'); 
        
    }

    public function certificados($id)
    {
        $evento = Evento::findOrFail($id);
        $tipos = TipoParticipacao::orderBy('ds_tipo_participacao_tip')->get();
        return view('eventos/certificados', compact('evento','tipos'));
    }

    public function create()
    {
        return view('eventos/create');
    }

    public function edit($id)
    {
        $evento = Evento::findOrFail($id);
        return view('eventos/edit', compact('evento'));
    }

    public function store(Request $request)
    {
        try {
            
            Evento::create($request->all());
            $retorno = array('flag' => true,
                             'msg' => "Dados inseridos com sucesso");

        } catch (\Illuminate\Database\QueryException $e) {

            dd($e);
            $retorno = array('flag' => false,
                             'msg' => Utils::getDatabaseMessageByCode($e->getCode()));

        } catch (Exception $e) {
            
            $retorno = array('flag' => true,
                             'msg' => "Ocorreu um erro ao inserir o registro");
        }

        if ($retorno['flag']) {
            Flash::success($retorno['msg']);
            return redirect('eventos')->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect('eventos/create')->withInput();
        }
    }

    public function update(Request $request, Evento $evento)
    {
        try {
        
            $evento->update($request->all());
            $retorno = array('flag' => true,
                             'msg' => "Dados atualizados com sucesso");
        } catch (\Illuminate\Database\QueryException $e) {
            $retorno = array('flag' => false,
                             'msg' => Utils::getDatabaseMessageByCode($e->getCode()));
        } catch (Exception $e) {
            $retorno = array('flag' => true,
                             'msg' => "Ocorreu um erro ao atualizar o registro");
        }

        if ($retorno['flag']) {
            Flash::success($retorno['msg']);
            return redirect('eventos')->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect()->route('evento.edit', $evento->cd_evento_eve)->withInput();
        }
    }
}