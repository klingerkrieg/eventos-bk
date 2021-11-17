<?php
require_once "AbstractModel.php";


class Evento_model extends AbstractModel {

    protected $table = "eventos";
    protected $logicExclusion = true;
    

    public function getEvento(){
        return $this->db->query("select * from eventos")->row_array();
    }

    public function getEstatisticas(){

        $arr = [];
        $this->db->where(["deleted"=>0, "nivel"=>NIVEL_PARTICIPANTE]);
        $arr["inscritos"] = $this->db->from("usuarios")->count_all_results();

        $this->db->where(["deleted"=>0]);
        $arr["submetidos"] = $this->db->from("trabalhos")->count_all_results();

        $this->db->where(["deleted"=>0,"status != "=>PENDENTE]);
        $arr["corrigidos"] = $this->db->from("trabalhos")->count_all_results();

        $this->db->where(["deleted"=>0]);
        $this->db->where_in("status",[APROVADO, APROVADO_CORRECOES, APROVADO_CORRECOES_PENDENTES]);
        $arr["aprovados"] = $this->db->from("trabalhos")->count_all_results();

        $this->db->where(["deleted"=>0]);
        $arr["minicursos_submetidos"] = $this->db->from("minicursos")->count_all_results();

        $this->db->where(["deleted"=>0,"status"=>APROVADO]);
        $arr["minicursos_aprovados"] = $this->db->from("minicursos")->count_all_results();

        $this->db->where(["deleted"=>0,"status"=>APROVADO]);
        $this->db->select_sum("vagas");
        $arr["total_vagas_minicursos"] = $this->db->from("minicursos")->get()->row_array();
        $arr["total_vagas_minicursos"] = $arr["total_vagas_minicursos"]["vagas"];

        
        $arr["total_vagas_ocupadas_minicursos"] = $this->db->from("matriculas")->count_all_results();
        
        return $arr;
    }

    public function verificaDatasEncerramentos(){
        $evento = $this->getEvento();

        $data = [];

        if (time() > strtotime($evento["submissoes_ate"]) && $evento["aceitando_submissoes"]){
            $data["aceitando_submissoes"] = false;
        }
        if (time() > strtotime($evento["correcoes_ate"]) && $evento["aceitando_correcoes"]){
            $data["aceitando_correcoes"] = false;
        }
        if (time() > strtotime($evento["minicursos_ate"]) && $evento["aceitando_submissoes_minicursos"]){
            $data["aceitando_submissoes_minicursos"] = false;
        }
        if (time() > strtotime($evento["matriculas_ate"]) && $evento["aceitando_matriculas_minicursos"]){
            $data["aceitando_matriculas_minicursos"] = false;
        }

        if (time() > strtotime($evento["aberto_ate"]) && $evento["evento_encerrado"] == false && 
                $evento["aceitando_submissoes"] && $evento["aceitando_correcoes"] && $evento["aceitando_submissoes_minicursos"]){
            $data["evento_encerrado"] = true;
            $data["aceitando_submissoes"] = false;
            $data["aceitando_correcoes"] = false;
            $data["aceitando_submissoes_minicursos"] = false;
            $data["aceitando_matriculas_minicursos"] = false;
        }

        if (count($data)>0){
            $this->db->set($data);
            $this->db->update($this->table);
        }
        
    }
    


}