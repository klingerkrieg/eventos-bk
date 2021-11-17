<?php

#class Db2 extends CI_DB {
class Db2 {

    private $db;
    private $lastId;
    private $userId = null;

    public function __construct($db){
        $this->db = $db;

    }

    function userid($id){
        $this->userId = $id;
    }

    function __call($name,$args){
        if ($name == "insert"){
            return $this->insert(...$args);
        } else
        if ($name == "update"){
            return $this->update(...$args);
        } else
        if ($name == "delete"){
            return $this->delete(...$args);
        } else
        if ($name == "insert_id"){
            return $this->insert_id();
        }
        return $this->db->{$name}(...$args);
    }

    protected function insert_id(){
        return $this->lastId;
    }

    protected function insert($table = '', $set = NULL, $escape = NULL){
        $r = $this->db->insert($table,$set,$escape);
        $this->lastId = $this->db->insert_id();
        $this->log("inseriu",$table);
        return $r;
    }

    protected function update($table = '', $set = NULL, $where = NULL, $limit = NULL){
        $r = $this->db->update($table, $set, $where, $limit);
        $this->log("atualizou",$table);
        return $r;
    }

    protected function delete($table = '', $where = '', $limit = NULL, $reset_data = true){
        $r = $this->db->delete($table, $where, $limit, $reset_data);
        $this->log("deletou",$table);
        return $r;
    }

    protected function log($cmd,$table,$iduser=null){

        $sql = $this->db->last_query();
        $desc = $cmd." em ".$table;
        if ($iduser == null){
            if (isset($_SESSION["user"])){
                $iduser = $_SESSION["user"]["id"];
            } else
            if (isset($_SESSION["admin_user"])){
                $iduser = $_SESSION["admin_user"]["id"];
            } else
            #se o iduser estiver vazio, é um usuário que está se cadastrando
            if ($table == "usuarios"){
                $iduser = $this->lastId;
            }
            if ($this->userId != null){
                $iduser = $this->userId;
            }
        }

        $data = ["iduser"=>$iduser, "descricao"=>$desc, "tabela"=>$table, "sql_code"=>$sql];
        $this->db->insert("log",$data);
    }

    
}

abstract class Logger extends CI_Model {

    public function __construct(){
        parent::__construct();

        $db = new Db2($this->db);
        $this->db = $db;
    }
    
    
}