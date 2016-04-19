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
class ProgramModel extends EntityModel {

    public $table = 'program';
    /**
     * @param null|string $sequence
     *
     * @return \DibiFluent
     */
    public function getRoomsFluent($sequence = NULL) {
        $stm = $this->database->select( "*" )->from( "room" );
        if ( !is_null( $sequence ) )
            $stm->where( "id IN ($sequence)" )->orderBy( "FIND_IN_SET (id, \"$sequence\")" );

        return $stm;
    }

    /**
     * @param null|string $sequence
     *
     * @return array
     */
    public function getRooms($sequence = NULL){
        $stm = $this->getRoomsFluent($sequence);
     //   echo $stm->test();exit;
        return $stm->fetchAll();
        
    }

    /**
     * @param null|string $sequence
     *
     * @return array
     */
    public function getRoomsPairs($sequence = NULL)
    {
        $stm = $this->getRoomsFluent($sequence);

        return $stm->fetchPairs('id', 'title');
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
               
               ->orderBy("program.timeFrom")->groupBy("program.id,content.id");
        $programs = $stm->fetchAll();
        foreach($programs as $key => $program){
            $programs[$key]->typeIcon = $this->typeIcon($program->typeId);
            $programs[$key]->image = $this->getImage($program->imageUrl);
        }
        return $programs;
        
    }

    /**
     * @return \DibiFluent
     */
    public function getProgramsListFluent() {
        $stm = $this->database->select( "program.*, attachment.url AS imageUrl, room.title AS roomTitle, programtype.title AS programType, programgenre.title AS programGenre, content.title as contentTitle, content.author, route.url, UNIX_TIMESTAMP(program.timeFrom) AS startTs" )
                  ->from( "program" )
                  ->leftJoin( "content", "ON program.contentId = content.id" )
                  ->leftJoin( "route", "ON content.id = route.contentId" )
                  ->leftJoin( "room", "ON program.roomId = room.id" )
                  ->leftJoin( "programtype", "ON programtype.id = program.typeId" )
                  ->leftJoin( "programgenre", "ON programgenre.id = program.genreId" )
                  ->leftJoin( "attachment", "ON attachment.contentId = content.id AND attachment.mime = \"IMAGE\"" )
                  //->where( "statusId = 14" )
        ;

        return $stm;
    }

    public function getProgramsListForGrid() {
        $fluent = $this->getProgramsListFluent();
        $fluent->groupBy('program.id');

        return $fluent;
    }

    public function getCollisionsFluent() {
        $stm = $this->database->select("
            *
        ")
        ->from("program")
        ->leftJoin("program p2", "ON program.id = p2.id")
        ->where("p2.timeStart < program.timeEnd")
        ->where("p2.timeEnd > program.timeStart");

        return $stm;
    }

    public function getMaxMinTimeForGrid() {
        $fluent = $this->database->select( 'MIN(a.TimeFrom) AS minTimeFrom, MAX(a.TimeFrom) AS maxTimeFrom')
            ->from( $this->getProgramsListForGrid())->as('a');

        $res = $fluent->fetchAll();
        return empty($res) ? ['minTimeFrom' => null , 'maxTimeFrom' => null] : $res[0];
    }
    
    public function getProgramsList($sectionId = NULL, $typeId = NULL, $roomId = NULL ,$startTime = NULL, $endTime = NULL, $orderBy = "content.title"){
         $stm = $this->getProgramsListFluent();

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
               ->leftJoin("ro
               om", "ON program.roomId = room.id")
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

    /**
     * Return program row with array of attachments
     * @param $programId
     *
     * @return array
     */
    public function getFormDefaults($programId) {
        $stm = $this->database->select( "program.id as programId, content.title as contentTitle, content.id AS contentId, content.text AS contentText, room.id AS roomId, programgenre.id AS genreId, programtype.id AS typeId, content.author, UNIX_TIMESTAMP(program.timeFrom) AS startTs" )
                                ->select("program.timeFrom, program.timeTo")
                                ->select("attachment.url AS attachmentUrl, attachment.id AS attachmentId, attachment.mime AS attachmentMime")
                                ->from( "program" )
                                ->leftJoin( "content", "ON program.contentId = content.id" )
            ->leftJoin("room", "ON program.roomId = room.id")
            ->leftJoin( "programtype", "ON programtype.id = program.typeId" )
            ->leftJoin( "programgenre", "ON programgenre.id = program.genreId" )

            ->leftJoin( "attachment", "ON attachment.contentId = content.id")

            ->where('program.id = ?', $programId);

        $result = $stm->fetchAll();

        $program = $result[ 0 ]->toArray();
        $program[ 'attachments' ] = [ ];
        //can have multiple attachments: flatten
        if(count($result) > 0) {

            foreach($result as $row) {
                if(!empty($row['attachmentId'])) {
                    $program[ 'attachments' ][] = [
                        'id'   => $row[ 'attachmentId' ],
                        'url'  => $row[ 'attachmentUrl' ],
                        'mime' => $row[ 'attachmentMime' ]
                    ];
                }
            }
        }


        return $program;
    }

    public function getTypesFluent()
    {
        $stm = $this->database->select('*')
                ->from('programtype');

        return $stm;
    }

    public function getTypesPairs()
    {
        $res = $this->getTypesFluent()
            ->fetchPairs('id', 'title');

        return $res;
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
