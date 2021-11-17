<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class GrandesAreas extends CI_Controller {

    public function __construct(){
        parent::__construct();
        
        #so permite admin e equipe
        if (isset($_SESSION["admin_user"]) 
            && $_SESSION["admin_user"]["nivel"] <= NIVEL_AVALIADOR || !isset($_SESSION["admin_user"])){
            redirect("admin/logout");
        }
    }

    public function index($id=null){
        $this->load->model("GrandeArea_model");
        if ($id == null){
            $this->load->library('pagination');
            $lista = $this->GrandeArea_model->listar($_GET);
        } else {
            if ($id == "form"){
                $dados = [];
            } else {
                $dados = $this->GrandeArea_model->get($id);
            }
            #se tiver sido uma submissão
            if (isset($_POST["id"])){
                $dados = $this->input->post();
            }
        }

        $csrf = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );


        include view("admin/grandes_areas");
    }


    public function salvar(){

        $this->form_validation->set_rules('grande_area', 'Nome da grande área', 'required');

        if ($this->form_validation->run()) {
            #valido
            $this->load->model("GrandeArea_model");
            $id = $this->GrandeArea_model->salvar($this->input->post());
            if ($id == null){
                $this->session->set_flashdata("error","Não foi possível salvar o registro.");
            } else {
                $this->session->set_flashdata("success","Registro salvo.");
            } 
            redirect("painel/grandesareas/index/$id");
        } else {
            #invalido
            $this->index("form");
        }
    }

    public function deletar($id){
        $this->load->model("GrandeArea_model");
        $this->GrandeArea_model->deletar($id);
        $this->session->set_flashdata("warning","Registro deletado.");
        redirect("painel/grandesareas/");
    }


}
