from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from webdriver_manager.chrome import ChromeDriverManager

# Configura o serviço automaticamente com o ChromeDriverManager
service = Service(ChromeDriverManager().install())

# Inicializa o driver
driver = webdriver.Chrome(service=service)

# Teste básico
driver.get("https://www.google.com")
print("Página carregada com sucesso!")
driver.quit()