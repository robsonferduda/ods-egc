<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classificacao extends Model
{
    use SoftDeletes;
    
    protected $connection = 'pgsql';
    protected $table = 'documento_ods';
    protected $primaryKey = 'id';
    protected $fillable = [""];
}