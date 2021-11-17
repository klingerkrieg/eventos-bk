<?php
require_once "AbstractModel.php";



class Usuario_model extends AbstractModel {

    protected $table = "usuarios";
    protected $logicExclusion = true;

    protected $select = "usuarios.*, instituicoes.instituicao, cursos.curso, "
                        ." bairros.bairro, cidades.cidade, ufs.uf ";
    protected $joins = [["instituicoes","instituicoes.id = idinstituicao","left"],
                        ["cursos","cursos.id = idcurso","left"],
                        ["bairros","bairros.id = idbairro","left"],
                        ["cidades","cidades.id = bairros.idcidade","left"],
                        ["ufs","ufs.id = cidades.iduf","left"]];

    protected $filtros = ["usuarios.nome_completo","usuarios.nome_social","usuarios.cpf","usuarios.email"];
    protected $orderBy = "usuarios.nome_completo";


    public $tiposInscricao = [ALUNO_MEDIO=>"Aluno do ensino médio",
                            ALUNO_TECNICO=>"Aluno de nível técnico", 
                            ALUNO_GRADUACAO=>"Aluno de graduação", 
                            ALUNO_POS=>"Aluno de pós-graduação", 
                            SERVIDOR=>"Servidor", 
                            DOCENTE=>"Docente", 
                            OUTROS=>"Outros"];

    public $niveis = [NIVEL_PARTICIPANTE=>"Participante", 
                        NIVEL_AVALIADOR=>"Avaliador", 
                        NIVEL_EQUIPE=>"Equipe", 
                        NIVEL_ADMIN=>"Admin"];



    public function get($id){
        $user = parent::get($id);

        $CI =& get_instance();
        $CI->load->model('AvaliadorArea_model');
        $user["areas"] = $CI->AvaliadorArea_model->getAreas($user["id"]);

        return $user;
    }



    public function listar($dados=[]){
        if (isset($_GET["nivel"]) && $_GET["nivel"] != ""){
            $dados['filtro_equals'] = ["nivel"=> $_GET["nivel"]];
        }

        return parent::listar($dados);
    }

    public function filterSUAPService($dados){
        $CI =& get_instance();
        $CI->load->model('Instituicao_model');
        

        if (count($dados["vinculo"]) > 0){
            $dados["nome_completo"] = $dados["vinculo"]["nome"];
            $dados["foto"]          = "https://suap.ifrn.edu.br/".$dados["url_foto_75x100"];
            if ($dados["vinculo"]["categoria"] == "docente"){
                $dados["tipoInscricao"] = DOCENTE;
            } else {
                $dados["tipoInscricao"] = OUTROS;
            }
            
            $dados["idinstituicao"] = $CI->Instituicao_model->getBySigla($dados["vinculo"]["campus"]);
            if ($dados["idinstituicao"] != null){
                $dados["idinstituicao"] = $dados["idinstituicao"]["id"];
            }
        }
        
        return $dados;
    }

    public function getOrCreateCompleto($dados){
        $usr = $this->db->get_where($this->table, ["email"=> $dados["email"]])->row_array();

        
        if ($usr == null){

            $dados_usr = [];
            foreach(["nome_completo","email","matricula","cpf","tipoInscricao","foto","idinstituicao"] as $field){
                if (isset($dados[$field])){
                    $dados_usr[$field] = $dados[$field];
                }
            }
            #false porque o usuario nao tera senha ainda, a menos que faça o cadastro com senha
            $dados_usr["email_confirmado"] = '0';
            $dados_usr["pago"] = true;
            $dados_usr["nivel"] = NIVEL_PARTICIPANTE;
            #todo docente do suap será automaticamente avaliador
            if ($dados_usr["tipoInscricao"] == DOCENTE){
                $dados_usr["nivel"] = NIVEL_AVALIADOR;
            }

            $id = parent::salvar($dados_usr);
            $usr = $this->db->get_where($this->table, ["id"=>$id])->row_array();
        }

        return $usr;
    }

    public function getOrCreate($nome, $email){
        $rw = $this->getByEmail($email);
        if ($rw == null){
            return $this->salvar(["nome_completo"=>$nome, "email"=>$email,"nivel"=>NIVEL_PARTICIPANTE],false);
        } else {
            return $rw['id'];
        }
    }

    public function getByEmail($email){
        $sql = "select * from usuarios where email = ?";
        $rs = $this->db->query($sql, [$email]);
        return $rs->row_array();
    }
    
