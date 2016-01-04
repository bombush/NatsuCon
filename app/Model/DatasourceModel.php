<?php
namespace Natsu\Model;
use Nette;

class DatasourceModel extends EntityModel {

    public function table($id = NULL){
        return $this->database->select("*")->from($this->table);
        
    }


}
?>
