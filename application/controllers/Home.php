<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Ivmelo\SUAP\SUAP;

class Home extends CI_Controller {



    public function index(){
        $this->load->model("Minicurso_model");
        $datasHorarios = $this->Minicurso_model->datasHorarios;
        $lista = $this->Minicurso_model->all();

        include view('home');
    }

    /**
     * Login
     */

    public function login(){

        $this->load->model("Evento_model");
        $this->Evento_model->verificaDatasEncerramentos();  

        if (isset($_SESSION["user"]) && $_SESSION["user"]["email"] != null){
            redirect("trabalhos/submissoes");
        } else {

            $csrf = array(
                'name' => $this->security->get_csrf_token_name(),
                'hash' => $this->security->get_csrf_hash()
            );

            include view('area/login');
        }
    }

    public function logout(){
        session_destroy();
        redirect("Home");
    }

    public function logar(){
        $this->form_validation->set_rules('email', 'E-mail', 'required');
        $this->form_validation->set_rules('password', 'Senha', 'required');

        if ($this->form_validation->run()) {

            if (is_numeric($_POST["email"])){
                $suap = new SUAP('token');
                try {
                    $data = $suap->autenticar($_POST["email"], $_POST["password"]);
                } catch (Exception  $th) {
                    $this->session->set_flashdata('error', 'Matrícula do SUAP ou senha inválida.');
                    redirect("Home/login");
                    die();
                }

                $dados = $suap->getMeusDados();
                
                $this->load->model("Usuario_model");
                $dados = $this->Usuario_model->filterSUAPService($dados);
                $user = $this->Usuario_model->getOrCreateCompleto($dados);

            } else {
                #login normal
                $this->load->model("Login_model");
                $user = $this->Login_model->logar($this->input->post("email"),$this->input->post("password"));
                #confirmação do e-mail é só para o uso da senha
                if ($user["email_confirmado"] == 0){
                    $this->session->set_flashdata('error', 'O seu e-mail precisa ser verificado antes que você possa 
                                                            entrar no sistema, para isso nós te enviamos um e-mail, 
                                                            verifique sua caixa de e-mail. 
                                                            Caso você não tenha recebido o e-mail, tente a 
                                                            <a href="'.site_url('home/recuperar_senha').'">recuperação de senha</a>.');
                    redirect("Home/login");
                }
            }


            if ($user != null){


                $_SESSION["user"] = [];
                $_SESSION["user"]['nome_completo']              = $user["nome_completo"];
                $_SESSION["user"]['foto']                       = $user["foto"];
                $_SESSION["user"]['email']                      = $user["email"];
                $_SESSION["user"]['nivel']                      = $user["nivel"];
                $_SESSION["user"]['id']                         = $user["id"];
                $_SESSION["user"]['pago']                       = $user["pago"];
                $_SESSION["user"]['certificado_participante']   = $user["certificado_participante"];
                redirect("trabalhos/submissoes");
                
            } else {
                $this->session->set_flashdata('error', 'E-mail ou senha inválida.');
                redirect("Home/login");
            }
        } else {
            #invalido
            $this->login();
        }

    }


    /**
     * Registro
     */

    public function registrar(){

        if (evento_encerrado()){
            redirect("Home/login");
        }

        if (isset($_SESSION["user"]) && $_SESSION["user"]["email"] != null){
            redirect("trabalhos/submissoes");
        }

        $this->load->library("CaptchaLib");
        $this->captchalib->create_by_access();
        

        $dados = [];
        if ($this->input->post("nome_completo") != ""){
            $dados = $this->input->post();
        }

        $this->load->model("Instituicao_model");
        $instituicoes = $this->Instituicao_model->all();

        $this->load->model("Curso_model");
        $cursos = $this->Curso_model->all();
        $niveis_cursos = $this->Curso_model->niveis_cursos;

        $this->load->model("Usuario_model");
        $tiposInscricao = $this->Usuario_model->tiposInscricao;

        $csrf = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );

        include view('area/registrar');
    }

    public function cpf_check($cpf){
        return cpf_check($cpf);
    }

    public $previamenteCadastrado = false;
    public function email_duplicado_check($email){
        $this->load->model("Usuario_model");
        $row = $this->Usuario_model->getByEmail($email);
        if ($row == null){
            return true;
        } else {
            #é um coautor ou orientador que está fazendo o cadastro
            #if ($row['email_confirmado'] == false){
            if ($row['email_confirmado'] == false && $row["matricula"] == ""){
                $this->previamenteCadastrado = $row['id'];
                return true;
            } else {
                return false;
            }
        }
    }

