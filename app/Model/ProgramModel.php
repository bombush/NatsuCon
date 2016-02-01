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
       $stm = $this->database->select("program.*, programtype.title AS programType, programgenre.title AS programGenre, content.title, content.author, route.url, UNIX_TIMESTAMP(program.timeFrom) AS startTs")
               ->from("program")
               ->leftJoin("content", "ON program.contentId = content.id")
               ->leftJoin("route", "ON content.id = route.contentId")
               ->leftJoin("room", "ON program.roomId = room.id")
               ->leftJoin("programtype", "ON programtype.id = program.typeId")
               ->leftJoin("programgenre", "ON programgenre.id = program.genreId")
               ->where("statusId = 14 AND program.sectionId = ?", $sectionId)
               ->orderBy("program.timeFrom");
        $programs = $stm->fetchAll();
        foreach($programs as $key => $program){
            $programs[$key]->typeIcon = $this->typeIcon($program->typeId);
        }
        return $programs;
        
    }
    
    public function getProgramByContentId($contentId){
        $stm = $this->database->select("program.*, content.title, room.title AS roomTitle, programgenre.title AS genre, programtype.title AS type, content.author, route.url, UNIX_TIMESTAMP(program.timeFrom) AS startTs")
               ->from("program")
               ->leftJoin("content", "ON program.contentId = content.id")
               ->leftJoin("route", "ON content.id = route.contentId")
               ->leftJoin("room", "ON program.roomId = room.id")
               ->leftJoin("programtype", "ON programtype.id = program.typeId")
               ->leftJoin("programgenre", "ON programgenre.id = program.genreId")
               ->where("statusId = 14 AND program.contentId = ?", $contentId)
               ->orderBy("program.timeFrom");
        $programs = $stm->fetchAll();
        foreach($programs as $key => $program){
            $programs[$key]->typeIcon = $this->typeIcon($program->typeId);
        }
        return $programs;
        
    }
    
    private function typeIcon($typeId){
        $icon = "";
        switch($typeId){
            case 1: $icon = "fa-graduation-cap"; break;
            case 2: $icon = "fa-soccer-ball-o"; break;
            case 3: $icon = "fa-video-camera"; break;
            case 4: $icon = "fa-paint-brush"; break;
            case 5: $icon = "fa-square"; break;
            case 6: $icon = "fa-user"; break;
            case 7: $icon = "fa-users";break;
        }
        return $icon;
    }
    
}
