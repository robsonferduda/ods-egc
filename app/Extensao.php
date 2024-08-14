<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Extensao extends Model
{
    use SoftDeletes;
    
    protected $connection = 'pgsql';
    protected $table = 'extensao';
    protected $primaryKey = 'id';
    protected $fillable = ["titulo","resumo","coordenador","participantes","depto"];
}