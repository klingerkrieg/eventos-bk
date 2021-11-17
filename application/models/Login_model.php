<?php

class Login_model extends CI_Model {

    protected $table = "usuarios";

    function logar($email,$senha){

        $senha = sha1($senha);
        $sql = "select * from usuarios where email = ? and password = ? and deleted = 0";
        $q = $this->db->query($sql, [$email, $senha]);
        return $q->row_array();

    }

    

}