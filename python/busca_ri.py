#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import csv
import time
import re
import sys
import argparse
from urllib.parse import urljoin
import os

import requests
from bs4 import BeautifulSoup
import psycopg2

# =========================
# CONFIGURAÇÕES
# =========================
BASE = "https://repositorio.ufsc.br"
START = "https://repositorio.ufsc.br/handle/123456789/214239/recent-submissions"
HEADERS = {"User-Agent": "Mozilla/5.0 (UFSC-Scraper/1.2)"}

MIN_YEAR = 2021          # parar quando encontrar ano <= MIN_YEAR
ID_DIMENSAO = 6          # Pós-Graduação
ID_PPG = 9999            # <<< AJUSTE
ID_CENTRO = 8888         # <<< AJUSTE

# PostgreSQL via env ou ajuste aqui
PG_HOST = os.getenv("PG_HOST", "localhost")
PG_DB   = os.getenv("PG_DB",   "perfil_ods")
PG_USER = os.getenv("PG_USER", "postgres")
PG_PASS = os.getenv("PG_PASS", "postgres")
PG_PORT = int(os.getenv("PG_PORT", "5432"))

# Funções / vínculos
ID_FUNCAO_AUTOR = 2        # Aluno
ID_FUNCAO_ORIENTADOR = 1   # Orientador
ID_VINCULO_AUTOR = 1       # Discente
ID_VINCULO_ORIENTADOR = 2  # Docente

# =========================
# LOG
# =========================
def log(msg):
    sys.stdout.write(msg.rstrip() + "\n")
    sys.stdout.flush()

# =========================
# REDE / SOUP
# =========================
def get_soup(url, retries=2, delay=1.0):
    for i in range(retries + 1):
        try:
            resp = requests.get(url, headers=HEADERS, timeout=30)
            if resp.status_code == 404:
                log(f"[WARN] 404 Not Found: {url}")
                return None
            resp.raise_for_status()
            return BeautifulSoup(resp.text, "html.parser")
        except requests.exceptions.HTTPError as e:
            if 500 <= getattr(e.response, "status_code", 0) < 600 and i < retries:
                log(f"[WARN] {e} → retry {i+1}/{retries} em {delay}s")
                time.sleep(delay)
                continue
            raise
        except requests.exceptions.RequestException as e:
            if i < retries:
                log(f"[WARN] {e} → retry {i+1}/{retries} em {delay}s")
                time.sleep(delay)
                continue
            raise

def extract_links_from_recent(page_url):
    soup = get_soup(page_url)
    if soup is None:
        return [], None

    links = []
    for a in soup.select('a[href*="/handle/123456789/"]'):
        href = a.get("href", "")
        if not href or "recent-submissions" in href:
            continue
        url = urljoin(BASE, href.split("?")[0])
        if "/handle/123456789/" in url and len(url.split("/")) >= 6:
            links.append(url)

    seen, unique = set(), []
    for u in links:
        if u not in seen:
            seen.add(u)
            unique.append(u)

    next_link = None
    for a in soup.find_all("a"):
        t = a.get_text(" ", strip=True).lower()
        if t in ("next page", "next"):
            next_link = urljoin(BASE, a.get("href"))
            break
    if not next_link:
        tag = soup.find("link", attrs={"rel": "next"})
        if tag and tag.get("href"):
            next_link = urljoin(BASE, tag["href"])
    return unique, next_link

# =========================
# NOMES
# =========================
def split_people_list(s):
    if not s:
        return []
    parts = re.split(r"\s*;\s*", s.strip())
    return [p for p in parts if p]

def reorder_name(name):
    if not name:
        return name
    parts = [p.strip() for p in name.split(",", 1)]
    if len(parts) == 2:
        last, firsts = parts[0].strip(" ,"), parts[1].strip(" ,")
        if firsts and last:
            return f"{firsts} {last}"
    return name.strip()

def normalize_people_list(values):
    nomes = []
    for v in (values or []):
        spl = split_people_list(v) or [v]
        for raw in spl:
            n = reorder_name(raw)
            if n and n not in nomes:
                nomes.append(n)
    return nomes

# =========================
# PARSE ITEM
# =========================
def map_tipo_documento(dc_type_value: str):
    if not dc_type_value:
        return None
    val = dc_type_value.lower()
    if "tese" in val:
        return 2
    if "dissertação" in val or "dissertacao" in val:
        return 1
    return None

