<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParticipanteCiki extends Model
{    
    protected $connection = 'pgsql_ciki';

    protected $table = 'participantes';

    public function certificado()
    {
        return $this->hasOne('App\CertificadoCiki', 'id_participante', 'id_participante');
    }
    
    public function getEvento($modelo)
    {
        return DB::select('SELECT t1.nm_evento 
                            FROM eventos t1, certificados_modelo t2 
                            WHERE t1.id_evento = t2.id_evento 
                            AND t2.id_certificado_modelo = '.$modelo)[0]->nm_evento;
    }
}