<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pessoa extends Model
{
    use SoftDeletes;
    
    protected $connection = 'pgsql';
    protected $table = 'pessoa_pes';
    protected $primaryKey = 'id_pessoa_pes';
    protected $fillable = ['ds_image_pes'];
}