<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Centro extends Model
{    
    protected $connection = 'pgsql';
    protected $table = 'centro_cen';

    protected $fillable = [''];   
}