<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Evento extends CI_Controller {

    public function __construct(){
        parent::__construct();
        
        #so permite admin e equipe
        if (isset($_SESSION["admin_user"]) 
            && $_SESSION["admin_user"]["nivel"] <= NIVEL_AVALIADOR || !isset($_SESSION["admin_user"])){
            redirect("admin/logout");
        }
    }

    public function index(){
        $this->load->model("Evento_model");
        
        $dados = $this->Evento_model->get(1);
        #se tiver sido uma submissão
        if (isset($_POST["id"])){
            $dados = $this->input->post();
        }

        $csrf = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );

        include view("admin/evento");
    }


    public function salvar(){

        $this->form_validation->set_rules('evento', 'Nome do evento', 'required');
        $this->form_validation->set_rules('limite_submissoes', 'Limite de submissões', 'required');
        $this->form_validation->set_rules('limite_avaliadores_trabalhos', 'Limite de avaliadores de trabalhos', 'required');
        $this->form_validation->set_rules('data_inicio', 'Data de início', 'required');
        $this->form_validation->set_rules('data_fim', 'Data de fim', 'required');
        
        if ($this->form_validation->run()) {
            #valido
            $this->load->model("Evento_model");
            $id = $this->Evento_model->salvar($this->input->post());
            if ($id == null){
                $this->session->set_flashdata("error","Não foi possível salvar o registro.");
            } else {
                $this->session->set_flashdata("success","Registro salvo.");
            } 

            get_evento_data();

            redirect("painel/evento/index/");
        } else {
            #invalido
            $this->index("form");
        }
    }



}
