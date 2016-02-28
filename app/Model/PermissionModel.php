<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Natsu\Model;
    use Nette;
/**
 * Description of PermissionModel
 *
 * @author campari
 */

 /**
  *  isInRule($user,$role[])
  *
  *
  */
class PermissionModel extends EntityModel {
    //put your code here
    public $tableName = "permission";
    private $userId;
    private $roleId;
    private $rules;
    const ADMIN_ROLE  = 1;
    const FUHRER_ROLE = 2;
    const SUPERVISOR_ROLE = 3;
    const REGISTERED_ROLE = 4;
    const EDITOR_ROLE = 5;
    const ACTIVE_STATUS = 1;
    const INACTIVE_STATUS = 2;


    public function setUserId($userId){
        $this->userId = $userId;
    }

    public function setRoleId($roleId){
        $this->roleId = $roleId;
    }
    
    public function setRules($rules){
        $this->rules = $rules;
    }
    
    public function getRules(){
        return $this->rules;
    }

    public function checkContent($content){
     //  var_dump($content); exit;
        if($content == false){
            return false;
        }
        
        
        if($this->roleId == self::ADMIN_ROLE || $this->roleId == self::FUHRER_ROLE){
            return true;
        }
        
        
        if($this->userId){
            if($this->userId == $content->userId) return true;

            $this->loadRules($content->id);
            return $this->performUserRules();

        }else{
            if($content->isDraft) {
                return false;
            }else{
                return true;
            }
        }

        return false;
    }
    
    
    public function loadRules($contentId){
         $stm = $this->database->select("permission.deletable, permission.writable, permission.forbidden")
                   ->from("permission")
                   ->where("contentId = ? AND (userId = ? OR roleId = ?)", $contentId, $this->userId, $this->roleId );
         $rows = $stm->fetchAll();
         $rules = null;
         foreach($rows as $row){
             if(!isset($rules)){
                 $rules = $row;
             }else{
                 if($row->deletable == 1){$rules->deletable = 1;}
                 if($row->writable == 1){$rules->writable = 1;}
                 if($row->forbidden == 1){$rules->forbidden = 1;}
             }
         }
         $this->setRules($rules);
    }
    
    
    public function performUserRules(){
        if(!isset($this->rules)){
            return true;
        }else if($rules->forbidden == 1){
            return false;
        }else{
            return true;
        }
    }

}
?>
