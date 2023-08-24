<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CertificadoCiki extends Model
{    
    protected $connection = 'pgsql_ciki';
    
    protected $table = 'certificados_participante';

    public function participante()
    {
        return $this->belongsTo('App\ParticipanteCiki', 'id_participante', 'id_participante');
    }

    public function situacao()
    {
        return $this->belongsTo('App\Situacao', 'fl_ativo', 'cd_situacao_sit');
    }    

}