def parse_full_record(item_url):
    full_url = item_url + ("?show=full" if "?show=full" not in item_url else "")
    log(f"[GET] {full_url}")
    soup = get_soup(full_url)
    if soup is None:
        return None

    meta = {}
    for tr in soup.select("table tr"):
        tds = tr.find_all("td")
        if len(tds) >= 2:
            key = tds[0].get_text(" ", strip=True)
            val = tds[1].get_text(" ", strip=True)
            if key and val:
                meta.setdefault(key, []).append(val)

    def first(k): return (meta.get(k) or [None])[0]
    def listvals(k): return meta.get(k) or []

    ano_raw = first("dc.date.issued")
    ano = None
    if ano_raw:
        m = re.search(r"\d{4}", str(ano_raw))
        if m:
            try:
                ano = int(m.group(0))
            except ValueError:
                ano = None

    titulo = first("dc.title")
    autores = normalize_people_list(listvals("dc.contributor.author") or [])
    orientadores = normalize_people_list(listvals("dc.contributor.advisor") or [])
    dc_type = first("dc.type")
    id_tipo_documento = map_tipo_documento(dc_type)

    resumos = listvals("dc.description.abstract") or []
    resumo_pt, abstract_en = None, None
    for r in resumos:
        r_clean = r.strip()
        if r_clean.lower().startswith("abstract:"):
            abstract_en = r_clean
        elif resumo_pt is None:
            resumo_pt = r_clean

    return {
        "url": item_url,
        "ano": ano,
        "titulo": titulo,
        "resumo_pt": resumo_pt,
        "abstract_en": abstract_en,
        "autores": autores,
        "orientadores": orientadores,
        "id_tipo_documento": id_tipo_documento,
        "dc_type_raw": dc_type,
    }

# =========================
# POSTGRES (linha a linha)
# =========================
def get_conn():
    return psycopg2.connect(
        host=PG_HOST, dbname=PG_DB, user=PG_USER, password=PG_PASS, port=PG_PORT
    )

def get_or_create_documento(cur, titulo, ano, resumo_pt, resumo_en, url, id_tipo_documento):
    cur.execute("""
        SELECT id_documento_ods
        FROM documento_ods
        WHERE titulo = %s AND ano = %s
        LIMIT 1
    """, (titulo, ano))
    row = cur.fetchone()
    if row:
        return row[0]

    cur.execute("""
        INSERT INTO documento_ods
            (titulo, texto, resumo_en, ano, url, id_dimensao, id_tipo_documento, id_ppg, id_centro, created_at)
        VALUES
            (%s,     %s,    %s,        %s,  %s,  %s,          %s,               %s,      %s,       NOW())
        RETURNING id_documento_ods
    """, (titulo, resumo_pt, resumo_en, ano, url, ID_DIMENSAO, id_tipo_documento, ID_PPG, ID_CENTRO))
    return cur.fetchone()[0]

def get_or_create_pessoa(cur, nome, id_vinculo):
    cur.execute("""
        SELECT id_pessoa_pes
        FROM pessoa_pes
        WHERE lower(nm_pessoa_pes) = lower(%s)
        LIMIT 1
    """, (nome,))
    row = cur.fetchone()
    if row:
        return row[0]
    cur.execute("""
        INSERT INTO pessoa_pes (nm_pessoa_pes, id_vinculo_vin, created_at)
        VALUES (%s, %s, NOW())
        RETURNING id_pessoa_pes
    """, (nome, id_vinculo))
    return cur.fetchone()[0]

def link_documento_pessoa(cur, id_documento, id_pessoa, id_funcao):
    cur.execute("""
        SELECT 1
        FROM documento_pessoa_dop
        WHERE id_documento_ods = %s AND id_pessoa_pes = %s AND id_funcao_fun = %s
        LIMIT 1
    """, (id_documento, id_pessoa, id_funcao))
    if cur.fetchone():
        return
    cur.execute("""
        INSERT INTO documento_pessoa_dop (id_documento_ods, id_pessoa_pes, id_funcao_fun)
        VALUES (%s, %s, %s)
    """, (id_documento, id_pessoa, id_funcao))

def persist_one_record(conn, rec):
    """
    Persiste UM item em transação própria. Se der erro, levanta exceção para ser logada
    e o chamador segue para o próximo.
    """
    with conn:
        with conn.cursor() as cur:
            titulo = (rec.get("titulo") or "").strip()
            ano = rec.get("ano")
            resumo_pt = rec.get("resumo_pt") or None
            resumo_en = rec.get("abstract_en") or None
            url = rec.get("url") or None
            id_tipo_documento = rec.get("id_tipo_documento")

            # Documento
            id_doc = get_or_create_documento(cur, titulo, ano, resumo_pt, resumo_en, url, id_tipo_documento)

            # Autores (Discente/Aluno)
            for nome in rec.get("autores") or []:
                id_p = get_or_create_pessoa(cur, nome, ID_VINCULO_AUTOR)
                link_documento_pessoa(cur, id_doc, id_p, ID_FUNCAO_AUTOR)

            # Orientadores (Docente/Orientador)
            for nome in rec.get("orientadores") or []:
                id_p = get_or_create_pessoa(cur, nome, ID_VINCULO_ORIENTADOR)
                link_documento_pessoa(cur, id_doc, id_p, ID_FUNCAO_ORIENTADOR)