    /* Usado pela equipe */
    public function salvar($dados,$password = true){

        #seta o endereço, se tiver sido preenchido
        if (_v($dados,"bairro") != ""){
            $end = ["bairro"=>$dados["bairro"], "cidade"=>$dados["cidade"], "uf"=>$dados["uf"]];
            $CI =& get_instance();
            $CI->load->model('Endereco_model');
            $endereco = $CI->Endereco_model->getOrCreate($end);
            $dados["idbairro"] = $endereco["idbairro"];
        } else {
            $dados["idbairro"] = null;
        }
        
        #transforma o nome dos campos que vem do formulário para o nome no db
        $fields = ["id","nome_completo","nome_social","email","cpf","tipoInscricao",
                    "instituicao"=>"idinstituicao",
                    "curso"=>"idcurso","pago","logradouro","idnivelcurso",
                    "aprovado_certificado_avaliador","aprovado_certificado_participante",
                    "aprovado_certificado_palestrante", "aprovado_certificado_mesa_redonda",
                    "titulo_palestra", "titulo_mesa_redonda",
                    "nivel","cep","numero","idbairro","outra_instituicao"];

        $tratados = $this->replaceNames($dados,$fields);
        if (!isset($dados["id"])){
            $tratados["id"] = null;
        }

        #nao deixa setar niveis mais altos que o proprio
        if (_v($dados,"id") != ""){
            if ($tratados["nivel"] > $_SESSION["admin_user"]["nivel"]){
                return false;
            }
        }

        #caso ele tenha marcado outra instituicao
        if (isset($tratados["idinstituicao"])){
            if ($tratados["idinstituicao"] == "outra"){
                $tratados["idinstituicao"] = null;
            } else {
                $tratados["outra_instituicao"] = null;
            }
        }

        if (_v($dados,"password") != ""){
            if (_v($dados,"id") == "" && !isset($dados["password"])){
                $tratados["password"] = sha1("12345678");
            } else {
                $tratados["password"] = sha1($dados["password"]);
            }
            if ($password == false){
                $tratados["password"] = null;
            }
        }

        
        return parent::salvar($tratados);
    }


    /**
     * Usado para se cadastrar
     */
    public function salvar_meu_cadastro($dados,$password = true){

        #seta o endereço, se tiver sido preenchido
        if (_v($dados,"bairro") != ""){
            $end = ["bairro"=>$dados["bairro"], "cidade"=>$dados["cidade"], "uf"=>$dados["uf"]];
            $CI =& get_instance();
            $CI->load->model('Endereco_model');
            $endereco = $CI->Endereco_model->getOrCreate($end);
            $dados["idbairro"] = $endereco["idbairro"];
        } else {
            $dados["idbairro"] = null;
        }
        
        #transforma o nome dos campos que vem do formulário para o nome no db
        $fields = ["id","nome_completo","nome_social","email","cpf","tipoInscricao",
                    "curriculo","lattes","telefone","foto",
                    "instituicao"=>"idinstituicao",
                    "curso"=>"idcurso","pago","logradouro","idnivelcurso",
                    "cep","numero","idbairro","outra_instituicao"];

        $tratados = $this->replaceNames($dados,$fields);
        if (!isset($dados["id"])){
            $tratados["id"] = null;
        }
        

        #caso ele tenha marcado outra instituicao
        if (isset($tratados["idinstituicao"])){
            if ($tratados["idinstituicao"] == "outra"){
                $tratados["idinstituicao"] = null;
            } else {
                $tratados["outra_instituicao"] = null;
            }
        }

        if (_v($dados,"password") != ""){
            if (_v($dados,"id") == "" && !isset($dados["password"])){
                $tratados["password"] = sha1("12345678");
            } else {
                $tratados["password"] = sha1($dados["password"]);

                #se eu estiver alterando a minha propria senha na area de perfil
                if (isset($_SESSION["user"]) && _v($_SESSION["user"],"id") == $dados["id"]){
                    $tratados["email_confirmado"] = true;
                }
            }
            if ($password == false){
                $tratados["password"] = null;
            }
        }

        #todo mundo terá pago por padrão
        $tratados['pago'] = true;

        #todo mundo é participante por padrão
        if (_v($dados,"id") == ""){
            $tratados['nivel'] = NIVEL_PARTICIPANTE;
        }
        
        
        return parent::salvar($tratados);
    }
    
    public function replaceNames($dados,$fields){

        $dadosNew = [];

        foreach($fields as $k=>$v){
            
            if (is_numeric($k) && isset($dados[$v])){
                $dadosNew[$v] = $dados[$v];
            } else
            if (isset($dados[$k])) {
                $dadosNew[$v] = $dados[$k];
            }
            
        }
        return $dadosNew;
    }


    public function novoHashDeSenha($email,$cpf){

        $this->db->select("id");
        $rw = $this->db->get_where("usuarios",["email"=>$email,"cpf"=>$cpf],1)->row_array();

        if ($rw == null){
            return false;
        }

        $hash = sha1(rand());
		$this->db->set("hash",$hash);
        $this->db->where("id",$rw["id"]);
        $this->db->userid($rw["id"]);
		$this->db->update("usuarios");
        
        return ["id"=>$rw["id"], "hash"=>$hash];
    }

    public function getByHash($hash, $id){
        $this->db->select("id");
		$rw = $this->db->get_where("usuarios",["hash"=>$hash,
                                                "id"=>$id],1)->row_array();

        if ($rw == null){
            return false;
        } else {
            return true;
        }

    }

