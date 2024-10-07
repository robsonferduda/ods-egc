<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Log extends Model
{
    use SoftDeletes;
    
    protected $connection = 'pgsql';
    protected $table = 'log_acesso';
    protected $primaryKey = 'id';
    protected $fillable = ["ip","cidade","uf"];
}