<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Relacao extends Model
{
    use SoftDeletes;
    
    protected $connection = 'pgsql';
    protected $table = 'relacao';
    protected $primaryKey = 'id';
    protected $fillable = ["id_coordenador","id_participante"];
}