import psycopg2
from psycopg2.extras import RealDictCursor
import os

# Conexão
conn = psycopg2.connect(
    host="162.241.40.125",
    dbname="ods",
    user="postgres",
    password="DMK@rr19",
    port="5432"
)
cursor = conn.cursor()

# Busca registros da tabela inovacao
cursor.execute("SELECT id, titulo, resumo, dt_deposito, participantes FROM inovacao WHERE id_producao_intelectual IS NOT NULL AND id_producao_intelectual NOT IN(select id_producao_intelectual FROM documento_ods WHERE id_dimensao = 4)")
inovacoes = cursor.fetchall()

for inovacao in inovacoes:
    id_inov = inovacao[0]
    titulo = inovacao[1]
    resumo = inovacao[2] or ''
    ano = inovacao[3].year if inovacao[3] else None
    participantes = inovacao[4]
    
    # Inserção no documento_ods
    cursor.execute("""
        INSERT INTO documento_ods (id_producao_intelectual, titulo, texto, ano, id_dimensao, id_tipo_documento)
        VALUES (%s, %s, %s, %s, 4, 5)
        RETURNING id
    """, (id_inov, titulo, resumo, ano))
    id_documento = cursor.fetchone()[0]
    print(f"Inserido documento ODS: {id_documento} - {titulo}")

    # Processa participantes
    if participantes:
        nomes = [nome.strip().upper() for nome in participantes.split('/') if nome.strip()]

        for nome in nomes:
            # Verifica se pessoa já existe
            cursor.execute("SELECT id_pessoa_pes FROM pessoa_pes WHERE ds_nome_pessoa = %s", (nome,))
            result = cursor.fetchone()

            if result:
                id_pessoa = result[0]
            else:
                cursor.execute("""
                    INSERT INTO pessoa_pes (ds_nome_pessoa)
                    VALUES (%s) RETURNING id_pessoa_pes
                """, (nome,))
                id_pessoa = cursor.fetchone()[0]
                print(f"Pessoa inserida: {nome}")

            # Relaciona no documento
            cursor.execute("""
                INSERT INTO documento_pessoa_dop (id_documento_ods, id_pessoa_pes, id_funcao_fun)
                VALUES (%s, %s, 4)
            """, (id_documento, id_pessoa))
            print(f"Relacionamento criado: {nome} como INVENTOR no doc {id_documento}")

conn.commit()
cursor.close()
conn.close()
print("\nProcessamento finalizado.")
