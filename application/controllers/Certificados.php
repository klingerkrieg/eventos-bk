<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Certificados extends CI_Controller {

    public function __construct(){
        parent::__construct();
    }

    public function validar($hash){

        if (substr($hash,0,2) == "pl"){#palestrante
            $this->load->model("Usuario_model");
            $dados = $this->Usuario_model->get_by_cert_hash($hash);
            $file_name = "palestrante/$hash";
        } else
        if (substr($hash,0,2) == "mr"){#mesa redonda
            $this->load->model("Usuario_model");
            $dados = $this->Usuario_model->get_by_cert_hash($hash);
            $file_name = "mesa_redonda/$hash";
        } else
        if ($hash[0] == "t"){#trabalho autor principal
            $this->load->model("Trabalho_model");
            $dados = $this->Trabalho_model->get_by_cert_hash($hash);

            if ($dados == null){
                #se nao encontrou procura nos coautores
                $this->load->model("Coautor_model");
                $dados = $this->Coautor_model->get_by_cert_hash($hash);
            }

            $file_name = "trabalhos/$hash";
        } else
        if ($hash[0] == "p"){#participacao
            $this->load->model("Usuario_model");
            $dados = $this->Usuario_model->get_by_cert_hash($hash);
            $file_name = "participacao/$hash";
        } else
        if ($hash[0] == "a"){#avaliador
            $this->load->model("Usuario_model");
            $dados = $this->Usuario_model->get_by_cert_hash($hash);
            $file_name = "avaliador/$hash";
        } else
        if ($hash[0] == "m"){#minicurso autor principal
            $this->load->model("Minicurso_model");
            $dados = $this->Minicurso_model->get_by_cert_hash($hash);

            if ($dados == null){
                #se nao encontrou procura nos coautores
                $this->load->model("MinicursoCoautor_model");
                $dados = $this->MinicursoCoautor_model->get_by_cert_hash($hash);
            }
			
            if ($dados != null){
                $file_name = "minicursos/$hash";
            }
            

            if ($dados == null){
                #se nao encontrou procura nos coautores
                $this->load->model("Matricula_model");
                $dados = $this->Matricula_model->get_by_cert_hash($hash);
				
				if ($dados != null){
					$file_name = "matriculados/$hash";
				}
            }
            
        }

        
        include view("certificados/certificado_validacao");
    }


}