<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoParticipacao extends Model
{
    use SoftDeletes;
    
    protected $connection = 'pgsql';
    protected $table = 'tipo_participacao_tip';
    protected $primaryKey = 'id_tipo_participacao_tip';
    protected $fillable = [''];

    

}