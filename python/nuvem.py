import os, re, string, psycopg2, pandas as pd
from psycopg2.extras import RealDictCursor
from wordcloud import WordCloud
from unidecode import unidecode
import matplotlib.pyplot as plt

# -------- Config --------
DB = {
    "host": os.getenv("DB_HOST", "162.241.40.125"),
    "port": int(os.getenv("DB_PORT", "5432")),
    "dbname": os.getenv("DB_DATABASE", "ods"),
    "user": os.getenv("DB_USERNAME", "postgres"),
    "password": os.getenv("DB_PASSWORD", "DMK@rr19"),
}
ODS_ALVO = int(os.getenv("ODS_ALVO", "7"))  # mude aqui se quiser outro ODS
OUT_IMG  = f"wordcloud_ods{ODS_ALVO}.png"
OUT_CSV  = f"top_terms_ods{ODS_ALVO}.csv"

# -------- Stopwords --------
# Tenta carregar NLTK; se não tiver baixado, usa fallback enxuto
try:
    import nltk
    from nltk.corpus import stopwords
    try:
        _ = stopwords.words("portuguese")
    except LookupError:
        nltk.download("stopwords")
    PT_STOP = set(stopwords.words("portuguese"))
except Exception:
    PT_STOP = set("""
a ao aos à às de da das do dos e em no na nos nas num numa por para com sem sob sobre
o os a as um uma uns umas que se ser ter haver este esta esse essa aquele aquela isso isto
já mais menos muito pouco quando onde como porque porquê qual quais quem cujo cuja ou
""".split())

# Ruídos do domínio / ODS (ajuste à vontade)
DOMAIN_STOP = {
    "universidade","federal","santa","catarina","ufsc","aluno","alunos","disciplina","curso","diferentes",
    "professor","docente","pesquisa","extensao","inovacao","gestao","objetivo","sustentavel",
    "sustentabilidade","objetivos","desenvolvimento","documento","ano","trabalho","estudo",
    "projeto","resultado","resultados","dados","analise","metodo","metodos","sobre","nao","sao","alem","sistema","sistemas","modelo"
}

STOP = PT_STOP | DOMAIN_STOP

# -------- Helpers --------
def normalize_text(s: str) -> str:
    if not s:
        return ""
    # remove URLs, emails, números e pontuação; baixa e tira acento
    s = s.lower()
    s = re.sub(r"https?://\S+|www\.\S+|\S+@\S+", " ", s)
    s = unidecode(s)
    s = re.sub(r"\d+", " ", s)
    s = s.translate(str.maketrans("", "", string.punctuation))
    s = re.sub(r"\s+", " ", s).strip()
    return s

def tokenize(text: str):
    toks = [t for t in text.split() if len(t) > 2 and t not in STOP]
    return toks

# -------- Query --------
SQL = """
SELECT (COALESCE(titulo,'') || ' ' || COALESCE(texto,'')) AS conteudo
FROM public.documento_ods
WHERE ods = %s AND (titulo IS NOT NULL OR texto IS NOT NULL);
"""

def main():
    # 1) Busca do banco
    with psycopg2.connect(**DB) as con, con.cursor(cursor_factory=RealDictCursor) as cur:
        cur.execute(SQL, (ODS_ALVO,))
        rows = cur.fetchall()

    if not rows:
        print("Nenhum texto encontrado para esse ODS.")
        return

    # 2) Limpeza e tokenização
    textos = [normalize_text(r["conteudo"]) for r in rows]
    tokens = []
    for t in textos:
        tokens.extend(tokenize(t))

    if not tokens:
        print("Sem tokens após limpeza/stopwords.")
        return

    # 3) Frequência
    s = pd.Series(tokens, dtype="string")
    freq = s.value_counts().reset_index()
    freq.columns = ["termo", "frequencia"]

    # salva CSV de frequências
    freq.to_csv(OUT_CSV, index=False, encoding="utf-8")
    print(f"Frequências salvas em: {OUT_CSV}")

    # 4) Nuvem de palavras (usa dicionário termo->freq)
    freq_dict = dict(zip(freq["termo"], freq["frequencia"]))

    wc = WordCloud(
        width=1800, height=1000,
        background_color="white",
        prefer_horizontal=0.9,
        colormap="Wistia_r",   # azul→verde→amarelo, elegante para ODS
        max_words=400,
        collocations=False,  # evita juntar bigramas automaticamente
        normalize_plurals=True
    ).generate_from_frequencies(freq_dict)

    # 5) Salva imagem
    plt.figure(figsize=(18,10))
    plt.imshow(wc, interpolation="bilinear")
    plt.axis("off")
    plt.tight_layout(pad=0)
    plt.savefig(OUT_IMG, dpi=180)
    plt.close()
    print(f"Nuvem salva em: {OUT_IMG}")

if __name__ == "__main__":
    main()
