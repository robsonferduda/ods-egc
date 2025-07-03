<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dimensao extends Model
{
    use SoftDeletes;
    
    protected $connection = 'pgsql';
    protected $table = 'dimensao_ies';
    protected $primaryKey = 'id';
    protected $fillable = [""];

    public function tiposDocumentos()
    {
        return $this->hasMany('App\TipoDocumento', 'id_dimensao_ies', 'id');
    }
}