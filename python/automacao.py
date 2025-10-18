#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import csv
import os
import sys
import argparse
import subprocess
from datetime import datetime

# caminho do script principal
COLETOR = os.path.join(os.path.dirname(__file__), "python", "busca_ri.py")

def log(msg):
    sys.stdout.write(msg.rstrip() + "\n")
    sys.stdout.flush()

def ensure_dir(path):
    if not os.path.isdir(path):
        os.makedirs(path)

def main():
    ap = argparse.ArgumentParser(description="Driver: executa busca_ri.py para vários programas (TSV/CSV)")
    ap.add_argument("--file", required=True, help="TSV/CSV com colunas: nome,ppg,centro,url[,min_year,delay,max_pages]")
    ap.add_argument("--sep", default="\t", help="Separador (default: TAB '\\t', use ',' para CSV)")
    ap.add_argument("--out-dir", default="python/dados_coletados", help="Diretório base dos CSVs de saída")
    ap.add_argument("--logs-dir", default="logs", help="Diretório dos logs")
    ap.add_argument("--default-min-year", type=int, default=2021, help="min_year padrão")
    ap.add_argument("--default-delay", type=float, default=2.8, help="delay padrão")
    ap.add_argument("--default-max-pages", type=int, default=3, help="max_pages padrão")
    ap.add_argument("--persist", action="store_true", help="Habilita --persist no coletor")
    ap.add_argument("--skip-existing", action="store_true", help="Se CSV existir e tiver >0 bytes, pula")
    args = ap.parse_args()

    ensure_dir(args.out_dir)
    ensure_dir(args.logs_dir)

    if not os.path.isfile(COLETOR):
        log("[ERRO] Script coletor não encontrado em: {}".format(COLETOR))
        sys.exit(2)

    total = ok = fail = 0

    with open(args.file, "r", encoding="utf-8") as f:
        # detecta cabeçalho simples
        first = f.readline()
        f.seek(0)
        has_header = any(k in first.lower() for k in ["nome", "ppg", "centro", "url"])
        reader = csv.reader(f, delimiter=args.sep)
        if has_header:
            next(reader, None)

        for row in reader:
            if not row or len(row) < 4:
                continue

            nome = row[0].strip()
            ppg = row[1].strip()
            centro = row[2].strip()
            url = row[3].strip()

            min_year = int(row[4]) if len(row) > 4 and row[4].strip() else args.default_min_year
            delay = float(row[5]) if len(row) > 5 and row[5].strip() else args.default_delay
            max_pages = int(row[6]) if len(row) > 6 and row[6].strip() else args.default_max_pages

            # arquivo de saída por programa
            safe_nome = "".join(c if c.isalnum() or c in "-_." else "_" for c in nome)[:80]
            out_csv = os.path.join(args.out_dir, "ufsc_{}_ppg{}_centro{}.csv".format(safe_nome, ppg, centro))
            ts = datetime.now().strftime("%Y%m%d-%H%M%S")
            log_path = os.path.join(args.logs_dir, "run_ppg{}_centro{}_{}.log".format(ppg, centro, ts))

            total += 1
            if args.skip_existing and os.path.exists(out_csv) and os.path.getsize(out_csv) > 0:
                log("[SKIP] {} (ppg={}, centro={}) → CSV já existe: {}".format(nome, ppg, centro, out_csv))
                continue

            cmd = [
                sys.executable, COLETOR,
                "--out", out_csv,
                "--start", url,                  # <<<<<< passa a URL da linha
                "--min-year", str(min_year),
                "--delay", str(delay),
                "--max-pages", str(max_pages),
                "--ppg", str(ppg),
                "--centro", str(centro),
            ]
            if args.persist:
                cmd.append("--persist")

            log("[RUN] {} (ppg={}, centro={})".format(nome, ppg, centro))
            log("      CMD: {}".format(" ".join(cmd)))
            with open(log_path, "w", encoding="utf-8") as lf:
                lf.write("CMD: {}\n\n".format(" ".join(cmd)))
                lf.flush()
                try:
                    proc = subprocess.Popen(cmd, stdout=subprocess.PIPE, stderr=subprocess.STDOUT, universal_newlines=True)
                    for line in proc.stdout:
                        sys.stdout.write(line)
                        lf.write(line)
                    ret = proc.wait()
                    if ret == 0:
                        ok += 1
                        log("[OK ] Finalizado: {}  → log: {}".format(nome, log_path))
                    else:
                        fail += 1
                        log("[FAIL] Retorno {}: {}  → log: {}".format(ret, nome, log_path))
                except Exception as e:
                    fail += 1
                    log("[ERRO] Falha executando {}: {}  → log: {}".format(nome, str(e), log_path))

    log("\nResumo: total={}, ok={}, falhas={}".format(total, ok, fail))

if __name__ == "__main__":
    main()
