<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios extends CI_Controller {

    public function __construct(){
        parent::__construct();
        
        #so permite admin e equipe
        if (isset($_SESSION["admin_user"]) 
            && $_SESSION["admin_user"]["nivel"] <= NIVEL_AVALIADOR || !isset($_SESSION["admin_user"])){
            redirect("admin/logout");
        }
    }

    public function index($id=null){
        $this->load->model("Usuario_model");
        if ($id == null){
            $this->load->library('pagination');
            $lista = $this->Usuario_model->listar($_GET);
        } else {
            if ($id == "form"){
                $dados = [];
            } else {
                $dados = $this->Usuario_model->get($id);
            }
            #se tiver sido uma submissão
            if (isset($_POST["id"])){
                $dados = $this->input->post();
            }
        }

        $this->load->model("Instituicao_model");
        $instituicoes = $this->Instituicao_model->all();
        
        $this->load->model("Curso_model");
        $cursos = $this->Curso_model->all();
        $niveis_cursos = $this->Curso_model->niveis_cursos;

        $tiposInscricao = $this->Usuario_model->tiposInscricao;

        $niveis = $this->Usuario_model->niveis;

        $csrf = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );

        include view("admin/usuarios");
    }

    public function cpf_check($cpf){
        return cpf_check($cpf);
    }

    public function email_duplicado_check($email){
        $this->load->model("Usuario_model");
        $row = $this->Usuario_model->getByEmail($email);
        if ($row == null || $_POST["id"] == $row['id']){
            return true;
        } else {
            return false;
        }
    }

    public function salvar(){

        $this->form_validation->set_rules('nome_completo', 'Nome completo', 'required');
        $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email|callback_email_duplicado_check', array('email_duplicado_check' => "Este e-mail já está cadastrado."));
        $this->form_validation->set_rules('cpf', 'CPF', 'callback_cpf_check', array('cpf_check' => 'Digite um CPF válido.'));

        if (_v($_POST,"aprovado_certificado_mesa_redonda") ){
            $this->form_validation->set_rules('titulo_mesa_redonda', 'Título da mesa redonda', 'required');
        }

        if (_v($_POST,"aprovado_certificado_palestrante") ){
            $this->form_validation->set_rules('titulo_palestra', 'Título da palestra', 'required');
        }
        

        if ($this->form_validation->run()) {
            #valido
            $this->load->model("Usuario_model");
            $id = $this->Usuario_model->salvar($this->input->post());
            if ($id == null){
                $this->session->set_flashdata("error","Não foi possível salvar o registro.");
            } else {
                $this->session->set_flashdata("success","Registro salvo.");
            }           
            redirect("painel/usuarios/index/$id");
        } else {
            #invalido
            $this->index("form");
        }
    }

    public function deletar($id){
        $this->load->model("Usuario_model");
        $this->Usuario_model->deletar($id);
        $this->session->set_flashdata("warning","Registro deletado.");
        redirect("painel/usuarios/");
    }



    /**
     * Certificados participação
     */

    private function _gerar_pdf_certificado_participante($usuario){
        $hash = "p".rand(100,999)."u" . $usuario["id"];

        $file_name = "./certificados/participacao/$hash.pdf";

        $conf = $this->Usuario_model->salvar_certificado_participante($usuario["id"],$hash);

        $html_test = false;
        
        $view   = "certificado_participacao";
        if ($html_test){
            $certificado = base_url("/certificados_font/certificado.png");
            $ifrn_logo = base_url("/certificados_font/ifrn.png");
            $evento_logo = base_url("/certificados_font/evento_logo.png");

            $assin1 = base_url("/certificados_font/assinatura1.png");
            $assin2 = base_url("/certificados_font/assinatura2.png");
            $assin3 = base_url("/certificados_font/assinatura3.png");
            

            include view("certificados/layout");
        } else {
            $data = [];
            $data["certificado"] = dompdf_img("./certificados_font/certificado.png");
            $data["ifrn_logo"] = dompdf_img("./certificados_font/ifrn.png");
            $data["evento_logo"] = dompdf_img("./certificados_font/evento_logo.png");

            $data["assin1"] = dompdf_img("./certificados_font/assinatura1.png");
            $data["assin2"] = dompdf_img("./certificados_font/assinatura2.png");
            $data["assin3"] = dompdf_img("./certificados_font/assinatura3.png");
            $data["hash"] = $hash;
            $data["usuario"] = $usuario;
            
            $this->pdf->load_view($view,$data, $file_name);
        }
        return [$conf, $file_name, $hash];
    }

    private function _gerar_pdf_certificado_avaliador($usuario){
        $hash = "a".rand(100,999)."u" . $usuario["id"];

        $file_name = "./certificados/avaliador/$hash.pdf";

        $conf = $this->Usuario_model->salvar_certificado_avaliador($usuario["id"],$hash);

        $html_test = false;
        
        $view   = "certificado_avaliador";
        if ($html_test){
            $certificado = base_url("/certificados_font/certificado.png");
            $ifrn_logo = base_url("/certificados_font/ifrn.png");
            $evento_logo = base_url("/certificados_font/evento_logo.png");

            $assin1 = base_url("/certificados_font/assinatura1.png");
            $assin2 = base_url("/certificados_font/assinatura2.png");
            $assin3 = base_url("/certificados_font/assinatura3.png");
            

            include view("certificados/layout");
        } else {
            $data = [];
            $data["certificado"] = dompdf_img("./certificados_font/certificado.png");
            $data["ifrn_logo"] = dompdf_img("./certificados_font/ifrn.png");
            $data["evento_logo"] = dompdf_img("./certificados_font/evento_logo.png");

            $data["assin1"] = dompdf_img("./certificados_font/assinatura1.png");
            $data["assin2"] = dompdf_img("./certificados_font/assinatura2.png");
            $data["assin3"] = dompdf_img("./certificados_font/assinatura3.png");
            $data["hash"] = $hash;
            $data["usuario"] = $usuario;
            
            $this->pdf->load_view($view,$data, $file_name);
        }
        return [$conf, $file_name, $hash];
    }

    private function _gerar_pdf_certificado_palestrante($usuario){
        $hash = "pl".rand(100,999)."u" . $usuario["id"];

        $file_name = "./certificados/palestrante/$hash.pdf";

        $conf = $this->Usuario_model->salvar_certificado_palestrante($usuario["id"],$hash);

        $html_test = false;
        
        $view   = "certificado_palestrante";
        if ($html_test){
            $certificado = base_url("/certificados_font/certificado.png");
            $ifrn_logo = base_url("/certificados_font/ifrn.png");
            $evento_logo = base_url("/certificados_font/evento_logo.png");

            $assin1 = base_url("/certificados_font/assinatura1.png");
            $assin2 = base_url("/certificados_font/assinatura2.png");
            $assin3 = base_url("/certificados_font/assinatura3.png");
            

            include view("certificados/layout");
        } else {
            $data = [];
            $data["certificado"] = dompdf_img("./certificados_font/certificado.png");
            $data["ifrn_logo"] = dompdf_img("./certificados_font/ifrn.png");
            $data["evento_logo"] = dompdf_img("./certificados_font/evento_logo.png");

            $data["assin1"] = dompdf_img("./certificados_font/assinatura1.png");
            $data["assin2"] = dompdf_img("./certificados_font/assinatura2.png");
            $data["assin3"] = dompdf_img("./certificados_font/assinatura3.png");
            $data["hash"] = $hash;
            $data["usuario"] = $usuario;
            
            $this->pdf->load_view($view,$data, $file_name);
        }
        return [$conf, $file_name, $hash];
    }

    private function _gerar_pdf_certificado_mesa_redonda($usuario){
        $hash = "mr".rand(100,999)."u" . $usuario["id"];

        $file_name = "./certificados/mesa_redonda/$hash.pdf";

        $conf = $this->Usuario_model->salvar_certificado_mesa_redonda($usuario["id"],$hash);

        $html_test = false;
        
        $view   = "certificado_mesa_redonda";
        if ($html_test){
            $certificado = base_url("/certificados_font/certificado.png");
            $ifrn_logo = base_url("/certificados_font/ifrn.png");
            $evento_logo = base_url("/certificados_font/evento_logo.png");

            $assin1 = base_url("/certificados_font/assinatura1.png");
            $assin2 = base_url("/certificados_font/assinatura2.png");
            $assin3 = base_url("/certificados_font/assinatura3.png");
            

            include view("certificados/layout");
        } else {
            $data = [];
            $data["certificado"] = dompdf_img("./certificados_font/certificado.png");
            $data["ifrn_logo"] = dompdf_img("./certificados_font/ifrn.png");
            $data["evento_logo"] = dompdf_img("./certificados_font/evento_logo.png");

            $data["assin1"] = dompdf_img("./certificados_font/assinatura1.png");
            $data["assin2"] = dompdf_img("./certificados_font/assinatura2.png");
            $data["assin3"] = dompdf_img("./certificados_font/assinatura3.png");
            $data["hash"] = $hash;
            $data["usuario"] = $usuario;
            
            $this->pdf->load_view($view,$data, $file_name);
        }
        return [$conf, $file_name, $hash];
    }

    private function _gerar_certificado($idusuario){
        $this->load->library('pdf');

        $this->load->model("Usuario_model");
        $usuario = $this->Usuario_model->get($idusuario);

        #se o usuario não for encontrado
        if ($usuario == null){
            return false;
        }
        
        #verifica se o usuário pagou
        if ($usuario["pago"] == 1){
            
            if ($usuario["aprovado_certificado_participante"]){
                list($conf, $file_name, $hash) = $this->_gerar_pdf_certificado_participante($usuario);
                if ($conf){
                    $this->enviar_email_cert($usuario["email"],$file_name,$hash);
                } else {
                    return false;
                }
            }

            
            if ($usuario["aprovado_certificado_avaliador"]){
                list($conf, $file_name, $hash) = $this->_gerar_pdf_certificado_avaliador($usuario);
                if ($conf){
                    $this->enviar_email_cert($usuario["email"],$file_name,$hash);
                } else {
                    return false;
                }
            }

            if ($usuario["aprovado_certificado_palestrante"]){
                list($conf, $file_name, $hash) = $this->_gerar_pdf_certificado_palestrante($usuario);
                if ($conf){
                    $this->enviar_email_cert($usuario["email"],$file_name,$hash);
                } else {
                    return false;
                }
            }

            if ($usuario["aprovado_certificado_mesa_redonda"]){
                list($conf, $file_name, $hash) = $this->_gerar_pdf_certificado_mesa_redonda($usuario);
                if ($conf){
                    $this->enviar_email_cert($usuario["email"],$file_name,$hash);
                } else {
                    return false;
                }
            }

            return true;
        } else {
            return false;
        }
    }

    private function enviar_email_cert($para_email, $file, $hash){

            $url = site_url("certificados/validar/$hash");

            $msg = $this->load->view("emails/aviso_certificado",["url"=>$url],true);

            enviar_email($para_email,$msg);
    }

    public function gerar_certificado($idusuario){

        if ($this->_gerar_certificado($idusuario)){
            $this->session->set_flashdata("success","Certificado gerado.");
        } else {
            $this->session->set_flashdata("error","Não foi possível gerar o certificado.");
        }

        if ($this->config->item('envio_emails_ativo')) {
            redirect("painel/usuarios/index/$idusuario");
        } else {
            print "<a href='".site_url("painel/usuarios/index/$idusuario")."'>Continua</a>";
        }
        
    }

    public function gerar_certificados(){

        $this->load->model("Usuario_model");
        $usuarios = $this->Usuario_model->all();

        $total = count($usuarios);
        $gerado = 0;
        foreach($usuarios as $user){

            #pula usuários que já tiveram o certificado gerado
            if ($user["aprovado_certificado_participante"] && $user["certificado_participante"] == "" 
             || $user["aprovado_certificado_avaliador"] && $user["certificado_avaliador"] == "" 
             || $user["aprovado_certificado_mesa_redonda"] && $user["certificado_mesa_redonda"] == "" 
             || $user["aprovado_certificado_palestrante"] && $user["certificado_palestrante"] == ""){
                
                if ($this->_gerar_certificado($user["id"])){
                    $gerado++;
                }


            }

        }

        $this->session->set_flashdata("success","Foram gerados certificados para $gerado participantes.");
        
        if ($this->config->item('envio_emails_ativo')) {
            redirect("painel/usuarios/");
        } else {
            print "<a href='".site_url("painel/usuarios/")."'>Continua</a>";
        }

    }

    


}
