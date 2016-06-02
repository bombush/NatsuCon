<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Natsu\Model;
use Nette\Utils\ArrayHash;

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

    public function update($values) {
        $this->database->update(static::$tableContent, $values)->where('id = ?', $values->id)->execute();
        //$this->database->query( "UPDATE `{static::$tableContent}` SET ", $values, ' WHERE `id`= ?', $values->id );
    }

    public function insertContent(ArrayHash $values) {
        $this->database->query("INSERT INTO [". static::$tableContent . "]", $values);
        return $this->database->getInsertId();
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

    public function getContent($contentId) {
        $instance = $this->reflection('Content');
        $instance->setTable('content');

        return $instance->getPrimary(intval($contentId));
    }

    //put your code here
}
