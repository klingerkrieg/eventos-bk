<?php
require_once "AbstractModel.php";


class Instituicao_model extends AbstractModel {

    protected $table = "instituicoes";
    protected $filtros = ["instituicoes.instituicao"];
    protected $orderBy = "instituicoes.instituicao";

    public function getBySigla($sigla){
        return $this->db->get_where($this->table,["sigla"=>$sigla])->row_array();
    }
    

}