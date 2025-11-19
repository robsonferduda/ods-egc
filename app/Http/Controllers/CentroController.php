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

    public function calcularICS($id, $ano)
    {
        //Calcular o ICS (Índice de Crescimento Sustentável) de um Centro
        $ics = DB::select('SELECT * FROM mv_ics_por_centro_docods WHERE id_centro = ? AND ano = ?;', [$id, $ano]);

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

    public function indiceICT($id)
    {
        
        return view('indices.ict', compact('id'));
    }

    public function indiceIVC($id)
    {
        
        return view('indices.ivc', compact('id'));
    }

    public function calcularICT($id, $ano)
    {
        // ICT-ODS: Índice de Colaboração Temática
        // Mede a diversidade temática - quantidade de ODS diferentes / 16 (não temos ODS 17)
        
        $resultado = DB::select('
            SELECT 
                COUNT(DISTINCT ods) as ods_unicos,
                ROUND((COUNT(DISTINCT ods)::numeric / 16) * 100, 2) as ict_percentual
            FROM documento_ods
            WHERE id_centro = ?
            AND ano = ?
            AND ods BETWEEN 1 AND 16
        ', [$id, $ano]);

        if (count($resultado) > 0) {
            return response()->json([
                [
                    'ict_ods_unicos' => $resultado[0]->ods_unicos,
                    'ict_percentual' => $resultado[0]->ict_percentual,
                    'ano' => $ano
                ]
            ]);
        }

        return response()->json([]);
    }

    public function calcularIVC($id)
    {
        // IVC-ODS: Índice de Variação de Contribuição
        // Mede a evolução anual: (total_ano_atual - total_ano_anterior) / total_ano_anterior
        
        $ano_atual = date('Y');
        $ano_anterior = $ano_atual - 1;

        $dados = DB::select('
            SELECT 
                ano,
                COUNT(*) as total_documentos
            FROM documento_ods
            WHERE id_centro = ?
            AND ano IN (?, ?)
            GROUP BY ano
            ORDER BY ano
        ', [$id, $ano_anterior, $ano_atual]);

        if (count($dados) >= 2) {
            $total_anterior = $dados[0]->total_documentos;
            $total_atual = $dados[1]->total_documentos;
            
            $ivc = (($total_atual - $total_anterior) / $total_anterior) * 100;
            
            return response()->json([
                [
                    'ivc_percentual' => round($ivc, 2),
                    'ano_anterior' => $ano_anterior,
                    'total_anterior' => $total_anterior,
                    'ano_atual' => $ano_atual,
                    'total_atual' => $total_atual,
                    'variacao' => $ivc > 0 ? 'Crescimento' : ($ivc < 0 ? 'Queda' : 'Estável')
                ]
            ]);
        }

        return response()->json([]);
    }

    public function indiceCrescimento($id)
    {
        //Calcular o ICS (Índice de Crescimento Sustentável) de um Centro
        $ics = DB::select('SELECT * FROM mv_ics_por_centro_docods WHERE id_centro = ? ORDER BY ano;', [$id]);
        return view('indices.crescimento', compact('id', 'ics'));
    }

    public function indiceDimensao($id)
    {
        $centro = \App\Centro::where('cd_centro_cen', $id)->first();
        
        //Dimensão IES mais destacada
        $dimensoes_ies = DB::select('WITH dist AS (
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
                                ORDER BY total_docs DESC; ', [$id]);

        //Dimensão ODS predominante (Ambiental/Econômica/Institucional/Social)
        $dimensoes_ods = DB::select('WITH dist AS (
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
                                ORDER BY total_docs DESC; ', [$id]);

        //Distribuição por ODS e suas dimensões
        $ods_por_dimensao = DB::select('SELECT 
                                    f.ods_cod as ods,
                                    o.objetivo,
                                    o.cor,
                                    do2.cd_dimensao_ods,
                                    do2.ds_dimensao,
                                    COUNT(*) as total_docs
                                FROM public.fato_documento_ods f
                                JOIN public.ods o ON o.cod = f.ods_cod
                                JOIN public.dimensao_ods do2 ON do2.cd_dimensao_ods = f.id_dimensao_ods
                                WHERE f.id_centro_resolvido = ?
                                GROUP BY f.ods_cod, o.objetivo, o.cor, do2.cd_dimensao_ods, do2.ds_dimensao
                                ORDER BY f.ods_cod, total_docs DESC', [$id]);

        return view('indices.dimensoes', compact('id', 'centro', 'dimensoes_ies', 'dimensoes_ods', 'ods_por_dimensao'));
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

    public function panoramaCentro($id)
    {
        $centro = \App\Centro::where('cd_centro_cen', $id)->first();
        
        if (!$centro) {
            abort(404, 'Centro não encontrado');
        }
        
        // Total de documentos
        $sql = "SELECT count(*) as total_documentos
                FROM documento_ods 
                WHERE id_centro = ?
                AND ano >= EXTRACT(YEAR FROM CURRENT_DATE) - 5";
        
        $total_documentos = DB::connection('pgsql')->select($sql, [$id]);
        
        // Evolução anual
        $sql_evolucao = "SELECT ano, count(*) as total
                         FROM documento_ods
                         WHERE id_centro = ?
                         AND ano >= EXTRACT(YEAR FROM CURRENT_DATE) - 5
                         GROUP BY ano
                         ORDER BY ano";
        
        $evolucao = DB::connection('pgsql')->select($sql_evolucao, [$id]);
        
        // Distribuição por ODS
        $sql_ods = "SELECT t0.ods, t1.objetivo, t1.cor, count(*) as total
                    FROM documento_ods t0
                    JOIN ods t1 ON t1.cod = t0.ods
                    WHERE t0.id_centro = ?
                    GROUP BY t0.ods, t1.objetivo, t1.cor
                    ORDER BY total DESC
                    LIMIT 10";
        
        $ods_distribuicao = DB::connection('pgsql')->select($sql_ods, [$id]);
        
        // Distribuição por Dimensão IES
        $sql_dimensao = "SELECT d.nome, d.apelido, count(*) as total
                         FROM documento_ods doc
                         JOIN dimensao_ies d ON d.id = doc.id_dimensao
                         WHERE doc.id_centro = ?
                         GROUP BY d.nome, d.apelido
                         ORDER BY total DESC";
        
        $dimensoes = DB::connection('pgsql')->select($sql_dimensao, [$id]);
        
        // Documentos recentes
        $sql_documentos = "SELECT doc.id, doc.titulo, doc.ano, d.nome as dimensao, t.ds_tipo_documento as tipo, doc.ods
                           FROM documento_ods doc
                           JOIN dimensao_ies d ON d.id = doc.id_dimensao
                           JOIN tipo_documento t ON t.id_tipo_documento = doc.id_tipo_documento
                           WHERE doc.id_centro = ?
                           ORDER BY doc.ano DESC, doc.id DESC
                           LIMIT 10";
        
        $documentos_recentes = DB::connection('pgsql')->select($sql_documentos, [$id]);
        
        // Cor do ODS predominante
        $cor_predominante = !empty($ods_distribuicao) ? $ods_distribuicao[0]->cor : '#007bff';
        
        return view('panorama.centro', compact('centro', 'total_documentos', 'evolucao', 'ods_distribuicao', 'dimensoes', 'documentos_recentes', 'cor_predominante'));
    }

    public function panoramaDepartamento($id)
    {
        $departamento = \App\Departamento::where('id_departamento_dep', $id)->first();
        
        if (!$departamento) {
            abort(404, 'Departamento não encontrado');
        }
        
        // Total de documentos
        $sql = "SELECT count(*) as total_documentos
                FROM documento_ods 
                WHERE id_departamento = ?
                AND ano >= EXTRACT(YEAR FROM CURRENT_DATE) - 5";
        
        $total_documentos = DB::connection('pgsql')->select($sql, [$id]);
        
        // Evolução anual
        $sql_evolucao = "SELECT ano, count(*) as total
                         FROM documento_ods
                         WHERE id_departamento = ?
                         AND ano >= EXTRACT(YEAR FROM CURRENT_DATE) - 5
                         GROUP BY ano
                         ORDER BY ano";
        
        $evolucao = DB::connection('pgsql')->select($sql_evolucao, [$id]);
        
        // Distribuição por ODS
        $sql_ods = "SELECT t0.ods, t1.objetivo, t1.cor, count(*) as total
                    FROM documento_ods t0
                    JOIN ods t1 ON t1.cod = t0.ods
                    WHERE t0.id_departamento = ?
                    GROUP BY t0.ods, t1.objetivo, t1.cor
                    ORDER BY total DESC
                    LIMIT 10";
        
        $ods_distribuicao = DB::connection('pgsql')->select($sql_ods, [$id]);
        
        // Distribuição por Dimensão IES
        $sql_dimensao = "SELECT d.nome, d.apelido, count(*) as total
                         FROM documento_ods doc
                         JOIN dimensao_ies d ON d.id = doc.id_dimensao
                         WHERE doc.id_departamento = ?
                         GROUP BY d.nome, d.apelido
                         ORDER BY total DESC";
        
        $dimensoes = DB::connection('pgsql')->select($sql_dimensao, [$id]);
        
        // Documentos recentes
        $sql_documentos = "SELECT doc.id, doc.titulo, doc.ano, d.nome as dimensao, t.ds_tipo_documento as tipo, doc.ods
                           FROM documento_ods doc
                           JOIN dimensao_ies d ON d.id = doc.id_dimensao
                           JOIN tipo_documento t ON t.id_tipo_documento = doc.id_tipo_documento
                           WHERE doc.id_departamento = ?
                           ORDER BY doc.ano DESC, doc.id DESC
                           LIMIT 10";
        
        $documentos_recentes = DB::connection('pgsql')->select($sql_documentos, [$id]);
        
        // Cor do ODS predominante
        $cor_predominante = !empty($ods_distribuicao) ? $ods_distribuicao[0]->cor : '#28a745';
        
        return view('panorama.departamento', compact('departamento', 'total_documentos', 'evolucao', 'ods_distribuicao', 'dimensoes', 'documentos_recentes', 'cor_predominante'));
    }

    public function panoramaPPG($id)
    {
        $ppg = \App\PPG::where('id_ppg', $id)->first();
        
        if (!$ppg) {
            abort(404, 'Programa de Pós-Graduação não encontrado');
        }
        
        // Total de documentos
        $sql = "SELECT count(*) as total_documentos
                FROM documento_ods 
                WHERE id_ppg = ?
                AND ano >= EXTRACT(YEAR FROM CURRENT_DATE) - 5";
        
        $total_documentos = DB::connection('pgsql')->select($sql, [$id]);
        
        // Evolução anual
        $sql_evolucao = "SELECT ano, count(*) as total
                         FROM documento_ods
                         WHERE id_ppg = ?
                         AND ano >= EXTRACT(YEAR FROM CURRENT_DATE) - 5
                         GROUP BY ano
                         ORDER BY ano";
        
        $evolucao = DB::connection('pgsql')->select($sql_evolucao, [$id]);
        
        // Distribuição por ODS
        $sql_ods = "SELECT t0.ods, t1.objetivo, t1.cor, count(*) as total
                    FROM documento_ods t0
                    JOIN ods t1 ON t1.cod = t0.ods
                    WHERE t0.id_ppg = ?
                    GROUP BY t0.ods, t1.objetivo, t1.cor
                    ORDER BY total DESC
                    LIMIT 10";
        
        $ods_distribuicao = DB::connection('pgsql')->select($sql_ods, [$id]);
        
        // Distribuição por Dimensão IES
        $sql_dimensao = "SELECT d.nome, d.apelido, count(*) as total
                         FROM documento_ods doc
                         JOIN dimensao_ies d ON d.id = doc.id_dimensao
                         WHERE doc.id_ppg = ?
                         GROUP BY d.nome, d.apelido
                         ORDER BY total DESC";
        
        $dimensoes = DB::connection('pgsql')->select($sql_dimensao, [$id]);
        
        // Documentos recentes
        $sql_documentos = "SELECT doc.id, doc.titulo, doc.ano, d.nome as dimensao, t.ds_tipo_documento as tipo, doc.ods
                           FROM documento_ods doc
                           JOIN dimensao_ies d ON d.id = doc.id_dimensao
                           JOIN tipo_documento t ON t.id_tipo_documento = doc.id_tipo_documento
                           WHERE doc.id_ppg = ?
                           ORDER BY doc.ano DESC, doc.id DESC
                           LIMIT 10";
        
        $documentos_recentes = DB::connection('pgsql')->select($sql_documentos, [$id]);
        
        // Cor do ODS predominante
        $cor_predominante = !empty($ods_distribuicao) ? $ods_distribuicao[0]->cor : '#17a2b8';
        
        return view('panorama.ppg', compact('ppg', 'total_documentos', 'evolucao', 'ods_distribuicao', 'dimensoes', 'documentos_recentes', 'cor_predominante'));
    }
}