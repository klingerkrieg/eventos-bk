<?php
require_once "AbstractModel.php";


class Avaliador_model extends AbstractModel {

    protected $table = "avaliadores";
    protected $logicExclusion = true;

    public function salvar_avaliacao($dados){

        $saveData = [#"status"=>$dados["status"],
                    "observacao"=>$dados["observacao"],
                    "nota"=>$dados["nota"]];

        if ($saveData["nota"] > 100){
            $saveData["nota"] = 100;
        } else
        if ($saveData["nota"] < 0){
            $saveData["nota"] = 0;
        }

        return $this->db->update($this->table,$saveData,["id"=>$dados["id"],"idusuario"=>$_SESSION["admin_user"]["id"]]);
    }
    

    public function getAvaliadores($idTrab){
        $this->db->select("avaliadores.*, usuarios.nome_completo, usuarios.email");
        $this->db->join("usuarios","usuarios.id = avaliadores.idusuario");
        $query = $this->db->get_where($this->table, ["idtrabalho"=>$idTrab,
                                                    "avaliadores.deleted"=>0,
                                                    "usuarios.deleted"=>0]);
        return $query->result_array();
    }

    public function setarAvaliadores($dados){

        #deleta os avaliadores que foram removidos
        $this->db->where_not_in("idusuario",$dados["avaliador"]);
        $this->db->update($this->table,["deleted"=>1],["idtrabalho"=>$dados["id"]]);

        $CI =& get_instance();
        $CI->load->model('Evento_model');
        $evento = $CI->Evento_model->get(1);

        
        if (_v($dados,"avaliador") != ""){
            $dados["avaliador"] = array_slice($dados["avaliador"], 0, $evento["limite_avaliadores_trabalhos"]);
            
            foreach($dados["avaliador"] as $avaliador){
                if ($avaliador != "" && $avaliador != "0"){
                    $this->setarAvaliador($dados["id"], $avaliador);
                }
            }
        }

        return true;
    }

    public function setarAvaliador($idTrab, $idUsuario){
        $existe = $this->db->get_where($this->table,["idtrabalho"=>$idTrab, 
                                                    "idusuario"=>$idUsuario]);

        $av = $existe->row_array();
        if ($existe->num_rows() == 0){
            $this->db->insert($this->table,["idtrabalho"=>$idTrab, 
                                        "idusuario"=>$idUsuario]);
            return true;
        } else
        if ($av["deleted"]){
            $this->db->update($this->table,["deleted"=>0], 
                                        ["idtrabalho"=>$idTrab, 
                                        "idusuario"=>$idUsuario]);
            return true;
        }
        return false;
    }
    

    public function deleteVinculos($idTrab){
        if ($this->logicExclusion == false){
            $this->db->delete($this->table,["idtrabalho"=>$idTrab]);
        } else {
            $this->db->update($this->table,["deleted"=>1],["idtrabalho"=>$idTrab]);
        }
    }


}