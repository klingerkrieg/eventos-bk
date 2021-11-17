<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Minicursos extends CI_Controller {

    public function __construct(){
        parent::__construct();
        
        if (!isset($_SESSION["user"])){
            redirect("Home/login");
        }
    }

    public function index(){
        
        $this->load->model("Minicurso_model");
        $minicursos = $this->Minicurso_model->disponiveis();
        $turnos     = $this->Minicurso_model->turnos;
        $chs        = $this->Minicurso_model->chs;

        $csrf = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );


        include view ('./area/minicursos_disponiveis.php');
        
    }

    public function matricular($id){

        if (evento_encerrado()){
            redirect("minicursos");
        }

        $this->load->model("Matricula_model");

        #nao permite matricular em um minicurso no mesmo horário de outro
        if ($this->Matricula_model->temConflitoHorario($id)){
            $this->session->set_flashdata("error","Você já está matriculado em um minicurso que utiliza o mesmo horário.");
            redirect("minicursos");
        } else {
            if ($this->Matricula_model->matricular($id)){
                $this->session->set_flashdata("success","Matrícula realizada.");
            } else {
                $this->session->set_flashdata("error","Não foi possível fazer a matrícula.");
            } 
        }
        redirect("minicursos#curso$id");
    }

    public function cancelar_matricula($id){

        if (evento_encerrado()){
            redirect("minicursos");
        }

        $this->load->model("Matricula_model");
        if ($this->Matricula_model->cancelar($id)){
            $this->session->set_flashdata("warning","Matrícula cancelada.");
        } else {
            $this->session->set_flashdata("error","Não foi possível cancelar a matrícula.");
        } 
        redirect("minicursos#curso$id");
    }

    public function form($id = null){

        $dados = [];
        $this->load->model("Minicurso_model");
        if ($id != null){
            $dados = $this->Minicurso_model->get($id);
        }

        if ($this->input->post('titulo') != null){
            $dados = $this->input->post();
            $dados["coautores"] = [];
            foreach($this->input->post("nome_coautor") as $k=>$nome){
                array_push($dados["coautores"], ["nome_completo"=>$nome, "email"=>$dados["email_coautor"][$k]]);
            }
        }

        $this->load->model("Evento_model");
        $evento = $this->Evento_model->getEvento();

        $this->load->model("Area_model");
        $areas = $this->Area_model->all();

        $vagas  = $this->Minicurso_model->vagas;
        $chs    = $this->Minicurso_model->chs;
        $turnos = $this->Minicurso_model->turnos;
        $status = $this->Minicurso_model->status;
        $datasHorarios = $this->Minicurso_model->datasHorarios;

        $this->load->model("Usuario_model");
        $user = $this->Usuario_model->get($_SESSION["user"]["id"]);
        

        #se o trabalho nao for meu e eu não constar como coautor
        if ($id != null && $dados["idusuario"] != $_SESSION["user"]["id"]){
            $sair = true;
            foreach($dados["coautores"] as $user){
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

        if (_v($dados,"id") != ""){
            include view ('./area/visualizar_minicurso.php');
        } else {

            if (evento_encerrado()){
                redirect("minicursos");
            }
            include view ('./area/submeter_minicurso.php');
        }
    }



    /**
     * UPLOAD VARIABLES
     */
    private $upload_data;
	private $uploadPath = './uploads/minicursos/';
    
    public function upload_check($v){

		$config['upload_path']          = $this->uploadPath;
		$config['allowed_types']        = 'pdf';
        $config['max_size']             = 10000;
        $config['file_name']             = cleanString($_FILES["arquivo"]["name"]);

        $this->load->library('upload', $config);

		if ( ! $this->upload->do_upload("arquivo")){
            $this->form_validation->set_message('upload_check', "Você esqueceu de enviar o arquivo ou ele é grande demais ou não é um .pdf.");
			return false;
		} else {
            $this->upload_data = $this->upload->data();
			return true;
		}
	}

    public function submeter(){

        if (evento_encerrado()){
            redirect("minicursos");
        }

        #caso tenha dado erro na submissao, e a url seja requisitada via get
        #envia de volta para o formulário
        if (!isset($_POST['titulo'])){
            redirect("minicursos/form");
        }

        $this->form_validation->set_rules('telefone', 'Telefone', 'required');

        if ($_POST["lattesC"] == "lattes"){
            $this->form_validation->set_rules('lattes', 'Link do lattes', 'required');
        } else {
            $this->form_validation->set_rules('curriculo', 'Lattes', 'required');
        }

        $this->form_validation->set_rules('titulo', 'Título', 'required');
        $this->form_validation->set_rules('idarea', 'Área', 'required');
        $this->form_validation->set_rules('resumo', 'Resumo', 'required');
        $this->form_validation->set_rules('objetivo', 'Objetivo', 'required');
        #$this->form_validation->set_rules('descricao', 'Descrição', 'required');
        $this->form_validation->set_rules('ch', 'Carga horária sugerida', 'required');
        $this->form_validation->set_rules('vagas', 'Vagas sugeridas', 'required');
        $this->form_validation->set_rules('horarios_preferenciais[]', 'Horários preferenciais para a realização', 'required');

        /*if ($this->input->post("id") == "" || _v($_FILES['arquivo'],"name") != ""){
            $this->form_validation->set_rules('arquivo', 'Arquivo', 'callback_upload_check');
        }*/

        $this->form_validation->set_rules('email_coautor[]', 'E-mail do coautor', 'valid_email|required');
        $this->form_validation->set_rules('nome_coautor[]', 'Nome do coautor', 'required');

        #para cada e-mail vazio ou nome vazio remove do array para que não vá para a validação
        foreach($_POST["email_coautor"] as $k=>$val){
            if ($_POST["email_coautor"][$k] == "" && $_POST["nome_coautor"][$k] == ""){
                unset($_POST["email_coautor"][$k]);
                unset($_POST["nome_coautor"][$k]);
            }
        }

        if ($this->form_validation->run()) {

            #atualiza dados do submissor
            $this->load->model("Usuario_model");
            $userDados = ["id"=>$_SESSION["user"]["id"],
                    "telefone"=>$this->input->post("telefone"),
                    "lattes"=>$this->input->post("lattes"),
                    "curriculo"=>$this->input->post("curriculo")];
            $this->Usuario_model->salvar_meu_cadastro($userDados);


            #valido
            $this->load->model("Minicurso_model");
            /*if ($this->upload_data != null){
                $_POST["arquivo"] = $this->upload_data['file_name'];
            }*/
            $id = $this->Minicurso_model->salvar_submissao($this->input->post());
            if ($id == null){
                $this->session->set_flashdata("error","Não foi possível fazer a submissão.");
            } else {
                $this->session->set_flashdata("success","Submissão realizada.");

                if ($this->input->post("id") == ""){
                    #envia e-mail para coautores, só envia o alerta na primeira vez
                    #nao envia sobre atualizacoes
                    $this->alertar_coautores($id);
                }
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

    public function setar_url(){

        if (evento_encerrado()){
            redirect("minicursos");
        }

        $this->form_validation->set_rules('url', 'URL', 'required');
        if ($this->form_validation->run()) {
            #valido
            $this->load->model("Minicurso_model");
            if ($this->Minicurso_model->salvar_url($this->input->post())){
                $this->session->set_flashdata("success","A URL foi salva.");
            } else {
                $this->session->set_flashdata("error","Não foi possível salvar a URL.");
            } 

            redirect("minicursos/form/{$_POST['id']}");
        } else {
            #invalido
            $this->form();
        }

    }

    public function alertar_coautores($id){
        $curso = $this->Minicurso_model->get($id);
        
        $this->load->model("MinicursoCoautor_model");

        foreach($curso["coautores"] as $usr){

            $hash = "m".$curso["id"]."n".rand(1000,9999);
            $this->MinicursoCoautor_model->salvar_hash($usr["id"],$hash);
            $url = site_url("ciente/minicurso/$hash");
            $msg = $this->load->view("emails/aviso_minicurso_submetido",
                                        ["url"=>$url,
                                        "nome"=>$usr["nome_completo"]],true);

            enviar_email($usr["email"],$msg);
            
            
        }

    }



    /*public function submeter_correcao(){
        
        $this->form_validation->set_rules('arquivoCorrigido', 'Arquivo', 'callback_upload_check');
        
        if ($this->form_validation->run()) {
            #valido
            $this->load->model("Minicurso_model");
            $_POST["arquivoCorrigido"] = $this->upload_data['file_name'];
            
            $trab = $this->Minicurso_model->salvar_trabalho_corrigido($_POST);
            if ($trab == null){
                $this->session->set_flashdata("error","Não foi possível fazer a correção.");
            } else {
                $this->session->set_flashdata("success","Correção realizada.");
            } 

            redirect("minicursos/submissoes");
        } else {
            #invalido
            $this->form($_POST["id"]);
        }
    }*/

    public function cancelar($id){
        $this->load->model("Minicurso_model");

        $trab = $this->Minicurso_model->get($id);
        if ($trab != null){
            if ($trab["status"] == PENDENTE){
                $this->Minicurso_model->deletar($id);
                $this->session->set_flashdata("warning","Submissão cancelada.");
            } else {
                $this->session->set_flashdata("error","Não foi possível cancelar o minicurso.");
            }
            redirect("trabalhos/submissoes");
        } else {
            $this->session->set_flashdata("error","Não foi possível cancelar o minicurso.");
        }
    }




    public function salvar_diario(){
        $this->load->model("Matricula_model");
        if ($this->Matricula_model->salvar_diario($this->input->post())){
            $this->session->set_flashdata("success","Diário salvo.");
        } else {
            $this->session->set_flashdata("error","Falha ao salvar o diário.");
        }

        redirect("minicursos/form/{$_POST['id']}");
    }


    public function gerar_lista($idMinicurso){

        $this->load->model("Minicurso_model");
        $minicurso = $this->Minicurso_model->get($idMinicurso);

        include view('area/lista_presenca_minicurso');

    }


}