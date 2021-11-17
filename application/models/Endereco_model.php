<?php
require_once "AbstractModel.php";


class Endereco_model extends AbstractModel {

    #aqui existe uma vulnerabilidade
    #o correto era cadastrar o endereco que vem do WS consultado pelo PHP
    #mas o servidor do IFRN nao vai permitir isso
    #entao eu estou cadastrando o que vem do formulário.
    public function getOrCreate($dados){

        $iduf = $this->_getOrCreate("ufs","uf",$dados["uf"],"idpais",1);
        $idcidade = $this->_getOrCreate("cidades","cidade",$dados["cidade"],"iduf",$iduf);
        $idbairro = $this->_getOrCreate("bairros","bairro",$dados["bairro"],"idcidade",$idcidade);

        return ["iduf"=>$iduf,"idcidade"=>$idcidade,"idbairro"=>$idbairro];
    }

    private function _getOrCreate($table, $field, $value, $estrangeiraField, $estrangeiraValor){

        $this->db->where($field,$value);
        $this->db->where($estrangeiraField,$estrangeiraValor);
        $rs = $this->db->get($table);
        
        $row = $rs->row_array();
        if ($row == null){
            $this->db->insert($table,[$field=>$value,$estrangeiraField=>$estrangeiraValor]);
            return $this->db->insert_id();
        } else {
            return $row["id"];
        }
    }
    


}