from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.common.action_chains import ActionChains
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import csv
import time

# Configuração inicial do Selenium
def setup_selenium():
    options = webdriver.ChromeOptions()
    options.add_argument("--headless")  # Executa em modo headless (sem interface gráfica)
    driver = webdriver.Chrome(options=options)  # Certifique-se de ter o ChromeDriver instalado
    return driver

# Função para buscar projetos por ano e tipo
def search_projects(driver):
    all_projects = []

    # Anos e tipos de atividades
    anos = [2019, 2020, 2021, 2022, 2023, 2024]
    tipos_atividades = {
        "PROJETO": "2",
        "CURSO": "3",
        "EVENTO": "4"
    }

    # URL base
    base_url = "https://sigaa.ufpe.br/sigaa/public/extensao/consulta_extensao.jsf"

    for ano in anos:
        for tipo, valor_tipo in tipos_atividades.items():
            print(f"Buscando {tipo}s para o ano {ano}...")
            
            # Acessa a página inicial
            driver.get(base_url)
            time.sleep(2)  # Aguarda a página carregar

            try:
                # Habilita o filtro por ano
                checkbox_ano = driver.find_element(By.ID, "formBuscaAtividade:selectBuscaAno")
                if not checkbox_ano.is_selected():
                    checkbox_ano.click()

                # Preenche o campo de ano
                input_ano = driver.find_element(By.ID, "formBuscaAtividade:buscaAno")
                input_ano.clear()
                input_ano.send_keys(str(ano))

                # Habilita o filtro por tipo de atividade
                checkbox_tipo = driver.find_element(By.ID, "formBuscaAtividade:selectBuscaTipoAtividade")
                if not checkbox_tipo.is_selected():
                    checkbox_tipo.click()

                # Seleciona o tipo de atividade
                select_tipo = driver.find_element(By.ID, "formBuscaAtividade:buscaTipoAcao")
                select_tipo.send_keys(valor_tipo)

                # Clica no botão "Buscar"
                botao_buscar = driver.find_element(By.ID, "formBuscaAtividade:btBuscar")
                botao_buscar.click()
                time.sleep(3)  # Aguarda os resultados carregarem

                # Extrai os resultados da tabela
                table = driver.find_element(By.CLASS_NAME, "listagem")
                rows = table.find_elements(By.TAG_NAME, "tr")

                for row in rows:
                    columns = row.find_elements(By.TAG_NAME, "td")
                    if len(columns) >= 3:
                        title_tag = columns[0].find_element(By.TAG_NAME, "a")
                        title = title_tag.text.strip()
                        activity_type = columns[1].text.strip()
                        department = columns[2].text.strip()

                        # Extrai o ID da atividade do atributo onclick
                        onclick = title_tag.get_attribute("onclick")
                        id_atividade = (
                            onclick.split("'idAtividadeExtensaoSelecionada':'")[1].split("'")[0]
                            if "'idAtividadeExtensaoSelecionada':" in onclick
                            else "N/A"
                        )

                        all_projects.append({
                            "title": title,
                            "activity_type": activity_type,
                            "department": department,
                            "id_atividade": id_atividade
                        })

            except Exception as e:
                print(f"Falha ao buscar {tipo}s para o ano {ano}: {e}")

    return all_projects

# Função para extrair detalhes de cada projeto
def extract_project_details(driver, id_atividade):
    detail_url = f"https://sigaa.ufpe.br/sigaa/public/extensao/detalheAtividadeExtensao.jsf?idAtividadeExtensaoSelecionada={id_atividade}&acao=0"
    driver.get(detail_url)
    time.sleep(3)  # Aguarda a página de detalhes carregar

    try:
        # Extrai o título
        title_tag = driver.find_element(By.TAG_NAME, "h2")
        title = title_tag.text.strip() if title_tag else "N/A"

        # Extrai o ano (geralmente está no título ou em um campo específico)
        year = title.split(" - ")[0] if " - " in title else "N/A"

        # Extrai a Área Principal
        area_principal_tag = driver.find_element(By.XPATH, "//h4[text()='Área Principal']/following-sibling::p")
        area_principal = area_principal_tag.text.strip() if area_principal_tag else "N/A"

        # Extrai o Responsável pela Ação
        responsavel_tag = driver.find_element(By.XPATH, "//h4[text()='Responsável pela Ação']/following-sibling::p")
        responsavel = responsavel_tag.text.strip() if responsavel_tag else "N/A"

        # Extrai o Resumo
        resumo_tag = driver.find_element(By.XPATH, "//h4[text()='Resumo']/following-sibling::p")
        resumo = resumo_tag.text.strip() if resumo_tag else "N/A"

        # Extrai os Membros da Equipe
        membros_equipe = []
        equipe_table = driver.find_element(By.ID, "tbEquipe")
        if equipe_table:
            rows = equipe_table.find_elements(By.TAG_NAME, "tr")
            for row in rows:
                cols = row.find_elements(By.TAG_NAME, "td")
                if len(cols) > 1:  # Ignora linhas sem dados relevantes
                    nome = cols[1].text.strip()
                    membros_equipe.append(nome)

        return {
            "title": title,
            "year": year,
            "area_principal": area_principal,
            "responsavel": responsavel,
            "resumo": resumo,
            "membros_equipe": ", ".join(membros_equipe) if membros_equipe else "N/A"
        }

    except Exception as e:
        print(f"Falha ao extrair detalhes do projeto com ID {id_atividade}: {e}")
        return {}

# Função principal para executar todo o processo
def scrape_and_extract():
    driver = setup_selenium()
    try:
        # Passo 1: Buscar todos os projetos
        projects = search_projects(driver)

        # Passo 2: Extrair detalhes de cada projeto
        all_data = []
        for project in projects:
            print(f"Extraindo detalhes do projeto '{project['title']}' (ID: {project['id_atividade']})")
            details = extract_project_details(driver, project["id_atividade"])
            all_data.append(details)

        # Passo 3: Salvar os dados em um arquivo CSV
        with open('project_details.csv', mode='w', newline='', encoding='utf-8') as file:
            writer = csv.DictWriter(file, fieldnames=["title", "year", "area_principal", "responsavel", "resumo", "membros_equipe"])
            writer.writeheader()
            writer.writerows(all_data)

        print("Extração concluída. Os dados foram salvos em 'project_details.csv'.")

    finally:
        driver.quit()

# Executa a função principal
scrape_and_extract()