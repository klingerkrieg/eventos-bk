<?php
require_once "Logger.php";


class AbstractModel extends Logger {

    protected $table = null;
    protected $select = "*";
    protected $joins = [];
    protected $logicExclusion = false;
    protected $filtros = [];
    protected $orderBy = null;

    function get($id){
        
        if ($id != null){
            $this->db->select($this->select);
            foreach($this->joins as $join){
                #tbl, cond, type
                $this->db->join($join[0],$join[1],$join[2]);
            }
            if ($this->logicExclusion) {
                $this->db->where("$this->table.deleted",0);
            }
            $this->db->where("$this->table.id",$id);

            $rs = $this->db->get($this->table);
            return $rs->row_array();
        }
        return null;
    }

    public function all(){
        
        $this->db->select($this->select);
        foreach($this->joins as $join){
            #tbl, cond, type
            $this->db->join($join[0],$join[1],$join[2]);
        }
        
        
        if ($this->logicExclusion) {
            $this->db->where("$this->table.deleted",0);
        }
        
        $rs = $this->db->get($this->table);
        
        return $rs->result_array();

    }

    function listar($arr=[]){
        $per_page = 10;
        $page = 1;
        if (isset($_GET["page"])){
            $page = (int) $_GET["page"];
        }

        #recupera o filtro que vem do formulario e prepara o where
        if (isset($arr["filtro"])){
            $arr["where"] = [];
            foreach($this->filtros as $field){
                $arr["where"][$field] = $arr["filtro"];
            }
        }

        

        
        $this->db->start_cache();
        if (_v($arr,"distinct") == true){
            $this->db->distinct();
        }
        if (_v($arr,"group_by") != null){
            $this->db->group_by($arr["group_by"]);
        }

        #caso seja para filtrar em um campo especifico
        #passe como parametro: $arr['filtro_equals'] = ['nome_do_campo'=>'valor'];
        if (isset($arr["filtro_equals"])){
            foreach($arr["filtro_equals"] as $key=>$value){
                $this->db->where($key,$value);
            }
        }

        
        $this->db->select($this->select);
        if ($this->logicExclusion) {
            $this->db->where("{$this->table}.deleted",0);
        }
        foreach($this->joins as $join){
            #tbl, cond, type
            $this->db->join($join[0],$join[1],$join[2]);
        }
        #where
        if (isset($arr["where"])){
            $this->db->group_start();
            $this->db->or_like($arr["where"]);
            $this->db->group_end();
        }

        

        #conta quantos tem
        $this->db->stop_cache();
        $qtd = $this->db->count_all_results($this->table);
        
        if (!isset($arr['paginado']) || $arr['paginado'] == true){
            #recupera pagina
            $this->db->limit($per_page,($page-1)*$per_page);
        }

        if (isset($arr["order_by"])){
            if (isset($arr["order"]) && ($arr["order"] == "asc" || $arr["order"] == "desc")){
                $this->db->order_by($arr["order_by"],$arr["order"]);
            } else {
                $this->db->order_by($arr["order_by"]);
            }
        } else 
        if ($this->orderBy != null){
            $this->db->order_by($this->orderBy);
        }

        $rs = $this->db->get($this->table);
        #print $this->db->get_compiled_select($this->table, FALSE);
        #die();
        $this->db->flush_cache();
        

        
        $lista = ["dados"=>$rs->result_array(),"total_rows"=>$qtd, "per_page"=>$per_page, "page_query_string"=>true,'query_string_segment'=>'page'];

        $lista['full_tag_open'] = '<div class="ui pagination menu">';
        $lista['full_tag_close'] ='</div>';
        $lista['num_tag_open'] = '<li class="item">';
        $lista['num_tag_close'] = '</li>';
        $lista['cur_tag_open'] = '<li class="active item">';
        $lista['cur_tag_close'] = '</li>';
        $lista['next_tag_open'] = '<li class="item">';
        $lista['next_tagl_close'] = '</li>';
        $lista['prev_tag_open'] = '<li class="item">';
        $lista['prev_tagl_close'] = '</li>';
        $lista['first_tag_open'] = '<li class="item">';
        $lista['first_tagl_close'] = '</li>';
        $lista['last_tag_open'] = '<li class="item">';
        $lista['last_tagl_close'] = '</li>';
        $lista['reuse_query_string']  = true;
        $lista['use_page_numbers'] = true;
        return $lista;
    }



    public function salvar($dados){

        unset($dados[$this->security->get_csrf_token_name()]);

        if (!isset($dados["id"])){
            $dados["id"] = null;
        }
        $id = $dados["id"];
        unset($dados["id"]);

        foreach($dados as $k=>$v){
            if ($v == ""){
                $dados[$k] = null;
            }
        }

        if ($id == ""){
            $rs = $this->db->insert($this->table, $dados);
            return $this->db->insert_id();
        } else {
            $this->db->update($this->table, $dados,["id"=>$id]);
            return $id;
        }

    }


    public function deletar($id){

        if ($this->logicExclusion == false){
            $this->db->delete($this->table,["id"=>$id]);
        } else {
            $this->db->update($this->table,["deleted"=>1],["id"=>$id]);
        }
    }


}