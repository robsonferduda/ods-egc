<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Ods;
use App\Avaliacao;
use App\Documento;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
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

    public function repositorio()
    {
        $ods = Ods::orderBy('cod')->get();
        return view('repositorio', compact('ods'));
    }

    public function getDadosOds($ods)
    {
        $ods = Ods::find($ods);

        return response()->json($ods);
    }

    public function getDocumentos($dimensao, $ods)
    {
        $where = " WHERE 1=1 ";

        switch ($dimensao) {
            case 'extensao':
                $where = ' AND id_dimensao = 2 '; 
                break;

            case 'pesquisa':
                $where = ' AND id_dimensao = 1 ';
                break;
            
            default:
                $where = "";
                break;
        }

        if($ods > 0) $where .= " AND t3.cod = $ods";

        $sql = "SELECT t0.ods, 
	            t3.cor, 
	            t1.nm_programa,
                CASE 
                    WHEN t1.nm_subtipo_producao IS NOT NULL THEN t1.nm_subtipo_producao
                    WHEN t1.nm_subtipo_producao IS NULL THEN 'Projeto de Extensão'
                END AS complemento,
                CASE 
                    WHEN t1.nm_producao IS NOT NULL THEN t1.nm_producao
                    WHEN t2.titulo IS NOT NULL THEN t2.titulo
                END AS titulo
                FROM documento_ods t0
                LEFT JOIN capes_teses_dissertacoes_ctd t1 ON t1.id_producao_intelectual = t0.id_producao_intelectual 
                LEFT JOIN extensao t2 ON t2.id = t0.id_producao_intelectual 
                JOIN ods t3 ON t3.cod = t0.ods    
                $where
                LIMIT 5";

        $dados = DB::connection('pgsql')->select($sql);

        return $dados;
    }

    public function getTotalGeral($dimensao)
    {
        $where = "";

        switch ($dimensao) {
            case 'extensao':
                $where = ' WHERE id_dimensao = 2 '; 
                break;

            case 'pesquisa':
                $where = ' WHERE id_dimensao = 1 ';
                break;
            
            default:
                $where = "";
                break;
        }

        $sql = "SELECT t1.ods, t2.cor, count(*) as total 
                FROM capes_teses_dissertacoes_ctd t0
                RIGHT JOIN documento_ods t1 ON t1.id_producao_intelectual = t0.id_producao_intelectual 
                JOIN ods t2 ON t2.cod = t1.ods 
                $where
                GROUP BY t1.ods, t2.cor 
                ORDER BY t1.ods";

        $dados = DB::connection('pgsql')->select($sql);


        $ods = array_column($dados, 'ods');
        $cor = array_column($dados, 'cor');
        $total = array_column($dados, 'total');

        $ods[] = 17;
        $cor[] = '#19486A';
        $total[] = 837;

        $totais = array('ods' => $ods, 'cor' => $cor, 'total' => $total);

        return response()->json($totais);
    }

    public function getTotalGeralPPG($ppg)
    {
        $sql = "SELECT t1.ods, t2.cor, count(*) as total 
                FROM capes_teses_dissertacoes_ctd t0
                JOIN documento_ods t1 ON t1.id_producao_intelectual = t0.id_producao_intelectual 
                JOIN ods t2 ON t2.cod = t1.ods 
                WHERE nm_programa = '$ppg'
                GROUP BY t1.ods, t2.cor 
                ORDER BY t1.ods";

        $dados = DB::connection('pgsql')->select($sql);


        $ods = array_column($dados, 'ods');
        $cor = array_column($dados, 'cor');
        $total = array_column($dados, 'total');

        /*
        $ods[] = 17;
        $cor[] = '#19486A';
        $total[] = 0;
        */

        $totais = array('ods' => $ods, 'cor' => $cor, 'total' => $total);

        return response()->json($totais);
    }

    public function classificar()
    {
        $texto = Documento::find(rand(1681, 2681));
        $ods = $texto->classificacao->ods;

        return view('classificar', compact('texto','ods'));
    }

    public function avaliacoes()
    {
        $sql = "SELECT * 
            FROM capes_teses_dissertacoes_ctd t1
            JOIN documento_ods t2 ON t2.id_producao_intelectual = t1.id_producao_intelectual 
            JOIN avaliacao t3 ON t3.id_documento = t1.id_producao_intelectual 
            JOIN ods t4 ON t4.id = t2.ods 
            WHERE t3.usuario = ".Auth::user()->id;

        $dados = DB::connection('pgsql')->select($sql);
        
        return view('avaliacoes', compact('dados'));
    }

    public function classificarManual($id, $voto)
    {
        $valor = 0;
        $documento = Documento::where('id_producao_intelectual',$id)->first();
        switch ($voto) {
            case 'positivo':
                $valor = 1;
                $documento->classificacao->positivo = $documento->classificacao->positivo + 1;
                break;
            case 'negativo':
                $valor = -1;
                $documento->classificacao->negativo = $documento->classificacao->negativo + 1;
                break;
            case 'neutro':
                $valor = 0;
                $documento->classificacao->neutro = $documento->classificacao->neutro + 1;
            break;
        }
        $documento->classificacao->save();

        $dados = array("tipo" => 1,
                        "id_documento" => $id,     
                        "usuario" => Auth::user()->id,
                        "voto" => $valor);

        Avaliacao::create($dados);

        return redirect('classificar');
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
        
        usort($dados, function($a, $b) {
            return $b['probabilidade'] <=> $a['probabilidade'];
        });

        $resultado['probabilidades'] = $probabilidades;
        $resultado['resultado'] = $dados;

        return response()->json($resultado);
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

        $sql = "SELECT DISTINCT nm_orientador, t2.chave 
                FROM capes_teses_dissertacoes_ctd t1
                LEFT JOIN docente_foto t2 ON t2.nm_docente = t1.nm_orientador 
                WHERE nm_programa = '$ppg'
                ORDER BY nm_orientador";

        $dados = DB::connection('pgsql')->select($sql);
        return response()->json($dados);
    }

    public function getODS($ppg, $docente){

        $sql = "SELECT DISTINCT t2.ods, t3.cor, count(*) AS total 
                FROM capes_teses_dissertacoes_ctd t1 
                JOIN documento_ods t2 ON t2.id_producao_intelectual = t1.id_producao_intelectual 
                JOIN ods t3 ON t3.cod = t2.ods 
                WHERE nm_orientador = '$docente'
                /*AND nm_programa = '$ppg'*/
                GROUP BY t2.ods, t3.cor 
                ORDER BY ods ";

        $dados = DB::connection('pgsql')->select($sql);

        return response()->json($dados);
    }

    public function getTotalProfessores(){

        $sql = "SELECT t1.nm_orientador, chave, count(*) AS total 
                FROM capes_teses_dissertacoes_ctd t1 
                JOIN documento_ods t2 ON t2.id_producao_intelectual = t1.id_producao_intelectual
                LEFT JOIN docente_foto t3 ON t3.nm_docente = t1.nm_orientador 
                GROUP BY nm_orientador, chave 
                ORDER BY total DESC, nm_orientador ASC
                LIMIT 5";

        $dados = DB::connection('pgsql')->select($sql);

        return response()->json($dados);
    }

    public function getTotalProfessoresPPG($ppg){

        $sql = "SELECT t1.nm_orientador, chave, count(*) AS total 
                FROM capes_teses_dissertacoes_ctd t1 
                JOIN documento_ods t2 ON t2.id_producao_intelectual = t1.id_producao_intelectual 
                LEFT JOIN docente_foto t3 ON t3.nm_docente = t1.nm_orientador
                WHERE nm_programa = '$ppg'
                GROUP BY nm_orientador, chave 
                ORDER BY total DESC, nm_orientador ASC
                LIMIT 5";

        $dados = DB::connection('pgsql')->select($sql);

        return response()->json($dados);
    }

    public function getRanking($docente){

        $sql = "SELECT rank_number FROM(
                    SELECT nm_orientador, count(*) AS total , 
                        RANK () OVER ( 
                            
                            ORDER BY count(*) DESC
                        ) rank_number
                    FROM capes_teses_dissertacoes_ctd
                    GROUP BY nm_orientador 
                    ORDER BY total DESC) ranking 
                WHERE nm_orientador ILIKE '%$docente%'";

        $dados = DB::connection('pgsql')->select($sql)[0];

        return response()->json($dados);
    }

    public function getImagem($docente){

        $sql = "SELECT * FROM docente_foto WHERE nm_docente ILIKE '%$docente%'";

        $dados = DB::connection('pgsql')->select($sql)[0];

        return response()->json($dados);
    }

    public function getMaxRanking(){

        $sql = "SELECT count(distinct nm_orientador) AS total FROM capes_teses_dissertacoes_ctd";

        $dados = DB::connection('pgsql')->select($sql)[0];

        return response()->json($dados);
    }
}