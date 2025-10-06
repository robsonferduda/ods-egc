<?php

namespace App\Http\Controllers;

use DB;
use Excel;
use PDF;
use Mail;
use App\Utils;
use App\Ods;
use App\Cidade;
use App\Dimensao;
use App\ParticipanteCiki;
use App\Exports\DadosExport;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use App\Imports\ParticipanteImport;
use App\Http\Requests\CertificadoArquivoRequest;

class CentroController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function grafo($id)
    {
        //Em cada Centro, em qual Dimensão IES ele mais se destaca
        $dimensoes = DB::select('WITH dist AS (
                                SELECT
                                    c.cd_centro_cen            AS id_centro,
                                    c.ds_sigla_cen             AS sigla_centro,
                                    di.id                      AS id_dim_ies,
                                    di.nome                    AS nm_dim_ies,
                                    COUNT(*)                   AS total_docs
                                FROM public.fato_documento_ods f
                                JOIN public.centro_cen c  ON c.cd_centro_cen = f.id_centro_resolvido
                                JOIN public.dimensao_ies di ON di.id = f.id_dimensao
                                WHERE c.cd_centro_cen = ?
                                GROUP BY c.cd_centro_cen, c.ds_sigla_cen, di.id, di.nome
                                ),
                                ranked AS (
                                SELECT *,
                                        ROW_NUMBER() OVER (PARTITION BY id_centro ORDER BY total_docs DESC, id_dim_ies) AS rk
                                FROM dist
                                )
                                SELECT *
                                FROM ranked
                                ORDER BY sigla_centro, total_docs DESC; ', [$id]);

        return response()->json($dimensoes);
    }
}