<?php
namespace Natsu\Model;
use Nette;

class DatasourceModel extends EntityModel {

    public function table($id = NULL){
        return $this->database->select("*")->from($this->table);
        
    }
    
    
    public function userList(){
        return $this->database->select("user.id, user.username, contact.fullname, contact.email")->from("user")->leftJoin("contact", "ON user.contactId = contact.id");
        
    }
    


}
?>