    public function atualizarSenha($id, $password){
		#altera a senha no banco
		$this->db->set("hash",null);
		$this->db->set("password",sha1($password));
        $this->db->set("email_confirmado",1);
        $this->db->where("id", $id);
        $this->db->userid($id);
		$this->db->update("usuarios");
    }


    public function confirmaEmail($id){
        $this->db->set("hash",null);
		$this->db->set("email_confirmado",1);
        $this->db->where("id", $id);
        $this->db->userid($id);
		$this->db->update("usuarios");
    }


    public function salvar_certificado_participante($idusuario, $hash){
        $dados = ["id"=>$idusuario, "certificado_participante"=>$hash, "certificado_participante_data"=>date('Y-m-d H:i_s')];

        $rw = $this->db->get_where($this->table,["id"=>$idusuario])->row();
        #se encontrar é para salvar o certificado do participante
        if ($rw != null){
            return parent::salvar($dados);
        } else {
            return false;
        }
    }

    public function salvar_certificado_avaliador($idusuario, $hash){
        $dados = ["id"=>$idusuario, "certificado_avaliador"=>$hash, "certificado_avaliador_data"=>date('Y-m-d H:i_s')];

        $rw = $this->db->get_where($this->table,["id"=>$idusuario])->row();
        #se encontrar é para salvar o certificado do participante
        if ($rw != null){
            return parent::salvar($dados);
        } else {
            return false;
        }
    }

    public function salvar_certificado_palestrante($idusuario, $hash){
        $dados = ["id"=>$idusuario, "certificado_palestrante"=>$hash, "certificado_palestrante_data"=>date('Y-m-d H:i_s')];

        $rw = $this->db->get_where($this->table,["id"=>$idusuario])->row();
        if ($rw != null){
            return parent::salvar($dados);
        } else {
            return false;
        }
    }

    public function salvar_certificado_mesa_redonda($idusuario, $hash){
        $dados = ["id"=>$idusuario, "certificado_mesa_redonda"=>$hash, "certificado_mesa_redonda_data"=>date('Y-m-d H:i_s')];

        $rw = $this->db->get_where($this->table,["id"=>$idusuario])->row();
        if ($rw != null){
            return parent::salvar($dados);
        } else {
            return false;
        }
    }

    public function get_by_cert_hash($hash){
        if ($hash != null){
            $this->db->select($this->select);
            foreach($this->joins as $join){
                #tbl, cond, type
                $this->db->join($join[0],$join[1],$join[2]);
            }
            if ($this->logicExclusion) {
                $this->db->where("$this->table.deleted",0);
            }
            $this->db->group_start();
            $this->db->or_where("$this->table.certificado_avaliador",$hash);
            $this->db->or_where("$this->table.certificado_mesa_redonda",$hash);
            $this->db->or_where("$this->table.certificado_palestrante",$hash);
            $this->db->or_where("$this->table.certificado_participante",$hash)->group_end();

            #$sql = $this->db->get_compiled_select($this->table);
            #print $sql;

            $rs = $this->db->get($this->table);

            $user = $rs->row_array();

            if ($user == null){
                return null;
            }

            return $user;
        }
        return null;
    }


    public function getAvaliadores($idarea=null){

        /*
        Irá selecionar todos os avaliadores, mas irá colocar em primeiro lugar os avaliadores da área
        */

        $this->db->select("usuarios.id, nome_completo, email, foto,
                        count(avaliadores.id) as qtd_trabalhos, 
                        avaliadores_areas.idarea,
                        GROUP_CONCAT(DISTINCT areas.area separator ';') as areas ");

        if ($idarea != null){
            $this->db->select("(select count(*) from avaliadores_areas 
            where avaliadores_areas.idusuario = usuarios.id 
            and avaliadores_areas.idarea = $idarea) as tem_area");
        }

        



        $this->db->join("avaliadores","avaliadores.idusuario = usuarios.id and avaliadores.deleted = 0","left");
        $this->db->join("avaliadores_areas","avaliadores_areas.idusuario = usuarios.id","left");
        $this->db->join("areas","avaliadores_areas.idarea = areas.id","left");

        $this->db->group_by("usuarios.id");
        if ($idarea != null){
            $this->db->order_by("tem_area desc");
            $this->db->order_by("nome_completo asc");
        }

        #print $this->db->get_compiled_select($this->table);
        #die();

        
        return $this->db->get_where($this->table,["usuarios.deleted"=>0, "nivel >="=>NIVEL_AVALIADOR])->result_array();
    }

    public function getAvaliadoresDisponiveis($idarea){
        $this->db->select("usuarios.id, count(avaliadores.id) as qtd");
        $this->db->join("avaliadores","avaliadores.idusuario = usuarios.id and avaliadores.deleted = 0","left");
        $this->db->join("avaliadores_areas","avaliadores_areas.idusuario = usuarios.id","inner");
        $this->db->where("usuarios.nivel",NIVEL_AVALIADOR);
        $this->db->where("avaliadores_areas.idarea",$idarea);
        $this->db->group_by("usuarios.id");
        $this->db->order_by("qtd");
        return $this->db->get($this->table)->result_array();
    }


}