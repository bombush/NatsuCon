<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Custom\Program;
use Natsu\Model\ProgramModel;

/**
 * Description of ProgramLoader
 *
 * @author yasuri
 */
class ProgramLoader {
    private $sectionId;
    /**
     *
     * @var Natsu\Model\ProgramModel
     */
    private $em;
   
    public function __construct($em) {

        $this->em = $em->reflection("program");
    }
    
    public function setSectionId($sectionId) {
        $this->sectionId = $sectionId;
        return $this;
    }
    
    
    public function getRooms($rooms){
       return $this->em->getRooms($rooms);

    }    
    
    public function getPrograms(){
       $programs = $this->em->getPrograms($this->sectionId);
       foreach($programs as $i => $program){
           $programs[$i]->startTs = $programs[$i]->timeFrom->getTimestamp();
       }
       return $programs;
    }
}
