<?php
require_once "AbstractModel.php";


class Coautor_model extends AbstractModel {

    protected $table = "coautores";
    #protected $logicExclusion = true;
    #não terá exclusão lógica, porque trabalha basicamente com chaves
    public $niveis = [0=>"Coautor", 1=>"Orientador"];


    private function _get($idTrab, $tipo){
        $this->db->select("coautores.*, usuarios.nome_completo, usuarios.email");
        $this->db->join("usuarios","usuarios.id = coautores.idusuario");
        $query = $this->db->get_where($this->table, ["idtrabalho"=>$idTrab, "tipo"=>$tipo, "coautores.deleted"=>0]);
        return $query->result_array();
    }

    public function getCoautores($idTrab){
        return $this->_get($idTrab, 0);
    }

    public function getOrientadores($idTrab){
        return $this->_get($idTrab, 1);
    }

    private function setar($idTrab, $idUsuario, $tipo){
        $existe = $this->db->get_where($this->table,["idtrabalho"=>$idTrab, 
                                                    "idusuario"=>$idUsuario, 
                                                    "tipo"=>$tipo]);
        if ($existe->num_rows() == 0){
            $this->db->insert($this->table,["idtrabalho"=>$idTrab, 
                                        "idusuario"=>$idUsuario, 
                                        "tipo"=>$tipo]);
        }
    }
    
    public function setarCoautor($idTrab, $idUsuario){
        $this->setar($idTrab,$idUsuario,0);
    }

    public function setarOrientador($idTrab, $idUsuario){
        $this->setar($idTrab,$idUsuario,1);
    }

    public function deleteVinculos($idTrab){
        if ($this->logicExclusion == false){
            $this->db->delete($this->table,["idtrabalho"=>$idTrab]);
        } else {
            $this->db->update($this->table,["deleted"=>1],["idtrabalho"=>$idTrab]);
        }
    }

    public function salvar_certificado($dados){

        $rw = $this->db->get_where($this->table, 
                        ["idtrabalho"=>$dados["idtrabalho"],
                        "idusuario"=>$dados["idusuario"]])->row_array();
        
        if ($rw != null){
            $dados["id"] = $rw['id'];
            return $this->salvar($dados);
        } else {
            return false;
        }

    }

    public function get_by_cert_hash($hash){
        return $this->db->get_where($this->table,["certificado"=>$hash])->row_array();
    }


    public function salvar_hash($id, $hash){
        return $this->db->update($this->table,["ciente_hash"=>$hash],["id"=>$id]);
    }

    public function get_by_ciente_hash($hash){
        return $this->db->get_where($this->table,["ciente_hash"=>$hash])->row_array();
    }

    public function registrar_ciencia_by_hash($hash){
        return $this->db->update($this->table,["ciente"=>true],["ciente_hash"=>$hash]);
    }

}