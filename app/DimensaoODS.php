<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DimensaoODS extends Model
{
    use SoftDeletes;
    
    protected $connection = 'pgsql';
    protected $table = 'dimensao_ods';
    protected $primaryKey = 'cd_dimensao_ods';
    protected $fillable = [""];
}