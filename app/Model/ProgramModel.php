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
       $stm = $this->database->select("program.*, attachment.url AS imageUrl,programtype.title AS programType, programgenre.title AS programGenre, content.title, content.author, route.url, UNIX_TIMESTAMP(program.timeFrom) AS startTs")
               ->from("program")
               ->leftJoin("content", "ON program.contentId = content.id")
               ->leftJoin("route", "ON content.id = route.contentId")
               ->leftJoin("room", "ON program.roomId = room.id")
               ->leftJoin("programtype", "ON programtype.id = program.typeId")
               ->leftJoin("programgenre", "ON programgenre.id = program.genreId")
               ->leftJoin("attachment", "ON attachment.contentId = content.id AND attachment.mime = \"IMAGE\"")  
               ->where("statusId = 14 AND program.sectionId = ?", $sectionId)
               
               ->orderBy("program.timeFrom")->groupBy("program.id,content.id,attachment.id");
        $programs = $stm->fetchAll();
        foreach($programs as $key => $program){
            $programs[$key]->typeIcon = $this->typeIcon($program->typeId);
            $programs[$key]->image = $this->getImage($program->imageUrl);
        }
        return $programs;
        
    }
    
    
    public function getProgramsList($sectionId = NULL, $typeId = NULL, $roomId = NULL ,$startTime = NULL, $endTime = NULL, $orderBy = "content.title"){
         $stm = $this->database->select("program.*, attachment.url AS imageUrl, room.title AS roomTitle, programtype.title AS programType, programgenre.title AS programGenre, content.title, content.author, route.url, UNIX_TIMESTAMP(program.timeFrom) AS startTs")
               ->from("program")
               ->leftJoin("content", "ON program.contentId = content.id")
               ->leftJoin("route", "ON content.id = route.contentId")
               ->leftJoin("room", "ON program.roomId = room.id")
               ->leftJoin("programtype", "ON programtype.id = program.typeId")
               ->leftJoin("programgenre", "ON programgenre.id = program.genreId")
               ->leftJoin("attachment", "ON attachment.contentId = content.id AND attachment.mime = \"IMAGE\"")  
               ->where("statusId = 14");
             
          if(isset($sectionId)){
              $stm->where("program.sectionId = $sectionId");
          }
          
          if(isset($typeId)){
              $stm->where("program.typeId = $typeId");
          }
          
           if(isset($roomId)){
              $stm->where("program.roomId = $roomId");
          }
        
          if(isset($startTime)){
              $startTimeTs = strtotime($startTime);
              $stm->where("UNIX_TIMESTAMP(program.start_time) >= $startTimeTs");
          }  
          
           if(isset($endTime)){
              $endTimeTs = strtotime($endTime);
              $stm->where("UNIX_TIMESTAMP(program.end_time) >= $endTimeTs");
          }
          
          $stm->groupBy("content.id,attachment.id");
          $stm->orderBy($orderBy);
          
           $programs = $stm->fetchAll();
           foreach($programs as $key => $program){
            $programs[$key]->typeIcon = $this->typeIcon($program->typeId);
            $programs[$key]->image = $this->getImage($program->imageUrl);
        }
        return $programs;
        
        
    }
    
    private function getImage($url){
            if(empty($url)){
                return false;
            }
            
            
            $attachment = \Nette\Utils\ArrayHash::from(array("url" => $url));
            
            $image = new \Custom\Content\ImageAttachment();
            $image->setRow($attachment);
            $image->run();
            return $image;
        
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
            case 2: $icon = "fa-trophy"; break;
            case 3: $icon = "fa-video-camera"; break;
            case 4: $icon = "fa-paint-brush"; break;
            case 5: $icon = "fa-square"; break;
            case 6: $icon = "fa-user"; break;
            case 7: $icon = "fa-users";break;
        }
        return $icon;
    }
    
}
