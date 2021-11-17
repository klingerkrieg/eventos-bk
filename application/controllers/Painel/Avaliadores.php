<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Avaliadores extends CI_Controller {

    public function __construct(){
        parent::__construct();
        
        if (isset($_SESSION["admin_user"]) 
            && $_SESSION["admin_user"]["nivel"] <= NIVEL_PARTICIPANTE || !isset($_SESSION["admin_user"])){
            redirect("admin/logout");
        }
    }


    public function salvar_areas(){

        $this->form_validation->set_rules('areas[]', 'Áreas', 'required');

        if ($this->form_validation->run()) {
            #valido
            $this->load->model("AvaliadorArea_model");
            if ($this->AvaliadorArea_model->salvar_areas($this->input->post())){
                $this->session->set_flashdata("success","Áreas atualizadas.");
            } else {
                $this->session->set_flashdata("error","Não foi possível salvar suas áreas.");
            } 
            redirect("admin/painel/");
        } else {
            #invalido
            redirect("admin/painel/");
        }
    }


}
