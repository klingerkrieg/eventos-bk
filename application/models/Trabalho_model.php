<?php
require_once "AbstractModel.php";


class Trabalho_model extends AbstractModel {

    protected $table = "trabalhos";
    protected $logicExclusion = true;

    public $status = [PENDENTE=>PENDENTE_TXT, REPROVADO=>REPROVADO_TXT, APROVADO_CORRECOES_PENDENTES=>APROVADO_CORRECOES_PENDENTES_TXT, APROVADO=>APROVADO_TXT, APROVADO_CORRECOES=>APROVADO_CORRECOES_TXT];

    protected $select = "trabalhos.*, 
                        autor.nome_completo as nome_autor,
                        autor.email as email_autor,
                        autor.pago as pago,
                        areas.area as area,
                        (select count(*) from avaliadores where avaliadores.idtrabalho = trabalhos.id and avaliadores.deleted = 0) as avaliadores_qtd,
                        (select sum(nota)/avaliadores_qtd from avaliadores where avaliadores.idtrabalho = trabalhos.id and avaliadores.deleted = 0) as nota,
                        gts.gt as gt ";
    protected $joins = [["usuarios autor","autor.id = trabalhos.idusuario ","left"],
                        ["gts","gts.id = idgt ","left"],
                        ["areas","areas.id = idarea ","left"],
                        //["avaliadores","avaliadores.idtrabalho = trabalhos.id and avaliadores.deleted = 0 ","left"],
                    ];
    protected $filtros = ["trabalhos.titulo"];
    protected $orderBy = "trabalhos.titulo";

    public $tiposTrabalhos = [1=>"Artigo",2=>"Poster"];

    public $trilhas = [1=>"Projeto de extensão", 2=>"Projeto integrador", 3=>"Projeto de pesquisa", 4=>"TCC", 5=>"RPP"];
    

    public function listar($dados=[]){

        $dados['filtro_equals'] = [];

        if (isset($_GET["status"]) && $_GET["status"] != "0"){
            $dados['filtro_equals']["trabalhos.status"] = $_GET["status"];
        }

        if (isset($_GET["idarea"]) && $_GET["idarea"] != "0"){
            $dados['filtro_equals']["trabalhos.idarea"] = $_GET["idarea"];
        }

        if (isset($_GET["idtrilha"]) && $_GET["idtrilha"] != "0"){
            $dados['filtro_equals']["trabalhos.idtrilha"] = $_GET["idtrilha"];
        }

        #so apresenta os trabalhos onde sou responsável
        if ($_SESSION["admin_user"]["nivel"] == NIVEL_AVALIADOR || _v($_GET,"avaliador")){
            $dados['filtro_equals']["avaliadores.idusuario"] = $_SESSION["admin_user"]["id"];
        }


        $dados['distinct'] = true;
        $dados['group_by'] = "trabalhos.id, avaliadores_qtd";

        $lista = parent::listar($dados);

        
        $CI =& get_instance();
        $CI->load->model('Avaliador_model');
        foreach($lista["dados"] as $k=>$ln){
            $lista["dados"][$k]["avaliadores"] = $CI->Avaliador_model->getAvaliadores($lista["dados"][$k]['id']);

            if (_v($dados,"getCoautores")){
                $CI->load->model('Coautor_model');
                $lista["dados"][$k]['coautores'] = $CI->Coautor_model->getCoautores($lista["dados"][$k]['id']);
                $lista["dados"][$k]['orientadores'] = $CI->Coautor_model->getOrientadores($lista["dados"][$k]['id']);
            }
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

            $trab = $rs->row_array();

            if ($trab == null){
                return null;
            }

            #seta os coautores
            $CI =& get_instance();
            $CI->load->model('Coautor_model');
            $trab['coautores'] = $CI->Coautor_model->getCoautores($trab['id']);
            $trab['orientadores'] = $CI->Coautor_model->getOrientadores($trab['id']);

            $CI->load->model('Avaliador_model');
            $trab['avaliadores'] = $CI->Avaliador_model->getAvaliadores($trab['id']);

            return $trab;
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

            $trab = $rs->row_array();

            if ($trab == null){
                return null;
            }

            #seta os coautores
            $CI =& get_instance();
            $CI->load->model('Coautor_model');
            $trab['coautores'] = $CI->Coautor_model->getCoautores($trab['id']);
            $trab['orientadores'] = $CI->Coautor_model->getOrientadores($trab['id']);

            return $trab;
        }
        return null;
    }


