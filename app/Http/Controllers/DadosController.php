<?php

namespace App\Http\Controllers;

use DB;
use Excel;
use PDF;
use Mail;
use App\Utils;
use App\Certificado;
use App\CertificadoMetadado;
use App\Participante;
use App\ModeloCertificado;
use App\CertificadoCiki;
use App\ParticipanteCiki;
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