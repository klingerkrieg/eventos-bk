<?php
require_once "AbstractModel.php";


class Minicurso_model extends AbstractModel {

    protected $table = "minicursos";
    protected $logicExclusion = true;

    public $status = [PENDENTE=>PENDENTE_TXT, REPROVADO=>REPROVADO_TXT, APROVADO=>APROVADO_TXT];

    protected $select = "minicursos.*, 
                        autor.nome_completo as nome_autor,
                        autor.email as email_autor,
                        autor.pago as pago,
                        autor.lattes as lattes,
                        autor.curriculo as curriculo,
                        autor.telefone as telefone,
                        areas.area as area,
                        grandes_areas.grande_area as grande_area ";
    protected $joins = [["usuarios autor","autor.id = minicursos.idusuario ","left"],
                        ["areas","areas.id = idarea ","left"],
                        ["grandes_areas","grandes_areas.id = areas.idgrandearea ","left"]
                        ];
    protected $filtros = ["minicursos.titulo"];
    protected $orderBy = "minicursos.titulo";

    #é preciso setar o null nos horários que não existem nos demais dias
    #para melhor exibição na view
    public $datasHorarios = ["2021-04-12"=>[null,    "13:30", null, "20:00"],
                            "2021-04-13"=>["08:00", "13:30", "17:00", "20:00"],
                            "2021-04-14"=>["08:00"]];

    public $chs = [1.5=>"1h30", 3=>"3h"];
    public $vagas = [5,10,15,20,25,30,35,40];
    
    public $turnos = ["Matutino"=>[1=>"1° Turno 8h às 10h", 2=>"2° Turno 10h às 12h"],
                  "Vespertino"=>[3=>"1° Turno 14h às 16h",4=>"2° Turno 16h às 18h"],
                  "Noturno"=>[5=>"1° Turno 19h às 20h30", 6=>"2° Turno 20h30 às 22h"]];


    public function all(){
        $lista = parent::all();
        $CI =& get_instance();
        $CI->load->model('MinicursoCoautor_model');
        for ($i = 0; $i < count($lista); $i++){ 
            $lista[$i]["horarios_preferenciais"]    = json_decode($lista[$i]["horarios_preferenciais"]);
            $lista[$i]["horarios_escolhidos"]       = json_decode($lista[$i]["horarios_escolhidos"]);

            $lista[$i]['coautores'] = $CI->MinicursoCoautor_model->getCoautores($lista[$i]['id']);
        }

        return $lista;
    }


    public function listar($dados=[]){
        if (isset($_GET["status"]) && $_GET["status"] != ""){
            $dados['filtro_equals'] = ["status"=> $_GET["status"]];
        }

        $lista = parent::listar($dados);

        $CI =& get_instance();
        $CI->load->model('Matricula_model');
        foreach($lista['dados'] as $k=>$curso){
            $lista['dados'][$k]['qtdMatriculados']          = $CI->Matricula_model->qtdMatriculados($curso['id']);
            $lista['dados'][$k]['aprovadosComCertificado']  = $CI->Matricula_model->aprovadosComCertificado($curso['id']);
        }

        return $lista;

    }

