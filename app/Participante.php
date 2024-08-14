<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Participante extends Model
{
    use SoftDeletes;
    
    protected $connection = 'pgsql';
    protected $table = 'participante';
    protected $primaryKey = 'id';
    protected $fillable = ["nome"];
}