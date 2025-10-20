#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import csv
import time
import re
import sys
import argparse
from urllib.parse import urljoin
import os
from pathlib import Path

import requests
from bs4 import BeautifulSoup
import psycopg2

# ---- dotenv opcional (carrega .env um nível acima) ----
try:
    from dotenv import load_dotenv
except ImportError:
    load_dotenv = None

ENV_PATH = Path(__file__).resolve().parent.parent / ".env"
if load_dotenv is not None and ENV_PATH.exists():
    load_dotenv(str(ENV_PATH))
    sys.stdout.write("[INFO] Variáveis carregadas de {}\n".format(ENV_PATH))
    sys.stdout.flush()
else:
    sys.stdout.write("[WARN] .env não encontrado ou python-dotenv não instalado. Usando valores padrão/CLI.\n")
    sys.stdout.flush()

# =========================
# CONFIG PADRÃO
# =========================
BASE = "https://repositorio.ufsc.br"
HEADERS = {"User-Agent": "Mozilla/5.0 (UFSC-Scraper/1.6-compat)"}
DEFAULT_START = "https://repositorio.ufsc.br/handle/123456789/214239/recent-submissions"  # fallback

# Critério: nunca gravar ano <= MIN_YEAR; parada depende de "paciência"
MIN_YEAR = int(os.getenv("MIN_YEAR", "2021"))

# Dimensão fixa (Pós-Graduação)
ID_DIMENSAO = 6

# PostgreSQL por .env ou CLI
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
# UTIL
# =========================
def normalize_start_url(u):
    if not u:
        return DEFAULT_START
    u = u.strip()
    if not u.endswith("/recent-submissions"):
        # alguns links vieram sem o sufixo; normaliza
        u = u.rstrip("/") + "/recent-submissions"
    return u

def safe_name(s):
    return "".join(c if c.isalnum() or c in "-_." else "_" for c in s)[:80]

# =========================
# REDE / SOUP
# =========================
def get_soup(url, retries=2, delay=1.0):
    for i in range(retries + 1):
        try:
            resp = requests.get(url, headers=HEADERS, timeout=30)
            if resp.status_code == 404:
                log("[WARN] 404 Not Found: {}".format(url))
                return None
            resp.raise_for_status()
            return BeautifulSoup(resp.text, "html.parser")
        except requests.exceptions.HTTPError as e:
            code = getattr(e.response, "status_code", 0)
            if 500 <= code < 600 and i < retries:
                log("[WARN] {} → retry {}/{} em {}s".format(e, i+1, retries, delay))
                time.sleep(delay)
                continue
            raise
        except requests.exceptions.RequestException as e:
            if i < retries:
                log("[WARN] {} → retry {}/{} em {}s".format(e, i+1, retries, delay))
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
        if tag is not None and tag.get("href"):
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
            return "{} {}".format(firsts, last)
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
# HANDLE ID
# =========================
def extract_handle_id(item_url):
    m = re.search(r"/handle/123456789/(\d+)", item_url or "")
    if not m:
        return None
    try:
        return int(m.group(1))
    except ValueError:
        return None

# =========================
# PARSE ITEM
# =========================
def map_tipo_documento(dc_type_value):
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
    log("[GET] {}".format(full_url))
    soup = get_soup(full_url)
    if soup is None:
        return None

    meta = {}
    rows = soup.select("table tr")
    for tr in rows:
        tds = tr.find_all("td")
        if len(tds) >= 2:
            key = tds[0].get_text(" ", strip=True)
            val = tds[1].get_text(" ", strip=True)
            if key and val:
                meta.setdefault(key, []).append(val)

    def first(k):
        vals = meta.get(k) or [None]
        return vals[0]

    def listvals(k):
        return meta.get(k) or []

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
        "repo_id": extract_handle_id(item_url),
    }

