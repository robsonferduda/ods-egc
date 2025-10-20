<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentoPessoa extends Model
{    
    protected $connection = 'pgsql';
    protected $table = 'documento_pessoa_dop';
    protected $primaryKey = 'id';
    protected $fillable = [""];

}