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

        $total_documentos = 0;
        $documentos_sem_ods = 0;
        $documentos_com_ods = 0;
        $dimensao_predominante = '';
        $indice_crescimento_sustentavel = 0;
        $indice_engajamento_sustentavel = 0;
        $docente_destaque = '';

        $html = view('relatorio.estatisticas', compact('grafico_total',
        'grafico_evolucao','periodo','total_documentos',
        'documentos_sem_ods','documentos_com_ods','dimensao_predominante','indice_crescimento_sustentavel','indice_engajamento_sustentavel','docente_destaque'))->render();

        $file = date('Y-m-d_H-i-s_perfil_ods_resumo.pdf');

        $pdf = PDF::loadHTML($html);
        $path = public_path('relatorios/'.$file);
        $pdf->save($path);

        return response()->json(['url' => asset('relatorios/'.$file)]);
    }
}