import psycopg2
import networkx as nx
import matplotlib.pyplot as plt

# Conexão
conn = psycopg2.connect(
    host="162.241.40.125",
    dbname="ods",
    user="postgres",
    password="DMK@rr19",
    port="5432"
)
cur = conn.cursor()

# Consulta os pares de pessoas colaboradoras em documentos
cur.execute("""
    SELECT
      dp1.id_pessoa_pes,
      dp2.id_pessoa_pes,
      COUNT(*) as peso
    FROM documento_pessoa_dop dp1
    JOIN documento_pessoa_dop dp2
      ON dp1.id_documento_ods = dp2.id_documento_ods
     AND dp1.id_pessoa_pes < dp2.id_pessoa_pes
    GROUP BY dp1.id_pessoa_pes, dp2.id_pessoa_pes
""")
pares = cur.fetchall()

# Consulta nomes
cur.execute("SELECT id_pessoa_pes, ds_nome_pessoa FROM pessoa_pes")
nomes_dict = dict(cur.fetchall())

# Constrói o grafo
G = nx.Graph()

for id1, id2, peso in pares:
    nome1 = nomes_dict.get(id1, f"ID:{id1}")
    nome2 = nomes_dict.get(id2, f"ID:{id2}")
    G.add_edge(nome1, nome2, weight=peso)

# Plot
plt.figure(figsize=(14, 10))
pos = nx.spring_layout(G, k=0.5)
nx.draw(G, pos, with_labels=True, node_color='skyblue', node_size=1500, font_size=9, edge_color='gray')
weights = nx.get_edge_attributes(G, 'weight')
nx.draw_networkx_edge_labels(G, pos, edge_labels=weights)
plt.title("Rede de Relacionamentos entre Pessoas")
plt.tight_layout()
plt.show()

cur.close()
conn.close()