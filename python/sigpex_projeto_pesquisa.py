import pandas as pd
import psycopg2
from psycopg2 import sql
from datetime import datetime

# CONFIGURAÇÕES DE CONEXÃO – SUBSTITUA PELOS SEUS DADOS
DB_HOST = '162.241.40.125'
DB_PORT = '5432'
DB_NAME = 'ods'
DB_USER = 'postgres'
DB_PASS = 'DMK@rr19'

# Caminho da planilha
file_path = 'projetos_.xlsx'

# Conecta ao banco
conn = psycopg2.connect(
    host=DB_HOST, port=DB_PORT, dbname=DB_NAME,
    user=DB_USER, password=DB_PASS
)
cur = conn.cursor()

# Funções auxiliares
def extrair_ano(data):
    if pd.isnull(data):
        return None
    return pd.to_datetime(data).year

def extrair_nomes(campo):
    if pd.isnull(campo):
        return []
    texto = str(campo).strip().strip("[]")
    return [nome.strip().upper() for nome in texto.split(",") if nome.strip()]

def inserir_pessoa(nome):
    cur.execute("SELECT 1 FROM pessoa_pes WHERE ds_nome_pessoa = %s", (nome,))
    if not cur.fetchone():
        cur.execute(
            "INSERT INTO pessoa_pes (ds_nome_pessoa) VALUES (%s)",
            (nome,)
        )
        print(f"[+] Pessoa inserida: {nome}")
    else:
        print(f"[=] Pessoa já existe: {nome}")

def inserir_documento(texto, titulo, ano):
    cur.execute("""
        INSERT INTO documento_ods (texto, titulo, ano)
        VALUES (%s, %s, %s)
    """, (texto, titulo, ano))
    print(f"[✓] Documento inserido – Ano: {ano} | Título: {titulo[:30]}...")

# Carrega os dados da planilha
df = pd.read_excel(file_path, sheet_name='Sheet0')

print(f"📄 Total de registros na planilha: {len(df)}")

for i, row in df.iterrows():
    print(f"\n🔄 Processando linha {i+1}...")

    resumo = row.get("Resumo", "").strip() if not pd.isnull(row.get("Resumo")) else None
    tipo = row.get("Tipo", "").strip() if not pd.isnull(row.get("Tipo")) else None
    ano = extrair_ano(row.get("Início"))

    try:
        inserir_documento(resumo, tipo, ano)

        coordenadores = extrair_nomes(row.get("Coordenador", ""))
        participantes = extrair_nomes(row.get("Participantes", ""))
        for nome in coordenadores + participantes:
            inserir_pessoa(nome)

        conn.commit()
        print("[💾] Transação confirmada.")
    except Exception as e:
        conn.rollback()
        print(f"[!] Erro na linha {i+1}. Alterações revertidas. Detalhe: {e}")

# Finaliza a conexão
cur.close()
conn.close()
print("\n✅ Importação finalizada com sucesso.")