<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pontuacao extends Model
{
    use SoftDeletes;
    
    protected $connection = 'pgsql';
    protected $table = 'pontuacao';
    protected $primaryKey = 'id';
    protected $fillable = ["id_usuario","total_pts","acao"];
}