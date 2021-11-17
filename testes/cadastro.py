from selenium import webdriver
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.firefox.options import Options
from Page import *
import os

class CadastroLogin(Page):

    dados = {"nome_completo":"Usuario teste integracao",
                "email":"",
                "cpf":"111.111.111-11",
                "telefone":"(22) 22222-2222",
                "tipoInscricao":"2",
                "lattes":"www.teste.com.br",
                "curriculo":"",
                "instituicao":"3",
                "curso":"3",
                "idnivelcurso":"2",
                "foto":"no-photo.jpg",
                "password":"12345678"}

    def __init__(self,driver):
        super().__init__(driver)
        self.dados["email"] = self.email
    
    def cadastrar(self):

        cpf = self.dados["cpf"].replace(".","").replace("-","")
        telefone = self.dados["telefone"].replace(" ","").replace("-","").replace("(","").replace(")","")

        #faz o cadastro
        self.get(site_url("/home/registrar"))
        self.input("nome_completo",self.dados["nome_completo"])
        self.input("email",self.dados["email"])
        self.input("emailConfirm",self.dados["email"])
        self.input("telefone",telefone)
        self.input("cpf",cpf)
        self.input("tipoInscricao",self.dados["tipoInscricao"])
        self.input("instituicao",self.dados["instituicao"])
        self.input("curso",self.dados["curso"])
        self.input("idnivelcurso",self.dados["idnivelcurso"])
        self.input("password1",self.dados["password"])
        self.input("password2",self.dados["password"])
        self.submit()

        #entra no link de confirmacao
        try:
            elem = self.driver.find_element_by_partial_link_text("home/confirmacao")
        except:
            raise Exception("UsuarioLinkConfirmacaoNaoEncontrado")
        href = elem.get_attribute("href")
        self.get(href)

        #faz o login
        self.logar(self.dados["password"])
        
        self.logout()
        
        return True

    def verificar(self):
        self.logar(self.dados["password"])
        self.get(site_url("perfil/index"))
        
        if self.value("nome_completo") != self.dados["nome_completo"]:
            raise Exception("NomeCompletoFail")
        if self.value("email") != self.dados["email"]:
            raise Exception("EmailFail")
        if self.value("cpf") != self.dados["cpf"]:
            raise Exception("CPFFail")
        if self.value("//div[@id='tipoInscricao']") != "Servidor":
            raise Exception("InscricaoFail")
        if self.value("instituicao") != self.dados["instituicao"]:
            raise Exception("InstituicaoFail")
        if self.value("curso") != self.dados["curso"]:
            raise Exception("CursoFail")
        if self.value("curriculo") != self.dados["curriculo"]:
            raise Exception("CurriculoFail")
        if self.value("idnivelcurso") != self.dados["idnivelcurso"]:
            raise Exception("NivelCursoFail")
        if self.value("//img[@id='fotoImg']").find(self.dados["foto"]) == -1:
            raise Exception("FotoFail")

        return True

    def alterar_perfil(self):
        self.logar(self.dados["password"])
        self.get(site_url("perfil/index"))


        self.driver.find_element_by_xpath("//input[@id='alterarSenha']").click()
        sleep(1)
        self.driver.find_element_by_xpath("//input[@id='radioCurriculo']").click()
        sleep(1)

        self.dados["nome_completo"] += " alterado"
        self.dados["password"]      = "123456789"
        self.dados["telefone"]      = "(33) 33333-3333"
        self.dados["cpf"]           = "444.444.444-44"
        self.dados["instituicao"]   = "4"
        self.dados["curso"]         = "4"
        self.dados["idnivelcurso"]  = "3"
        self.dados["foto"]          = "user"
        self.dados["curriculo"]     = "curriculo"

        cpf = self.dados["cpf"].replace(".","").replace("-","")
        telefone = self.dados["telefone"].replace(" ","").replace("-","").replace("(","").replace(")","")
        
        self.input("nome_completo",self.dados["nome_completo"])
        self.input("telefone",telefone)
        self.input("cpf",cpf)
        self.input("curriculo",self.dados["curriculo"])
        self.input("instituicao",self.dados["instituicao"])
        self.input("curso",self.dados["curso"])
        self.input("idnivelcurso",self.dados["idnivelcurso"])
        self.input("password1",self.dados["password"])
        self.input("password2",self.dados["password"])
        self.input("foto",os.getcwd()+"\\testes\\user.png")
        self.submit()

        self.logout()

        return True



        



class RecSenha(Page):
    
    def run(self):

        self.logout()
        
        #faz o cadastro
        self.get(site_url("/home/recuperar_senha"))
        self.input("email",self.email)
        self.input("cpf","44444444444")
        self.driver.implicitly_wait(10)
        self.submit()
        
        #entra no link de confirmacao
        try:
            elem = self.driver.find_element_by_partial_link_text("home/alterar_senha")
        except:
            raise Exception("RecSenhaLinkConfirmacaoNaoEncontrado")
        href = elem.get_attribute("href")
        self.get(href)
        
        #altera a senha
        self.input("password1","123456")
        self.input("password2","123456")
        self.submit()
        
        #faz o login
        self.logar()
        try:
            elem = self.driver.find_element_by_xpath("//*[contains(text(), '"+self.email+"')]")
        except:
            return False
        
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