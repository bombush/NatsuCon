<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Natsu\Model;

/**
 * Description of ContentModel
 *
 * @author yasuri
 */
class ContentModel extends EntityModel {
    static $tableContent = 'content';
    static $tableAttachments = 'attachment';
    static $tablePermission = 'permission';
    static $tableComponent = 'contentcomponent';
    static $tableProgram = 'program';
    static $tableRoute = 'route';

    
    
    
    public function deleteContent($contentId){
        $this->deleteAttachments($contentId);
        $this->deleteRoutes($contentId);
        $this->deletePrograms($contentId);
        $this->deletePermissions($contentId);
        $this->delete($contentId);
        
        
        
    }
    
    public function deleteAttachment($id){
        $this->database->delete(self::$tableAttachments)->where(array("id" => $id))->execute();
    }
    
    public function deletePermission($id){
        $this->database->delete(self::$tablePermission)->where(array("id" => $id))->execute();
    }
    
    public function deleteComponent($id){
        $this->database->delete(self::$tableComponent)->where(array("id" => $id))->execute();
    }
    
    public function delete($contentId){
        $this->database->delete(self::$tableContent)->where(array("id" => $contentId))->execute();
    }
    
    
    public function deleteAttachments($contentId){
        $this->database->delete(self::$tableAttachments)->where(array("contentId" => $contentId))->execute();
    }
    
    public function deleteRoutes($contentId){
         $this->database->delete(self::$tableRoute)->where(array("contentId" => $contentId))->execute();
    }
    
    public function deletePrograms($contentId){
        $this->database->delete(self::$tableProgram)->where(array("contentId" => $contentId))->execute();
    }
    
    public function deletePermissions($contentId){
        $this->database->delete(self::$tablePermission)->where(array("contentId" => $contentId))->execute();
    }
    
     public function deleteComponents($contentId){
         $this->database->delete(self::$tableComponent)->where(array("contentId" => $contentId))->execute();
    }
    
    //put your code here
}
