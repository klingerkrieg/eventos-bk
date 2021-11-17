<?php
require_once "AbstractModel.php";


class GTS_model extends AbstractModel {

    protected $table = "gts";
    protected $logicExclusion = true;
    protected $filtros = ["gts.gt"];
    protected $orderBy = "gts.gt";


}