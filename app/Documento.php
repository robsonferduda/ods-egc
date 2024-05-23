<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Documento extends Model
{
    use SoftDeletes;
    
    protected $connection = 'pgsql';
    protected $table = 'capes_teses_dissertacoes_ctd';
    protected $primaryKey = 'id_capes_teses_dissertacoes_ctd';
    protected $fillable = [""];
}