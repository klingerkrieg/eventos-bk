from selenium.webdriver.support.ui import Select
from selenium.common.exceptions import ElementNotInteractableException

from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from time import sleep


#class TestError(Exception):



def site_url(url=""):
    return "http://127.0.0.1/expotecNC/"+url


class Page:

    driver = None
    email = "integracao@gmail.com"
    delay = 0

    def __init__(self,driver):
        self.driver = driver

    def get(self,url):
        self.driver.get(url)
        sleep(self.delay)

    def value(self, name):
        
        if name.startswith("//"):
            elem = self.driver.find_element_by_xpath(name)
        else:
            elem = self.driver.find_element_by_name(name)

        if elem.tag_name == 'img':
            return elem.get_attribute("src")
        if elem.tag_name in ['input','textarea','select']:
            return elem.get_attribute("value")
        else:
            return elem.get_attribute("innerHTML").strip()
    
    def input(self, name,value=""):

        if type(name) != str:
            elem = name
        elif name.startswith("//"):
            elem = self.driver.find_element_by_xpath(name)
        else:
            elem = self.driver.find_element_by_name(name)
        
        
        if elem.tag_name == 'input' and elem.get_attribute("type") in ['checkbox', 'radio']:
            elem.click()
        elif elem.tag_name == 'input' or elem.tag_name == 'textarea' or elem.get_attribute("type") == "file":
            elem.clear()
            elem.send_keys(value)
        elif elem.tag_name == 'select':
            select = Select(elem)
            try:
                select.select_by_value(value)
            except ElementNotInteractableException:

                #get index of value
                #import pdb; pdb.set_trace()
                sel = self.driver.find_element_by_xpath("//select[@name='"+name+"']")
                options = sel.find_elements_by_tag_name("option")
                index = 0
                for i in range(len(options)):
                    if options[i].get_attribute("value") == value:
                        index = i
                        break

                #abre o input
                elems = self.driver.find_element_by_xpath("//select[@name='"+name+"']/following-sibling::input").click()
                #seleciona o elemento
                wait = WebDriverWait(self.driver, 5)
                #seleciona o elemento pela posicao e nao pelo valor
                element = wait.until(EC.element_to_be_clickable((By.CSS_SELECTOR, ".visible > .item:nth-child("+str(index)+")")))
                element.click()
                #no chrome
                #$x('//div[@class="item"][@data-value=3]')

            #elem = elem.find_elements_by_xpath('//option[@value='+value+']')
            #if len(elem) > 0:
            #    elem[0].click()
        sleep(self.delay)

    def submit(self):
        elem = self.driver.find_element_by_tag_name("form")
        elem.submit()
        sleep(self.delay)


    def logout(self):
        self.get(site_url("/home/logout"))
        self.logado = False
        sleep(1)
        
    def isLogado(self):
        try:
            elem = self.driver.find_element_by_xpath("//*[contains(text(), '"+self.email+"')]")
            return True
        except:
            return False

    def logar(self,senha='123456'):
        self.get(site_url("/home/login"))
        sleep(1)
        self.driver.implicitly_wait(0)
        try:
            elem = self.driver.find_element_by_xpath("//input[@name='email']")
            self.input("email",self.email)
            self.input("password",senha)
            self.submit()
            self.driver.implicitly_wait(1)
            if self.isLogado():
                return True
            else:
                raise Exception("NaoConsegueLogar")
        except:
            self.driver.implicitly_wait(1)
            if self.isLogado():
                return True
            else:
                raise Exception("NaoConsegueAcessarLogin")


            

            
                
            