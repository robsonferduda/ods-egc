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
        $grafico = $request->input('grafico'); // base64 da imagem

        $html = view('relatorio.estatisticas', compact('grafico'))->render();

        $pdf = PDF::loadHTML($html);
        $path = storage_path('app/public/estatisticas.pdf');
        $pdf->save($path);
        return response()->json(['url' => asset('storage/estatisticas.pdf')]);
    }
}