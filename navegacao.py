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

ficheiro = open('docentes.csv', 'rt',  encoding='utf-8')
reader = csv.reader(ficheiro)
for linha in reader:
    driver.get("https://buscatextual.cnpq.br/buscatextual/busca.do?metodo=apresentar")

    search_box = driver.find_element(by=By.NAME, value="textoBusca")
    search_box.send_keys(linha)

    btn_search = driver.find_element(by=By.ID, value="botaoBuscaFiltros")
    btn_search.click()
    time.sleep(3) 

    elements = driver.find_element(by=By.CLASS_NAME, value="resultado")
    texto = elements.find_element(By.TAG_NAME, value="a").get_attribute("href")
    found = re.search('\(\'(.+?)\'', texto).group(1)

    foto = 'https://servicosweb.cnpq.br/wspessoa/servletrecuperafoto?tipo=1&id='+found

    path = "public/img/docentes/"+found+".jpg"
    request = requests.get(foto, stream=True)
    with open(path, "wb+") as file:
        for c in request:
            file.write(c)

    docente = ''.join((str(e) for e in linha))

    csvfile = open('docente_lattes.csv', 'a+')
    csv.writer(csvfile, delimiter=';').writerow([docente,foto])
    csvfile.close()
