<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Analise extends Model
{
    use SoftDeletes;
    
    protected $connection = 'pgsql';
    protected $table = 'analises';
    protected $primaryKey = 'id';
    protected $fillable = ["cd_usuario","texto","ods","id_modelo","probabilidade"];

    public function odsDetectado()
    {
        return $this->hasOne('App\Ods', 'id', 'ods');
    }

    public function modelo()
    {
        return $this->hasOne('App\Modelo', 'id', 'id_modelo');
    }
}