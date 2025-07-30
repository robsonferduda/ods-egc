<?php

namespace App\Http\Controllers;

use DB;
use Excel;
use PDF;
use Mail;
use App\Utils;
use App\Ods;
use App\Cidade;
use App\CertificadoMetadado;
use App\Participante;
use App\ModeloCertificado;
use App\CertificadoCiki;
use App\ParticipanteCiki;
use App\Exports\DadosExport;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use App\Imports\ParticipanteImport;
use App\Http\Requests\CertificadoArquivoRequest;

class DadosController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index()
    {
        return view('dados/upload');
    }

    public function cidades($id)
    {
        $cidades = Cidade::where('cd_estado', $id)->orderBy('nm_cidade')->get();
        return response()->json($cidades);
    }

    public function grafo($id)
    {
        // 1. Relações de coautoria (mesmo documento)
        $relacoes = DB::select('
            SELECT dp1.id_pessoa_pes as p1, dp2.id_pessoa_pes as p2, COUNT(*) as peso
            FROM documento_pessoa_dop dp1
            JOIN documento_pessoa_dop dp2
              ON dp1.id_documento_ods = dp2.id_documento_ods
             AND dp1.id_pessoa_pes < dp2.id_pessoa_pes
             WHERE dp1.id_pessoa_pes = ?
            GROUP BY dp1.id_pessoa_pes, dp2.id_pessoa_pes
        ', [4077]);

        // 2. Todas as pessoas com ID envolvido nas relações
        $ids = collect($relacoes)->flatMap(function($r) {
            return [$r->p1, $r->p2];
        })->unique()->values();

        // 3. Nomes das pessoas
        $pessoas = DB::table('pessoa_pes')
            ->whereIn('id_pessoa_pes', $ids)
            ->get()
            ->keyBy('id_pessoa_pes');

        // 4. Funções (uma por pessoa, com prioridade pela menor id_funcao_fun)
        $funcoes = DB::table('documento_pessoa_dop as dop')
                    ->join('funcao_fun as f', 'f.id_funcao_fun', '=', 'dop.id_funcao_fun')
                    ->select('dop.id_pessoa_pes', 'f.ds_funcao_fun')
                    ->whereIn('dop.id_pessoa_pes', $ids)
                    ->groupBy('dop.id_pessoa_pes', 'f.ds_funcao_fun', 'dop.id_funcao_fun') // adicionado aqui
                    ->orderBy('dop.id_pessoa_pes')
                    ->orderBy('dop.id_funcao_fun') // agora está OK
                    ->get()
                    ->groupBy('id_pessoa_pes')
                    ->map(function($grupo) {
                        return $grupo->first()->ds_funcao_fun;
                    });

        // 5. Montar nodes e edges
        $nodes = [];
        $edges = [];
        $idsAdicionados = [];

        foreach ($relacoes as $r) {
            foreach ([$r->p1, $r->p2] as $id) {
                if (!in_array($id, $idsAdicionados)) {

                    $funcao = $funcoes[$id] ?? 'Outro';
                    $cor = '#6c757d'; // cor padrão

                    if ($funcao === 'Orientador') {
                        $cor = '#007bff';
                    } elseif ($funcao === 'Participante') {
                        $cor = '#ffc107';
                    } elseif ($funcao === 'Inventor') {
                        $cor = '#28a745';
                    }

                    $nodes[] = [
                        'id' => $id,
                        'label' => $pessoas[$id]->ds_nome_pessoa ?? "ID:$id",
                        'color' => $cor
                    ];
                    $idsAdicionados[] = $id;
                }
            }

            $edges[] = [
                'from' => $r->p1,
                'to' => $r->p2,
                'value' => $r->peso
            ];
        }

        $dados = array('nodes' => $nodes, 'edges' => $edges);

        return response()->json($dados);

    }

    public function excel(Request $request)
    {
        $dados = array();

        $where = "WHERE 1=1";

        switch ($request->dimensao) {
            case 'extensao':
                $where .= ' AND id_dimensao = 2 '; 
                break;

            case 'pesquisa':
                $where .= ' AND id_dimensao = 1 ';
                break;
            
            default:
                
                break;
        }

        if($request->ppg){
            $where .= " AND nm_programa = '$request->ppg'";
        }

        if($request->ano_inicial and $request->ano_fim){
            $where .= " AND an_base BETWEEN '$request->ano_inicial' AND '$request->ano_fim' ";
        }

        if($request->tipo and $request->tipo != "todos"){
            $where .= " AND nm_subtipo_producao = '$request->tipo' ";
        }

        if($request->docente){
            $where .= " AND nm_orientador = '$request->docente'";
        }

        /*
            Definição dos ODS existentes

        */

        $sql = "SELECT t1.ods, t2.cor, count(*) as total 
                FROM capes_teses_dissertacoes_ctd t0
                JOIN documento_ods t1 ON t1.id_producao_intelectual = t0.id_producao_intelectual 
                RIGHT JOIN ods t2 ON t2.cod = t1.ods 
                $where
                GROUP BY t1.ods, t2.cor 
                ORDER BY t1.ods";

        $dados = DB::connection('pgsql')->select($sql);
        
        $ods_encontrados = array_column($dados, 'ods');
        
        $anos = array();
        $anos[] = (int) $request->ano_inicial;
        $inicio = (int) $request->ano_inicial;

        for ($i=$request->ano_inicial; $i < $request->ano_fim ; $i++) { 
            $anos[] = $inicio += 1;
        }

        $lista = array();
        $lista_ods = Ods::orderBy('cod')->get();
        $totais = array();

        for ($i=0; $i < count($anos); $i++) { 

            $totais = array();

            foreach ($lista_ods as $key => $ods) {

                $complemento = ' AND an_base = '.$anos[$i].'
                AND ods = '.$ods->cod.'
                GROUP BY t1.ods, an_base, t2.cor 
                ORDER BY t1.ods, an_base';

                $sql = "SELECT t1.ods, t0.an_base, t2.cor, count(*) as total 
                        FROM capes_teses_dissertacoes_ctd t0
                        JOIN documento_ods t1 ON t1.id_producao_intelectual = t0.id_producao_intelectual 
                        RIGHT JOIN ods t2 ON t2.cod = t1.ods 
                        $where
                        $complemento";

                $resultado = DB::connection('pgsql')->select($sql);

                if($resultado){
                    $totais[] = $resultado[0]->total;
                }else{
                    $totais[] = 0;
                }
            }

            $lista[$i]['ano'] = $anos[$i];

            for ($j=0; $j < count($totais); $j++) { 
                
                $indice = $j + 1;
                $lista[$i][$indice] = $totais[$j];

            }

            $lista[$i]['total'] = array_sum($totais);

        }

        return Excel::download(new DadosExport($lista), 'dados_evolucao.xlsx');
    }

    public function lerArquivo(Request $request)
    {

        $file = $request->file('arquivo');
        $extensions = array("csv","CSV");
        $delimiter = '|';

        if (!file_exists($file) || !is_readable($file))
        return false;

            $header = null;
            $data = array();
            if (($handle = fopen($file, 'r')) !== false)
            {
                while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
                {
                    if(count($row) == 4)
                        echo $row[1].";".$row[0].";".$row[2]."<br/>";
                }
                fclose($handle);
            }

            return $data;

        //return redirect('certificados/cadastrar/arquivo')->withInput();
    }
}