<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Ivmelo\SUAP\SUAP;

class Admin extends CI_Controller {

    public function __construct(){
        parent::__construct();

        #cria as tabelas se nao existirem
        #essas linhas podem ser comentadas após a configuração inicial do sistema
        $this->load->model("Database_model");
        $this->Database_model->creat_all_if_not_exists();


        $this->load->model("Evento_model");
        $this->Evento_model->verificaDatasEncerramentos();        
    }

    public function index(){
		
        #Se ja tiver feito o login
        if (isset($_SESSION["admin_user"]) 
            && $_SESSION["admin_user"]["email"] != null 
            && $_SESSION["admin_user"]["nivel"] >= NIVEL_AVALIADOR){
            redirect("admin/painel");
        }

        $csrf = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );
        

        #limpa os captchas
        #$this->load->library("CaptchaLib");
        #$this->captchalib->clear_dir();
        include view("admin/login");
        
    }

    public function login(){


        $this->form_validation->set_rules('email', 'E-mail', 'required');
        $this->form_validation->set_rules('senha', 'Senha', 'required');

        

        if ($this->form_validation->run()) {

            if (is_numeric($_POST["email"])){
                $suap = new SUAP('token');
                try {
                    $data = $suap->autenticar($_POST["email"], $_POST["senha"]);
                } catch (Exception  $th) {
                    $this->session->set_flashdata('error', 'Matrícula do SUAP ou senha inválida.');
                    redirect("admin/index");
                    die();
                }
    
                $dados = $suap->getMeusDados();
                
                $this->load->model("Usuario_model");
                $dados = $this->Usuario_model->filterSUAPService($dados);
                $user = $this->Usuario_model->getOrCreateCompleto($dados);
                
            } else {
                #login normal
                $this->load->model("Login_model");
                $user = $this->Login_model->logar($this->input->post("email"),$this->input->post("senha"));
                #confirmação do e-mail é só para o uso da senha
                if ($user["email_confirmado"] == 0){
                    $this->session->set_flashdata('error', 'O seu e-mail precisa ser verificado antes que você possa 
                                                            entrar no sistema, para isso nós te enviamos um e-mail, 
                                                            verifique sua caixa de e-mail. 
                                                            Caso você não tenha recebido o e-mail, tente a 
                                                            <a href="'.site_url('home/recuperar_senha').'">recuperação de senha</a>.');
                    redirect("Admin/login");
                }
            }

            
            
            
            if ($user != null && $user["nivel"] >= NIVEL_AVALIADOR){
                $_SESSION["admin_user"]['nivel']                    = $user["nivel"];
                $_SESSION["admin_user"]['email']                    = $user["email"];
                $_SESSION["admin_user"]['id']                       = $user["id"];
                $_SESSION["admin_user"]['certificado_participante'] = $user["certificado_participante"];
                $_SESSION["admin_user"]['certificado_avaliador']    = $user["certificado_avaliador"];
                $_SESSION["admin_user"]['certificado_palestrante']  = $user["certificado_palestrante"];
                $_SESSION["admin_user"]['certificado_mesa_redonda'] = $user["certificado_mesa_redonda"];
                redirect("admin/painel");
            } else {
                $this->session->set_flashdata('error', 'E-mail e senha inválida ou talvez você ainda não possua privilégios para acessar esta área..');
                redirect("admin/index");
            }
        } else {
            #invalido
            $this->index();
        }

    }

    public function logout(){
        session_destroy();
        redirect("Admin");
    }

    public function painel(){

        #so permite admin, equipe e revisor
        if (isset($_SESSION["admin_user"]) 
            && $_SESSION["admin_user"]["nivel"] <= NIVEL_PARTICIPANTE || !isset($_SESSION["admin_user"])){
            redirect("admin/logout");
        }

        
        $this->load->helper("date");
        $this->load->model("Log_model");
        $topLogs = $this->Log_model->top();

        $this->load->model("Evento_model");
        $estatisticas = $this->Evento_model->getEstatisticas();

        $this->load->model("Area_model");
        $areas = $this->Area_model->all();

        $this->load->model("AvaliadorArea_model");
        $minhasAreas = $this->AvaliadorArea_model->minhasAreas();

        $this->load->model("Trabalho_model");
        $trabalhosCert = $this->Trabalho_model->getCertificados($_SESSION['admin_user']['id']);

        $this->load->model("Minicurso_model");
        $minicursosCert = $this->Minicurso_model->getCertificados($_SESSION['admin_user']['id']);

        $csrf = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );

        include view("admin/painel");
    }


    public function recuperarSenha(){

    }



}
