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

class CertificadoController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index()
    {
        $certificados = Certificado::whereIn('id_modelo_certificado_moc', [18,19,20,21,22])->get();
        //$certificados = Certificado::all();
        return view('certificados/index', compact('certificados'));
    }

    public function arquivo()
    {
        $modelos = ModeloCertificado::all();
        return view('certificados/arquivos', compact('modelos'));
    }

    public function create()
    {
        $participantes = Participante::orderBy('ds_nome_par')->get();
        $modelos = ModeloCertificado::all();
        return view('certificados/create', compact('participantes','modelos'));
    }

    public function cadastrarLista(CertificadoArquivoRequest $request)
    {

        $file = $request->file('arquivo');
        $extensions = array("csv","CSV");

        if($file){

            $path = $file->getRealPath();
            if(in_array($file ->getClientOriginalExtension(),$extensions)){
                
                    $data = Excel::toCollection(new ParticipanteImport,$file);

                    foreach($data as $participante){

                        foreach($participante as $p){

                            $participante = Participante::where('ds_email_par',$p->get('email'))->with('certificado')->first();

                            DB::transaction(function () use ($p, $request, $participante) {
    
                                if(!$participante){

                                    $pa = new Participante();
                                    $pa->ds_nome_par = $p->get('nome');
                                    $pa->ds_email_par = $p->get('email');
                                    $participante = $pa->save();
    
                                    $flag = true;
    
                                    while($flag){
                                        $hash = Utils::gerarHash();
                                        if(!Certificado::where('ds_hash_cer',$hash)->first()){
                                            $flag = false;
                                        }
                                    }
    
                                    $certicado = new Certificado();
                                    $certicado->id_participante_par = $pa->id_participante_par;
                                    $certicado->id_modelo_certificado_moc = $request->id_modelo_certificado_moc;
                                    $certicado->id_situacao_sit = 1;
                                    $certicado->ds_hash_cer = $hash;
                                    $certicado->save();

                                    if($request->id_modelo_certificado_moc == 19)
                                    {
                                        $metadados = new CertificadoMetadado();
                                        $metadados->id_certificado_cer = $certicado->id_certificado_cer;
                                        $metadados->label_metadado_cem = '#autores';
                                        $metadados->valor_metadado_cem = $p->get('autores');
                                        $metadados->save();

                                        $metadados = new CertificadoMetadado();
                                        $metadados->id_certificado_cer = $certicado->id_certificado_cer;
                                        $metadados->label_metadado_cem = '#titulo';
                                        $metadados->valor_metadado_cem = $p->get('titulo');
                                        $metadados->save();
                                    }
    
                                }else{
    
                                    $flag = true;
    
                                    while($flag){
                                        $hash = Utils::gerarHash();
                                        if(!Certificado::where('ds_hash_cer',$hash)->first()){
                                            $flag = false;
                                        }
                                    }
    
                                    $certicado = new Certificado();
                                    $certicado->id_participante_par = $participante->id_participante_par;
                                    $certicado->id_modelo_certificado_moc = $request->id_modelo_certificado_moc;
                                    $certicado->id_situacao_sit = 1;
                                    $certicado->ds_hash_cer = $hash;
                                    $certicado->save();

                                    if($request->id_modelo_certificado_moc == 19)
                                    {
                                        $metadados = new CertificadoMetadado();
                                        $metadados->id_certificado_cer = $certicado->id_certificado_cer;
                                        $metadados->label_metadado_cem = '#autores';
                                        $metadados->valor_metadado_cem = $p->get('autores');
                                        $metadados->save();

                                        $metadados = new CertificadoMetadado();
                                        $metadados->id_certificado_cer = $certicado->id_certificado_cer;
                                        $metadados->label_metadado_cem = '#titulo';
                                        $metadados->valor_metadado_cem = $p->get('titulo');
                                        $metadados->save();
                                    }
    
                                }
                                

                            });

                        }
                    }
            }

        }

        return redirect('certificados/cadastrar/arquivo')->withInput();
    }

    public function store(Request $request)
    {
        try {

            $flag = true;

            while($flag){
                $hash = Utils::gerarHash();
                if(!Certificado::where('ds_hash_cer',$hash)->first()){
                    $flag = false;
                }
            }

            $certicado = new Certificado();
            $certicado->id_participante_par = $request->id_participante_par;
            $certicado->id_modelo_certificado_moc = $request->id_modelo_certificado_moc;
                                
            $certicado->ds_hash_cer = $hash;
            $cert = $certicado->save();   
            
            dd($cert);
            
            $retorno = array('flag' => true,
                             'msg' => "Dados inseridos com sucesso");

        } catch (\Illuminate\Database\QueryException $e) {

            $retorno = array('flag' => false,
                             'msg' => Utils::getDatabaseMessageByCode($e->getCode()));

        } catch (Exception $e) {
            
            $retorno = array('flag' => true,
                             'msg' => "Ocorreu um erro ao inserir o registro");
        }

        if ($retorno['flag']) {
            Flash::success($retorno['msg']);
            return redirect('certificados')->withInput();
        } else {
            Flash::error($retorno['msg']);
            return redirect('certificados/create')->withInput();
        }
    }

    public function validarHash($hash){

        $dados = Certificado::where('ds_hash_cer', $hash)->with('participante')->with('situacao')->first();

        if($dados){
      
            $dados->increment('nu_total_validacoes_cer');
            return redirect('certificados/validacao')->with(['certificado' => $dados])->withInput();

        }else{
            Flash::error("Código de autenticação inválido ou não encontrado");
            return redirect('certificados/validacao')->withInput();
        }

    }

    public function validarCertificado(Request $request)
    {
        $dados = Certificado::where('ds_hash_cer', $request->chave)->with('participante')->with('situacao')->first();

        if($dados){
      
            $dados->increment('nu_total_validacoes_cer');
            return redirect('certificados/validacao')->with(['certificado' => $dados])->withInput();

        }else{
            Flash::error("Código de autenticação inválido ou não encontrado");
            return redirect('certificados/validacao')->withInput();
        }

    }

    public function emitirCertificado(Request $request)
    {
        $dados = Certificado::whereHas('participante', function($q) use ($request){
                    $q->where('ds_email_par', $request->email);
                })->with('modelo')
                ->get();
        
        if($dados){
      
            return redirect('certificados/segunda-via')->with(['dados' => $dados])->withInput();

        }else{
            Flash::error("Não foi encontrado nenhum participante com o código informado");
            return redirect('certificados/segunda-via')->withInput();
        }

    }

    public function gerarCertificado($codigo)
    {
        $dados = Certificado::where('ds_hash_cer', $codigo)->with('participante')->first();
        
        $tipo = strtolower($dados->modelo->tipo->ds_tipo_participacao_tip);

        if($dados){

            $equipe = "";
            $dados->increment('nu_total_impressoes_cer');

            if($dados->metadados->where('label_metadado_cem','#autores')->first()){

                $partes = explode(",", $dados->metadados->where('label_metadado_cem','#autores')->first()->valor_metadado_cem);
                if(count($partes)){
                    for ($i=0; $i < count($partes); $i++) { 
                        
                        if($i == (count($partes) - 2))
                            $equipe .= $partes[$i]." e ";
                        elseif($i != (count($partes) - 1))
                            $equipe .= $partes[$i].", ";
                        else
                            $equipe .= $partes[$i];
                        
                    }
                }

            }        

            $nome_arquivo = 'certificado_'.$tipo.'_'.$dados->participante->ds_email_par.'.pdf';
            $data = [
                'certificado' => $dados,
                'equipe' => $equipe
            ];
            
            $pdf = PDF::loadView('templates.'.$dados->modelo->ds_template_moc, $data,  [], ['title' => 'SGC - Certificado', 'format' => 'A4-L', 'margin_bottom' => 0]);
            return $pdf->download($nome_arquivo);

        }else{
            Flash::error("Código de autenticação inválido ou não encontrado");
            return redirect('validar')->withInput();
        }

    }

    public function validar(Request $request)
    {
        $dados = CertificadoCiki::where('de_hash', $request->chave)->with('participante')->with('situacao')->first();

        if($dados){
      
            return redirect('validar')->with(['certificado' => $dados])->withInput();

        }else{
            Flash::error("Código de autenticação inválido ou não encontrado");
            return redirect('validar')->withInput();
        }

    }

    public function emitir(Request $request)
    {
        $dados = ParticipanteCiki::where('nr_documento', $request->chave)->with('certificado')->get();

        if($dados){
      
            return redirect('emitir')->with(['dados' => $dados])->withInput();

        }else{
            Flash::error("Não foi encontrado nenhum participante com o código informado");
            return redirect('emitir')->withInput();
        }

    }

    public function gerarCertificadoPdf($codigo)
    {
        $dados = CertificadoCiki::where('de_hash', $codigo)->with('participante')->first();

        if($dados){

            $nome_arquivo = 'certificado_'.$codigo;
            $data = [
                'certificado' => $dados
            ];
            
            $pdf = PDF::loadView('certificados.template_antigo', $data,  [], ['title' => 'SGC - Certificado', 'format' => 'A4-L']);
            return $pdf->download('certificado.pdf');

        }else{
            Flash::error("Código de autenticação inválido ou não encontrado");
            return redirect('validar')->withInput();
        }

    }

    public function notificar($id)
    {
        $certificado = Certificado::where('id_certificado_cer', $id)->first();

        if($certificado){

            $to_name = $certificado->participante->ds_nome_par;
            $to_email = $certificado->participante->ds_email_par;
            $hash = $certificado->ds_hash_cer; 
            $evento = $certificado->modelo->evento->nm_evento_eve;
            $tipo = $certificado->modelo->tipo->ds_tipo_participacao_tip;

            /* Atualização da flag de notificação */
            $certificado->fl_notificacao_cer = 'S';
            $certificado->save();

            $data = array("name"=> $to_name, 'hash' => $hash, 'evento' => $evento, 'tipo' => $tipo);

            Mail::send('email.notificacao', $data, function($message) use ($to_name, $to_email, $hash, $evento, $tipo) {
                $message->to($to_email, $to_name)
                        ->subject('Certificado de '.$tipo.' disponível - '.$evento)
                        ->from('nao.responda.studiokem@gmail.com','Não responda');
            });

            Flash::success("Participante $to_name notificado com sucesso no email $to_email");
            return redirect('certificados')->withInput();

        }else{
            Flash::error("Certificado não encontrado");
            return redirect('certificados')->withInput();
        }

    }

    public function excluir($id)
    {
        $certificado = Certificado::where('id_certificado_cer', $id)->first();
        $certificado->delete();

        Flash::success("Certificado excluído com sucesso");
        return redirect('certificados')->withInput();
    }
}