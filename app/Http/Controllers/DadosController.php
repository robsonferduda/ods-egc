<?php

namespace App\Http\Controllers;

use DB;
use Excel;
use PDF;
use Mail;
use App\Utils;
use App\Ods;
use App\Cidade;
use App\Dimensao;
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
        ', [$id]);

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
                        $cor = '#FD6925';
                    }elseif ($funcao === 'Aluno') {
                        $cor = '#28a745';
                    }elseif ($funcao === 'Coordenador') {
                        $cor = '#C5192D';
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

        $filtros = '';

        $filtros .= 'Período de '.$request->ano_inicial.' a '.$request->ano_fim;

        $dados = array();

        $where = "WHERE 1=1";

        $id_dimensao = 0;

        switch ($request->dimensao) {
            
            case 'pos-graduacao':
                $where .= ' AND t0.id_dimensao = 6 ';
                $id_dimensao = 6;
                break;

            case 'pesquisa':
                $where .= ' AND t0.id_dimensao = 5 ';
                $id_dimensao = 5;
                break;

            case 'extensao':
                $where .= ' AND t0.id_dimensao = 2 '; 
                $id_dimensao = 2;
                break;            
            
            case 'gestao':
                $where .= ' AND t0.id_dimensao = 3 ';
                $id_dimensao = 3;
                break;

            case 'inovacao':
                $where .= ' AND t0.id_dimensao = 4 ';
                $id_dimensao = 4;
                break;

            case 'ensino':
                $where .= ' AND t0.id_dimensao = 1 ';
                $id_dimensao = 1;
                break;
                
            default:
                
                break;
        }

        if($id_dimensao){
            $dimensao = Dimensao::where('id', $id_dimensao)->first();
            $filtros .= ' / Dimensão IES: '.$dimensao->nome;
        }

        //Filtro por tipo de documento
        if($request->tipo and $request->tipo != "todos"){
            $where .= " AND t0.id_tipo_documento = '$request->tipo' ";
        }

        //Filtro por ano
        if($request->ano_inicial and $request->ano_fim){
            $where .= " AND ano BETWEEN '$request->ano_inicial' AND '$request->ano_fim' ";
        }

        //Filtro por centro
        if($request->centro){
            $where .= " AND id_centro = '$request->centro' ";
        }

        //Filtro por departamento
        if($request->departamento){
            $where .= " AND id_departamento = '$request->departamento' ";
        }

        //Filtro por programa
        if($request->ppg){
            $where .= " AND id_ppg = '$request->ppg' ";
        }

         $sql = "SELECT t0.ods, t1.cor, count(*) as total 
                FROM documento_ods t0
                RIGHT JOIN ods t1 ON t1.cod = t0.ods 
                LEFT JOIN documento_pessoa_dop t2 ON t2.id_documento_ods = t0.id
                LEFT JOIN pessoa_pes t3 ON t3.id_pessoa_pes = t2.id_pessoa_pes
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

        $lista = array();
        $lista_ods = Ods::orderBy('cod')->get();
        $totais = array();

        for ($i=0; $i < count($anos); $i++) { 

            $totais = array();

            foreach ($lista_ods as $key => $ods) {

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
        
        // Após o loop que monta $lista
        $colunas = count($lista_ods); // número de ODS/colunas
        $soma_colunas = array_fill(1, $colunas, 0);
        $soma_total = 0;

        foreach ($lista as $linha) {
            for ($i = 1; $i <= $colunas; $i++) {
                $soma_colunas[$i] += isset($linha[$i]) ? $linha[$i] : 0;
            }
            $soma_total += isset($linha['total']) ? $linha['total'] : 0;
        }

        // Adiciona linha de somatório ao final
        $linha_soma = ['ano' => 'Total'];
        for ($i = 1; $i <= $colunas; $i++) {
            $linha_soma[$i] = $soma_colunas[$i];
        }
        $linha_soma['total'] = $soma_total;

        $lista[] = $linha_soma;

        $linha_percentual = ['ano' => '%'];
        for ($i = 1; $i <= $colunas; $i++) {
            $perc = $soma_total > 0 ? ($soma_colunas[$i] / $soma_total) * 100 : 0;
            $linha_percentual[$i] = number_format($perc, 2, ',', '') . '%';
        }
        $linha_percentual['total'] = '100,00%';

        $lista[] = $linha_percentual;

        $lista[count($lista) - 1]['filtros'] = $filtros;

        $nome_arquivo = date('Y-m-d-H-i-s').'_dados_evolucao.xlsx';

        return Excel::download(new DadosExport($lista), $nome_arquivo);
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