    function get($id){
        
        if ($id != null){
            $this->db->select($this->select);
            foreach($this->joins as $join){
                #tbl, cond, type
                $this->db->join($join[0],$join[1],$join[2]);
            }
            if ($this->logicExclusion) {
                $this->db->where("$this->table.deleted",0);
            }
            $this->db->where("$this->table.id",$id);

            $rs = $this->db->get($this->table);

            $curso = $rs->row_array();

            if ($curso == null){
                return null;
            }

            #seta os coautores
            $CI =& get_instance();
            $CI->load->model('MinicursoCoautor_model');
            $curso['coautores'] = $CI->MinicursoCoautor_model->getCoautores($curso['id']);


            #busca os alunos matriculados
            $CI->load->model('Matricula_model');
            $curso['matriculados'] = $CI->Matricula_model->getMatriculados($curso['id']);

            #decodifica o json
            $curso["horarios_preferenciais"] = json_decode($curso["horarios_preferenciais"]);
            $curso["horarios_escolhidos"] = json_decode($curso["horarios_escolhidos"]);
            if ($curso["horarios_escolhidos"] == null){
                $curso["horarios_escolhidos"] = [];
            }

            return $curso;
        }
        return null;
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
            $this->db->where("$this->table.certificado",$hash);

            $rs = $this->db->get($this->table);

            $curso = $rs->row_array();

            if ($curso == null){
                return null;
            }

            #seta os coautores
            $CI =& get_instance();
            $CI->load->model('MinicursoCoautor_model');
            $curso['coautores'] = $CI->MinicursoCoautor_model->getCoautores($curso['id']);

            return $curso;
        }
        return null;
    }


    public function meusMinicursos($incluindoCoautoria=true){
        
        $this->db->select($this->select);
        $this->db->distinct();
        $joins = $this->joins;
        array_push($joins, ["minicursos_coautores","minicursos_coautores.idminicurso = minicursos.id ","left"]);
        
        foreach($joins as $join){
            #tbl, cond, type
            $this->db->join($join[0],$join[1],$join[2]);
        }
        if ($this->logicExclusion) {
            $this->db->where("$this->table.deleted",0);
        }
        #só permite ver os seus minicursos
        $this->db->group_start();
        $this->db->or_where("minicursos.idusuario",$_SESSION['user']["id"]);
        if ($incluindoCoautoria){
            $this->db->or_where("minicursos_coautores.idusuario",$_SESSION['user']["id"]);
        }
        $this->db->group_end();
        
        $query = $this->db->get($this->table);

        $cursos = [];
        $CI =& get_instance();
        $CI->load->model('MinicursoCoautor_model');
        while($curso = $query->unbuffered_row('array')){
            $curso['coautores'] = $CI->MinicursoCoautor_model->getCoautores($curso['id']);
            array_push($cursos, $curso);
        }
        return $cursos;
    }

    /**
     * Usado pelos avaliadores
     */
    public function salvar_correcao($dados){

        $curso = ["observacao"=>$dados["observacao"],
                    "status"=>$dados["status"], 
                    "id"=>$dados["id"], 
                    "idarea"=>$dados["idarea"], 
                    "vagas"=>$dados["vagas"],
                    "url"=>$dados["url"],
                    "matricula_disponivel"=>$dados["matricula_disponivel"],
                    "descricao"=>$dados["descricao"],
                    "ch"=>$dados["ch"]];

        if (!isset($dados["horarios_escolhidos"])){
            $dados["horarios_escolhidos"] = [];
        }
        sort($dados["horarios_escolhidos"]);
        $curso["horarios_escolhidos"] = json_encode($dados["horarios_escolhidos"]);

        if ($dados["url"] != "" && strstr($dados["url"],"http") == false){
            $curso["url"] = "http://".$curso["url"];
        }

        if ($curso["status"] == REPROVADO){
            $curso["matricula_disponivel"] = 0;
        }

        return parent::salvar($curso);
    }

    public function salvar_certificado($idminicurso, $idusuario, $hash){
        $dados = ["id"=>$idminicurso, 
                    "idusuario"=>$idusuario, 
                    "certificado"=>$hash, 
                    "certificado_data"=>date('Y-m-d H:i_s')];

        $rw = $this->db->get_where($this->table,["id"=>$idminicurso, "idusuario"=>$idusuario])->row();
        #se encontrar é para salvar o certificado do autor principal
        if ($rw != null){
            return parent::salvar($dados);
        } else {
            #se nao é o certificado dos coautores
            $CI =& get_instance();
            $CI->load->model('MinicursoCoautor_model');
            $dados["idminicurso"] = $dados["id"];
            unset($dados["id"]);
            return $CI->MinicursoCoautor_model->salvar_certificado($dados);
        }
        
    }
    




    /**
     * Usado pelos participantes
     */

    public function disponiveis(){
        $this->db->select($this->select);
        foreach($this->joins as $join){
            #tbl, cond, type
            $this->db->join($join[0],$join[1],$join[2]);
        }
        if ($this->logicExclusion) {
            $this->db->where("$this->table.deleted",0);
        }
        #só permite ver os seus minicursos
        $this->db->where("minicursos.matricula_disponivel",1);
        
        $query = $this->db->get($this->table);

        #busca os coautores, quantidade de matriculas e se está matriculado
        $cursos = [];
        $CI =& get_instance();
        $CI->load->model('MinicursoCoautor_model');
        $CI->load->model('Matricula_model');

        while($curso = $query->unbuffered_row('array')){
            $curso['coautores'] = $CI->MinicursoCoautor_model->getCoautores($curso['id']);

            #nao exibirá minicursos onde sou autor/coautor
            if ($curso["idusuario"] == $_SESSION["user"]["id"]){
                continue;
            }
            foreach($curso['coautores'] as $usr){
                if ($usr["idusuario"] == $_SESSION["user"]["id"]){
                    continue 2;
                }
            }

            $curso['minhaMatricula']      = $CI->Matricula_model->minhaMatricula($curso['id']);
            $curso['qtdMatriculados']     = $CI->Matricula_model->qtdMatriculados($curso['id']);

            $curso["horarios_preferenciais"]  = json_decode($curso["horarios_preferenciais"]);
            $curso["horarios_escolhidos"]     = json_decode($curso["horarios_escolhidos"]);

            array_push($cursos, $curso);
        }

        

        return $cursos;
    }



    public function salvar_submissao($dados){

        $curso = ["titulo"=>$dados["titulo"], 
                "idarea"=>$dados["idarea"], 
                "idusuario"=>$_SESSION['user']["id"],
                "ch"=>$dados["ch"],
                "vagas"=>$dados["vagas"],
                "resumo"=>$dados["resumo"],
                "objetivo"=>$dados["objetivo"],
                "informacoes_adicionais"=>$dados["informacoes_adicionais"]];

        if (isset($dados["id"])){
            $curso["id"] = $dados["id"];
        }
        
        if ( _v($dados,"arquivo") != ""){
            $curso["arquivo"] = $dados["arquivo"];
        }

        $curso["horarios_preferenciais"] = json_encode($dados["horarios_preferenciais"]);
        $curso["status"] = PENDENTE;
        
        $CI =& get_instance();
        $CI->load->model('Usuario_model');


        $CI->load->model('Evento_model');
        $evento = $CI->Evento_model->getEvento();
        

        
        #obedece ao limite de coautores
        $coautores = [];
        $count = 0;
        foreach($dados["email_coautor"] as $key=>$email){
            if ($count == $evento['limite_coautores_minicursos']){
                break;
            }

            #nao permite se adicionar como coautor
            if ($email == $_SESSION["user"]["email"]){
                continue;
            }

            $nome = $dados["nome_coautor"][$key];
            array_push($coautores, $CI->Usuario_model->getOrCreate($nome,$email));
            $count++;
        }
        
        
        
        
        #nao permite salvar se nao estiver aceitando submissoes
        if ($evento['aceitando_submissoes_minicursos'] == false 
                && $evento['aceitando_correcoes_minicursos'] == false){
            return false;
        }

        $meus_cursos = $this->meusMinicursos(false);
        $idCurso = false;
        #nao permite salvar se ja tiver submetido o limite maximo
        if (count($meus_cursos) < $evento['limite_submissoes_minicursos'] || $curso["id"] != ""){
            $idCurso = $this->salvar($curso);
            $CI->load->model('MinicursoCoautor_model');

            #deleta todos os vinculos (Necessário para exclusão de algum coautor)
            $CI->MinicursoCoautor_model->deleteVinculos($idCurso);


            #refaz todos os vínculos
            foreach($coautores as $idCo){
                $CI->MinicursoCoautor_model->setarCoautor($idCurso, $idCo);
            }
        }

        
        return $idCurso;
    }

    

    public function salvar($dados){

        if (!isset($dados["id"])){
            $dados["id"] = null;
        }
        $id = $dados["id"];
        unset($dados["id"]);

        foreach($dados as $k=>$v){
            if ($v == ""){
                $dados[$k] = null;
            }
        }

        if ($id == ""){
            $rs = $this->db->insert($this->table, $dados);
            return $this->db->insert_id();
        } else {
            #não permite alterar um minicurso que não é seu
            $this->db->update($this->table, $dados,
                                    ["id"=>$id, 
                                    "idusuario"=>$_SESSION['user']["id"]]);
            return $id;
        }

    }

    public function salvar_url($dados){
        #nao permite salvar um trabalho que nao é seu
        $query = $this->db->get_where($this->table,
                                        ["id"=>$dados["id"], 
                                        "idusuario"=>$_SESSION['user']["id"]]);
        $row = $query->row();
        if ($row == null){
            return false;
        }
        if ($dados["url"] != "" && strstr($dados["url"],"http") == false){
            $dados["url"] = "http://".$dados["url"];
        }
        return $this->db->update($this->table, 
                                ["url"=>$dados["url"]], 
                                #where
                                ["id"=>$dados["id"],
                                "idusuario"=>$_SESSION['user']["id"]]);
    }


    public function admin_deletar($id){

        #desfaz os vínculos
        $CI =& get_instance();
        $CI->load->model('MinicursoCoautor_model');
        $CI->MinicursoCoautor_model->deleteVinculos($id);
        
        #deleta o trabalho
        if ($this->logicExclusion == false){
            $this->db->delete($this->table,["id"=>$id]);
        } else {
            $this->db->update($this->table,
                    ["deleted"=>1],
                    #where
                    ["id"=>$id]);
        }
    }



    public function deletar($id){

        #nao permite deletar um trabalho que nao é seu
        $query = $this->db->get_where($this->table,
                                        ["id"=>$id, 
                                        "idusuario"=>$_SESSION['user']["id"]]);
        $row = $query->row();
        if ($row == null){
            return false;
        }
        
        #desfaz os vínculos
        $CI =& get_instance();
        $CI->load->model('MinicursoCoautor_model');
        $CI->MinicursoCoautor_model->deleteVinculos($id);
        
        #deleta o trabalho
        if ($this->logicExclusion == false){
            $this->db->delete($this->table,["id"=>$id,"idusuario"=>$_SESSION['user']["id"]]);
        } else {
            $this->db->update($this->table,
                        ["deleted"=>1],
                        #where
                        ["id"=>$id,
                        "idusuario"=>$_SESSION['user']["id"]]);
        }
    }





    public function clonar($idminicurso){

        $dados = $this->get($idminicurso);
        $coautores = $dados["coautores"];
        
        unset($dados["nome_autor"]);
        unset($dados["email_autor"]);
        unset($dados["pago"]);
        unset($dados["lattes"]);
        unset($dados["curriculo"]);
        unset($dados["telefone"]);
        unset($dados["area"]);
        unset($dados["grande_area"]);
        unset($dados["matriculados"]);
        unset($dados["coautores"]);
        unset($dados["id"]);
        unset($dados["certificado"]);
        unset($dados["certificado_data"]);
        $dados["horarios_preferenciais"] = json_encode($dados["horarios_preferenciais"]);
        $dados["horarios_escolhidos"] = json_encode($dados["horarios_escolhidos"]);
        $this->db->insert($this->table,$dados);
        $idClone = $this->db->insert_id();

        print $idClone;


        $CI =& get_instance();
        $CI->load->model('MinicursoCoautor_model');
        foreach($coautores as $coautor){
            $CI->MinicursoCoautor_model->setarCoautor($idClone,$coautor["idusuario"]);
        }
    

    }



    function temMinicursosDisponiveis(){
        $this->db->select("count(id) as qtd");
        $this->db->where("matricula_disponivel",true);
        $res = $this->db->get_where($this->table)->row_array();
        return $res['qtd'];
    }


    public function getCertificados($idUsuario){
        $this->db->select("certificado, titulo");
        $cert1 = $this->db->get_where("minicursos",["idusuario"=>$idUsuario])->result_array();

        $this->db->select("minicursos_coautores.certificado, titulo");
        $this->db->join("minicursos","minicursos.id = minicursos_coautores.idminicurso","join");
        $cert2 = $this->db->get_where("minicursos_coautores",["minicursos_coautores.idusuario"=>$idUsuario])->result_array();

        $all = array_merge($cert1, $cert2);
        
        return $all;
    }




}