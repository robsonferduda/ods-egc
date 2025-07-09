<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Documento extends Model
{
    use SoftDeletes;
    
    protected $connection = 'pgsql';
    protected $table = 'documento_ods';
    protected $primaryKey = 'id';
    protected $fillable = [""];

    public function probabilidades()
    {
        return $this->hasOne(Probabilidade::class, 'id_documento_ods', 'id');
    }
}