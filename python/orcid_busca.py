import psycopg2
import requests
import urllib.parse
import time

# CONFIGURAÇÕES DE CONEXÃO – SUBSTITUA PELOS SEUS DADOS
DB_HOST = '162.241.40.125'
DB_PORT = '5432'
DB_NAME = 'ods'
DB_USER = 'postgres'
DB_PASS = 'DMK@rr19'

# Conecta ao PostgreSQL
conn = psycopg2.connect(
    host=DB_HOST, port=DB_PORT, dbname=DB_NAME,
    user=DB_USER, password=DB_PASS
)
cur = conn.cursor()

# Função que busca ORCID por nome com filtro da UFSC
def buscar_orcid(nome_completo):
    nomes = nome_completo.strip().split()
    if len(nomes) < 2:
        return None  # Nome incompleto

    primeiro_nome = nomes[0]
    sobrenome = nomes[-1]

    query = (
        f"given-names:{urllib.parse.quote(primeiro_nome)}"
        f"+AND+family-name:{urllib.parse.quote(sobrenome)}"
        f"+AND+(affiliation-org-name:UFSC+OR+affiliation-org-name:%22Universidade%20Federal%20de%20Santa%20Catarina%22)"
    )
    url = f"https://pub.orcid.org/v3.0/expanded-search/?q={query}"
    headers = {"Accept": "application/json"}

    try:
        response = requests.get(url, headers=headers)
        if response.status_code != 200:
            print(f"[!] Erro na API ORCID: {response.status_code} - {nome_completo}")
            return None

        resultados = response.json().get("expanded-result", [])

        # Retorna somente se encontrar exatamente 1 ORCID
        if len(resultados) == 1:
            return resultados[0].get("orcid-id")
        return None
    except Exception as e:
        print(f"[Erro] Falha na requisição para {nome_completo}: {e}")
        return None

# Consulta nomes sem ORCID
cur.execute("SELECT id_pessoa_pes, ds_nome_pessoa FROM pessoa_pes WHERE ds_orcid_pes IS NULL")
registros = cur.fetchall()

print(f"🔍 Encontrados {len(registros)} registros sem ORCID.")

for id_pessoa, nome in registros:
    print(f"\n🔎 Buscando ORCID para: {nome}")
    orcid = buscar_orcid(nome)

    if orcid:
        try:
            cur.execute(
                "UPDATE pessoa_pes SET ds_orcid_pes = %s WHERE id_pessoa_pes = %s",
                (orcid, id_pessoa)
            )
            conn.commit()
            print(f"[✓] ORCID encontrado e salvo: {orcid}")
        except Exception as e:
            conn.rollback()
            print(f"[X] Falha ao atualizar ORCID no banco: {e}")
    else:
        print("[ ] Nenhum ORCID único encontrado.")

    time.sleep(1)  # Evita sobrecarga na API pública

# Finaliza
cur.close()
conn.close()
print("\n✅ Processo concluído.")
