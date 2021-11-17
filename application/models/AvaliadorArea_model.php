<?php
require_once "AbstractModel.php";


class AvaliadorArea_model extends AbstractModel {

    protected $table = "avaliadores_areas";

    public function getAreas($id=null){
        if ($id == null){
            $id = $_SESSION["admin_user"]["id"];
        }
        $this->db->select("areas.area, areas.id");
        $this->db->join("areas","areas.id = avaliadores_areas.idarea","inner");
        return $this->db->get_where($this->table,["idusuario"=>$id])->result_array();
    }

    public function minhasAreas($id=null){

        if ($id == null){
            $id = $_SESSION["admin_user"]["id"];
        }

        $arr = $this->db->get_where($this->table,["idusuario"=>$id])->result_array();
        $ret = [];
        foreach($arr as $row){
            array_push($ret,$row["idarea"]);
        }
        return $ret;
    }

    public function salvar_areas($dados){

        #deleta todas as minhas areas
        $this->db->where("idusuario",$_SESSION["admin_user"]["id"]);
        $this->db->delete($this->table);

        #salva as novas areas
        foreach($dados["areas"] as $area){
            $this->db->insert($this->table,["idusuario"=>$_SESSION["admin_user"]["id"], "idarea"=>$area]);
        }

        return true;
    }
    



}