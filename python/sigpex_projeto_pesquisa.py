#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""
Importa projetos de EXTENSÃO a partir de Excel e grava em:
- documento_ods (com id_dimensao=2, id_tipo_documento=2, id_departamento resolvido)
- pessoa_pes (coordenador como DOCENTE; participantes sem vínculo definido, salvo se já houver)
- documento_pessoa_dop (funções: Coordenador=3, Participante=5)

Regras atualizadas:
- Departamento vem em formato "CAD/CSE" e AGORA usamos a **primeira** parte ("CAD")
  para buscar em departamento_dep.ds_sigla_dep. Se encontrar, grava id em documento_ods.id_departamento.
- Nomes salvos sempre em UPPER CASE.

Requer: pandas, psycopg2-binary
"""

import os
import re
import sys
import math
import argparse
import logging
from typing import Optional, Tuple, List

import pandas as pd
import psycopg2
import psycopg2.extras


# =========================
# CONFIGURAÇÃO / CONSTANTES
# =========================

# ---- PLACEHOLDERS DE CONEXÃO (preencha) ----
PG_HOST = "162.241.40.125"
PG_PORT = 5432
PG_DB   = "ods"
PG_USER = "postgres"
PG_PASS = "DMK@rr19"

# IDs “fixos” conforme alinhado anteriormente (ajuste se necessário)
DIMENSAO_EXTENSAO      = 5   # documento_ods.id_dimensao
TIPO_PROJ_EXTENSAO     = 3   # documento_ods.id_tipo_documento

FUNCAO_COORDENADOR     = 3   # documento_pessoa_dop.id_funcao_fun
FUNCAO_PARTICIPANTE    = 5

VINCULO_DISCENTE       = 1
VINCULO_DOCENTE        = 2
VINCULO_ADMINISTRATIVO = 3
VINCULO_EXTERNO        = 4

BATCH_SIZE             = 100  # commits a cada N linhas

# Colunas esperadas na planilha (ajuste nomes se necessário)
COL_TIPO_ACAO     = "Tipo"                  # (não usado diretamente)
COL_DT_INICIO     = "Início"            # exemplo no sample; ajuste para o cabeçalho correto da sua planilha
COL_DT_FIM        = "Término"            # idem
COL_STATUS        = "Situação"                        # se houver uma coluna “Status”, ajuste o cabeçalho real
COL_TITULO        = "Título"  # título (ajuste para o nome real da coluna)
COL_RESUMO        = "Resumo"                           # resumo (ajuste para o nome real da coluna)
COL_COORDENADOR   = "Coordenador"        # coordenador (ajuste)
COL_DEPTO         = "Depto"                          # depto (ajuste)
COL_PARTICIPANTES = "Participantes"   # participantes (ajuste)

# OBS: Os nomes acima são os valores mostrados no exemplo. 
# Substitua por **NOMES DE CABEÇALHO reais** da sua planilha (ex.: "Título", "Resumo", "Coordenador", "Departamento", "Participantes", "Início", "Fim", "Status").


# =========================
# LOGGING
# =========================

def setup_logging():
    logging.basicConfig(
        level=logging.INFO,
        format="%(asctime)s [%(levelname)s] %(message)s",
        handlers=[
            logging.StreamHandler(sys.stdout),
            logging.FileHandler("projetos_pesquisa.log", encoding="utf-8")
        ]
    )


# =========================
# HELPERS
# =========================

def to_upper(s: Optional[str]) -> Optional[str]:
    if s is None:
        return None
    return str(s).strip().upper()

def parse_bracket_list(s: Optional[str]) -> List[str]:
    """
    Recebe algo como "[Nome A, Nome B, Nome C]" ou "Nome A, Nome B" e retorna lista de nomes.
    Remove colchetes e divide por vírgula.
    """
    if not s or (isinstance(s, float) and math.isnan(s)):
        return []
    txt = str(s).strip()
    txt = re.sub(r'^\[|\]$', '', txt).strip()  # remove colchetes iniciais/finais
    if not txt:
        return []
    # separa por vírgula
    parts = [to_upper(p) for p in re.split(r',', txt)]
    # limpa vazios
    return [p for p in parts if p]

def parse_coordenador(s: Optional[str]) -> Optional[str]:
    """
    Coordenador chega como "[NOME]" ou "NOME": extrai e retorna UPPER.
    """
    if not s or (isinstance(s, float) and math.isnan(s)):
        return None
    txt = str(s).strip()
    txt = re.sub(r'^\[|\]$', '', txt).strip()
    return to_upper(txt) if txt else None

def extrai_ano(dt) -> Optional[int]:
    """
    Extrai ano de string/datetime se possível.
    """
    if pd.isna(dt):
        return None
    try:
        # pandas tenta converter
        v = pd.to_datetime(dt, errors="coerce")
        if pd.isna(v):
            return None
        return int(v.year)
    except Exception:
        return None


# =========================
# DB HELPERS
# =========================

def get_conn():
    return psycopg2.connect(
        host=PG_HOST,
        port=PG_PORT,
        dbname=PG_DB,
        user=PG_USER,
        password=PG_PASS
    )

def resolve_departamento(cur, depto_raw: Optional[str]) -> Tuple[Optional[int], Optional[str], Optional[str]]:
    """
    Usa a 1ª parte de "CAD/CSE" -> "CAD" para buscar por ds_sigla_dep na tabela departamento_dep.
    Retorna (id_departamento_dep, ds_sigla_dep, ds_sigla_cen) ou (None, None, None) se não achar.
    """
    if not depto_raw:
        return (None, None, None)

    partes = str(depto_raw).split("/")
    primeira = to_upper(partes[0]) if len(partes) >= 1 else None  # ex.: CAD

    if primeira:
        cur.execute("""
            SELECT id_departamento_dep, ds_sigla_dep, ds_sigla_cen
              FROM departamento_dep
             WHERE ds_sigla_dep = %s
             LIMIT 1
        """, (primeira,))
        row = cur.fetchone()
        if row:
            logging.info(f"Departamento '{primeira}' encontrado (id={row['id_departamento_dep']}).")
            return (row["id_departamento_dep"], row["ds_sigla_dep"], row["ds_sigla_cen"])
        else:
            logging.warning(f"Departamento '{primeira}' não encontrado.")
            return (None, None, None)

    return (None, None, None)

def get_or_create_pessoa(cur, nome_upper: str, vinculo_id: Optional[int]) -> int:
    """
    Busca pessoa por ds_nome_pessoa (UPPER exato). Se não existir, cria.
    Mantém vinculo existente se já houver; se não houver e vier um novo, atualiza.
    Retorna id_pessoa_pes.
    """
    cur.execute("""
        SELECT id_pessoa_pes, id_vinculo_vin
          FROM pessoa_pes
         WHERE ds_nome_pessoa = %s
         LIMIT 1
    """, (nome_upper,))
    row = cur.fetchone()
    if row:
        pid, vinculo_atual = row["id_pessoa_pes"], row["id_vinculo_vin"]
        # Atualiza vínculo se não existir e um válido foi informado
        if vinculo_atual is None and vinculo_id is not None:
            cur.execute("""
                UPDATE pessoa_pes
                   SET id_vinculo_vin = %s,
                       updated_at = NOW()
                 WHERE id_pessoa_pes = %s
            """, (vinculo_id, pid))
        return pid

    # cria
    cur.execute("""
        INSERT INTO pessoa_pes (ds_orcid_pes, ds_nome_pessoa, id_vinculo_vin, created_at, updated_at)
        VALUES (NULL, %s, %s, NOW(), NOW())
        RETURNING id_pessoa_pes
    """, (nome_upper, vinculo_id))
    new_id = cur.fetchone()["id_pessoa_pes"]
    logging.info(f"Pessoa criada: {nome_upper} (id={new_id}, vinculo={vinculo_id})")
    return new_id

def insert_documento(cur, titulo: str, resumo: Optional[str], ano: Optional[int],
                     id_departamento: Optional[int]) -> int:
    """
    Insere documento_ods para Extensão (id_dimensao=2, id_tipo_documento=2),
    seta id_departamento se disponível.
    """
    cur.execute("""
        INSERT INTO documento_ods
            (id_producao_intelectual, ods, positivo, negativo, neutro, id_dimensao,
             sentimento, ano, titulo, texto, texto_analisado, id_tipo_documento,
             id_ppg, id_dimensao_ods, id_departamento, created_at, updated_at)
        VALUES
            (NULL, NULL, 0, 0, 0, %s,
             0, %s, %s, %s, NULL, %s,
             NULL, NULL, %s, NOW(), NOW())
        RETURNING id
    """, (DIMENSAO_EXTENSAO, ano, titulo, resumo, TIPO_PROJ_EXTENSAO, id_departamento))
    new_id = cur.fetchone()["id"]
    logging.info(f"Documento inserido (id={new_id}) — título='{titulo[:80]}'")
    return new_id

def link_pessoa_documento(cur, documento_id: int, pessoa_id: int, funcao_id: int):
    """
    Cria relação em documento_pessoa_dop (se não existir ainda).
    """
    cur.execute("""
        SELECT 1
          FROM documento_pessoa_dop
         WHERE id_documento_ods = %s
           AND id_pessoa_pes = %s
           AND id_funcao_fun = %s
         LIMIT 1
    """, (documento_id, pessoa_id, funcao_id))
    exists = cur.fetchone()
    if exists:
        return
    cur.execute("""
        INSERT INTO documento_pessoa_dop (id_documento_ods, id_pessoa_pes, id_funcao_fun)
        VALUES (%s, %s, %s)
    """, (documento_id, pessoa_id, funcao_id))


# =========================
# PROCESSAMENTO
# =========================

def process_row(cur, row, colmap):
    """
    Processa uma linha do DataFrame conforme mapeamento de colunas.
    """
    titulo  = to_upper(row[colmap["titulo"]]) if colmap["titulo"] else None
    resumo  = row[colmap["resumo"]] if colmap["resumo"] else None
    resumo  = None if (isinstance(resumo, float) and math.isnan(resumo)) else resumo
    resumo  = str(resumo) if resumo is not None else None

    coordenador_raw = row[colmap["coordenador"]] if colmap["coordenador"] else None
    coordenador     = parse_coordenador(coordenador_raw)

    depto_raw = row[colmap["depto"]] if colmap["depto"] else None

    participantes_raw = row[colmap["participantes"]] if colmap["participantes"] else None
    participantes = parse_bracket_list(participantes_raw)

    ano_inicio = extrai_ano(row[colmap["inicio"]]) if colmap["inicio"] else None
    # (Se quiser usar fim ou status, extraia também via colmap["fim"], colmap["status"])

    # Resolve departamento (id)
    id_dep, sigla_dep, sigla_cen = resolve_departamento(cur, depto_raw)

    # Insere documento
    doc_id = insert_documento(cur, titulo=titulo or "(SEM TÍTULO)", resumo=resumo, ano=ano_inicio, id_departamento=id_dep)

    # Coordenador → DOCENTE
    if coordenador:
        pid_coord = get_or_create_pessoa(cur, coordenador, VINCULO_DOCENTE)
        link_pessoa_documento(cur, doc_id, pid_coord, FUNCAO_COORDENADOR)

    # Participantes → sem vínculo definido (a não ser que a pessoa já exista com um vínculo)
    for p in participantes:
        # tenta encontrar a pessoa antes
        cur.execute("""
            SELECT id_pessoa_pes, id_vinculo_vin
              FROM pessoa_pes
             WHERE ds_nome_pessoa = %s
             LIMIT 1
        """, (p,))
        rowp = cur.fetchone()
        if rowp:
            pid = rowp["id_pessoa_pes"]
        else:
            pid = get_or_create_pessoa(cur, p, None)  # sem vínculo
        link_pessoa_documento(cur, doc_id, pid, FUNCAO_PARTICIPANTE)


def main():
    setup_logging()

    parser = argparse.ArgumentParser(description="Importar Extensão (Excel) → documento_ods/pessoa/documento_pessoa_dop")
    parser.add_argument("excel", help="Caminho para a planilha Excel")
    # Mapeamento de nomes REAIS de colunas da planilha para os campos esperados
    parser.add_argument("--col-titulo", required=True, help="Nome da coluna do Título")
    parser.add_argument("--col-resumo", required=True, help="Nome da coluna do Resumo")
    parser.add_argument("--col-coordenador", required=True, help="Nome da coluna do Coordenador")
    parser.add_argument("--col-participantes", required=True, help="Nome da coluna dos Participantes")
    parser.add_argument("--col-depto", required=True, help="Nome da coluna do Departamento (ex.: 'CAD/CSE')")
    parser.add_argument("--col-inicio", required=True, help="Nome da coluna da Data de Início")
    # se quiser: --col-fim, --col-status
    args = parser.parse_args()

    path_xlsx = args.excel
    if not os.path.exists(path_xlsx):
        logging.error(f"Arquivo não encontrado: {path_xlsx}")
        sys.exit(1)

    # Lê Excel
    try:
        df = pd.read_excel(path_xlsx, engine="openpyxl")
    except Exception as e:
        logging.exception(f"Falha ao ler Excel: {e}")
        sys.exit(1)

    # Verifica colunas
    required = [
        args.col_titulo, args.col_resumo, args.col_coordenador,
        args.col_participantes, args.col_depto, args.col_inicio
    ]
    missing = [c for c in required if c not in df.columns]
    if missing:
        logging.error(f"Colunas ausentes na planilha: {missing}")
        sys.exit(1)

    # Monta colmap
    colmap = {
        "titulo": args.col_titulo,
        "resumo": args.col_resumo,
        "coordenador": args.col_coordenador,
        "participantes": args.col_participantes,
        "depto": args.col_depto,
        "inicio": args.col_inicio,
        # "fim": args.col_fim if quiser,
        # "status": args.col_status if quiser
    }

    # Conecta ao Postgres
    try:
        conn = get_conn()
        conn.autocommit = False
        cur = conn.cursor(cursor_factory=psycopg2.extras.RealDictCursor)
    except Exception as e:
        logging.exception(f"Erro de conexão ao Postgres: {e}")
        sys.exit(1)

    processed = 0
    try:
        for idx, row in df.iterrows():
            try:
                process_row(cur, row, colmap)
                processed += 1

                if processed % BATCH_SIZE == 0:
                    conn.commit()
                    logging.info(f"Commit parcial — {processed} linhas processadas.")
            except Exception as e:
                conn.rollback()
                logging.exception(f"Erro ao processar linha {idx}: {e}. Rollback executado. (continuando)")

        conn.commit()
        logging.info(f"Concluído! Linhas processadas com sucesso: {processed}")

    except KeyboardInterrupt:
        logging.warning("Execução interrompida pelo usuário. Efetuando commit do que foi gravado até agora…")
        try:
            conn.commit()
        except Exception:
            logging.exception("Falha ao efetuar commit final após interrupção.")
    finally:
        try:
            cur.close()
            conn.close()
        except Exception:
            pass


if __name__ == "__main__":
    main()
