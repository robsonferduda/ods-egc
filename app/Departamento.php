<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Departamento extends Model
{
    use SoftDeletes;
    
    protected $connection = 'pgsql';
    protected $table = 'departamento_dep';
    protected $primaryKey = 'id_departamento_dep';
    protected $fillable = [""];

}