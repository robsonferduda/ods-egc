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
        
        // Calcula documentos com ODS (pelo menos 1 ODS detectado)
        $sql_docs_com_ods = "SELECT COUNT(DISTINCT t0.id) as total 
                            FROM documento_ods t0
                            LEFT JOIN documento_pessoa_dop t2 ON t2.id_documento_ods = t0.id
                            LEFT JOIN pessoa_pes t3 ON t3.id_pessoa_pes = t2.id_pessoa_pes
                            $where AND t0.ods IS NOT NULL";
        
        $result_docs_com_ods = DB::connection('pgsql')->select($sql_docs_com_ods);
        $documentos_com_ods = $result_docs_com_ods[0]->total ?? 0;
        
        // Calcula documentos sem ODS
        $documentos_sem_ods = $total_documentos - $documentos_com_ods;
              
        $total_ods_detectados = count(array_filter($dados, function($item) {
            return $item->total > 0;
        }));

        // Busca os indicadores se houver centro selecionado
        $ics_valor = null;
        $ics_nivel = null;
        $ies_valor = null;
        $ies_nivel = null;
        $dimensao_predominante = null;
        $dimensao_predominante_percentual = null;

        if($request->centro && $request->centro != 'todos'){
            
            // ICS - Índice de Crescimento Sustentável
            $sql_ics = "SELECT ics_norm_0_100 FROM mv_ics_por_centro_docods 
                       WHERE id_centro = '$request->centro' 
                       AND ano = '$request->ano_fim'
                       LIMIT 1";
            $result_ics = DB::connection('pgsql')->select($sql_ics);
            
            if(!empty($result_ics)){
                $ics_valor = number_format($result_ics[0]->ics_norm_0_100, 1, ',', '.');
                $ics_float = floatval($result_ics[0]->ics_norm_0_100);
                
                if($ics_float > 50) {
                    $ics_nivel = 'Crescimento';
                    $ics_badge = 'success';
                } else if($ics_float == 50) {
                    $ics_nivel = 'Estável';
                    $ics_badge = 'warning';
                } else {
                    $ics_nivel = 'Queda';
                    $ics_badge = 'danger';
                }
            }

            // IES - Índice de Engajamento Sustentável
            $sql_ies = "SELECT sec_index FROM mv_ies_centro 
                       WHERE cd_centro = '$request->centro'
                       LIMIT 1";
            $result_ies = DB::connection('pgsql')->select($sql_ies);
            
            if(!empty($result_ies)){
                $ies_valor = number_format($result_ies[0]->sec_index, 1, ',', '.');
                $ies_float = floatval($result_ies[0]->sec_index);
                
                if($ies_float >= 70) {
                    $ies_nivel = 'Alto';
                    $ies_badge = 'success';
                } else if($ies_float >= 40) {
                    $ies_nivel = 'Médio';
                    $ies_badge = 'warning';
                } else {
                    $ies_nivel = 'Baixo';
                    $ies_badge = 'danger';
                }
            }

            // Dimensão Predominante ODS
            $sql_dim = "SELECT nm_dim_ods, total_docs, rk 
                       FROM mv_dimensao_ods_centro 
                       WHERE id_centro = '$request->centro'
                       ORDER BY rk ASC
                       LIMIT 1";
            $result_dim = DB::connection('pgsql')->select($sql_dim);
            
            if(!empty($result_dim)){
                $dimensao_predominante = $result_dim[0]->nm_dim_ods;
                
                // Calcula o percentual
                $sql_total_dim = "SELECT SUM(total_docs) as total FROM mv_dimensao_ods_centro 
                                 WHERE cd_centro = '$request->centro'";
                $result_total_dim = DB::connection('pgsql')->select($sql_total_dim);
                
                if(!empty($result_total_dim) && $result_total_dim[0]->total > 0){
                    $percentual = ($result_dim[0]->total_docs / $result_total_dim[0]->total) * 100;
                    $dimensao_predominante_percentual = number_format($percentual, 1, ',', '.');
                }
            }
        }

        $docente_destaque = '';

        $html = view('relatorio.estatisticas', compact('grafico_total',
        'grafico_evolucao','periodo','total_documentos',
        'total_ods_detectados',
        'documentos_sem_ods','documentos_com_ods',
        'ics_valor','ics_nivel','ics_badge',
        'ies_valor','ies_nivel','ies_badge',
        'dimensao_predominante','dimensao_predominante_percentual',
        'docente_destaque', 'centro'))->render();

        $file = date('Y-m-d_H-i-s_perfil_ods_resumo.pdf');

        $pdf = PDF::loadHTML($html);
        $path = public_path('relatorios/'.$file);
        $pdf->save($path);

        return response()->json(['url' => asset('relatorios/'.$file)]);
    }
}