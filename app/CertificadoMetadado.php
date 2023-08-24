<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CertificadoMetadado extends Model
{
    use SoftDeletes;
    
    protected $connection = 'pgsql';
    protected $table = 'certificado_metadados_cem';
    protected $primaryKey = 'id_certificado_metadados_cem';
    protected $fillable = ['id_certificado_cer','label_metadado_cer','valor_metadado_cer'];

}