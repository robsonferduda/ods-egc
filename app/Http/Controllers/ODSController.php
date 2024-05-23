<?php

namespace App\Http\Controllers;

use DB;
use App\Ods;
use App\Documento;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ODSController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index()
    {
        
    }

    public function getDadosOds($ods)
    {
        $ods = Ods::find($ods);

        return response()->json($ods);
    }

    public function classificar()
    {
        $texto = Documento::find(rand(1681, 2681));
        $ods = rand(1,17);

        return view('classificar', compact('texto','ods'));
    }

    public function descobrir(Request $request)
    {
        $dados = array();

        $item = array(
            '--texto' => $request->texto
        );

        $args = "";
        foreach ($item as $k=>$v) {
            $args .= escapeshellarg(str_replace( array(' ', 'à','á','â','ã','ä', 'ç', 'è','é','ê','ë', 'ì','í','î','ï', 'ñ', 'ò','ó','ô','õ','ö', 'ù','ú','û','ü', 'ý','ÿ', 'À','Á','Â','Ã','Ä', 'Ç', 'È','É','Ê','Ë', 'Ì','Í','Î','Ï', 'Ñ', 'Ò','Ó','Ô','Õ','Ö', 'Ù','Ú','Û','Ü', 'Ý'), array(' ', 'a','a','a','a','a', 'c', 'e','e','e','e', 'i','i','i','i', 'n', 'o','o','o','o','o', 'u','u','u','u', 'y','y', 'A','A','A','A','A', 'C', 'E','E','E','E', 'I','I','I','I', 'N', 'O','O','O','O','O', 'U','U','U','U', 'Y'), $v));
        }
        $cmd = "python3 ".base_path()."/ods-leve.py $args";

        $result = exec($cmd, $output, $return);

        $output = str_replace("\r\n","", $output);

        $probabilidades = explode (";", $output[0]);

        for ($i=0; $i < count($probabilidades)-1; $i++) { 
            if($probabilidades[$i] > 0.1){
                $dados[] = array('ods' => $i+1, 'probabilidade' => $probabilidades[$i]);
            }
        }       
        return response()->json($dados);
    }

    public function getPPG($instituicao){

        $sql = "SELECT DISTINCT nm_programa 
                FROM capes_teses_dissertacoes_ctd
                ORDER BY nm_programa";

        $dados = DB::connection('pgsql')->select($sql);
        return response()->json($dados);
    }

    public function getAno(){

        $sql = "SELECT DISTINCT an_base 
                FROM capes_teses_dissertacoes_ctd
                ORDER BY an_base";

        $dados = DB::connection('pgsql')->select($sql);
        return response()->json($dados);
    }

    public function getDocente($ppg){

        $sql = "SELECT DISTINCT nm_orientador 
                FROM capes_teses_dissertacoes_ctd
                WHERE nm_programa = '$ppg'
                ORDER BY nm_orientador";

        $dados = DB::connection('pgsql')->select($sql);
        return response()->json($dados);
    }

    public function getODS($ppg, $docente){

        $sql = "SELECT DISTINCT t2.ods 
                FROM capes_teses_dissertacoes_ctd t1 
                JOIN documento_ods t2 ON t2.id_producao_intelectual = t1.id_producao_intelectual 
                WHERE nm_programa = '$ppg'
                AND nm_orientador = '$docente'
                ORDER BY ods ";

        $dados = DB::connection('pgsql')->select($sql);

        return response()->json($dados);
    }
}