# =========================
# CSV + CRAWL + persistência por item
# =========================
def crawl_line_by_line(outpath, start_url, min_year, max_pages, delay, persist, id_ppg, id_centro, patience_below=3):
    fieldnames = [
        "ano", "titulo", "resumo_pt", "abstract_en",
        "autores", "orientadores", "id_tipo_documento",
        "dc_type_raw", "url", "repo_id"
    ]
    total_csv = 0
    total_ok_db = 0
    page_count = 0
    seen = set()
    conn = None

    if persist:
        conn = get_conn()

    # contagem de consecutivos abaixo/igual ao limite
    below_streak = 0

    with open(outpath, "w", newline="", encoding="utf-8") as f:
        w = csv.DictWriter(f, fieldnames=fieldnames)
        w.writeheader()

        page_url = start_url
        while page_url:
            page_count += 1
            log("\n[PAGE] {} → {}".format(page_count, page_url))
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
                    log("[ERRO] HTTPError em {} → {}".format(link, e))
                    continue
                except requests.exceptions.RequestException as e:
                    log("[ERRO] RequestException em {} → {}".format(link, e))
                    continue
                except Exception as e:
                    log("[ERRO] Exception em {} → {}".format(link, e))
                    continue

                if not data:
                    continue

                ano = data.get("ano")
                titulo = (data.get("titulo") or "").strip()
                log("[ITEM] ano={} | tipo={} | repo_id={} | titulo={}".format(
                    ano, data.get("id_tipo_documento"), data.get("repo_id"),
                    (titulo[:90] + ("..." if len(titulo) > 90 else ""))
                ))

                # --- regra de parada com "paciência" ---
                # Nunca gravar se ano <= min_year; parar somente após 'patience_below' consecutivos
                if ano is not None and ano <= min_year:
                    below_streak += 1
                    log("[SKIP] ano={} <= min_year={} (streak {}/{})".format(
                        ano, min_year, below_streak, patience_below if patience_below > 0 else 0
                    ))
                    if patience_below > 0 and below_streak >= patience_below:
                        log("[STOP] atingiu {} consecutivos com ano <= {} → encerrando coleta.".format(below_streak, min_year))
                        stop = True
                        break
                    continue

                # ano > min_year → zera a contagem
                if ano is not None and ano > min_year:
                    below_streak = 0

                # CSV imediato
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
                    "repo_id": data.get("repo_id") or ""
                }
                w.writerow(row)
                total_csv += 1

                if persist:
                    try:
                        persist_one_record(conn, data, id_ppg=id_ppg, id_centro=id_centro)
                        total_ok_db += 1
                        log("[DB✓] Persistido: {}".format(titulo[:80] + ("..." if len(titulo) > 80 else "")))
                    except psycopg2.Error as e:
                        log("[DB×] ERRO ao persistir '{}': {}".format(titulo[:80], e.pgerror or str(e)))
                    except Exception as e:
                        log("[DB×] ERRO inesperado em '{}': {}".format(titulo[:80], str(e)))

                time.sleep(delay)

            if stop:
                break
            page_url = next_link
            if max_pages and page_count >= max_pages:
                log("[STOP] atingido max_pages={}".format(max_pages))
                break

    if conn:
        conn.close()

    log("\n[OK] Coleta concluída. CSV linhas: {} | Persistidos OK: {}. CSV: {}".format(total_csv, total_ok_db, outpath))
    return total_csv, total_ok_db

# =========================
# POSTGRES
# =========================
def get_conn():
    return psycopg2.connect(
        host=PG_HOST, dbname=PG_DB, user=PG_USER, password=PG_PASS, port=PG_PORT
    )