# =========================
# CRAWL → CSV + persistência por item
# =========================
def crawl_line_by_line(outpath, start_url=START, min_year=MIN_YEAR, max_pages=None, delay=1.8, persist=False):
    fieldnames = ["ano", "titulo", "resumo_pt", "abstract_en", "autores", "orientadores", "id_tipo_documento", "dc_type_raw", "url"]
    total_csv = 0
    total_ok_db = 0
    page_count = 0
    seen = set()
    conn = None

    if persist:
        conn = get_conn()

    with open(outpath, "w", newline="", encoding="utf-8") as f:
        w = csv.DictWriter(f, fieldnames=fieldnames)
        w.writeheader()

        page_url = start_url
        while page_url:
            page_count += 1
            log(f"\n[PAGE] {page_count} → {page_url}")
            links, next_link = extract_links_from_recent(page_url)
            if not links:
                log("[INFO] Nenhum link de item nesta página.")
            stop = False

            for link in links:
                if link in seen or link.endswith("/214239"):
                    continue
                seen.add(link)

                try:
                    data = parse_full_record(link)
                except requests.exceptions.HTTPError as e:
                    log(f"[ERRO] HTTPError em {link} → {e}")
                    continue
                except requests.exceptions.RequestException as e:
                    log(f"[ERRO] RequestException em {link} → {e}")
                    continue
                except Exception as e:
                    log(f"[ERRO] Exception em {link} → {e}")
                    continue

                if not data:
                    continue

                ano = data.get("ano")
                titulo = (data.get("titulo") or "").strip()
                log(f"[ITEM] ano={ano} | tipo={data.get('id_tipo_documento')} | titulo={titulo[:90]}{'...' if len(titulo)>90 else ''}")

                if ano is not None and ano <= min_year:
                    log(f"[STOP] encontrado ano <= {min_year}: {ano} → encerrando coleta.")
                    stop = True
                    break

                # CSV (imediato)
                row = {
                    "ano": data.get("ano") or "",
                    "titulo": data.get("titulo") or "",
                    "resumo_pt": data.get("resumo_pt") or "",
                    "abstract_en": data.get("abstract_en") or "",
                    "autores": " / ".join(data.get("autores") or []) or "",
                    "orientadores": " / ".join(data.get("orientadores") or []) or "",
                    "id_tipo_documento": data.get("id_tipo_documento") or "",
                    "dc_type_raw": data.get("dc_type_raw") or "",
                    "url": data.get("url") or "",
                }
                w.writerow(row)
                total_csv += 1

                # Persistência (imediata, transação por item)
                if persist:
                    try:
                        persist_one_record(conn, data)
                        total_ok_db += 1
                        log(f"[DB✓] Persistido: {titulo[:80]}{'...' if len(titulo)>80 else ''}")
                    except psycopg2.Error as e:
                        log(f"[DB×] ERRO ao persistir '{titulo[:80]}': {e.pgerror or str(e)}")
                    except Exception as e:
                        log(f"[DB×] ERRO inesperado em '{titulo[:80]}': {str(e)}")

                time.sleep(delay)

            if stop:
                break
            page_url = next_link
            if max_pages and page_count >= max_pages:
                log(f"[STOP] atingido max_pages={max_pages}")
                break

    if conn:
        conn.close()

    log(f"\n[OK] Coleta concluída. CSV linhas: {total_csv} | Persistidos OK: {total_ok_db}. CSV: {outpath}")
    return total_csv, total_ok_db

# =========================
# CLI
# =========================
def main():
    ap = argparse.ArgumentParser(description="UFSC Recent Submissions → CSV + persistência linha a linha")
    ap.add_argument("--out", default="ufsc_recent.csv", help="CSV de saída")
    ap.add_argument("--start", default=START, help="URL inicial (recent-submissions)")
    ap.add_argument("--min-year", type=int, default=MIN_YEAR, help="Parar quando encontrar ano <= min-year")
    ap.add_argument("--max-pages", type=int, default=None, help="Máximo de páginas")
    ap.add_argument("--delay", type=float, default=1.8, help="Delay entre itens (s)")
    ap.add_argument("--persist", action="store_true", help="Ativa a gravação no PostgreSQL (linha a linha)")
    args = ap.parse_args()

    crawl_line_by_line(
        outpath=args.out,
        start_url=args.start,
        min_year=args.min_year,
        max_pages=args.max_pages,
        delay=args.delay,
        persist=args.persist,
    )

if __name__ == "__main__":
    main()
