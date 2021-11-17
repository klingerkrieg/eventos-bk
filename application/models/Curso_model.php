<?php
require_once "AbstractModel.php";


class Curso_model extends AbstractModel {

    protected $table = "cursos";
    public $niveis_cursos = [1=>"Técnico", 2=>"Graduação", 3=>"Mestrado", 4=>"Doutorado"];
    protected $filtros = ["cursos.curso"];
    protected $orderBy = "cursos.curso";
    

}