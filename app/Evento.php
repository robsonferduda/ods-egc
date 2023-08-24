<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Evento extends Model
{
    use SoftDeletes;
    
    protected $connection = 'pgsql';
    protected $table = 'evento_eve';
    protected $primaryKey = 'cd_evento_eve';
    protected $fillable = ['nm_evento_eve','dt_inicio_eve','dt_final_eve'];

    public function certificado()
    {
        return $this->hasMany('App\Certificado', 'cd_evento_eve', 'cd_evento_eve');
    }
}