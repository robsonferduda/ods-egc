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
        
        $ods_encontrados = array_column($dados, 'ods');
        
        $total_documentos = 0;
        $documentos_sem_ods = 0;
        $documentos_com_ods = 0;
        $dimensao_predominante = '';
        $indice_crescimento_sustentavel = 0;
        $indice_engajamento_sustentavel = 0;
        $docente_destaque = '';

        $html = view('relatorio.estatisticas', compact('lista','grafico_total',
        'grafico_evolucao','periodo','total_documentos',
        'documentos_sem_ods','documentos_com_ods','dimensao_predominante','indice_crescimento_sustentavel','indice_engajamento_sustentavel','docente_destaque', 'centro'))->render();

        $file = date('Y-m-d_H-i-s_perfil_ods_resumo.pdf');

        $pdf = PDF::loadHTML($html);
        $path = public_path('relatorios/'.$file);
        $pdf->save($path);

        return response()->json(['url' => asset('relatorios/'.$file)]);
    }
}