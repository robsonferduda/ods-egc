<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Ods;
use App\Analise;
use App\Pontuacao;
use App\Avaliacao;
use App\Documento;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ODSController extends Controller
{
    private $user;

    public function __construct()
    {
        //$this->middleware('auth');
        $this->user = Auth::user();
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

    public function getDocumentos(Request $request)
    {
        $where = " WHERE 1=1 ";

        switch ($request->dimensao) {
            
            case 'pesquisa':
                $where .= ' AND id_dimensao = 1 ';
                break;

            case 'extensao':
                $where .= ' AND id_dimensao = 2 '; 
                break;            
            
            case 'gestao':
                $where .= ' AND id_dimensao = 3 ';
                break;

            case 'inovacao':
                $where .= ' AND id_dimensao = 4 ';
                break;

            case 'ensino':
                $where .= ' AND id_dimensao = 5 ';
                break;
                
            default:
                
                break;
        }

        //if($ods > 0) $where .= " AND t3.cod = $ods";

        if($request->ppg){
            $where .= " AND nm_programa = '$request->ppg'";
        }

        if($request->ano_inicial and $request->ano_fim){
            if($request->dimensao == 1)
                $where .= " AND ano BETWEEN '$request->ano_inicial' AND '$request->ano_fim' ";
        }

        if($request->tipo and $request->tipo != "todos"){
            $where .= " AND nm_subtipo_producao = '$request->tipo' ";
        }

        if($request->docente){
            $where .= " AND nm_orientador = '$request->docente'";
        }

        $sql = "SELECT t0.ods, 
                t1.cor, 
                t0.id,
                t0.id_dimensao,
                titulo,
                t2.nome,
                t3.ds_tipo_documento 
                FROM documento_ods t0
                JOIN ods t1 ON t1.cod = t0.ods 
                JOIN dimensao_ies t2 ON t2.id = t0.id_dimensao 
                JOIN tipo_documento t3 ON t3.id_tipo_documento = t0.id_tipo_documento 
                $where
                LIMIT 5";

        $dados = DB::connection('pgsql')->select($sql);

        return $dados;
    }

    public function getTotalGeral(Request $request)
    {
        $where = "WHERE 1=1";

        switch ($request->dimensao) {
            
            case 'pesquisa':
                $where .= ' AND id_dimensao = 5 ';
                break;

            case 'extensao':
                $where .= ' AND id_dimensao = 2 '; 
                break;            
            
            case 'gestao':
                $where .= ' AND id_dimensao = 3 ';
                break;

            case 'inovacao':
                $where .= ' AND id_dimensao = 4 ';
                break;

            case 'ensino':
                $where .= ' AND id_dimensao = 1 ';
                break;

            default:
                
                break;
        }

        if($request->ano_inicial and $request->ano_fim){
            $where .= " AND ano BETWEEN '$request->ano_inicial' AND '$request->ano_fim' ";
        }

        if($request->ppg){
            $where .= " AND nm_programa = '$request->ppg'";
        }

        if($request->tipo and $request->tipo != "todos"){
            $where .= " AND id_tipo_documento = '$request->tipo' ";
        }

        $sql = "SELECT t0.ods, t2.cor, count(*) as total 
                FROM documento_ods t0
                RIGHT JOIN ods t2 ON t2.cod = t0.ods 
                $where
                GROUP BY t0.ods, t2.cor 
                ORDER BY t0.ods";

        $dados = DB::connection('pgsql')->select($sql);
        
        $ods = array_column($dados, 'ods');

        $j = 0;

        for ($i=0; $i < 17; $i++) { 
            if(!in_array($i+1, $ods)){
                $obj = (object) ['ods' => $i+1, 'cor' => '#000000', 'total' => 0];
                $resultado[] = $obj;
            }else{
                $resultado[] = $dados[$j];
                $j++;
            }
        }

        $ods = array_column($resultado, 'ods');
        $cor = array_column($resultado, 'cor');
        $total = array_column($resultado, 'total');

        $totais = array('ods' => $ods, 'cor' => $cor, 'total' => $total);

        return response()->json($totais);
    }


    public function getTotalGeralFrequencia(Request $request)
    {
        $where = "WHERE 1=1";

        switch ($request->dimensao) {

            case 'pesquisa':
                $where .= ' AND id_dimensao = 5 ';
                break;

            case 'extensao':
                $where .= ' AND id_dimensao = 2 '; 
                break;            
            
            case 'gestao':
                $where .= ' AND id_dimensao = 3 ';
                break;

            case 'inovacao':
                $where .= ' AND id_dimensao = 4 ';
                break;

            case 'ensino':
                $where .= ' AND id_dimensao = 1 ';
                break;
            
            default:
                $where .= ' AND t0.id_dimensao IN(1,2,3,4, 5) ';
                break;
        }

        if($request->ano_inicial and $request->ano_fim){
            $where .= " AND ano BETWEEN '$request->ano_inicial' AND '$request->ano_fim' ";
        }

        /*

        if($request->ppg){
            $where .= " AND nm_programa = '$request->ppg'";
        }

        if($request->docente){
            $where .= " AND nm_orientador = '$request->docente'";
        }

        */

        if($request->tipo and $request->tipo != "todos"){
            $where .= " AND id_tipo_documento = '$request->tipo' ";
        }

        $sql = "SELECT t0.ods, t1.cor, count(*) as total 
                FROM documento_ods t0
                RIGHT JOIN ods t1 ON t1.cod = t0.ods 
                $where
                GROUP BY t0.ods, t1.cor 
                ORDER BY t0.ods";

        $dados = DB::connection('pgsql')->select($sql);
        
        $ods_encontrados = array_column($dados, 'ods');
        
        $anos = array();
        $anos[] = (int) $request->ano_inicial;
        $inicio = (int) $request->ano_inicial;

        for ($i=$request->ano_inicial; $i < $request->ano_fim ; $i++) { 
            $anos[] = $inicio += 1;
        }

        $frequencias = array();
        $lista_ods = Ods::orderBy('cod')->get();

        foreach ($lista_ods as $key => $ods) {

            $historico = array();

            if(in_array($ods->cod, $ods_encontrados)){

                for ($i=0; $i < count($anos); $i++) { 

                    $complemento = ' AND ano = '.$anos[$i].'
                                AND t0.ods = '.$ods->cod.'
                                GROUP BY t0.ods, ano, t1.cor 
                                ORDER BY t0.ods, ano';

                    $sql = "SELECT t0.ods, t0.ano, t1.cor, count(*) as total 
                            FROM documento_ods t0
                            RIGHT JOIN ods t1 ON t1.cod = t0.ods 
                            $where
                            $complemento";

                    $resultado = DB::connection('pgsql')->select($sql);

                    if($resultado){
                        $historico[] = $resultado[0]->total;
                    }else{
                        $historico[] = 0;
                    }
                }

                $totais = $anos;
                $frequencias[] = array('ods' => $ods->cod, 'cor' => $ods->cor, 'totais' => $historico);
            }
            
        }

        $dados = array('sequencia' => $anos, 'frequencias' => $frequencias);

        return response()->json($dados);
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

    public function getTotalDimensaoODS(Request $request)
    {
        $where = "WHERE 1=1";

        switch ($request->dimensao) {
            
            case 'pesquisa':
                $where .= ' AND id_dimensao = 5 ';
                break;

            case 'extensao':
                $where .= ' AND id_dimensao = 2 '; 
                break;            
            
            case 'gestao':
                $where .= ' AND id_dimensao = 3 ';
                break;

            case 'inovacao':
                $where .= ' AND id_dimensao = 4 ';
                break;

            case 'ensino':
                $where .= ' AND id_dimensao = 1 ';
                break;
            
            default:
                //$where = "";
                break;
        }

        /*

        if($request->ppg){
            $where .= " AND nm_programa = '$request->ppg'";
        }*/

        if($request->ano_inicial and $request->ano_fim){
            $where .= " AND ano BETWEEN '$request->ano_inicial' AND '$request->ano_fim' ";
        }

        if($request->tipo and $request->tipo != "todos"){
            $where .= " AND id_tipo_documento = '$request->tipo' ";
        }

        $sql = "SELECT t0.ods, objetivo, t2.cor, count(*) as total 
                FROM documento_ods t0
                JOIN ods t2 ON t2.cod = t0.ods 
                $where
               	GROUP BY ods, objetivo, cor 
               	ORDER BY total DESC  
               	LIMIT 5";

        $dados = DB::connection('pgsql')->select($sql);

        return response()->json($dados);
    }

    public function classificar()
    {
        $texto = Documento::find(rand(1681, 2681));
        $ods = $texto->classificacao->ods;

        // Divide o texto em "parágrafos" com base nos pontos finais
        $paragrafos = preg_split('/\.\s+/', $texto->ds_resumo);

        // Remove espaços extras e filtra vazios
        $paragrafos = array_filter(array_map('trim', $paragrafos));

        // Transforma em array indexado novamente
        $paragrafos = array_values($paragrafos);

        // Escolhe um índice aleatório
        //$indiceAleatorio = array_rand($paragrafos);

        // Mostra o parágrafo, adicionando o ponto final de volta
        $texto_avaliacao = $paragrafos[0] . '.';

        $texto_avaliacao = ucfirst(mb_strtolower($texto_avaliacao, 'UTF-8'));

        return view('classificar', compact('texto','ods','texto_avaliacao'));
    }

    public function avaliacoes()
    {
        $sql = "SELECT * 
            FROM capes_teses_dissertacoes_ctd t1
            JOIN documento_ods t2 ON t2.id_producao_intelectual = t1.id_producao_intelectual 
            JOIN avaliacao t3 ON t3.id_documento = t1.id_producao_intelectual 
            JOIN ods t4 ON t4.id = t2.ods 
            WHERE t3.id_usuario = ".Auth::user()->id;

        $dados = DB::connection('pgsql')->select($sql);
        
        return view('avaliacoes', compact('dados'));
    }

    public function analises()
    {
        $dados = Analise::where('cd_usuario', Auth::user()->id)->get();
        
        return view('analises', compact('dados'));
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

        $user = Auth::user();

        $dados = array('id_usuario' => $user->id,
                            'acao' => 'colaboracao',
                            'total_pts' => 15);

            Pontuacao::create($dados);

            $user->pts += 15;
            $user->save(); 

        // Total de pontos para mudar de nível: 50 (Perfil completo + 5*15 (5 avaliações) = 125 pontos)

        if($user->id_nivel == 1 and $user->pts >= 125){
            $user->id_nivel = 2;
            $user->save();
            Flash::success('Parabéns! Você alcançou o nível Prata. Continue contribuindo para alcançar o nível Ouro!');
        }

        if($user->id_nivel == 2 and $user->pts >= 200){
            $user->id_nivel = 3;
            $user->save();
            Flash::success('Parabéns! Você alcançou o nível Ouro. Continue contribuindo para alcançar o nível Diamante!');
        }

        $dados = array("tipo" => 1,
                        "id_documento" => $id,     
                        "id_usuario" => Auth::user()->id,
                        "voto" => $valor);

        Avaliacao::create($dados);

        return redirect('classificar')->withInput();
    }

    public function descobrir(Request $request)
    {
        $dados = array();
        $distribuicao = array();

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
            $distribuicao[] = array('ods' => $i+1, 'probabilidade' => $probabilidades[$i]);
        }    
        
        usort($dados, function($a, $b) {
            return $b['probabilidade'] <=> $a['probabilidade'];
        });

        $resultado['probabilidades'] = $distribuicao;
        $resultado['resultado'] = $dados;

        return response()->json($resultado);
    }

    public function descobrirSalvar(Request $request)
    {
        $dados = array();
        $distribuicao = array();

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
            $distribuicao[] = array('ods' => $i+1, 'probabilidade' => $probabilidades[$i]);
        }    
        
        usort($dados, function($a, $b) {
            return $b['probabilidade'] <=> $a['probabilidade'];
        });

        $resultado['probabilidades'] = $distribuicao;
        $resultado['resultado'] = $dados;

        $probabilidade = ($dados and $dados[0]) ? round($dados[0]['probabilidade'] * 100, 2) : 0;
        $ods = ($dados and $dados[0]) ? $dados[0]['ods'] : 0;

        $analise = new Analise();
        $analise->cd_usuario = Auth::user()->id;
        $analise->texto = $request->texto;
        $analise->ods = $ods;
        $analise->probabilidade = $probabilidade;
        $analise->id_modelo = 1;
        $analise->save();

        if($ods){
            Flash::success('Texto analisado com sucesso. A ODS vinculada a ele é a '.$ods.'. Para maiores detalhes, acesso a opção "Minhas Análises"');
        }else{
            Flash::error('Não foi possível determinar uma ODS relacionada ao texto informado.');
        }

        return redirect('analisar')->withInput();
        
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