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
    
    
    public static function getRoleIds(){
        return array(
            self::ADMIN_ROLE => 'Admin',
            self::FUHRER_ROLE => 'Fuhrer',
            self::SUPERVISOR_ROLE => 'Vedouci',
            self::REGISTERED_ROLE => 'Registrovany',
            self::EDITOR_ROLE => 'Editor',
            
        );
        
    }
    
    public static function getStatusIds(){
        return array(
            self::ACTIVE_STATUS => 'Aktivní',
            self::INACTIVE_STATUS => 'Neaktivní'
            
        );
        
    }

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
    
    public function getAllRules(){
        $rules = new \stdClass;
        $rules->deletable = 1;
        $rules->writable = 1;
        $rules->forbidden = 0;
        return $rules;
    }
    
    private function checkTimeValidity($content){
        $validTo = false;
        $validFrom = false;
        
        
        if($content->activeFrom == "0000-00-00 00:00:00" || $content->activeFrom == ""){
            $validFrom = true;
        }
        
        if($content->activeUntil == "0000-00-00 00:00:00" || $content->activeUntil == ""){
           $validTo = true; 
        }
        
        if($validFrom == false){
            if($content->activeFrom->getTimestamp() < time()){
                 $validFrom = true;
            }
        }
        
        if($validTo== false){
            if($content->activeUntil->getTimestamp() > time()){
                 $validTo = true;
            }
        }
        
        
        return $validFrom && $validTo;
        
    }

    public function checkContent($content){
     //  var_dump($content); exit;
        if($content == false){
            return false;
        }
        
        
        if($this->roleId == self::ADMIN_ROLE || $this->roleId == self::FUHRER_ROLE){
            $this->setRules($this->getAllRules());
            return true;
        }
        
        
        if($this->userId){
            if($this->userId == $content->userId){ $this->setRules($this->getAllRules());return true;}

            $this->loadRules($content->id);
            return $this->performUserRules();

        }else{
          //  print_r($content);
            if($content->isDraft) {
                return false;
            }elseif(!$content->isDraft && !$this->checkTimeValidity($content)){
                return false;
            }else{
                return true;
            }
        }

        return false;
    }
    
    
    public function getPermissions($contentId){
        $stm = $this->database->select("permission.*, role.title AS roleTitle")
                ->from("permission")
                ->leftJoin("role", "ON role.id = permission.roleId")
                ->where("contentId = ?", $contentId);
        return $stm->fetchAll();
        
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
        }else if($this->rules->forbidden == 1){
            return false;
        }else{
            return true;
        }
    }
    
    
    public function getPermissionById($permissionId){
        $stm = $this->database->select("*")->from("permission")->where("id = ?", $permissionId);
        return $stm->fetch();
        
    }
    
    
    public function getRoles(){
        $stm = $this->database->select("role.*")->from("role");
        $roles = $stm->fetchAll();
        $o = [];
        foreach($roles as $role){
            $o[$role->id] = $role->title;
        }
        
        return $o;
    }

    /**
     * @param $contentId
     * @param $userId
     * @param $roleId
     * @param $writable
     * @param $deletable
     * @param $forbidden
     *
     * @return int
     * @throws \DibiException
     */
    public function addPermission($contentId, $userId, $roleId, $writable, $deletable, $forbidden)
    {
        $values = [
            'contentId' => intval($contentId),
            'userId' => intval($userId),
            'roleId' => intval($roleId),
            'writable' => intval($writable),
            'deletable' => intval($deletable),
            'forbidden' => intval($forbidden)
        ];

        /*$this->database
            ->insert($this->tableName, $values)
            ->query();*/ // @TODO <---- WHY WON'T THIS WORK????
        $this->database->query( "INSERT INTO [" . $this->tableName . "]", $values );

        return $this->database->getInsertId();
    }

    public function getContentRules($user, $contentId)
    {
        $contentId = intval($contentId);

        $contentModel = $this->reflection( 'Content' );
        $content = $contentModel->getContent($contentId);
        if(!$content)
            throw new Exception('Content Id (' . $contentId . ') not found.');

        $pm = $this->reflection( "Permission" ); // new instance because we'll be setting state
        $userId = $user->loggedIn ? $user->id : 0;
        $pm->setUserId( $userId );
        $roleId = $userId ? $user->getIdentity()->roleId : 0;
        $pm->setRoleId( $roleId );
        $result = $pm->checkContent( $content );

        return $pm->getRules();
    }
}
?>
