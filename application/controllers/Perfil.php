<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Perfil extends CI_Controller {

    public function __construct(){
        parent::__construct();
        
        if (!isset($_SESSION["user"])){
            redirect("Home/login");
        }
    }

    public function index(){
        $this->load->model("Usuario_model");
        $dados = $this->Usuario_model->get($_SESSION['user']["id"]);
        $tiposInscricao = $this->Usuario_model->tiposInscricao;

        $this->load->model("Instituicao_model");
        $instituicoes = $this->Instituicao_model->all();

        $this->load->model("Curso_model");
        $cursos = $this->Curso_model->all();
        $niveis_cursos = $this->Curso_model->niveis_cursos;

        $csrf = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );

        include view("area/perfil");
    }

    public function cpf_check($cpf){
        return cpf_check($cpf);
    }


    


    /**
     * UPLOAD VARIABLES
     */
    private $upload_data;
	private $uploadPath = './static/fotos/';
    
    public function upload_check($v){

		$config['upload_path']          = $this->uploadPath;
		$config['allowed_types']        = 'png|jpg|jpeg';
        $config['max_size']             = 5000;
        $config['file_name']             = cleanString($_FILES["foto"]["name"]);

        $this->load->library('upload', $config);

		if ( ! $this->upload->do_upload("foto")){
            $this->form_validation->set_message('upload_check', "Você esqueceu de enviar o arquivo ou ele é grande demais ou não é uma imagem.");
			return false;
		} else {
            $this->upload_data = $this->upload->data();
			return true;
		}
	}

    public function salvar(){

        $this->form_validation->set_rules('nome_completo', 'Nome completo', 'required');
        $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email');
        if (isset($_POST["tipoInscricao"])){
            $this->form_validation->set_rules('tipoInscricao', 'Tipo de inscrição', 'required');
        }
        $this->form_validation->set_rules('cpf', 'CPF', 'required|callback_cpf_check', array('cpf_check' => 'Digite um CPF válido.'));

        if (isset($_POST["password1"]) || isset($_POST["password2"])){
            $this->form_validation->set_rules('password1', 'Senha', 'required|min_length[6]');
            $this->form_validation->set_rules('password2', 'Confirmação da senha', 'required|matches[password1]');        
        }

        if (_v($_FILES['foto'],"name") != ""){
            $this->form_validation->set_rules('foto', 'Foto', 'callback_upload_check');
        }

        if ($this->form_validation->run()) {
            if ($this->upload_data != null){
                $_POST["foto"] = base_url($this->uploadPath."/".$this->upload_data['file_name']);
            }

            #valido
            $_POST["id"] = $_SESSION['user']["id"];
            if (isset($_POST["password1"])){
                $_POST["password"] = $_POST["password1"];
            }
            
            $this->load->model("Usuario_model");
            $id = $this->Usuario_model->salvar_meu_cadastro($this->input->post());
            if ($id == null){
                $this->session->set_flashdata("error","Não foi possível salvar os dados.");
            } else {
                $this->session->set_flashdata("success","Seus dados foram salvos.");
            } 
            redirect("perfil/index");
        } else {
            #invalido
            $this->index();
        }
    }


}