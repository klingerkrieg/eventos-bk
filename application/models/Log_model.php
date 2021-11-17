<?php
require_once "AbstractModel.php";


class Log_model extends AbstractModel {

    protected $table = "log";
    protected $select = "log.*, usuarios.nome_completo";
    protected $joins = [["usuarios","usuarios.id = log.iduser","left"]];
    protected $filtros = ["log.descricao"];
    protected $orderBy = "data_hora";

    public function top(){
        $this->db->select($this->select);
        foreach($this->joins as $join){
            #tbl, cond, type
            $this->db->join($join[0],$join[1],$join[2]);
        }
        $this->db->limit(10);
        $this->db->order_by("data_hora","desc");
        return $this->db->get($this->table);
    }
    

}