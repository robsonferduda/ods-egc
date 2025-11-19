<?php

namespace App\Http\Controllers;

use DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Ods;
use App\Log;
use App\Dimensao;
use App\Certificado;
use App\Participante;
use Illuminate\Http\Request;

class RelatorioController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index()
    {
        
    }

    public function gerarPdf(Request $request)
    {
        $grafico_total = $request->input('grafico'); // base64 da imagem
        $grafico_evolucao = $request->input('grafico_evolucao'); // base64 da imagem

        $periodo = 'Período de '.$request->ano_inicio.' a '.$request->ano_fim;

        if ($request->centro && $request->centro != 'todos') {
            $centro = DB::table('centro_cen')->where('cd_centro_cen', $request->centro)->value('ds_nome_cen');
        } else {
            $centro = 'Todos os Centros';
        }

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
        
        // Calcula o total de documentos analisados
        $sql_total_docs = "SELECT COUNT(DISTINCT t0.id) as total 
                          FROM documento_ods t0
                          LEFT JOIN documento_pessoa_dop t2 ON t2.id_documento_ods = t0.id
                          LEFT JOIN pessoa_pes t3 ON t3.id_pessoa_pes = t2.id_pessoa_pes
                          $where";
        
        $result_total_docs = DB::connection('pgsql')->select($sql_total_docs);
        $total_documentos = $result_total_docs[0]->total ?? 0;
        
        // Calcula documentos com ODS (quando ods != 0)
        $sql_docs_com_ods = "SELECT COUNT(DISTINCT t0.id) as total 
                            FROM documento_ods t0
                            LEFT JOIN documento_pessoa_dop t2 ON t2.id_documento_ods = t0.id
                            LEFT JOIN pessoa_pes t3 ON t3.id_pessoa_pes = t2.id_pessoa_pes
                            $where AND t0.ods != 0";
        
        $result_docs_com_ods = DB::connection('pgsql')->select($sql_docs_com_ods);
        $documentos_com_ods = $result_docs_com_ods[0]->total ?? 0;
        
        // Calcula documentos sem ODS (quando ods = 0)
        $sql_docs_sem_ods = "SELECT COUNT(DISTINCT t0.id) as total 
                            FROM documento_ods t0
                            LEFT JOIN documento_pessoa_dop t2 ON t2.id_documento_ods = t0.id
                            LEFT JOIN pessoa_pes t3 ON t3.id_pessoa_pes = t2.id_pessoa_pes
                            $where AND t0.ods = 0";
        
        $result_docs_sem_ods = DB::connection('pgsql')->select($sql_docs_sem_ods);
        $documentos_sem_ods = $result_docs_sem_ods[0]->total ?? 0;
              
        $total_ods_detectados = count(array_filter($dados, function($item) {
            return $item->total > 0;
        }));

        // Busca os indicadores se houver centro selecionado
        $ict_valor = null;
        $ict_nivel = null;
        $ict_ods_unicos = null;
        $ivc_valor = null;
        $ivc_nivel = null;
        $ivc_ano_anterior = null;
        $ivc_total_anterior = null;
        $ivc_ano_atual = null;
        $ivc_total_atual = null;
        $dimensao_predominante = null;
        $dimensao_predominante_percentual = null;

        if($request->centro && $request->centro != 'todos'){
            
            // ICT-ODS - Índice de Colaboração Temática
            $resultado_ict = DB::select('
                SELECT 
                    COUNT(DISTINCT ods) as ods_unicos,
                    ROUND((COUNT(DISTINCT ods)::numeric / 16) * 100, 2) as ict_percentual
                FROM documento_ods
                WHERE id_centro = ?
                AND ano = ?
                AND ods BETWEEN 1 AND 16
            ', [$request->centro, $request->ano_fim]);

            if(!empty($resultado_ict) && $resultado_ict[0]->ods_unicos > 0){
                $ict_valor = number_format($resultado_ict[0]->ict_percentual, 1, ',', '.');
                $ict_ods_unicos = $resultado_ict[0]->ods_unicos;
                $ict_float = floatval($resultado_ict[0]->ict_percentual);
                
                if($ict_float >= 75) {
                    $ict_nivel = 'Alto';
                    $ict_badge = 'success';
                } else if($ict_float >= 50) {
                    $ict_nivel = 'Médio';
                    $ict_badge = 'warning';
                } else {
                    $ict_nivel = 'Baixo';
                    $ict_badge = 'danger';
                }
            }

            // IVC-ODS - Índice de Variação de Contribuição
            $ano_atual = $request->ano_fim;
            $ano_anterior = $ano_atual - 1;

            $resultado_ivc = DB::select('
                SELECT 
                    ano,
                    COUNT(*) as total_documentos
                FROM documento_ods
                WHERE id_centro = ?
                AND ano IN (?, ?)
                GROUP BY ano
                ORDER BY ano
            ', [$request->centro, $ano_anterior, $ano_atual]);

            if(count($resultado_ivc) >= 2){
                $total_anterior = $resultado_ivc[0]->total_documentos;
                $total_atual = $resultado_ivc[1]->total_documentos;
                
                $ivc = (($total_atual - $total_anterior) / $total_anterior) * 100;
                $ivc_valor = number_format(abs($ivc), 1, ',', '.');
                
                $ivc_ano_anterior = $ano_anterior;
                $ivc_total_anterior = $total_anterior;
                $ivc_ano_atual = $ano_atual;
                $ivc_total_atual = $total_atual;
                
                if($ivc > 0) {
                    $ivc_nivel = 'Crescimento';
                    $ivc_badge = 'success';
                } else if($ivc < 0) {
                    $ivc_nivel = 'Queda';
                    $ivc_badge = 'danger';
                } else {
                    $ivc_nivel = 'Estável';
                    $ivc_badge = 'warning';
                }
            }

            // Dimensão Predominante ODS
            $sql_dim = "WITH dist AS (
                            SELECT
                                c.cd_centro_cen           AS id_centro,
                                c.ds_sigla_cen            AS sigla_centro,
                                do2.cd_dimensao_ods       AS id_dim_ods,
                                do2.ds_dimensao           AS nm_dim_ods,
                                COUNT(*)                  AS total_docs
                            FROM public.fato_documento_ods f
                            JOIN public.centro_cen c  ON c.cd_centro_cen = f.id_centro_resolvido
                            JOIN public.dimensao_ods do2 ON do2.cd_dimensao_ods = f.id_dimensao_ods
                            WHERE c.cd_centro_cen = '$request->centro'
                            GROUP BY c.cd_centro_cen, c.ds_sigla_cen, do2.cd_dimensao_ods, do2.ds_dimensao
                            ),
                            ranked AS (
                            SELECT *,
                                    ROW_NUMBER() OVER (PARTITION BY id_centro ORDER BY total_docs DESC, id_dim_ods) AS rk
                            FROM dist
                            )
                            SELECT *
                            FROM ranked
                            WHERE rk = 1
                            ORDER BY sigla_centro, total_docs DESC
                            LIMIT 1";
            $result_dim = DB::connection('pgsql')->select($sql_dim);
            
            if(!empty($result_dim)){
                $dimensao_predominante = $result_dim[0]->nm_dim_ods;
                
                // Calcula o percentual
                $sql_total_dim = "SELECT COUNT(*) as total 
                                 FROM public.fato_documento_ods f
                                 JOIN public.centro_cen c ON c.cd_centro_cen = f.id_centro_resolvido
                                 WHERE c.cd_centro_cen = '$request->centro'";
                $result_total_dim = DB::connection('pgsql')->select($sql_total_dim);
                
                if(!empty($result_total_dim) && $result_total_dim[0]->total > 0){
                    $percentual = ($result_dim[0]->total_docs / $result_total_dim[0]->total) * 100;
                    $dimensao_predominante_percentual = number_format($percentual, 1, ',', '.');
                }
            }
        }

        // Busca dados para tabela de documentos por ODS e por ano
        $sql_tabela = "SELECT 
                        t0.ods,
                        t0.ano,
                        COUNT(DISTINCT t0.id) as total
                      FROM documento_ods t0
                      LEFT JOIN documento_pessoa_dop t2 ON t2.id_documento_ods = t0.id
                      LEFT JOIN pessoa_pes t3 ON t3.id_pessoa_pes = t2.id_pessoa_pes
                      $where
                      GROUP BY t0.ods, t0.ano
                      ORDER BY t0.ano ASC, t0.ods ASC";
        
        $dados_tabela = DB::connection('pgsql')->select($sql_tabela);
        
        // Organiza os dados em formato de tabela (anos x ODS)
        $anos = [];
        $tabela_ods = [];
        
        foreach($dados_tabela as $linha){
            $ano = (int)$linha->ano;
            $ods = (int)$linha->ods;
            $total = (int)$linha->total;
            
            if(!in_array($ano, $anos)){
                $anos[] = $ano;
            }
            
            if(!isset($tabela_ods[$ods])){
                $tabela_ods[$ods] = [];
            }
            
            $tabela_ods[$ods][$ano] = $total;
        }
        
        sort($anos);
        ksort($tabela_ods);

        $docente_destaque = '';

        $html = view('relatorio.estatisticas', compact('grafico_total',
        'grafico_evolucao','periodo','total_documentos',
        'total_ods_detectados',
        'documentos_sem_ods','documentos_com_ods',
        'ict_valor','ict_nivel','ict_badge','ict_ods_unicos',
        'ivc_valor','ivc_nivel','ivc_badge',
        'ivc_ano_anterior','ivc_total_anterior','ivc_ano_atual','ivc_total_atual',
        'dimensao_predominante','dimensao_predominante_percentual',
        'docente_destaque', 'centro', 'tabela_ods', 'anos'))->render();

        $file = date('Y-m-d_H-i-s_perfil_ods_resumo.pdf');

        $pdf = PDF::loadHTML($html);
        $path = public_path('relatorios/'.$file);
        $pdf->save($path);

        return response()->json(['url' => asset('relatorios/'.$file)]);
    }
}