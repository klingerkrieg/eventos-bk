<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends CI_Controller {

    public function __construct(){
        parent::__construct();
        
        if (!isset($_SESSION["user"])){
            redirect("Home/login");
        }
    }

    public function email_locate(){
        $this->load->model("Usuario_model");
        $usr = $this->Usuario_model->getByEmail($this->input->get("email"));
        print $usr["nome_completo"];
    }


}