    public function meusTrabalhos($incluindoCoautoria=true){
        
        $this->db->distinct();
        $this->db->select($this->select);

        $joins = $this->joins;
        array_push($joins, ["coautores","coautores.idtrabalho = trabalhos.id ","left"]);

        foreach($joins as $join){
            #tbl, cond, type
            $this->db->join($join[0],$join[1],$join[2]);
        }
        if ($this->logicExclusion) {
            $this->db->where("$this->table.deleted",0);
        }
        #só permite ver os seus trabalhos
        $this->db->group_start();
        $this->db->or_where("trabalhos.idusuario",$_SESSION['user']["id"]);
        if ($incluindoCoautoria){
            $this->db->or_where("coautores.idusuario",$_SESSION['user']["id"]);
        }
        $this->db->group_end();
        
        $query = $this->db->get($this->table);

        $trabs = [];
        $CI =& get_instance();
        $CI->load->model('Coautor_model');
        while($trab = $query->unbuffered_row('array')){
            $trab['coautores'] = $CI->Coautor_model->getCoautores($trab['id']);
            $trab['orientadores'] = $CI->Coautor_model->getOrientadores($trab['id']);

            $CI->load->model('Avaliador_model');
            $trab['avaliadores'] = $CI->Avaliador_model->getAvaliadores($trab['id']);
            
            array_push($trabs, $trab);
        }
        return $trabs;
    }


    /**
     * Usado pela equipe/admin
     */
    public function salvar_avaliadores($dados){
        $trab = ["apresentado"=>$dados["apresentado"],
                "idarea"=>$dados["idarea"], 
                "idtrilha"=>$dados["idtrilha"], 
                "status"=>$dados["status"], 
                "premiado"=>$dados["premiado"], 
                "observacao"=>$dados["observacao"], 
                "id"=>$dados["id"]];

        $trabOrig = $this->db->get_where($this->table,["id"=>$trab["id"]])->row_array();

        $newStatus = false;
        if ($trabOrig["status"] != $dados["status"] && $dados["status"] != PENDENTE){
            $newStatus = $this->status[$dados["status"]];
        }

        $id = parent::salvar($trab);

        $CI =& get_instance();
        $CI->load->model('Avaliador_model');
        $CI->Avaliador_model->setarAvaliadores($dados);

        return [$id, $newStatus];
    }

    /*public function salvar_correcao($dados){
        $trab = ["observacao"=>$dados["observacao"],
                "status"=>$dados["status"], 
                "id"=>$dados["id"], 
                "apresentado"=>$dados["apresentado"]];
        return parent::salvar($trab);
    }*/

    public function salvar_certificado($idtrabalho, $idusuario, $hash){
        $dados = ["id"=>$idtrabalho, "idusuario"=>$idusuario, "certificado"=>$hash, "certificado_data"=>date('Y-m-d H:i_s')];

        $rw = $this->db->get_where($this->table,["id"=>$idtrabalho, "idusuario"=>$idusuario])->row();
        #se encontrar é para salvar o certificado do autor principal
        if ($rw != null){
            return parent::salvar($dados);
        } else {
            #se nao é o certificado dos coautores
            $CI =& get_instance();
            $CI->load->model('Coautor_model');
            $dados["idtrabalho"] = $dados["id"];
            unset($dados["id"]);
            return $CI->Coautor_model->salvar_certificado($dados);
        }
        
    }
    




    /**
     * Usado pelos participantes
     */

