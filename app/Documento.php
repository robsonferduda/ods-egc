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

    public function dimensao()
    {
        return $this->hasOne('App\Dimensao', 'id', 'id_dimensao');
    }

    public function dimensaoOds()
    {
        return $this->hasOne('App\DimensaoODS', 'cd_dimensao_ods', 'id_dimensao_ods');
    }

    public function tipo()
    {
        return $this->hasOne('App\TipoDocumento', 'id_tipo_documento', 'id_tipo_documento');
    }

    public function classificacao()
    {
        return $this->hasOne('App\Ods', 'cod', 'ods');
    }
}