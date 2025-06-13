<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Avaliacao extends Model
{
    use SoftDeletes;
    
    protected $connection = 'pgsql';
    protected $table = 'avaliacao';
    protected $primaryKey = 'id';
    protected $fillable = ["tipo","id_documento","id_usuario","voto"];

    public function usuario()
    {
        return $this->hasOne('App\User', 'id', 'id_usuario');
    }
}