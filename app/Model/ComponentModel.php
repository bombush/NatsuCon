<?php
namespace Natsu\Model;

use Nette;

class ComponentModel extends EntityModel {
    public $table = "component";
    public $tableRelation = "contentcomponent";

    public function getComponents($contentId){
        $sql = "SELECT {$this->table}.*, {$this->tableRelation}.params, {$this->tableRelation}.id AS relationId
               FROM
                {$this->table}
                INNER JOIN {$this->tableRelation} ON {$this->table}.id = {$this->tableRelation}.componentId
                WHERE {$this->tableRelation}.contentId = {$contentId}";
        return $this->database->fetchAll($sql);
    }


}
?>
