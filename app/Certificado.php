<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Certificado extends Model
{
    use SoftDeletes;
    
    protected $connection = 'pgsql';
    protected $table = 'certificado_cer';
    protected $primaryKey = 'id_certificado_cer';
    protected $fillable = [];

    public function participante()
    {
        return $this->hasOne('App\Participante', 'id_participante_par', 'id_participante_par');
    }

    public function modelo()
    {
        return $this->hasOne('App\ModeloCertificado', 'id_modelo_certificado_moc', 'id_modelo_certificado_moc');
    }

    public function situacao()
    {
        return $this->belongsTo('App\Situacao', 'id_situacao_sit', 'id_situacao_sit');
    } 

    public function metadados()
    {
        return $this->hasMany('App\CertificadoMetadado', 'id_certificado_cer', 'id_certificado_cer');
    }
}