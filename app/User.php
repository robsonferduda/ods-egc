<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laratrust\Traits\LaratrustUserTrait;

class User extends Authenticatable
{
    use LaratrustUserTrait;
    use Notifiable;

    protected $connection = 'pgsql';

    protected $fillable = [
        'name', 'email', 'password','sexo','dt_nascimento','cd_estado','cd_cidade','id_nivel','pts'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function nivel()
    {
        return $this->hasOne(Nivel::class, 'id', 'id_nivel');
    }

    public function analise()
    {
        return $this->hasMany(Analise::class, 'cd_usuario', 'id');
    }

    public function avaliacao()
    {
        return $this->hasMany(Avaliacao::class, 'id_usuario', 'id');
    }
}