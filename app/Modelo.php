<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Modelo extends Model
{
    use SoftDeletes;
    
    protected $connection = 'pgsql';
    protected $table = 'modelo';
    protected $primaryKey = 'id';
    protected $fillable = [""];
}