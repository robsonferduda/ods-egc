<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Probabilidade extends Model
{
    use SoftDeletes;
    
    protected $connection = 'pgsql';
    protected $table = 'probabilidades';
    protected $primaryKey = 'id';
    protected $fillable = [""];
}