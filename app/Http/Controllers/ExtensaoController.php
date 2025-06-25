<?php

namespace App\Http\Controllers;

use DB;
use Excel;
use App\Utils;
use App\Extensao;
use App\Participante;
use App\Relacao;
use App\TipoParticipacao;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Imports\ParticipanteImport;
use App\Exports\ExtensaoExport;

class ExtensaoController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index()
    {
        $rows = Excel::toArray(new ExtensaoExport, public_path('exportacao/projetos.xlsx'))[0];

        dd($rows);
    }

    public function extracao()
    {
        $extensao = Extensao::all();

        
    }

    public function grafo()
    {
        return view('extensao/grafo');
    }
    
    public function importar()
    {
        $rows = Excel::toArray(new ExtensaoExport, public_path('exportacao/projetos.xls'))[0];
        
        foreach ($rows as $key => $row) {

            $coordenador = preg_replace('/[\[\]]/ui', '', $row[2]);
            $participantes = preg_replace('/[\[\]]/ui', '', $row[4]);
            
            $insert = array('titulo' => $row[0],
                            'resumo' => $row[1],
                            'coordenador' => $coordenador,
                            'depto' => $row[3],
                            'participantes' => $participantes);

            Extensao::create($insert);
        }
    }

    public function mapCoordenador()
    {
        $extensao = Extensao::all();

        foreach ($extensao as $key => $ext) {
            
            $participante = Participante::where("nome", $ext->coordenador)->first();

            if(!$participante){

                $insert = array("nome" => $ext->coordenador);
                Participante::create($insert);
            }

        }
    }

    public function mapParticipantes()
    {
        $extensao = Extensao::all();

        foreach ($extensao as $key => $ext) {

            $str = $ext->participantes;
            $arr = explode(",", $str);

            for ($i=0; $i < count($arr); $i++) { 
                
                $coordenador = Participante::where("nome", $ext->coordenador)->first();
                $participante = Participante::where("nome", $arr[$i])->first();

                if(!$participante){

                    $insert = array("nome" => $arr[$i]);
                    $participante = Participante::create($insert);
                }

                if($coordenador->id != $participante->id){

                    $relacao = array('id_coordenador' => $coordenador->id, "id_participante" => $participante->id);
                    Relacao::create($relacao);
                }
            }
        }
    }

    public function getRelacoes()
    {
        /*
        $dados = array(
                array("name" => "Giustino Tribuzi", "value" => 100),
                array("name" => "Robson Fernando Duda", "value" => 100),
                array("name" => "Teste", 
                      "value" => 100,
                      "children" => array(array("name" => "Amandeus", "value" => 100)))
            );
        */

        $dados = array();
        $relacoes = Relacao::limit(10)->get();

        foreach ($relacoes as $key => $relacao) {
            $dados[] = array("name" => $relacao->id_coordenador, "value" => 100);
        }

        return response()->json($dados);
    }
}