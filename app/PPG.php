<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PPG extends Model
{
    use SoftDeletes;
    
    protected $connection = 'pgsql';
    protected $table = 'ppg';
    protected $primaryKey = 'id';
    protected $fillable = [""];
}