def get_or_create_documento(cur, titulo, ano, resumo_pt, id_tipo_documento, id_ppg, id_centro, repo_id):
    # Unicidade preferencial por id_producao_intelectual (repo_id)
    if repo_id is not None:
        cur.execute("""
            SELECT id
            FROM documento_ods
            WHERE id_producao_intelectual = %s
            LIMIT 1
        """, (repo_id,))
        row = cur.fetchone()
        if row:
            return row[0]

    # Fallback por (titulo, ano)
    cur.execute("""
        SELECT id
        FROM documento_ods
        WHERE titulo = %s AND ano = %s
        LIMIT 1
    """, (titulo, ano))
    row = cur.fetchone()
    if row:
        return row[0]

    cur.execute("""
        INSERT INTO documento_ods
            (titulo, texto, ano, id_dimensao, id_tipo_documento, id_ppg, id_centro, id_producao_intelectual)
        VALUES
            (%s,     %s,    %s,  %s,           %s,               %s,      %s,       %s)
        RETURNING id
    """, (titulo, resumo_pt, ano, ID_DIMENSAO, id_tipo_documento, id_ppg, id_centro, repo_id))
    return cur.fetchone()[0]

def get_or_create_pessoa(cur, nome, id_vinculo):
    cur.execute("""
        SELECT id_pessoa_pes
        FROM pessoa_pes
        WHERE lower(ds_nome_pessoa) = lower(%s)
        LIMIT 1
    """, (nome,))
    row = cur.fetchone()
    if row:
        return row[0]
    cur.execute("""
        INSERT INTO pessoa_pes (ds_nome_pessoa, id_vinculo_vin)
        VALUES (%s, %s)
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

def persist_one_record(conn, rec, id_ppg, id_centro):
    with conn:
        with conn.cursor() as cur:
            titulo = (rec.get("titulo") or "").strip()
            ano = rec.get("ano")
            resumo_pt = rec.get("resumo_pt") or None
            id_tipo_documento = rec.get("id_tipo_documento")
            repo_id = rec.get("repo_id")

            id_doc = get_or_create_documento(
                cur,
                titulo=titulo,
                ano=ano,
                resumo_pt=resumo_pt,
                id_tipo_documento=id_tipo_documento,
                id_ppg=id_ppg,
                id_centro=id_centro,
                repo_id=repo_id
            )

            for nome in rec.get("autores") or []:
                id_p = get_or_create_pessoa(cur, nome, ID_VINCULO_AUTOR)
                link_documento_pessoa(cur, id_doc, id_p, ID_FUNCAO_AUTOR)

            for nome in rec.get("orientadores") or []:
                id_p = get_or_create_pessoa(cur, nome, ID_VINCULO_ORIENTADOR)
                link_documento_pessoa(cur, id_doc, id_p, ID_FUNCAO_ORIENTADOR)

# =========================
# BATCH TSV
# =========================
def run_batch(file_path, sep, out_dir, logs_dir, default_min_year, default_delay, default_max_pages, default_patience, persist):
    if not os.path.isdir(out_dir):
        os.makedirs(out_dir)
    if logs_dir and not os.path.isdir(logs_dir):
        os.makedirs(logs_dir)

    total = ok = fail = 0

    with open(file_path, "r", encoding="utf-8") as f:
        first = f.readline()
        f.seek(0)
        has_header = any(k in first.lower() for k in ["nome", "ppg", "centro", "url"])
        reader = csv.reader(f, delimiter=sep)
        if has_header:
            next(reader, None)

        for row in reader:
            if not row or len(row) < 4:
                continue

            nome = row[0].strip()
            ppg = int(row[1].strip())
            centro = int(row[2].strip())
            url = normalize_start_url(row[3].strip())

            min_year = int(row[4]) if len(row) > 4 and row[4].strip() else default_min_year
            delay = float(row[5]) if len(row) > 5 and row[5].strip() else default_delay
            max_pages = int(row[6]) if len(row) > 6 and row[6].strip() else default_max_pages
            patience = int(row[7]) if len(row) > 7 and row[7].strip() else default_patience

            out_csv = os.path.join(out_dir, "ufsc_{}_ppg{}_centro{}.csv".format(safe_name(nome), ppg, centro))
            if logs_dir:
                log_path = os.path.join(logs_dir, "batch_ppg{}_centro{}.log".format(ppg, centro))
            else:
                log_path = None

            log("[RUN] {} (ppg={}, centro={})".format(nome, ppg, centro))
            try:
                crawl_line_by_line(
                    outpath=out_csv,
                    start_url=url,
                    min_year=min_year,
                    max_pages=max_pages,
                    delay=delay,
                    persist=persist,
                    id_ppg=ppg,
                    id_centro=centro,
                    patience_below=patience
                )
                ok += 1
                if log_path:
                    with open(log_path, "a", encoding="utf-8") as lf:
                        lf.write("OK {}\n".format(out_csv))
                log("[OK ] Finalizado: {}".format(out_csv))
            except Exception as e:
                fail += 1
                if log_path:
                    with open(log_path, "a", encoding="utf-8") as lf:
                        lf.write("FAIL: {}\n".format(str(e)))
                log("[FAIL] {} → {}".format(nome, str(e)))
            total += 1

    log("\n[RESUMO BATCH] total={}, ok={}, falhas={}".format(total, ok, fail))

# =========================
# CLI
# =========================
def main():
    ap = argparse.ArgumentParser(description="UFSC Recent Submissions → CSV + persistência (linha a linha) com parada por paciência; suporta batch TSV")
    ap.add_argument("--out", default="ufsc_recent.csv", help="CSV de saída (modo single)")
    ap.add_argument("--start", default=DEFAULT_START,
                    help="URL inicial (recent-submissions) em modo single")
    ap.add_argument("--min-year", type=int, default=MIN_YEAR, help="Nunca gravar ano <= min-year")
    ap.add_argument("--patience-below", type=int,
                    default=int(os.getenv("PATIENCE_BELOW", "3")),
                    help="Encerrar após N consecutivos com ano <= min-year (0 desativa)")
    ap.add_argument("--max-pages", type=int, default=None, help="Máximo de páginas a varrer")
    ap.add_argument("--delay", type=float, default=float(os.getenv("DELAY", "1.8")), help="Delay entre itens (s)")
    ap.add_argument("--persist", action="store_true", help="Ativa gravação no PostgreSQL (linha a linha)")
    ap.add_argument("--ppg", type=int, default=int(os.getenv("ID_PPG", "0")), help="Valor para id_ppg (single)")
    ap.add_argument("--centro", type=int, default=int(os.getenv("ID_CENTRO", "0")), help="Valor para id_centro (single)")

    # Batch TSV
    ap.add_argument("--batch-file", help="Arquivo TSV/CSV com colunas: nome,ppg,centro,url[,min_year,delay,max_pages,patience_below]")
    ap.add_argument("--sep", default="\t", help="Separador do batch (default: TAB '\\t'; use ',' para CSV)")
    ap.add_argument("--out-dir", default="python/dados_coletados", help="Diretório base para CSVs do batch")
    ap.add_argument("--logs-dir", default="logs", help="Diretório de logs do batch (opcional)")

    args = ap.parse_args()

    # MODO BATCH
    if args.batch_file:
        run_batch(
            file_path=args.batch_file,
            sep=args.sep,
            out_dir=args.out_dir,
            logs_dir=args.logs_dir,
            default_min_year=args.min_year,
            default_delay=args.delay,
            default_max_pages=args.max_pages,
            default_patience=args.patience_below,
            persist=args.persist
        )
        return

    # MODO SINGLE
    if args.persist and (args.ppg <= 0 or args.centro <= 0):
        log("[ERRO] Para persistir em modo single, informe --ppg e --centro (ou defina ID_PPG/ID_CENTRO no .env).")
        sys.exit(2)

    crawl_line_by_line(
        outpath=args.out,
        start_url=normalize_start_url(args.start),
        min_year=args.min_year,
        max_pages=args.max_pages,
        delay=args.delay,
        persist=args.persist,
        id_ppg=(args.ppg if args.ppg > 0 else None),
        id_centro=(args.centro if args.centro > 0 else None),
        patience_below=args.patience_below,
    )

if __name__ == "__main__":
    main()