    public function salvar_registro(){

        if (evento_encerrado()){
            redirect("Home/login");
        }

        $this->load->library("CaptchaLib");
        $this->captchalib->form_validation($this->form_validation);
        $this->form_validation->set_rules('nome_completo', 'Nome completo', 'required');
        $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email|callback_email_duplicado_check', array('email_duplicado_check' => "Este e-mail já está cadastrado, tente a <a href='".site_url('home/recuperar_senha')."'>recuperação de senha</a> ao invés do cadastro."));
        $this->form_validation->set_rules('emailConfirm', 'Confirmar e-mail', 'required|matches[email]', array('matches' => 'A confirmação do e-mail está diferente do campo e-mail.'));
        $this->form_validation->set_rules('tipoInscricao', 'Tipo de inscrição', 'required');
        $this->form_validation->set_rules('password1', 'Senha', 'required|min_length[6]');
        $this->form_validation->set_rules('password2', 'Confirmação da senha', 'required|matches[password1]', array('matches' => 'As senhas digitadas estão diferentes.'));
        $this->form_validation->set_rules('cpf', 'CPF', 'required|callback_cpf_check', array('cpf_check' => 'Digite um CPF válido.'));

        if ($this->input->post("instituicao") == "outra"){
            $this->form_validation->set_rules('outra_instituicao', 'Nos informe o nome da sua instituição.', 'required', array('required' => 'Nos informe o <b>nome</b> da sua instituição.'));
        }

        if ($this->form_validation->run()) {
            #valido
            $this->load->model("Usuario_model");
            if ($this->previamenteCadastrado){
                $_POST["id"] = $this->previamenteCadastrado;
            } else {
                $_POST["id"] = "";
            }
            $_POST["password"] = $this->input->post("password1");

            $id = $this->Usuario_model->salvar_meu_cadastro($this->input->post());
            
            $this->enviar_email_confirmacao($this->input->post("email"), $this->input->post("cpf"));
        } else {
            #invalido
            $this->registrar();
        }
    }

    public function confirmacao(){
        $hash = _v($_GET,'hash');
        $id = _v($_GET,'u');

        $this->load->model("Usuario_model");
        $confirmacao = $this->Usuario_model->getByHash($hash, $id);
        $this->Usuario_model->confirmaEmail($id);
        $this->session->set_flashdata("success","Parabéns, seu e-mail foi confirmado, agora você pode entrar no sistema.");
        redirect("home/login");
    }

    public function enviar_email_confirmacao($emailCadastro, $cpf){
		
        
        #pede o hash para o model
        $this->load->model("Usuario_model");
        $dados = $this->Usuario_model->novoHashDeSenha($emailCadastro, $cpf);
        
		if ( $dados == false){
            #falso retorno
			redirect("home/login");
        }
        
        $this->session->set_flashdata("success","Abra o seu e-mail e acesse o link enviado para confirmação do cadastro.");
		$link = site_url("home/confirmacao?hash={$dados['hash']}&u={$dados['id']}");
		
		
        $msg = $this->load->view("emails/confirmacao_cadastro",["link"=>$link],true);

        enviar_email($emailCadastro,$msg);
        if ($this->config->item('envio_emails_ativo')) {
            redirect("home/login");
        }
    }


    /**
     * Recuperação de senha
     */
    public function recuperar_senha(){
        $this->load->library("CaptchaLib");
        $this->captchalib->create_by_access();

        $csrf = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );

        include view("area/recuperar_senha");
    }

    public function alterar_senha($hash=null,$id=null){
        if ($hash == null){
            $hash = _v($_GET,'hash');
            $id = _v($_GET,'u');
        }

        $_SESSION['hash'] = $hash;
        $_SESSION['hash_id'] = $id;
        
        $this->load->model("Usuario_model");
        $confirmacao = $this->Usuario_model->getByHash($hash, $id);

		if ($confirmacao){
            $_SESSION["recuperando_id"] = $id;
		} else {
			$_SESSION["recuperando_id"] = null;
            $this->session->set_flashdata("error","Seu link de recuperação de senha perdeu a validade. Tente novamente.");
            $this->recuperar_senha();
		}
		
		$csrf = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );

        include view("area/alterar_senha");
    }

    public function salvar_alteracao_senha(){

		if (_v($_SESSION,"recuperando_id") == null){
			#falso retorno
			redirect("home/login");
			return;
		}

		#valida a nova senha
        $this->load->library('form_validation');
		$this->form_validation->set_rules('password1', 'Senha', 'required|min_length[6]');
		$this->form_validation->set_rules('password2', 'Confirmação da senha', 'required|matches[password1]');
        
		if ($this->form_validation->run() == FALSE){
            $this->alterar_senha($_SESSION['hash'],$_SESSION['hash_id']);
			return;
        } else {
        
            $this->session->set_flashdata("success","Senha alterada com sucesso.");
            $this->load->model('Usuario_model');
            $this->Usuario_model->atualizarSenha($_SESSION["recuperando_id"], $this->input->post("password1"));

            redirect("home/login");
        }		
	}

    public function enviar_email_rec(){
        #validacao do captcha
		$this->load->library("CaptchaLib");
		$this->load->library('form_validation');

        $this->captchalib->form_validation($this->form_validation);
        $this->form_validation->set_rules('email','E-mail','required');
        $this->form_validation->set_rules('cpf','CPF','required');

		if ($this->form_validation->run() == FALSE){
			$this->captchalib->create();
			$this->recuperar_senha();
			return;
		}
        
        #pede o hash para o model
        $this->load->model("Usuario_model");
        $dados = $this->Usuario_model->novoHashDeSenha($this->input->post("email"),$this->input->post("cpf"));
		

		

		if ( $dados == false){
            $this->session->set_flashdata("error","Não encontramos nenhum usuário com esse e-mail e CPF, tente fazer o <a href='". site_url("home/registrar")."'>seu cadastro</a>");
            #falso retorno
            #diz que enviou o e-mail, mas não enviou nada porque o CPF estava errado
            #se quiser tenta novamente
			redirect("home/login");
        }
        
        $link = site_url("home/alterar_senha?hash={$dados['hash']}&u={$dados['id']}");
        
        $assunto = "Recuperação de senha ". nome_evento();
        $msg = $this->load->view("emails/recuperacao_senha",["link"=>$link],true);
        
        
        enviar_email($this->input->post('email'),$msg);
        if ($this->config->item('envio_emails_ativo')) {
            redirect("home/login");
        }
    }

}