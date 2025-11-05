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

    public function calcularIES($id)
    {
        //Calcular o IES (Índice de Engajamento Sustentável) de um Centro
        $ies = DB::select('SELECT * FROM mv_sec_por_centro WHERE id_centro = ?;', [$id]);

        return response()->json($ies);
    }

    public function calcularICS($id)
    {
        //Calcular o ICS (Índice de Crescimento Sustentável) de um Centro
        $ics = DB::select('SELECT * FROM mv_ics_por_centro_docods WHERE id_centro = ?;', [$id]);

        return response()->json($ics);
    }

    public function indiceEngajamento($id)
    {
        //Calcular o IES (Índice de Engajamento Sustentável) de um Centro
        $ies = DB::select('SELECT * FROM mv_sec_por_centro WHERE id_centro = ?;', [$id]);

        if (count($ies) > 0) {
            $ies_value = $ies[0]->sec_index;
        } else {
            $ies_value = null;
        }

        return view('indices.engajamento', compact('id', 'ies_value'));
    }

    public function indiceCrescimento($id)
    {
        //Calcular o ICS (Índice de Crescimento Sustentável) de um Centro
        $ics = DB::select('SELECT * FROM mv_ics_por_centro_docods WHERE id_centro = ? ORDER BY ano;', [$id]);
        return view('indices.crescimento', compact('id', 'ics_value'));
    }

    public function indiceDimensao($id)
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
                                WHERE rk = 1
                                ORDER BY sigla_centro, total_docs DESC; ', [$id]);

        return view('indices.dimensoes', compact('id', 'dimensoes'));
    }

    public function dimensao($id)
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

    public function dimensaoODS($id)
    {
        //Em cada Centro, em qual Dimensão ODS (Ambiental/Econômica/Institucional/Social) ele mais se destaca
        $dimensoes = DB::select('WITH dist AS (
                                SELECT
                                    c.cd_centro_cen           AS id_centro,
                                    c.ds_sigla_cen            AS sigla_centro,
                                    do2.cd_dimensao_ods       AS id_dim_ods,
                                    do2.ds_dimensao           AS nm_dim_ods,
                                    COUNT(*)                  AS total_docs
                                FROM public.fato_documento_ods f
                                JOIN public.centro_cen c  ON c.cd_centro_cen = f.id_centro_resolvido
                                JOIN public.dimensao_ods do2 ON do2.cd_dimensao_ods = f.id_dimensao_ods
                                WHERE c.cd_centro_cen = ?
                                GROUP BY c.cd_centro_cen, c.ds_sigla_cen, do2.cd_dimensao_ods, do2.ds_dimensao
                                ),
                                ranked AS (
                                SELECT *,
                                        ROW_NUMBER() OVER (PARTITION BY id_centro ORDER BY total_docs DESC, id_dim_ods) AS rk
                                FROM dist
                                )
                                SELECT *
                                FROM ranked
                                ORDER BY sigla_centro, total_docs DESC;', [$id]);

        return response()->json($dimensoes);
    }

    public function pesquisador($id)
    {

        $dimensoes = DB::select("WITH id_docente AS (
                                SELECT id_vinculo_vin AS id_docente_vin
                                FROM public.vinculo_vin
                                WHERE ds_vinculo_vin ILIKE 'Docente'
                                LIMIT 1
                                ),
                                contrib AS (
                                SELECT
                                    f.id_centro_resolvido      AS id_centro,
                                    c.ds_sigla_cen             AS sigla_centro,
                                    p.id_pessoa_pes            AS id_pessoa,
                                    p.ds_nome_pessoa           AS nome_pessoa,
                                    COUNT(DISTINCT f.id_documento_ods) AS total_docs
                                FROM public.fato_documento_ods f
                                JOIN public.documento_pessoa_dop dop ON dop.id_documento_ods = f.id_documento_ods
                                JOIN public.pessoa_pes p             ON p.id_pessoa_pes = dop.id_pessoa_pes
                                JOIN id_docente dvin                  ON p.id_vinculo_vin = dvin.id_docente_vin
                                JOIN public.centro_cen c              ON c.cd_centro_cen = f.id_centro_resolvido
                                WHERE f.id_centro_resolvido IS NOT NULL
                                AND f.id_centro_resolvido = ?
                                GROUP BY f.id_centro_resolvido, c.ds_sigla_cen, p.id_pessoa_pes, p.ds_nome_pessoa
                                ),
                                ranked AS (
                                SELECT *,
                                        ROW_NUMBER() OVER (PARTITION BY id_centro ORDER BY total_docs DESC, nome_pessoa) AS rk
                                FROM contrib
                                )
                                SELECT id_centro, sigla_centro, id_pessoa, nome_pessoa, total_docs
                                FROM ranked
                                WHERE rk = 1
                                ORDER BY total_docs DESC;", [$id]);

        return response()->json($dimensoes);
    }
}