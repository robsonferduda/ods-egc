<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModeloCertificado extends Model
{
    use SoftDeletes;
    
    protected $connection = 'pgsql';
    protected $table = 'modelo_certificado_moc';
    protected $primaryKey = 'id_modelo_certificado_moc';
    protected $keyType = 'integer';
    protected $fillable = ['cd_evento_eve', 'id_tipo_participacao_tip', 'total_horas_moc', 'ds_conteudo_moc','fl_complemento_moc', 'complemento_moc'];

    public function evento()
    {
        return $this->belongsTo('App\Evento', 'cd_evento_eve', 'cd_evento_eve');
    }

    public function tipo()
    {
        return $this->hasOne('App\TipoParticipacao', 'id_tipo_participacao_tip', 'id_tipo_participacao_tip');
    }
}