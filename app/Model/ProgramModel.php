<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Natsu\Model;
use Nette;

/**
 * Description of ProgramModel
 *
 * @author yasuri
 */
class ProgramModel extends EntityModel{
    
    public function getRooms($sequence){
        $stm = $this->database->select("*")->from("room")->where("id IN ($sequence)")->orderBy("FIND_IN_SET (id, \"$sequence\")");
     //   echo $stm->test();exit;
        return $stm->fetchAll();
        
    }
    
    
    public function getPrograms($sectionId){
       $stm = $this->database->select("program.*, content.title, content.author, route.url, UNIX_TIMESTAMP(program.timeFrom) AS startTs")
               ->from("program")
               ->leftJoin("content", "ON program.contentId = content.id")
               ->leftJoin("route", "ON content.id = route.contentId")
               ->leftJoin("room", "ON program.roomId = room.id")
               ->leftJoin("programtype", "ON programtype.id = program.typeId")
               ->where("statusId = 14 AND program.sectionId = ?", $sectionId)
               ->orderBy("program.timeFrom");
        return $stm->fetchAll(); 
        
    }
    
    public function getProgramByContentId($contentId){
        $stm = $this->database->select("program.*, content.title, room.title AS roomTitle, content.author, route.url, UNIX_TIMESTAMP(program.timeFrom) AS startTs")
               ->from("program")
               ->leftJoin("content", "ON program.contentId = content.id")
               ->leftJoin("route", "ON content.id = route.contentId")
               ->leftJoin("room", "ON program.roomId = room.id")
               ->leftJoin("programtype", "ON programtype.id = program.typeId")
               ->where("statusId = 14 AND program.contentId = ?", $contentId)
               ->orderBy("program.timeFrom");
        return $stm->count() ? $stm->fetchAll() : false; 
        
    }
    
}
