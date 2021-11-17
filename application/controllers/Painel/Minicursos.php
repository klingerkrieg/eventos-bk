<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Minicursos extends CI_Controller {

    private $certificadoErrMsg = "";
    private $certificadoDEBUG = false;
    private $envioEmail = false;

    public function __construct(){
        parent::__construct();
        
        #so permite admin, equipe e revisor
        if (isset($_SESSION["admin_user"]) 
            && $_SESSION["admin_user"]["nivel"] <= NIVEL_PARTICIPANTE || !isset($_SESSION["admin_user"])){
            redirect("admin/logout");
        }
    }

    public function index($id=null, $error=false){
        $this->load->model("Minicurso_model");
        if ($id == null){
            $this->load->library('pagination');
            $lista = $this->Minicurso_model->listar($_GET);
        } else {
            $dados = [];
            $dados = $this->Minicurso_model->get($id);
            
            #se tiver sido uma submissão
            if (isset($_POST["id"])){
                foreach($this->input->post() as $k=>$v){
                    $dados[$k] = $v;
                }
            }
        }

        $this->load->model("Area_model");
        $areas = $this->Area_model->all();
        
        $vagas  = $this->Minicurso_model->vagas;
        $chs    = $this->Minicurso_model->chs;
        $status = $this->Minicurso_model->status;
        $turnos = $this->Minicurso_model->turnos;
        $datasHorarios = $this->Minicurso_model->datasHorarios;

        $csrf = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );

        include view("admin/minicursos");
    }


    public function salvar(){

        
        $this->form_validation->set_rules('vagas', 'Vagas', 'required');
        $this->form_validation->set_rules('ch', 'Vagas', 'required');
        $this->form_validation->set_rules('vagas', 'Vagas', 'required');

        if ($_POST["matricula_disponivel"]){
            $_POST["status"] = APROVADO;
            $this->form_validation->set_rules('descricao', 'Descrição', 'required',
                    ["required"=>'É preciso definir a descrição do minicurso para disponibilizar o minicurso para matrícula.']);
            #$this->form_validation->set_rules('url', 'URL', 'required',
            #        ["required"=>'É preciso definir a URL para disponibilizar o minicurso para matrícula.']);
            $this->form_validation->set_rules('horarios_escolhidos[]', 
                    '' , 
                    'required', 
                    ["required"=>'É preciso escolher o horário para poder disponibilizar a matrícula.']);
        }
        
        if ($this->form_validation->run()) {

            #valido
            $this->load->model("Minicurso_model");
            $id = $this->Minicurso_model->salvar_correcao($this->input->post());

            if ($id == null){
                $this->session->set_flashdata("error","Não foi possível salvar o registro.");
            } else {

                $rw = $this->Minicurso_model->get($id);
                $this->session->set_flashdata("success","Registro salvo.");

                #caso o status seja modificado para algo diferente de pendente
                #envia um e-mail para o autor
                if ($_POST['status'] != "" && $_POST['status'] != PENDENTE){
                    $status = $this->Minicurso_model->status[$this->input->post("status")];
                    $msg = $this->load->view("emails/minicurso_corrigido",["status"=>$status],true);
                    enviar_email($rw["email_autor"],$msg);
                } else {
                    redirect("painel/minicursos/index/$id");
                }

            }   
            
            
            if ($this->config->item('envio_emails_ativo')) {
                redirect("painel/minicursos/index/$id");
            } else {
                print "<a href='".site_url("painel/minicursos/index/$id")."'>Continua</a>";
            }
            

        } else {
            $this->session->set_flashdata("error","Verifique os erros no formulário.");
            #invalido
            $this->index($_POST["id"], true);
        }

        
        
    }

    public function deletar($id){
        #so permite admin
        if (isset($_SESSION["admin_user"]) && $_SESSION["admin_user"]["nivel"] < NIVEL_ADMIN){
            redirect("admin/logout");
        }

        $this->load->model("Minicurso_model");
        $this->Minicurso_model->admin_deletar($id);
        $this->session->set_flashdata("warning","Registro deletado.");
        redirect("painel/minicursos/");
    }

    public function gerar_certificados(){

        $this->load->model("Minicurso_model");
        $minicursos = $this->Minicurso_model->all();

        $total = count($minicursos);
        $gerado = 0;
        foreach($minicursos as $curso){

            #pula trabalhos que já tiveram o certificado gerado
            if ($curso["certificado"] == ""){
                if ($this->_gerar_certificado($curso["id"])){
                    $gerado++;
                }
            }
            if ($this->_gerar_certificado_matriculados($curso["id"])){
                $gerado++;
            }
        }

        $this->session->set_flashdata("success","$gerado certificados gerados.");
        
        if ($this->config->item('envio_emails_ativo')) {
            redirect("painel/minicursos/");
        } else {
            print "<a href='".site_url("painel/minicursos/")."'>Continua</a>";
        }

    }

    private function _gerar_pdf_certificado($usuario,$dados){
        
        $hash = "m". $dados['id'] . "u" . $usuario["id"];

        $file_name = "./certificados/minicursos/$hash.pdf";

        $conf = $this->Minicurso_model->salvar_certificado($dados['id'],$usuario["id"],$hash);
        $chs  = $this->Minicurso_model->chs;

        if ($this->certificadoDEBUG){
            $certificado = base_url("/certificados_font/certificado.png");
            $ifrn_logo = base_url("/certificados_font/ifrn.png");
            $evento_logo = base_url("/certificados_font/evento_logo.png");

            $assin1 = base_url("/certificados_font/assinatura1.png");
            $assin2 = base_url("/certificados_font/assinatura2.png");
            $assin3 = base_url("/certificados_font/assinatura3.png");

            include view("certificados/certificado_minicurso_ministrante");
        } else {
            $data = [];
            $data["certificado"] = dompdf_img("./certificados_font/certificado.png");
            $data["ifrn_logo"] = dompdf_img("./certificados_font/ifrn.png");
            $data["evento_logo"] = dompdf_img("./certificados_font/evento_logo.png");

            $data["assin1"] = dompdf_img("./certificados_font/assinatura1.png");
            $data["assin2"] = dompdf_img("./certificados_font/assinatura2.png");
            $data["assin3"] = dompdf_img("./certificados_font/assinatura3.png");
            $data["dados"] = $dados;
            $data["hash"] = $hash;
            $data["usuario"] = $usuario;
            $data["chs"] = $chs;
            
            $this->pdf->load_view('certificado_minicurso_ministrante',$data, $file_name);
        }
        return [$conf, $file_name,$hash];
    }

    private function _gerar_certificado($idcurso){
        $this->load->library('pdf');

        $this->load->model("Minicurso_model");
        $dados = $this->Minicurso_model->get($idcurso);

        #se o trabalho não for encontrado
        if ($dados == null){
            return false;
        }


        #verifica se o usuario realizou o pagamento
        $this->load->model("Usuario_model");
        $usuario = $this->Usuario_model->get($dados['idusuario']);


        
        #verifica se o minicurso foi aprovado
        if ($dados["status"] == APROVADO && $usuario["pago"] == 1){

            list($conf, $file_name, $hash) = $this->_gerar_pdf_certificado($usuario,$dados);
            if ($conf){
                $this->enviar_email_aviso_certificado($dados["email_autor"],$file_name,$hash);
            } else {
                return false;
            }

            /**
             * Enviar e-mail para autores
             */
            foreach($dados["coautores"] as $usr){
                #adaptacao para permitir o uso da funcao _gerar_pdf_certificado
                $usr["id"] = $usr["idusuario"];
                list($conf, $file_name, $hash) = $this->_gerar_pdf_certificado($usr,$dados);
                if ($conf){
                    $this->enviar_email_aviso_certificado($usr["email"],$file_name,$hash);
                } else {
                    return false;
                }
            }
            

            return true;
        } else {
            $this->certificadoErrMsg = "Não foi possível gerar o certificado, pois o autor não efetuou o pagamento da inscrição.";
            return false;
        }
    }

    private function enviar_email_aviso_certificado($para_email, $file, $hash){
            
            $url = site_url("certificados/validar/$hash");
            
            $msg = $this->load->view("emails/aviso_certificado",["url"=>$url],true);

            enviar_email($para_email,$msg);
    }

    public function gerar_certificado($idcurso){

        if ($this->_gerar_certificado($idcurso)){
            $this->session->set_flashdata("success","Certificado gerado.");
        } else {
            if ($this->certificadoErrMsg != ""){
                $this->session->set_flashdata("error",$this->certificadoErrMsg);
            } else {
                $this->session->set_flashdata("error","Não foi possível gerar o certificado.");
            }
        }

        if ($this->config->item('envio_emails_ativo')) {
            redirect("painel/minicursos/index/$idcurso");
        } else {
            print "<a href='".site_url("painel/minicursos/index/$idcurso")."'>Continua</a>";
        }
        
    }

    public function salvar_diario(){
        $this->load->model("Matricula_model");
        if ($this->Matricula_model->salvar_diario_adm($this->input->post())){
            $this->session->set_flashdata("success","Diário salvo.");
        } else {
            $this->session->set_flashdata("error","Falha ao salvar o diário.");
        }

        redirect("painel/minicursos/index/{$_POST['id']}");
    }


    public function gerar_lista($idMinicurso){

        $this->load->model("Minicurso_model");
        $minicurso = $this->Minicurso_model->get($idMinicurso);

        include view('area/lista_presenca_minicurso');

    }



    public function clonar($idMinicurso){
        #so permite admin
        if (isset($_SESSION["admin_user"]) && $_SESSION["admin_user"]["nivel"] < NIVEL_ADMIN){
            redirect("admin/logout");
        }
        
        $this->load->model("Minicurso_model");
        $this->Minicurso_model->clonar($idMinicurso);
        redirect("painel/minicursos/index/{$idMinicurso}");
    }






    /** 
     * Certificados para os alunos matriculados e aprovados
     */



    private function _gerar_pdf_certificado_matriculado($matr,$dados){
        $hash = "m". $matr['idminicurso'] . "um" . $matr["idusuario"];

        $file_name = "./certificados/matriculados/$hash.pdf";

        $this->load->model("Matricula_model");
        $conf = $this->Matricula_model->salvar_certificado($matr['id'],$matr["idusuario"],$hash);
        $chs  = $this->Minicurso_model->chs;

        if ($this->certificadoDEBUG){
            $certificado = base_url("/certificados_font/certificado.png");
            $ifrn_logo = base_url("/certificados_font/ifrn.png");
            $evento_logo = base_url("/certificados_font/evento_logo.png");

            $assin1 = base_url("/certificados_font/assinatura1.png");
            $assin2 = base_url("/certificados_font/assinatura2.png");
            $assin3 = base_url("/certificados_font/assinatura3.png");
            $usuario = $matr;

            include view("certificados/certificado_minicurso_matriculado");
        } else {
            $data = [];
            $data["certificado"] = dompdf_img("./certificados_font/certificado.png");
            $data["ifrn_logo"] = dompdf_img("./certificados_font/ifrn.png");
            $data["evento_logo"] = dompdf_img("./certificados_font/evento_logo.png");

            $data["assin1"] = dompdf_img("./certificados_font/assinatura1.png");
            $data["assin2"] = dompdf_img("./certificados_font/assinatura2.png");
            $data["assin3"] = dompdf_img("./certificados_font/assinatura3.png");
            $data["dados"] = $dados;
            $data["hash"] = $hash;
            $data["usuario"] = $matr;
            $data["chs"] = $chs;
            
            $this->pdf->load_view('certificado_minicurso_matriculado',$data, $file_name);
        }
        return [$conf, $file_name,$hash];
    }




    private function _gerar_certificado_matriculados($idcurso){
        $this->load->library('pdf');

        $this->load->model("Minicurso_model");
        $dados = $this->Minicurso_model->get($idcurso);

        #se o trabalho não for encontrado
        if ($dados == null){
            return false;
        }

        foreach($dados['matriculados'] as $matr){
            #so gera certificado se o aluno tiver sido aprovado no minicurso
            if ($matr['aprovado']){
                list($conf, $file_name, $hash) = $this->_gerar_pdf_certificado_matriculado($matr,$dados);

                if ($conf){
                    $this->enviar_email_aviso_certificado($matr['email'],$file_name,$hash);
                } else {
                    return false;
                }
            }
        }


        return true;
    }



    public function gerar_certificado_matriculados($idcurso){

        if ($this->_gerar_certificado_matriculados($idcurso)){
            $this->session->set_flashdata("success","Certificads gerados.");
        } else {
            $this->session->set_flashdata("error","Não foi possível gerar os certificados.");
        }

        if ($this->config->item('envio_emails_ativo')) {
            redirect("painel/minicursos/index/$idcurso");
        } else {
            print "<a href='".site_url("painel/minicursos/index/$idcurso")."'>Continua</a>";
        }
        
    }


    public function distribuicao(){
        $this->load->model("Minicurso_model");
        $datasHorarios = $this->Minicurso_model->datasHorarios;

        $lista = $this->Minicurso_model->all();

        include view("admin/distribuicao_minicursos");
    }


}