    public function salvar_submissao($dados){

        $trab = ["titulo"=>$dados["titulo"], 
                "url"=>$dados["url"], 
                "idgt"=>$dados["idgt"], 
                "idusuario"=>$_SESSION['user']["id"],
                #"idtipo_trabalho"=>$dados["idtipo_trabalho"],
                "idarea"=>$dados["idarea"],
                "idtrilha"=>$dados["idtrilha"]];
        
        if ( $dados["arquivo"] != ""){
            $trab["arquivo"] = $dados["arquivo"];
        }

        $trab["status"] = PENDENTE;

        if ($dados["url"] != "" && strstr($dados["url"],"http") == false){
            $trab["url"] = "https://".$trab["url"];
        }

        $CI =& get_instance();
        $CI->load->model('Usuario_model');


        $CI->load->model('Evento_model');
        $evento = $CI->Evento_model->getEvento();
        

        
        #obedece ao limite de coautores e orientadores
        $orientadores = [];
        $count = 0;
        foreach($dados["email_orientador"] as $key=>$email){
            if ($count == $evento['limite_orientadores']){
                break;
            }

            #nao permite se adicionar como orientador
            if ($email == $_SESSION["user"]["email"]){
                continue;
            }

            $nome = $dados["nome_orientador"][$key];
            array_push($orientadores, $CI->Usuario_model->getOrCreate($nome,$email));
            $count++;
        }
        
        $coautores = [];
        $count = 0;
        foreach($dados["email_coautor"] as $key=>$email){
            if ($count == $evento['limite_coautores']){
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
        if ($evento['aceitando_submissoes'] == false && $evento['aceitando_correcoes'] == false){
            return false;
        }

        $meus_trabalhos = $this->meusTrabalhos(false);
        $idTrab = false;
        #nao permite salvar se ja tiver submetido o limite maximo
        if (count($meus_trabalhos) < $evento['limite_submissoes']){
            $idTrab = $this->salvar($trab);
            $CI->load->model('Coautor_model');

            #deleta todos os vinculos (Necessário para exclusão de algum coautor)
            $CI->Coautor_model->deleteVinculos($idTrab);


            #refaz todos os vínculos
            foreach($orientadores as $idOri){
                $CI->Coautor_model->setarOrientador($idTrab, $idOri);
            }

            foreach($coautores as $idCo){
                $CI->Coautor_model->setarCoautor($idTrab, $idCo);
            }
        }

        return $idTrab;
    }

    public function salvar_trabalho_corrigido($post){
        $dados = ["id"=>$post["id"],
                "idusuario"=>$_SESSION['user']["id"],
                "url"=>$post["url"],
                "correcao"=>Date('Y-m-d H:i:s')];
        
        if ($post["arquivoCorrigido"] != ""){
            $dados["arquivoCorrigido"] = $post["arquivoCorrigido"];
        }

        if ($dados["url"] != "" && strstr($dados["url"],"http") == false){
            $dados["url"] = "https://".$dados["url"];
        }

        
        $CI =& get_instance();
        $CI->load->model('Evento_model');
        $evento = $CI->Evento_model->getEvento();

        $rs = $this->db->get_where($this->table,["id"=>$dados["id"],"idusuario"=>$dados["idusuario"]]);
        $trab = $rs->row_array();

        if ($trab['status'] != REPROVADO){
            return $this->salvar($dados);
        }
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
            #não permite alterar um trabalho que não é seu
            $this->db->update($this->table, $dados,["id"=>$id, "idusuario"=>$_SESSION['user']["id"]]);
            return $id;
        }

    }


    public function admin_deletar($id){

        #desfaz os vínculos
        $CI =& get_instance();
        $CI->load->model('Coautor_model');
        $CI->Coautor_model->deleteVinculos($id);
        
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
        $query = $this->db->get_where($this->table,["id"=>$id, "idusuario"=>$_SESSION['user']["id"]]);
        $row = $query->row_array();
        if ($row == null){
            return false;
        }
        
        #desfaz os vínculos
        $CI =& get_instance();
        $CI->load->model('Coautor_model');
        $CI->Coautor_model->deleteVinculos($id);
        
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



    /*public function getAndUpdateStatus($idTrab){

        $trab = $this->get($idTrab);

        $qtd_avaliadores = count($trab["avaliadores"]);

        $reprovado = 0;
        $correcoes = 0;
        $correcoes_realizadas = 0;
        $aprovado = 0;
        $pendente = 0;

        foreach($trab["avaliadores"] as $av){
            if ($av["status"] == REPROVADO){
                $reprovado++;
            } else
            if ($av["status"] == APROVADO_CORRECOES_PENDENTES){
                $correcoes++;
            } else
            if ($av["status"] == APROVADO_CORRECOES){
                $correcoes_realizadas++;
            } else 
            if ($av["status"] == APROVADO){
                $aprovado++;
            } else {
                $pendente++;
            }

        }

        $atualizacao = [];

        $newStatus = "";

        #se a maioria ou igual tiver votado por reprovado
        if ($reprovado >= ceil($qtd_avaliadores/2)){

            #se ja estava como reprovado retorna false para dizer que não houve modificações
            #mudança em notas não são alertadas
            if ($trab["status"] == REPROVADO){
                return false;
            }
            $newStatus = REPROVADO_TXT;

            #atualiza no banco
            $atualizacao["status"] = REPROVADO;
            #a atualizacao sempre é feita, pois a nota pode ter mudado
            $this->db->update($this->table,$atualizacao,["id"=>$idTrab]);

            

        } else if ($pendente > 0){
            #se tiver 1 pendente o trabalho permanece pendente

            if ($trab["status"] == PENDENTE){
                return false;
            }
            $newStatus = PENDENTE_TXT;

            $atualizacao["status"] = PENDENTE;
            $this->db->update($this->table,$atualizacao,["id"=>$idTrab]);

            
        } else if ($correcoes > 0){
            #se tiver 1 com correções pendentes o trabalho ganha esse status

            if ($trab["status"] == APROVADO_CORRECOES_PENDENTES){
                return false;
            }
            $newStatus = APROVADO_CORRECOES_PENDENTES_TXT;

            #se alguem tiver votado com correcoes pendentes
            $atualizacao["status"] = APROVADO_CORRECOES_PENDENTES;
            $this->db->update($this->table,$atualizacao,["id"=>$idTrab]);

            

        } else if ($correcoes_realizadas > 0){
            #se tiver 1 com correções realizadas o trabalho ganha esse status

            if ($trab["status"] == APROVADO_CORRECOES){
                return false;
            }
            $newStatus = APROVADO_CORRECOES_TXT;


            #se alguem tiver votado com correcoes finalizadas
            $atualizacao["status"] = APROVADO_CORRECOES;
            $this->db->update($this->table,$atualizacao,["id"=>$idTrab]);

            

        } else if ($aprovado == count($trab["avaliadores"])){
            #caso nao seja nenhum outro a quantidade de aprovados será igual ao de avaliadores

            if ($trab["status"] == APROVADO){
                return false;
            }
            $newStatus = APROVADO_TXT;

            #se alguem tiver votado com correcoes finalizadas
            $atualizacao["status"] = APROVADO;
            $this->db->update($this->table,$atualizacao,["id"=>$idTrab]);

            
        }

        return $newStatus;
    }*/



    public function distribuir_trabalhos(){

        if ($_SESSION["admin_user"]["nivel"] < NIVEL_EQUIPE){
            return false;
        }
        
        $this->db->select("trabalhos.id, trabalhos.idarea, count(avaliadores.id) as qtd ");
        $this->db->join("avaliadores","avaliadores.idtrabalho = trabalhos.id and avaliadores.deleted = 0","left");
        $this->db->where("trabalhos.deleted = 0");
        $this->db->group_by("trabalhos.id");
        $this->db->having("qtd < 2");
        $arr = $this->db->get($this->table)->result_array();

        #para cada trabalho procura dois avaliadores que
        #estejam com a menor quantidade de trabalhos para avaliar
        #e que seja da area
        $CI =& get_instance();
        $CI->load->model('Usuario_model');
        $CI->load->model('Avaliador_model');

        $CI->load->model('Evento_model');
        $evento = $CI->Evento_model->get(1);

        $distribuidos = 0;

        foreach($arr as $trab){

            $avs = $CI->Usuario_model->getAvaliadoresDisponiveis($trab["idarea"],$trab["id"]);
            #quantidade atual de avaliadores neste trabalho
            $qtd = $trab["qtd"];
            #inicia do avaliador 0 da lista;
            #enquanto a quantidade for abaixo da determinada
            #e enquanto houver avaliador disponivel;
            #tenta inserir o proximo avaliador
            for($avIndex = 0; $qtd < $evento["limite_avaliadores_trabalhos"] && $avIndex < count($avs); $avIndex++){
                #se retornar true é porque foi inserido
                if ($CI->Avaliador_model->setarAvaliador($trab["id"], $avs[$avIndex]["id"])){
                    $qtd++;
                    $distribuidos++;
                }
            }

        }

        return $distribuidos;

    }


    public function getCertificados($idUsuario){
        $this->db->select("certificado, titulo");
        $cert1 = $this->db->get_where("trabalhos",["idusuario"=>$idUsuario])->result_array();

        $this->db->select("coautores.certificado, titulo");
        $this->db->join("trabalhos","trabalhos.id = coautores.idtrabalho","join");
        $cert2 = $this->db->get_where("coautores",["coautores.idusuario"=>$idUsuario])->result_array();

        $all = array_merge($cert1, $cert2);
        
        return $all;
    }


}