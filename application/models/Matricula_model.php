<?php
require_once "AbstractModel.php";


class Matricula_model extends AbstractModel {

    protected $table = "matriculas";
    #protected $logicExclusion = true;
    #não terá exclusão lógica, porque trabalha basicamente com chaves
    protected $select = "matriculas.*, usuarios.nome_completo, usuarios.email ";
    protected $joins = [["usuarios","usuarios.id = matriculas.idusuario","left"]];


    public function cancelar($idMinicurso){
        $dados = ["idminicurso"=>$idMinicurso, "idusuario"=>$_SESSION['user']['id'], "certificado"=>null];
        return $this->db->delete($this->table, $dados);
    }

    public function getMatriculados($idMinicurso){
        $this->db->select($this->select);
        foreach($this->joins as $join){
            #tbl, cond, type
            $this->db->join($join[0],$join[1],$join[2]);
        }
        return $this->db->get_where($this->table,["idminicurso"=>$idMinicurso])->result_array();
    }

    public function getMinhasMatriculas(){
        $this->db->join("minicursos","minicursos.id = matriculas.idminicurso","left");
        return $this->db->get_where($this->table,["matriculas.idusuario"=>$_SESSION["user"]["id"]])->result_array();
    }

    public function temConflitoHorario($idMinicurso){
        $minhasMatriculas = $this->getMinhasMatriculas();
        $this->load->model("Minicurso_model");
        $minicurso = $this->Minicurso_model->get($idMinicurso);

        foreach($minhasMatriculas as $matr){
            $matr["horarios_escolhidos"] = json_decode($matr["horarios_escolhidos"]);

            $arr = array_intersect($matr["horarios_escolhidos"], $minicurso["horarios_escolhidos"]);
            if (count($arr) > 0){
                return true;
            }
            
        }

        return false;

    }


    public function matricular($idMinicurso){
        
        $dados = ["idminicurso"=>$idMinicurso, "idusuario"=>$_SESSION['user']['id']];
        $rw = $this->db->get_where($this->table, 
                        $dados)->row_array();

        #nao permite matricular mais do que a quantidade de vagas disponíveis
        $qtd = $this->qtdMatriculados($idMinicurso);

        #nao permite matricular se nao tiver pago a inscricao
        if ($_SESSION['user']['pago'] == false){
            return false;
        }

        #nao permite se matricular no próprio minicurso ou em minicurso que é coautor
        $this->load->model("Minicurso_model");
        $curso = $this->Minicurso_model->get($idMinicurso);
        if ($curso["idusuario"] == $_SESSION["user"]["id"]){
            return false;
        }
        foreach($curso["coautores"] as $usr){
            if ($usr["idusuario"] == $_SESSION["user"]["id"]){
                return false;
            }
        }
        

        $this->db->select("vagas");
        $vagas = $this->db->get_where("minicursos",["id"=>$idMinicurso])->row_array();
        
        if ($rw == null && ($qtd < $vagas['vagas']) || $vagas['vagas'] == ""){
            return $this->salvar($dados);
        }
        return false;
    }

    public function minhaMatricula($idMinicurso){
        $dados = ["idminicurso"=>$idMinicurso, "idusuario"=>$_SESSION['user']['id']];
        $rw = $this->db->get_where($this->table, 
                        $dados)->row_array();
        
        if ($rw == null){
            return false;
        }
        return $rw;
    }

    public function qtdMatriculados($idMinicurso){
        return $this->db->get_where($this->table,["idminicurso"=>$idMinicurso])->num_rows();
    }





    private function _salvar_diario($diario){
        $ok = true;

        foreach($diario['aprovado'] as $idmatricula=>$aprovado){
            if (isset($diario['presenca'])){
                $presenca = $diario['presenca'][$idmatricula];
            } else {
                $presenca = null;
            }
            $conf = $this->db->update($this->table,
                            ["presenca"=>$presenca,"aprovado"=>$aprovado],
                            ["id"=>$idmatricula]);
            
            if ($conf == false){
                $ok = false;
            }
        }
        return $ok;
    }

    #este método não verifica se é o autor do minicurso
    public function salvar_diario_adm($diario){
        return $this->_salvar_diario($diario);
    }


    public function salvar_diario($diario){

        #nao permite salvar um diario que nao é o autor
        $rw = $this->db->get_where("minicursos",
                        ["id"=>$diario['id'], 
                        "idusuario"=>$_SESSION['user']['id']])->row_array();
        
        if ($rw == null){
            return false;
        }

        return $this->_salvar_diario($diario);
    }



    public function salvar_certificado($idmatricula, $idusuario, $hash){
        $dados = ["id"=>$idmatricula, 
                    "idusuario"=>$idusuario, 
                    "certificado"=>$hash, 
                    "certificado_data"=>date('Y-m-d H:i_s')];

        $rw = $this->db->get_where($this->table,["id"=>$idmatricula, "idusuario"=>$idusuario])->row();
        
        #se encontrar a matricula, salva o certificado
        if ($rw != null){
            return parent::salvar($dados);
        }
    }


    public function aprovadosComCertificado($idMinicurso){

        $this->db->where("(certificado is null or certificado = '')");
        $rw = $this->db->get_where($this->table, 
                        ["idminicurso"=>$idMinicurso, 
                        "aprovado"=>1])->row();

        
        if ($rw == null){
            return true;
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
            $this->db->where("$this->table.certificado",$hash);

            $rs = $this->db->get($this->table);

            $matr = $rs->row_array();

            return $matr;
        }
        return null;
    }

}