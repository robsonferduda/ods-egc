<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoDocumento extends Model
{
    use SoftDeletes;
    
    protected $connection = 'pgsql';
    protected $table = 'tipo_documento';
    protected $primaryKey = 'id_tipo_documento';
    protected $fillable = [''];
}