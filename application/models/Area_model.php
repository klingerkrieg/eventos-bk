<?php
require_once "AbstractModel.php";


class Area_model extends AbstractModel {

    protected $table = "areas";
    protected $logicExclusion = true;
    protected $select = "areas.*, grandes_areas.grande_area";
    protected $joins = [["grandes_areas","grandes_areas.id = areas.idgrandearea","left"]];
    protected $filtros = ["areas.area"];
    protected $orderBy = "areas.area";


}