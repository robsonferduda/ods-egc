<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Nivel extends Model
{
    use SoftDeletes;
    
    protected $connection = 'pgsql';
    protected $table = 'nivel';
    protected $primaryKey = 'id';
    protected $fillable = [""];
}