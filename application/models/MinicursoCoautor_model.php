<?php
require_once "AbstractModel.php";


class MinicursoCoautor_model extends AbstractModel {

    protected $table = "minicursos_coautores";
    #protected $logicExclusion = true;
    #não terá exclusão lógica, porque trabalha basicamente com chaves

    private function _get($idCurso, $tipo){
        $this->db->select("minicursos_coautores.*, usuarios.nome_completo, usuarios.email, usuarios.lattes,");
        $this->db->join("usuarios","usuarios.id = minicursos_coautores.idusuario");
        $query = $this->db->get_where($this->table, 
                        ["idminicurso"=>$idCurso, "minicursos_coautores.deleted"=>0]);
        return $query->result_array();
    }

    public function getCoautores($idCurso){
        return $this->_get($idCurso, 0);
    }


    private function setar($idCurso, $idUsuario){
        $existe = $this->db->get_where($this->table,["idminicurso"=>$idCurso, 
                                                    "idusuario"=>$idUsuario]);
        if ($existe->num_rows() == 0){
            $this->db->insert($this->table,["idminicurso"=>$idCurso, 
                                        "idusuario"=>$idUsuario]);
        }
    }
    
    public function setarCoautor($idCurso, $idUsuario){
        $this->setar($idCurso,$idUsuario,0);
    }


    public function deleteVinculos($idCurso){
        if ($this->logicExclusion == false){
            $this->db->delete($this->table,["idminicurso"=>$idCurso]);
        } else {
            $this->db->update($this->table,["deleted"=>1],["idminicurso"=>$idCurso]);
        }
    }

    public function salvar_certificado($dados){

        $rw = $this->db->get_where($this->table, 
                        ["idminicurso"=>$dados["idminicurso"],
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