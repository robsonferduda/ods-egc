import os
import numpy as np
import pandas as pd
import sys

# data modelling
from sklearn.model_selection import train_test_split
from sklearn.feature_extraction.text import CountVectorizer, TfidfVectorizer
from sklearn.feature_selection import SelectKBest, chi2, f_classif
from sklearn.linear_model import LogisticRegression
from sklearn.pipeline import Pipeline
from sklearn.metrics import confusion_matrix, classification_report, accuracy_score, top_k_accuracy_score, f1_score

from decouple import config

# regular expression import
import re

# uni-code library
import unicodedata

# natural language toolkit library/modules
import nltk
from nltk.tokenize.toktok import ToktokTokenizer
from nltk.corpus import stopwords
nltk.download('wordnet')
nltk.download('stopwords')

host = config('DB_HOST')
database = config('DB_DATABASE')
user = config('DB_USERNAME')
password = config('DB_PASSWORD')
path = config('APP_PATH')

dados_rotulados_osdg = pd.read_csv(path+'dados_treino.csv')
feature_ods = pd.read_csv(path+'feature_ods_5.csv')

X_train, X_test, y_train, y_test = train_test_split(
    dados_rotulados_osdg['docs'].values,
    dados_rotulados_osdg['sdg'].values,
    test_size = .4,
    random_state = 42
)

pipe = Pipeline([
    ('vectoriser', TfidfVectorizer(
        ngram_range = (1, 2),
        max_df = 0.75,
        min_df = 2,
        max_features = 100_000
    )),
    ('selector', SelectKBest(f_classif, k = 5_000)),
    ('clf', LogisticRegression(
        penalty = 'l2',
        C = .9,
        multi_class = 'multinomial',
        class_weight = 'balanced',
        random_state = 42,
        solver = 'newton-cg',
        max_iter = 100
    ))
])

pipe.fit(X_train, y_train)

y_hat = pipe.predict(X_test)

from googletrans import Translator
translator = Translator()

texto_predicao = sys.argv[1]
texto_traduzido = translator.translate(texto_predicao).text

str_proba = ""
separador=";"

y_hat = pipe.predict([texto_traduzido]).item()
y_proba = pipe.predict_proba([texto_traduzido]).flatten()
for i in y_proba:
    str_proba += str(i)+separador

print(str_proba, end = '')