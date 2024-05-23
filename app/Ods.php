<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ods extends Model
{
    use SoftDeletes;
    
    protected $connection = 'pgsql';
    protected $table = 'ods';
    protected $primaryKey = 'id';
    protected $fillable = [""];
}