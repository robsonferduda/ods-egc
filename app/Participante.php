<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Participante extends Model
{    
    protected $connection = 'pgsql';
    protected $table = 'participante_par';
    protected $primaryKey = 'id_participante_par';
    protected $fillable = ['ds_nome_par','ds_email_par'];
    

    public function certificado()
    {
        return $this->hasOne('App\Certificado', 'id_participante_par', 'id_participante_par');
    }
    
}