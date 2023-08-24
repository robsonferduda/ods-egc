<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Situacao extends Model
{   
    protected $connection = 'pgsql';
    protected $table = 'situacao_sit';
}