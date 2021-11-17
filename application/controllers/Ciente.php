<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ciente extends CI_Controller {

    public function __construct(){
        parent::__construct();
    }

    public function minicurso($hash){

        $this->load->model("MinicursoCoautor_model");
        $dados = $this->MinicursoCoautor_model->get_by_ciente_hash($hash);
        $minicurso = [];
        if ($dados != null){
            $this->load->model("Minicurso_model");
            $minicurso = $this->Minicurso_model->get($dados["idminicurso"]);
        }
        
        include view("area/ciente_minicurso");
    }

    public function trabalho($hash){

        $this->load->model("Coautor_model");
        $dados = $this->Coautor_model->get_by_ciente_hash($hash);
        $trab = [];
        if ($dados != null){
            $this->load->model("Trabalho_model");
            $trab = $this->Trabalho_model->get($dados["idtrabalho"]);
        }
        
        include view("area/ciente");
    }

    public function registrar_ciencia($hash){

        if ($hash[0] == "t"){
            $this->load->model("Coautor_model");
            $this->Coautor_model->registrar_ciencia_by_hash($hash);

            if (isset($_SESSION["user"]) && _v($_SESSION["user"],"id") != ""){
                redirect(site_url("trabalhos/submissoes"));
            } else {
                $this->session->set_flashdata('success', 'O trabalho foi confirmado, caso tenha interesse realize o cadastro no nosso sistema.');
                redirect(site_url("home/registrar"));
            }
        } else
        if ($hash[0] == "m"){
            $this->load->model("MinicursoCoautor_model");
            $this->MinicursoCoautor_model->registrar_ciencia_by_hash($hash);

            if (isset($_SESSION["user"]) && _v($_SESSION["user"],"id") != ""){
                redirect(site_url("trabalhos/submissoes"));
            } else {
                $this->session->set_flashdata('success', 'O minicurso foi confirmado, caso tenha interesse realize o cadastro no nosso sistema.');
                redirect(site_url("home/registrar"));
            }
        }
    }


}