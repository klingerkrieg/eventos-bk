from selenium import webdriver
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.firefox.options import Options

from Page import *
from cadastro import *
from minicursos import *
from trabalhos import *
import os, sys, traceback


class Testes():

    def run(self):
        options = Options()
        options.binary_location = r"C:/Program Files/Mozilla Firefox/firefox.exe"
        options.set_preference("dom.max_script_run_time", 1)
        self.driver = webdriver.Firefox(options=options, executable_path="C:/chromedriver/geckodriver.exe")
        self.driver.implicitly_wait(1)
        #limpa o banco
        self.driver.get(site_url("testes/clear"))
        print("LIMPA O BANCO")

        if self.driver.page_source != "<html><head></head><body></body></html>":
            sleep(30)
            exit()
        
        try:
            print ("**Cad login**")
            self.test_cad_login()
            print ("**Minicursos**")
            self.test_minicursos()
            print ("**Trabalhos**")
            self.test_trabalhos()
        except Exception as e:
            traceback.print_exc(file=sys.stdout)
        
        self.report()

    def report(self):
        print ("Testes:",self.testes)
        #print ("Errors:",self.errors)

    errors = 0
    testes = 0
    def assertTrue(self, value):
        self.testes += 1
        if value == False:
            self.errors += 1
            raise TesteFalho

    def __del__(self):
        self.driver.close()

    def test_cad_login(self):
        pag = CadastroLogin(self.driver)
        print ("\t- cadastrar")
        self.assertTrue(pag.cadastrar())
        self.assertTrue(pag.verificar())
        print ("\t- alterar")
        self.assertTrue(pag.alterar_perfil())
        self.assertTrue(pag.verificar())
        print ("\t- recuperar senha")
        pag = RecSenha(self.driver)
        self.assertTrue(pag.run())

    def test_minicursos(self):
        pag = Minicursos(self.driver)
        print ("\t- submeter")
        self.assertTrue(pag.cadastrar())
        self.assertTrue(pag.verificar())
        pag2 = CoautorMinicurso(self.driver)
        print ("\t- inscricao do coautor")
        self.assertTrue(pag2.cad())
        self.assertTrue(pag2.registrar_ciencia())
        print ("\t- deletar")
        self.assertTrue(pag.deletar())

    def test_trabalhos(self):
        pag = Trabalhos(self.driver)
        print ("\t- cadastrar")
        self.assertTrue(pag.cadastrar())
        self.assertTrue(pag.verificar())
        pag2 = CoautorTrabalho(self.driver)
        print ("\t- inscricao do coautor")
        self.assertTrue(pag2.cad())
        self.assertTrue(pag2.registrar_ciencia())
        pag3 = OrientadorTrabalho(self.driver)
        print ("\t- inscricao do orientador")
        self.assertTrue(pag3.cad())
        self.assertTrue(pag3.registrar_ciencia())
        print ("\t- deletar")
        self.assertTrue(pag.deletar())

        
        


if __name__ == "__main__":
    testes = Testes()
    testes.run()

    
    

