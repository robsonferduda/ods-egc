from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.service import Service
from webdriver_manager.chrome import ChromeDriverManager

import time
import re
import csv
import requests

# Inicializando o driver
service = Service(ChromeDriverManager().install())
driver = webdriver.Chrome(service=service)


foto = 'https://clipagens.com.br/fmanager/clipagem/web/arquivo1511166_1.jpeg'

path = "public/img/docentes/noticia.jpg"
request = requests.get(foto, stream=True)
with open(path, "wb+") as file:
    for c in request:
        file.write(c)

