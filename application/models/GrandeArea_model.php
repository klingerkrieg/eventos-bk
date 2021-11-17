<?php
require_once "AbstractModel.php";


class GrandeArea_model extends AbstractModel {

    protected $table = "grandes_areas";
    protected $logicExclusion = true;
    protected $filtros = ["grandes_areas.grande_area"];
    protected $orderBy = "grandes_areas.grande_area";


}