import seaborn as sns
import matplotlib.pyplot as plt
import pandas as pd
import numpy as np

# ======================================
# 1. Parte importante:
#    Isto usa a matriz confusion_abs já criada no código anterior.
# ======================================

data = {
    "ID": [241,543,565,616,617,619,642,672,673,674,676,
           715,729,748,761,763,797,807,815,820,875,
           1114,1121,1143,1331,1340,1344,1530,1722,1753],
    "Modelo": [3,3,13,7,7,7,12,4,4,4,4,
               7,7,7,15,15,4,11,4,13,9,
               4,4,11,14,4,4,3,4,3],
    "A1": [3,3,13,7,7,7,9,4,4,4,4,
           9,7,9,15,15,4,11,10,15,9,
           4,4,11,14,16,4,3,4,3],
    "A2": [3,16,13,7,7,9,12,4,4,4,4,
           7,13,7,15,15,4,11,10,13,9,
           4,4,11,2,10,4,3,4,3],
    "A3": [3,3,5,7,7,7,9,4,4,4,4,
           7,7,7,2,15,2,11,4,12,9,
           4,4,11,14,16,4,3,4,3],
    "A4": [3,3,5,7,7,7,9,4,4,4,4,
           9,9,9,15,15,4,11,4,13,9,
           4,4,11,2,4,4,3,4,3],
    "A5": [3,3,13,7,7,9,12,4,4,4,4,
           7,13,7,15,15,4,11,10,13,9,
           4,4,11,2,10,4,3,4,3],
}

df = pd.DataFrame(data)
avaliadores = ["A1", "A2", "A3", "A4", "A5"]

ods_all = list(range(1, 17))  # ODS 1 até 17
confusion_abs = pd.DataFrame(0, index=ods_all, columns=ods_all, dtype=int)

# Preenche a matriz de confusão:
#  - linha = ODS do modelo
#  - coluna = ODS do avaliador (para qualquer um dos 5)
for _, row in df.iterrows():
    modelo = row["Modelo"]
    for av in avaliadores:
        humano = row[av]
        confusion_abs.loc[modelo, humano] += 1

# Se quiser garantir que não haja divisão por zero:
row_sums = confusion_abs.sum(axis=1).replace(0, np.nan)

# matriz percentual (por linha = ODS modelo)
confusion_perc = confusion_abs.div(row_sums, axis=0) * 100

# transforma NaN em 0.0 para linhas total zero
confusion_perc = confusion_perc.fillna(0.0)

# ======================================
# 2. Plotar heatmap percentual
# ======================================

plt.figure(figsize=(11, 8), dpi=300)

sns.heatmap(
    confusion_perc,
    annot=True,
    fmt=".1f",
    cmap="YlOrBr",         # mesma paleta usada antes, estilizada
    cbar_kws={"label": "Percentual (%)"},
)

plt.xlabel("ODS atribuído pelos avaliadores")
plt.ylabel("ODS atribuído pelo modelo")
plt.tight_layout()

plt.savefig("matriz_confusao_percentual_1-17.png", dpi=300)
plt.close()

print("Arquivo salvo como matriz_confusao_percentual_1-17.png")
