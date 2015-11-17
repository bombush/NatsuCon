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


    public function setUserId($userId){
        $this->userId = $userId;
    }

    public function setRoleId($roleId){
        $this->roleId = $roleId;
    }

    public function checkContent($content){
        if($this->userId){
            if($this->userId == $content->userId) return true;

            // if($this->userId) return true;

        }else{
            if($content->isDraft) {
                return false;
            }else{
                return true;
            }


        }

        return false;
    }


}
?>
