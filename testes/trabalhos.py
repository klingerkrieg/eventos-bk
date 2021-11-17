from selenium import webdriver
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.firefox.options import Options
from Page import *
import os

class Trabalhos(Page):
        
    def deletar(self):
        self.logar()
        self.get(site_url("/trabalhos/submissoes"))
        try:
            elem = self.driver.find_element_by_partial_link_text("trabalho teste integracao")
        except:
            raise Exception("TrabalhoNaoCadastradoDeletar")
        elem.parent.find_element_by_tag_name("button").click()
        self.driver.find_element_by_xpath("//div[@onclick='confirmDelete();']").click()
        return True


    def cadastrar(self):
        self.logar()
        try:
            elem = self.driver.find_element_by_partial_link_text("Submeter trabalho")
        except:
            raise Exception("TrabalhoBotaoNaoEncontrado")
        
        elem.click()

        sleep(1)
        #submissao
        self.driver.find_element_by_xpath("//button[@id='addCoautores']").click()
        self.driver.find_element_by_xpath("//button[@id='addOrientadores']").click()
        sleep(1)
        #import pdb;pdb.set_trace()
        self.input(self.driver.find_elements_by_xpath("//input[@name='nome_coautor[]']")[0] ,"Coatuor 01 integracao trab")
        self.input(self.driver.find_elements_by_xpath("//input[@name='email_coautor[]']")[0] ,"coautortrab01@gmail.com")
        self.input(self.driver.find_elements_by_xpath("//input[@name='nome_coautor[]']")[1] ,"Coatuor 02 integracao trab")
        self.input(self.driver.find_elements_by_xpath("//input[@name='email_coautor[]']")[1] ,"coautortrab02@gmail.com")

        self.input(self.driver.find_elements_by_xpath("//input[@name='nome_orientador[]']")[0] ,"Orient 01 integracao trab")
        self.input(self.driver.find_elements_by_xpath("//input[@name='email_orientador[]']")[0] ,"orient01@gmail.com")
        self.input(self.driver.find_elements_by_xpath("//input[@name='nome_orientador[]']")[1] ,"Orient 02 integracao trab")
        self.input(self.driver.find_elements_by_xpath("//input[@name='email_orientador[]']")[1] ,"orient02@gmail.com")
        self.input("titulo","trabalho teste integracao")
        self.input("idarea","2")
        self.input("idtrilha","2")
        
        self.input("arquivo",os.getcwd()+"\\testes\\teste.pdf")

        self.submit()
        sleep(1)

        #ciencia do segundo coautor e segundo orientador
        link1 = self.driver.find_elements_by_partial_link_text("ciente/trabalho")[1].get_attribute("href")
        link2 = self.driver.find_elements_by_partial_link_text("ciente/trabalho")[3].get_attribute("href")

        self.get(link1)
        try:
            self.driver.find_element_by_link_text("Registrar ciência").click()
            sleep(1)
        except:
            raise Exception("TrabalhoBotaoCienciaNaoEncontrado")
        
        self.get(link2)
        try:
            self.driver.find_element_by_link_text("Registrar ciência").click()
            sleep(1)
        except:
            raise Exception("TrabalhoBotaoCienciaNaoEncontrado")

        return True


    def verificar(self):
        self.logar()
        
        self.get(site_url("/trabalhos/submissoes"))
        #entra no link do minicurso salvo
        try:
            elem = self.driver.find_element_by_partial_link_text("trabalho teste integracao")
        except:
            raise Exception("TrabalhoNaoCadastradoVerificar")
        href = elem.get_attribute("href")
        self.get(href)
        sleep(1)

        #confere os valores salvos
        
        if self.value("//input[@id='autor']") != "Usuario teste integracao alterado":
            raise Exception("TrabalhoAutorFail")
        if self.value("//input[@id='titulo']") != "trabalho teste integracao":
            raise Exception("TrabalhoTituloFail")
        if self.value("//input[@id='area']") != "Administração":
            raise Exception("TrabalhoAreaFail")
        if self.value("//input[@id='trilha']") != "Projeto integrador":
            raise Exception("TrabalhoTrilhaFail")
        if self.value("//div[@id='status']") != "Em avaliação":
            raise Exception("TrabalhoStatusFail")
        if self.value("//input[@id='url']") != "":
            raise Exception("TrabalhoUrlFail")
        if self.value("//a[@id='arquivo']").find("teste") == -1:
            raise Exception("TrabalhoArquivoFail")

        try:
            self.driver.find_element_by_xpath("//li[@id='coautor1']").find_element_by_xpath("//i[@class='icon checkmark green']")
        except:
            raise Exception("TrabalhoSemCienciaCoautorVerificar")
        try:
            self.driver.find_element_by_xpath("//li[@id='orientador1']").find_element_by_xpath("//i[@class='icon checkmark green']")
        except:
            raise Exception("TrabalhoSemCienciaOrientadorVerificar")

        
        
        return True




class CoautorTrabalho(Page):

    def __init__(self, driver):
        super().__init__(driver)
        self.email = "coautortrab01@gmail.com"



    def cad(self):
        self.logout()
        
        #faz o cadastro
        self.get(site_url("/home/registrar"))
        self.input("nome_completo","Coatuor 01 integracao trab")
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
            raise Exception("TrabalhoCoautorLinkConfirmacaoNaoEncontrado")
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
            elem = self.driver.find_element_by_partial_link_text("trabalho teste integracao")
        except:
            raise Exception("TrabalhoCoautorNaoCadastradoRegistrarCiencia")
        
        try:
            regBtn = elem.parent.find_element_by_partial_link_text("Registrar ciência")
        except:
            raise Exception("TrabalhoCoautorRegistrarCienciaBotaoNaoEncontrado")
        regBtn.click()

        try:
            elem = elem.parent.find_element_by_xpath("//*[text()[contains(.,'Você já registrou ciência')]]")
        except:
            raise Exception("TrabalhoCoautorRegistrarCienciaFail")

        self.logout()
        return True




class OrientadorTrabalho(Page):

    def __init__(self, driver):
        super().__init__(driver)
        self.email = "orient01@gmail.com"



    def cad(self):
        self.logout()
        
        #faz o cadastro
        self.get(site_url("/home/registrar"))
        self.input("nome_completo","Orientador 01 integracao")
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
            raise Exception("TrabalhoOrientadorLinkConfirmacaoNaoEncontrado")
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
            elem = self.driver.find_element_by_partial_link_text("trabalho teste integracao")
        except:
            raise Exception("TrabalhoOrientadorNaoCadastradoRegistrarCiencia")
        
        try:
            regBtn = elem.parent.find_element_by_partial_link_text("Registrar ciência")
        except:
            raise Exception("TrabalhoOrientadorRegistrarCienciaBotaoNaoEncontrado")
        regBtn.click()

        try:
            elem = elem.parent.find_element_by_xpath("//*[text()[contains(.,'Você já registrou ciência')]]")
        except:
            raise Exception("TrabalhoOrientadorRegistrarCienciaFail")

        self.logout()
        return True

        


