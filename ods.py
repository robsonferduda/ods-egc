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

def basic_clean(string):
    '''
    This function takes in a string and
    returns the string normalized.
    '''
    string = unicodedata.normalize('NFKD', string)\
             .encode('ascii', 'ignore')\
             .decode('utf-8', 'ignore')
    string = re.sub(r'[^\w\s]', '', string).lower()
    return string

def tokenize(string):
    '''
    This function takes in a string and
    returns a tokenized string.
    '''
    # Create tokenizer.
    tokenizer = nltk.tokenize.ToktokTokenizer()

    # Use tokenizer
    string = tokenizer.tokenize(string, return_str = True)

    return string

def stem(string):
    '''
    This function takes in a string and
    returns a string with words stemmed.
    '''
    # Create porter stemmer.
    ps = nltk.porter.PorterStemmer()

    # Use the stemmer to stem each word in the list of words we created by using split.
    stems = [ps.stem(word) for word in string.split()]

    # Join our lists of words into a string again and assign to a variable.
    string = ' '.join(stems)

    return string

def lemmatize(string):
    '''
    This function takes in string for and
    returns a string with words lemmatized.
    '''
    # Create the lemmatizer.
    wnl = nltk.stem.WordNetLemmatizer()

    # Use the lemmatizer on each word in the list of words we created by using split.
    lemmas = [wnl.lemmatize(word) for word in string.split()]

    # Join our list of words into a string again and assign to a variable.
    string = ' '.join(lemmas)

    return string

def remove_stopwords(string, extra_words = [], exclude_words = []):
    '''
    This function takes in a string, optional extra_words and exclude_words parameters
    with default empty lists and returns a string.
    '''
    # Create stopword_list.
    stopword_list = stopwords.words('portuguese')

    stopword_list.extend(["da", "meu", "em", "você", "de", "ao", "os","sao","nao","uso","analise","pesquisa","estudo","tambem","sobre","partir","sendo","estudos","trabalho","objetivo","modelo","resultado","avaliacao"])

    # Remove 'exclude_words' from stopword_list to keep these in my text.
    stopword_list = set(stopword_list) - set(exclude_words)

    # Add in 'extra_words' to stopword_list.
    stopword_list = stopword_list.union(set(extra_words))

    # Split words in string.
    words = string.split()

    # Create a list of words from my string with stopwords removed and assign to variable.
    filtered_words = [word for word in words if word not in stopword_list]

    # Join words in the list back into strings and assign to a variable.
    string_without_stopwords = ' '.join(filtered_words)

    return string_without_stopwords

def clean(text):
    '''
    This function combines the above steps and added extra stop words to clean text
    '''
    return remove_stopwords(lemmatize(basic_clean(text)))

dados_rotulados_osdg = pd.read_csv('https://zenodo.org/records/8397907/files/osdg-community-data-v2023-10-01.csv?download=1', sep='\t')
dados_rotulados_osdg = dados_rotulados_osdg.query('agreement >= .6 and labels_positive > labels_negative').copy()

#limpeza dos dados
for index, row in dados_rotulados_osdg.iterrows():
  dados_rotulados_osdg.loc[index, "docs"] = clean(row["text"])

X_train, X_test, y_train, y_test = train_test_split(
    dados_rotulados_osdg['docs'].values,
    dados_rotulados_osdg['sdg'].values,
    test_size = .3,
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