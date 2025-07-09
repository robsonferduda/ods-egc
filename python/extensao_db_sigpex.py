import psycopg2

# CONFIGURAÇÕES DE CONEXÃO
DB_HOST = 'localhost'
DB_PORT = '5432'
DB_NAME = 'nome_do_banco'
DB_USER = 'usuario'
DB_PASS = 'senha'

# Conexão com o banco
conn = psycopg2.connect(
    host=DB_HOST, port=DB_PORT, dbname=DB_NAME,
    user=DB_USER, password=DB_PASS
)
cur = conn.cursor()

# Constantes
VINCULO_DOCENTE = 2
FUNCAO_COORDENADOR = 3
FUNCAO_PARTICIPANTE = 5
DIMENSAO_EXTENSAO = 2
TIPO_PROJETO_EXTENSAO = 2

# Busca todos os registros da tabela extensao com id_producao_intelectual definido
cur.execute("""
    SELECT id, id_producao_intelectual, titulo, resumo, coordenador, participantes
    FROM extensao
    WHERE id_producao_intelectual IS NOT NULL
""")
registros = cur.fetchall()

print(f"🔍 Total de registros encontrados: {len(registros)}\n")

for linha in registros:
    try:
        id_ext, id_producao, titulo, resumo, coordenador, participantes = linha

        # 1. Inserir documento
        cur.execute("""
            INSERT INTO documento_ods (
                id_producao_intelectual, titulo, texto, ano,
                id_dimensao, id_tipo_documento
            )
            VALUES (%s, %s, %s, EXTRACT(YEAR FROM now())::int, %s, %s)
            RETURNING id
        """, (id_producao, titulo, resumo, DIMENSAO_EXTENSAO, TIPO_PROJETO_EXTENSAO))
        id_documento = cur.fetchone()[0]
        print(f"[✓] Documento inserido (ID {id_documento})")

        # 2. Inserir coordenador (DOCENTE, função Coordenador)
        if coordenador:
            nome_coord = coordenador.strip().upper()
            cur.execute("SELECT id_pessoa_pes FROM pessoa_pes WHERE ds_nome_pessoa = %s", (nome_coord,))
            row = cur.fetchone()
            if row:
                id_coord = row[0]
            else:
                cur.execute("""
                    INSERT INTO pessoa_pes (ds_nome_pessoa, id_vinculo_vin)
                    VALUES (%s, %s)
                    RETURNING id_pessoa_pes
                """, (nome_coord, VINCULO_DOCENTE))
                id_coord = cur.fetchone()[0]
                print(f"[+] Coordenador inserido: {nome_coord}")

            cur.execute("""
                INSERT INTO documento_pessoa_dop (id_documento_ods, id_pessoa_pes, id_funcao_fun)
                VALUES (%s, %s, %s)
            """, (id_documento, id_coord, FUNCAO_COORDENADOR))
            print(f"[→] Vínculo coordenador-documento criado.")

        # 3. Inserir participantes (função Participante, vínculo indefinido se não existir)
        if participantes:
            nomes_participantes = [p.strip().upper() for p in participantes.split(",") if p.strip()]
            for nome_part in nomes_participantes:
                cur.execute("SELECT id_pessoa_pes, id_vinculo_vin FROM pessoa_pes WHERE ds_nome_pessoa = %s", (nome_part,))
                row = cur.fetchone()
                if row:
                    id_part = row[0]
                else:
                    cur.execute("""
                        INSERT INTO pessoa_pes (ds_nome_pessoa, id_vinculo_vin)
                        VALUES (%s, NULL)
                        RETURNING id_pessoa_pes
                    """, (nome_part,))
                    id_part = cur.fetchone()[0]
                    print(f"[+] Participante inserido: {nome_part}")

                cur.execute("""
                    INSERT INTO documento_pessoa_dop (id_documento_ods, id_pessoa_pes, id_funcao_fun)
                    VALUES (%s, %s, %s)
                """, (id_documento, id_part, FUNCAO_PARTICIPANTE))
            print(f"[→] Vínculo(s) participantes-documento criado(s).")

        conn.commit()
        print("💾 Transação concluída.\n")

    except Exception as e:
        conn.rollback()
        print(f"[X] Erro ao processar ID {id_ext}: {e}\n")

# Finaliza conexão
cur.close()
conn.close()
print("✅ Processamento concluído.")
