<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Trabalhos extends CI_Controller {

    public function __construct(){
        parent::__construct();
        
        if (!isset($_SESSION["user"])){
            redirect("Home/login");
        }
    }

    public function index(){
        redirect("trabalhos/submissoes");
    }

    public function form($id = null){

        $dados = [];
        $this->load->model("Trabalho_model");
        if ($id != null){
            $dados = $this->Trabalho_model->get($id);
        }

        if (isset($_POST['titulo'])){
            $dados = $_POST;
            $dados["orientadores"] = [];
            $dados["coautores"] = [];
            foreach($_POST["nome_coautor"] as $k=>$nome){
                array_push($dados["coautores"], ["nome_completo"=>$nome, "email"=>$dados["email_coautor"][$k]]);
            }

            foreach($_POST["nome_orientador"] as $k=>$nome){
                array_push($dados["orientadores"], ["nome_completo"=>$nome, "email"=>$dados["email_orientador"][$k]]);
            }
        }

        $this->load->model("Evento_model");
        $evento = $this->Evento_model->getEvento();

        $this->load->model("GTS_model");
        $gts = $this->GTS_model->all();

        $tiposTrabalhos = $this->Trabalho_model->tiposTrabalhos;
        $trilhas        = $this->Trabalho_model->trilhas;

        $this->load->model("Area_model");
        $areas = $this->Area_model->all();

        $status = $this->Trabalho_model->status;

        #se o trabalho nao for meu e eu não constar como coautor
        if ($id != null && $dados["idusuario"] != $_SESSION["user"]["id"]){
            $sair = true;
            foreach(array_merge($dados["orientadores"],$dados["coautores"]) as $user){
                if ($user["idusuario"] == $_SESSION['user']['id']){
                    $sair = false;
                    break;
                }
            }

            if ($sair){
                redirect("home/logout");
            }
        }

        $csrf = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );



        if (_v($dados,"status") != ""){
            include view ('./area/submeter_correcao.php');
        } else {
            if (evento_encerrado()){
                redirect("trabalhos");
            }
            include view ('./area/submeter_trabalho.php');
        }
    }

    public function submissoes(){
        $this->load->model("Trabalho_model");
        $this->load->model("Minicurso_model");
        $trabalhos = $this->Trabalho_model->meusTrabalhos();
        $minicursos = $this->Minicurso_model->meusMinicursos();

        $trilhas        = $this->Trabalho_model->trilhas;
        $tiposTrabalhos = $this->Trabalho_model->tiposTrabalhos;

        $this->load->model("Trabalho_model");
        $status_trabalhos = $this->Trabalho_model->status;
        $status_minicursos = $this->Minicurso_model->status;

        $this->load->model("Evento_model");
        $evento = $this->Evento_model->getEvento();

        include view ('./area/submissoes.php');
    }



    /**
     * UPLOAD VARIABLES
     */
    private $upload_data;
	private $uploadPath = './uploads/trabalhos/';
    
    public function upload_check($v){

        if (isset($_FILES['arquivoCorrigido'])){
            $inputName = "arquivoCorrigido";
            $this->uploadPath = './uploads/trabalhos/correcoes/';
        } else {
            $inputName = "arquivo";
        }

		$config['upload_path']          = $this->uploadPath;
		$config['allowed_types']        = 'pdf';
        $config['max_size']             = 10000;
        $config['file_name']             = cleanString($_FILES[$inputName]["name"]);

        $this->load->library('upload', $config);

		if ( ! $this->upload->do_upload($inputName)){
            $this->form_validation->set_message('upload_check', "Você esqueceu de enviar o arquivo ou ele é grande demais ou não é um .pdf.");
			return false;
		} else {
            $this->upload_data = $this->upload->data();
			return true;
		}
	}

    public function submeter(){

        if (evento_encerrado()){
            redirect("trabalhos");
        }

        #caso tenha dado erro na submissao, e a url seja requisitada via get
        #envia de volta para o formulário
        if (!isset($_POST['titulo'])){
            redirect("trabalhos/form");
        }

        $this->form_validation->set_rules('titulo', 'Título', 'required');
        $this->form_validation->set_rules('idgt', 'Grupo de trabalho', 'required');
        #$this->form_validation->set_rules('idtipo_trabalho', 'Tipo de trabalho', 'required');
        $this->form_validation->set_rules('idtrilha', 'Trilha', 'required');
        $this->form_validation->set_rules('idarea', 'Área', 'required');
        

        if (_v($_POST,"id") == "" || _v($_FILES['arquivo'],"name") != ""){
            $this->form_validation->set_rules('arquivo', 'Arquivo', 'callback_upload_check');
        }

        $this->form_validation->set_rules('email_coautor[]', 'E-mail do coautor', 'valid_email|required');
        $this->form_validation->set_rules('email_orientador[]', 'E-mail do orientador', 'valid_email|required');
        $this->form_validation->set_rules('nome_coautor[]', 'Nome do coautor', 'required');
        $this->form_validation->set_rules('nome_orientador[]', 'Nome do orientador', 'required');

        #para cada e-mail vazio ou nome vazio remove do array para que não vá para a validação
        foreach($_POST["email_coautor"] as $k=>$val){
            if ($_POST["email_coautor"][$k] == "" && $_POST["nome_coautor"][$k] == ""){
                unset($_POST["email_coautor"][$k]);
                unset($_POST["nome_coautor"][$k]);
            }
        }

        foreach($_POST["email_orientador"] as $k=>$val){
            if ($_POST["email_orientador"][$k] == "" && $_POST["nome_orientador"][$k] == ""){
                unset($_POST["email_orientador"][$k]);
                unset($_POST["nome_orientador"][$k]);
            }
        }

        if ($this->form_validation->run()) {
            #valido
            $this->load->model("Trabalho_model");
            $_POST["arquivo"] = $this->upload_data['file_name'];
            
            $trab = $this->Trabalho_model->salvar_submissao($this->input->post());
            if ($trab == null){
                $this->session->set_flashdata("error","Não foi possível fazer a submissão.");
            } else {
                $this->session->set_flashdata("success","Submissão realizada.");


                #envia e-mail para orientadores
                $this->alertar_orientadores($trab);


            } 

            if ($this->config->item('envio_emails_ativo')) {
                redirect("trabalhos/submissoes");
            } else {
                print "<a href='".site_url("trabalhos/submissoes"). "'>Continua</a>";
            }
        } else {
            #invalido
            $this->form();
        }
    }

    public function alertar_orientadores($trab_id){
        $trab = $this->Trabalho_model->get($trab_id);
        
        $this->load->model("Coautor_model");

        foreach(array_merge($trab["orientadores"],$trab["coautores"]) as $ori){

            $hash = "t".$trab["id"]."n".rand(1000,9999);
            $this->Coautor_model->salvar_hash($ori["id"],$hash);
            $url = site_url("ciente/trabalho/$hash");
            $participacao = ($ori["tipo"] == 0) ? "Coautor" : "Orientador";

            $msg = $this->load->view("emails/aviso_trabalho_submetido",
                                        ["url"=>$url,
                                        "nome"=>$ori["nome_completo"],
                                        "participacao"=>$participacao],true);

            enviar_email($ori["email"],$msg);
            
            
        }

    }



    public function submeter_correcao(){

        if (evento_encerrado()){
            redirect("trabalhos");
        }

        $this->form_validation->set_rules('id', '', 'required');

        if ($_FILES["arquivoCorrigido"]["name"] != ""){
            $this->form_validation->set_rules('arquivoCorrigido', 'Arquivo', 'callback_upload_check');
        }
        
        if ($this->form_validation->run()) {
            #valido
            $this->load->model("Trabalho_model");
            $_POST["arquivoCorrigido"] = $this->upload_data['file_name'];
            
            $trab = $this->Trabalho_model->salvar_trabalho_corrigido($this->input->post());
            if ($trab == null){
                $this->session->set_flashdata("error","Não foi possível fazer a atualização no trabalho.");
            } else {
                $this->session->set_flashdata("success","Trabalho atualizado.");
            } 

            redirect("trabalhos/form/{$_POST["id"]}");
        } else {
            #invalido
            $this->form($_POST["id"]);
        }
    }

    public function cancelar($id){

        if (evento_encerrado()){
            redirect("trabalhos");
        }

        $this->load->model("Trabalho_model");

        $trab = $this->Trabalho_model->get($id);
        if ($trab != null){
            if ($trab["status"] == PENDENTE){
                $this->Trabalho_model->deletar($id);
                $this->session->set_flashdata("warning","Submissão cancelada.");
            } else {
                $this->session->set_flashdata("error","Não foi possível cancelar o trabalho.");
            }
            redirect("trabalhos/submissoes");
        } else {
            $this->session->set_flashdata("error","Não foi possível cancelar o trabalho.");
        }
    }


}