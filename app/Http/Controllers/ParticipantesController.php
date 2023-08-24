<?php

namespace App\Http\Controllers;

use DB;
use Excel;
use App\Utils;
use App\Certificado;
use App\Participante;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Imports\ParticipanteImport;

class ParticipantesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $participantes = Participante::all();
        return view('participantes/index', compact('participantes','participantes'));
    }

    public function create()
    {
        return view('participantes/create');
    }

    public function store(Request $request)
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

                            DB::transaction(function () use ($p, $participante) {

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
                                    $certicado->id_modelo_certificado_moc = 1;
                                    $certicado->id_situacao_sit = 1;
                                    $certicado->ds_hash_cer = $hash;
                                    $certicado->save();
    
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
                                    $certicado->id_modelo_certificado_moc = 1;
                                    $certicado->id_situacao_sit = 1;
                                    $certicado->ds_hash_cer = $hash;
                                    $certicado->save();
    
                                }

                            });

                            /*
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
                                $certicado->id_modelo_certificado_moc = 1;
                                $certicado->id_situacao_sit = 1;
                                $certicado->ds_hash_cer = $hash;
                                $certicado->save();

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
                                $certicado->id_modelo_certificado_moc = 1;
                                $certicado->id_situacao_sit = 1;
                                $certicado->ds_hash_cer = $hash;
                                $certicado->save();

                            }
                            */
                        }
                    }
                

            }

            /*
            if(in_array($file ->getClientOriginalExtension(),$extensions)){
                
                try {
                    $data =  Excel::import(new VaraImport,$file);
                } catch (\ErrorException $e) {
                    Flash::error('Erro ao atualizar dados. Msg: '.$e->getMessage());
                }
            }else{
                Flash::error('Erro ao atualizar dados');
            }
            */
        }else{
            Flash::error('Erro ao atualizar dados');
        }
        return redirect('participantes/create')->withInput();
    }
}