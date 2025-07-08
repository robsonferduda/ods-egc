import psycopg2

# CONFIGURAÇÕES DE CONEXÃO
DB_HOST = '162.241.40.125'
DB_PORT = '5432'
DB_NAME = 'ods'
DB_USER = 'postgres'
DB_PASS = 'DMK@rr19'

# CONECTA AO BANCO
conn = psycopg2.connect(
    host=DB_HOST, port=DB_PORT, dbname=DB_NAME,
    user=DB_USER, password=DB_PASS
)
cur = conn.cursor()

# IDs fixos conforme mapeamento
VINCULO_DOCENTE = 2
VINCULO_DISCENTE = 1
FUNCAO_ORIENTADOR = 1
FUNCAO_ALUNO = 2

# BUSCA OS PRIMEIROS 10 REGISTROS DA CAPES
cur.execute("""
    SELECT id_producao_intelectual, an_base, nm_subtipo_producao, nm_producao,
           ds_resumo, ds_abstract, ds_keyword, nm_orientador, nm_discente
    FROM capes_teses_dissertacoes_ctd
    WHERE id_producao_intelectual IS NOT NULL
    AND id_producao_intelectual NOT IN(select id_producao_intelectual FROM documento_ods WHERE id_dimensao = 5)
""")
registros = cur.fetchall()

for linha in registros:
    try:
        id_producao, ano, subtipo, titulo, resumo, abstract, keyword, orientador, discente = linha
        texto_analisado = (abstract or '') + ' ' + (keyword or '')
        tipo = 1 if subtipo == 'DISSERTAÇÃO' else 2
        dimensao = 5

        # 1. INSERE DOCUMENTO
        cur.execute("""
            INSERT INTO documento_ods (id_producao_intelectual, texto, titulo, ano, texto_analisado, id_tipo_documento, id_dimensao)
            VALUES (%s, %s, %s, %s, %s, %s, %s)
            RETURNING id
        """, (id_producao, resumo, titulo, ano, texto_analisado, tipo, dimensao))
        id_documento = cur.fetchone()[0]
        #print(f"[✓] Documento inserido (ID {id_documento})")

        # 2. INSERE ORIENTADOR
        if orientador:
            nome_orientador = orientador.strip().upper()
            cur.execute("SELECT id_pessoa_pes FROM pessoa_pes WHERE ds_nome_pessoa = %s", (nome_orientador,))
            row = cur.fetchone()
            if row:
                id_orientador = row[0]
            else:
                cur.execute("""
                    INSERT INTO pessoa_pes (ds_nome_pessoa, id_vinculo_vin)
                    VALUES (%s, %s)
                    RETURNING id_pessoa_pes
                """, (nome_orientador, VINCULO_DOCENTE))
                id_orientador = cur.fetchone()[0]
                #print(f"[+] Orientador inserido: {nome_orientador}")

            cur.execute("""
                INSERT INTO documento_pessoa_dop (id_documento_ods, id_pessoa_pes, id_funcao_fun)
                VALUES (%s, %s, %s)
            """, (id_documento, id_orientador, FUNCAO_ORIENTADOR))
            #print(f"[→] Vínculo orientador-documento criado.")

        # 3. INSERE DISCENTE
        if discente:
            nome_discente = discente.strip().upper()
            cur.execute("SELECT id_pessoa_pes FROM pessoa_pes WHERE ds_nome_pessoa = %s", (nome_discente,))
            row = cur.fetchone()
            if row:
                id_discente = row[0]
            else:
                cur.execute("""
                    INSERT INTO pessoa_pes (ds_nome_pessoa, id_vinculo_vin)
                    VALUES (%s, %s)
                    RETURNING id_pessoa_pes
                """, (nome_discente, VINCULO_DISCENTE))
                id_discente = cur.fetchone()[0]
                #print(f"[+] Discente inserido: {nome_discente}")

            cur.execute("""
                INSERT INTO documento_pessoa_dop (id_documento_ods, id_pessoa_pes, id_funcao_fun)
                VALUES (%s, %s, %s)
            """, (id_documento, id_discente, FUNCAO_ALUNO))
            #print(f"[→] Vínculo discente-documento criado.")

        conn.commit()
        #print("💾 Transação concluída.\n")

    except Exception as e:
        conn.rollback()
        #print(f"[X] Erro: {e}\n")

# FINALIZA
cur.close()
conn.close()
print("✅ Processamento concluído.")
