<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Trabalhos extends CI_Controller {

    public function __construct(){
        parent::__construct();
        
        #so permite admin, equipe e revisor
        if (isset($_SESSION["admin_user"]) 
            && $_SESSION["admin_user"]["nivel"] <= NIVEL_PARTICIPANTE || !isset($_SESSION["admin_user"])){
            redirect("admin/logout");
        }
    }

    public function index($id=null){
        $this->load->model("Trabalho_model");
        $dados = [];
        if ($id == null){
            $this->load->library('pagination');
            $lista = $this->Trabalho_model->listar($_GET);
        } else {
            if ($id != "form"){
                $dados = $this->Trabalho_model->get($id);
            }
            #se tiver sido uma submissão
            if (isset($_POST["id"])){
                $dados = $this->input->post();
            }
        }

        $status = $this->Trabalho_model->status;
        $tiposTrabalhos = $this->Trabalho_model->tiposTrabalhos;
        $trilhas = $this->Trabalho_model->trilhas;

        $this->load->model("Evento_model");
        $evento = $this->Evento_model->get(1);
        
        $this->load->model("Usuario_model");
        $avaliadores = $this->Usuario_model->getAvaliadores(_v($dados,"idarea"));

        $this->load->model("Area_model");
        $areas = $this->Area_model->all();

        $this->load->model("GTS_model");
        $gts = $this->GTS_model->all();

        $csrf = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );

        include view("admin/trabalhos");
    }

    public function salvar_avaliacao(){
        $this->load->model("Avaliador_model");
        $ret = $this->Avaliador_model->salvar_avaliacao($this->input->post());
        $idtrabalho = $this->input->post("idtrabalho");

        if ($ret == false){
            $this->session->set_flashdata("error","Não foi possível salvar a avaliação.");
        } else {
            $this->session->set_flashdata("success","Avaliação salva.");

            $this->load->model("Trabalho_model");
            #$newStatus = $this->Trabalho_model->getAndUpdateStatus($idtrabalho);

            #caso o status seja modificado para algo diferente de pendente
            #envia um e-mail para o autor
            /*if ($newStatus != false){
                $msg = $this->load->view("emails/trabalho_corrigido",["status"=>$newStatus],true);
                $rw = $this->Trabalho_model->get($idtrabalho);
                enviar_email($rw["email_autor"],$msg);
            } else {
                redirect("painel/trabalhos/index/$idtrabalho");
            }*/

        }   
        
        redirect("painel/trabalhos/index/$idtrabalho");
        

    }


    public function salvar(){

        #se um revisor tentar salvar
        if ($_SESSION["admin_user"]["nivel"] == NIVEL_AVALIADOR){
            redirect("admin/logout");
        }

        #valido
        $this->load->model("Trabalho_model");
        #caso o status tenha modificado ele retornara o novo status
        list($id, $newStatus) = $this->Trabalho_model->salvar_avaliadores($this->input->post());

        if ($id == null){
            $this->session->set_flashdata("error","Não foi possível salvar o registro.");
        } else {
            $this->session->set_flashdata("success","Registro salvo.");
        }

        if ($newStatus != false){
            $msg = $this->load->view("emails/trabalho_corrigido",["status"=>$newStatus],true);
            $rw = $this->Trabalho_model->get($id);
            enviar_email($rw["email_autor"],$msg);

            if ($this->config->item('envio_emails_ativo')) {
                redirect("painel/trabalhos/index/$id");
            } else {
                print "<a href='".site_url("painel/trabalhos/index/$id")."'>Continua</a>";
            }
        } else {
            redirect("painel/trabalhos/index/$id");
        }
        
        
    }

    public function gerar_planilha(){
        $this->load->model("Trabalho_model");
        $arr = $_GET;
        $arr["paginado"] = false;
        $arr["getCoautores"] = true;        
        $lista = $this->Trabalho_model->listar($arr);

        $trilhas = $this->Trabalho_model->trilhas;

        $this->load->model("Evento_model");
        $evento = $this->Evento_model->get(1);
        include view('admin/trabalhos_csv');
    }


    public function deletar($id){
        #so permite admin
        if (isset($_SESSION["admin_user"]) && $_SESSION["admin_user"]["nivel"] != NIVEL_ADMIN){
            redirect("admin/logout");
        }

        $this->load->model("Trabalho_model");
        $this->Trabalho_model->admin_deletar($id);
        $this->session->set_flashdata("warning","Registro deletado.");
        redirect("painel/trabalhos/");
    }

    public function gerar_certificados(){

        $this->load->model("Trabalho_model");
        $trabalhos = $this->Trabalho_model->all();

        $total = count($trabalhos);
        $gerado = 0;
        foreach($trabalhos as $trab){

            #pula trabalhos que já tiveram o certificado gerado
            if ($trab["certificado"] == ""){
                if ($this->_gerar_certificado($trab["id"])){
                    $gerado++;
                }
            }
        }

        $this->session->set_flashdata("success","Foram gerados certificados para $gerado trabalhos.");
        
        if ($this->config->item('envio_emails_ativo')) {
            redirect("painel/trabalhos/");
        } else {
            print "<a href='".site_url("painel/trabalhos/")."'>Continua</a>";
        }

    }

    private function _gerar_pdf_certificado($idtrabalho,$usuario,$dados){
        $hash = "t". $idtrabalho . "u" . $usuario["id"];

        $file_name = "./certificados/trabalhos/$hash.pdf";

        $conf = $this->Trabalho_model->salvar_certificado($idtrabalho,$usuario["id"],$hash);

        $data = [];
        $data["certificado"] = dompdf_img("./certificados_font/certificado.png");
        $data["ifrn_logo"] = dompdf_img("./certificados_font/ifrn.png");
        $data["evento_logo"] = dompdf_img("./certificados_font/evento_logo.png");

        $data["dados"] = $dados;
        $data["hash"] = $hash;
        $data["usuario"] = $usuario;
        
        $this->pdf->load_view('certificado_trabalho',$data, $file_name);
        
        return [$conf, $file_name,$hash];
    }


    private function _gerar_pdf_certificado_premiado($idtrabalho,$usuario,$dados){
        $hash = "t". $idtrabalho . "u" . $usuario["id"];

        $file_name = "./certificados/trabalhos/$hash.pdf";

        $conf = $this->Trabalho_model->salvar_certificado($idtrabalho,$usuario["id"],$hash);

        $data = [];
        $data["certificado"] = dompdf_img("./certificados_font/certificado.png");
        $data["ifrn_logo"] = dompdf_img("./certificados_font/ifrn.png");
        $data["evento_logo"] = dompdf_img("./certificados_font/evento_logo.png");

        $data["dados"] = $dados;
        $data["hash"] = $hash;
        $data["usuario"] = $usuario;

        $this->pdf->load_view('certificado_trabalho_premiado',$data, $file_name);
        
        return [$conf, $file_name,$hash];
    }

    private function _gerar_certificado($idtrabalho){
        $this->load->library('pdf');

        $this->load->model("Trabalho_model");
        $dados = $this->Trabalho_model->get($idtrabalho);

        #se o trabalho não for encontrado
        if ($dados == null){
            return false;
        }

        #verifica se o usuario realizou o pagamento
        $this->load->model("Usuario_model");
        $usuario = $this->Usuario_model->get($dados['idusuario']);

        
        #verifica se o usuário realizou o pagamento
        #            o trabalho foi apresentado
        #            o trabalho consta como aprovado ou aprovado com correções

        if ($usuario["pago"] == 1 && $dados["apresentado"] && ( $dados["status"] == APROVADO || $dados["status"] == APROVADO_CORRECOES ) ){
            

            if ($dados["premiado"]){
                list($conf, $file_name, $hash) = $this->_gerar_pdf_certificado_premiado($idtrabalho,$usuario,$dados);
            } else {
                list($conf, $file_name, $hash) = $this->_gerar_pdf_certificado($idtrabalho,$usuario,$dados);
            }
            
            if ($conf){
                $this->enviar_email_autor($dados["email_autor"],$file_name,$hash);
            } else {
                return false;
            }

            /**
             * Enviar e-mail para autores
             */
            foreach($dados["coautores"] as $usr){
                #adaptacao para permitir o uso da funcao _gerar_pdf_certificado
                $usr["id"] = $usr["idusuario"];

                if ($dados["premiado"]){
                    list($conf, $file_name, $hash) = $this->_gerar_pdf_certificado_premiado($idtrabalho,$usr,$dados);
                } else {
                    list($conf, $file_name, $hash) = $this->_gerar_pdf_certificado($idtrabalho,$usr,$dados);
                }
                
                
                if ($conf){
                    $this->enviar_email_autor($usr["email"],$file_name,$hash);
                } else {
                    return false;
                }
            }
            foreach($dados["orientadores"] as $usr){
                $usr["id"] = $usr["idusuario"];
                #adaptacao para permitir o uso da funcao _gerar_pdf_certificado
                if ($dados["premiado"]){
                    list($conf, $file_name, $hash) = $this->_gerar_pdf_certificado_premiado($idtrabalho,$usr,$dados);
                } else {
                    list($conf, $file_name, $hash) = $this->_gerar_pdf_certificado($idtrabalho,$usr,$dados);
                }
                if ($conf){
                    $this->enviar_email_autor($usr["email"],$file_name,$hash);
                } else {
                    return false;
                }
            }
            

            return true;
        } else {
            return false;
        }
    }

    private function enviar_email_autor($para_email, $file, $hash){

            $url = site_url("certificados/validar/$hash");

            $msg = $this->load->view("emails/aviso_certificado",["url"=>$url],true);

            enviar_email($para_email,$msg);
    }

    public function gerar_certificado($idtrabalho){

        if ($this->_gerar_certificado($idtrabalho)){
            $this->session->set_flashdata("success","Certificado gerado.");
        } else {
            $this->session->set_flashdata("error","Não foi possível gerar o certificado.");
        }

        if ($this->config->item('envio_emails_ativo')) {
            redirect("painel/trabalhos/index/$idtrabalho");
        } else {
            print "<a href='".site_url("painel/trabalhos/index/$idtrabalho")."'>Continua</a>";
        }
        
    }


    public function distribuir_trabalhos(){

        $this->load->model("Trabalho_model");
        $qtd = $this->Trabalho_model->distribuir_trabalhos();
        if ($qtd > 0){
            $this->session->set_flashdata("success","Foram distribuídos trabalhos para $qtd avaliadores.");
        } else {
            $this->session->set_flashdata("error","Nenhum trabalho foi distribuído.");
        }

        redirect("painel/trabalhos/");


    }


}
