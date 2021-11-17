from selenium import webdriver
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.firefox.options import Options
from Page import *

class Minicursos(Page):
        
    def deletar(self):
        self.logar()
        self.get(site_url("/trabalhos/submissoes"))
        try:
            elem = self.driver.find_element_by_partial_link_text("titulo teste integracao")
        except:
            raise Exception("MinicursoNaoCadastradoDeletar")
        elem.parent.find_element_by_tag_name("button").click()
        self.driver.find_element_by_xpath("//div[@onclick='confirmDelete();']").click()
        return True


    def cadastrar(self):
        self.logar()
        try:
            elem = self.driver.find_element_by_partial_link_text("Submeter minicurso")
        except:
            raise Exception("MinicursoBotaoNaoEncontrado")
        
        elem.click()

        #submissao
        self.input("telefone","84123451234")
        self.input("lattes","www.teste.com.br")
        sleep(1)
        self.driver.find_element_by_xpath("//button[@id='addCoautores']").click()
        sleep(1)
        #import pdb;pdb.set_trace()
        self.input(self.driver.find_elements_by_xpath("//input[@name='nome_coautor[]']")[0] ,"Coatuor 01 integracao")
        self.input(self.driver.find_elements_by_xpath("//input[@name='email_coautor[]']")[0] ,"coautor01@gmail.com")
        self.input(self.driver.find_elements_by_xpath("//input[@name='nome_coautor[]']")[1] ,"Coatuor 02 integracao")
        self.input(self.driver.find_elements_by_xpath("//input[@name='email_coautor[]']")[1] ,"coautor02@gmail.com")
        self.input("titulo","titulo teste integracao")
        self.input("resumo","resumo")
        self.input("objetivo","objetivo")
        self.input("idarea","2")
        self.input("vagas","10")
        self.input("ch","3")
        self.input("//input[@value='2021-04-12 13:30']")
        self.input("//input[@value='2021-04-13 13:30']")
        self.input("informacoes_adicionais","informacoes_adicionais")

        self.submit()
        sleep(1)

        #ciencia do segundo coautor
        link1 = self.driver.find_elements_by_partial_link_text("ciente/minicurso")[1].get_attribute("href")

        self.get(link1)
        try:
            self.driver.find_element_by_link_text("Registrar ciência").click()
            sleep(1)
        except:
            raise Exception("MinicursoBotaoCienciaNaoEncontrado")

        
        return True


    def verificar(self):
        self.logar()
        
        self.get(site_url("/trabalhos/submissoes"))
        #entra no link do minicurso salvo
        try:
            elem = self.driver.find_element_by_partial_link_text("titulo teste integracao")
        except:
            raise Exception("MinicursoNaoCadastradoVerificar")
        href = elem.get_attribute("href")
        self.get(href)

        #confere os valores salvos
        
        if self.value("//input[@id='autor']") != "Usuario teste integracao alterado":
            raise Exception("MinicursoAutorFail")
        if self.value("//input[@id='titulo']") != "titulo teste integracao":
            raise Exception("MinicursoTituloFail")
        if self.value("//textarea[@id='resumo']") != "resumo":
            raise Exception("MinicursoResumoFail")
        if self.value("//textarea[@id='objetivo']") != "objetivo":
            raise Exception("MinicursoObjetivoFail")
        if self.value("//input[@id='area']") != "Ciências Humanas E Sociais Aplicadas >> Administração":
            raise Exception("MinicursoAreaFail")
        if self.value("//input[@id='ch']") != "3h":
            raise Exception("MinicursoChFail")
        if self.value("//input[@id='vagas']") != "10":
            raise Exception("MinicursoVagasFail")
        if self.value("//textarea[@id='informacoes_adicionais']") != "informacoes_adicionais":
            raise Exception("MinicursoInformacoesAdcFail")
        if self.value("//div[@id='horario_preferencial']") != "<div><b>12/04/2021</b><br>13:30 - 15:00<br></div><div><b>13/04/2021</b><br>13:30 - 15:00<br></div>":
            raise Exception("MinicursoHorarioFail")


        try:
            self.driver.find_element_by_xpath("//li[@id='coautor1']").find_element_by_xpath("//i[@class='icon checkmark green']")
        except:
            raise Exception("MinicursoSemCienciaCoautorVerificar")

        
        return True




class CoautorMinicurso(Page):

    def __init__(self, driver):
        super().__init__(driver)
        self.email = "coautor01@gmail.com"



    def cad(self):
        self.logout()
        
        #faz o cadastro
        self.get(site_url("/home/registrar"))
        self.input("nome_completo","Coatuor 01 integracao")
        self.input("email",self.email)
        self.input("emailConfirm",self.email)
        self.input("cpf","11111111111")
        self.input("tipoInscricao","2")
        self.input("password1","123456")
        self.input("password2","123456")
        self.submit()

        

        #entra no link de confirmacao
        try:
            elem = self.driver.find_element_by_partial_link_text("home/confirmacao")
        except:
            raise Exception("MinicursoCoautorLinkConfirmacaoNaoEncontrado")
        href = elem.get_attribute("href")
        self.get(href)

        #faz o login
        self.logar()
        self.logout()
        
        return True

    def registrar_ciencia(self):
        self.logout()
        self.logar()

        try:
            elem = self.driver.find_element_by_partial_link_text("titulo teste integracao")
        except:
            raise Exception("MinicursoNaoCadastradoRegistrarCiencia")
        
        try:
            regBtn = elem.parent.find_element_by_partial_link_text("Registrar ciência")
        except:
            raise Exception("MinicursoRegistrarCienciaBotaoNaoEncontrado")
        regBtn.click()

        try:
            elem = elem.parent.find_element_by_xpath("//*[text()[contains(.,'Você já registrou ciência')]]")
        except:
            raise Exception("MinicursoRegistrarCienciaFail")

        self.logout()
        return True





        



# #assert "Python" in driver.title
# elem = driver.find_element_by_name("email")
# elem.clear()
# elem.send_keys("revisor@ifrn.edu.br")
# elem = driver.find_element_by_name("password")
# elem.clear()
# elem.send_keys("123456")
# elem.send_keys(Keys.RETURN)

# driver.implicitly_wait(3)
# driver.find_element_by_tag_name("form").submit()

# elem = driver.find_element_by_class_name("item.ui.dropdown.menu_superior")
# #elem1 = driver.find_element_by_partial_link_text("revisor@ifrn.edu.br")
# #print(dir(driver))
# #driver.
# #print(dir(elem))
# #print(elem.text)
# elem = driver.find_elements_by_xpath("//*[contains(text(), 'revisor@ifrn.edu.br2')]")

# print(elem[0].tag_name)
# print(elem[0].get_attribute("class"))
# print(dir(elem[0]))
# #assert "No results found." not in driver.page_source
# #print(driver.page_source)

# driver.close()