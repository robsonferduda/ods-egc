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