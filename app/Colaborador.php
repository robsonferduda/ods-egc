<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Colaborador extends Model
{
    use SoftDeletes;
    
    protected $connection = 'pgsql';
    protected $table = 'colaborador';
    protected $primaryKey = 'id';
    protected $fillable = [""];
}