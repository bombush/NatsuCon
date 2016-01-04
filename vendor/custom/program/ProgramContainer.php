<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Custom\Program;
use Tracy\Debugger;

/**
 * Description of newPHPClass
 *
 * @author yasuri
 */
class ProgramContainer {
    public $rooms;
    public $programs;
    public $timeTable;
    public $slots;
    /**
     *
     * @var Custom\Program\ProgramLoader
     */
    public $loader;
    public $roomSequence;
    public $sectionId;
    public $festivalStart;
    public $festivalEnd;
   
   
    
    public function setLoader($em){
        $this->loader = new \Custom\Program\ProgramLoader($em);
        
    }
    
    public function run(){
        $this->rooms = $this->loader->setSectionId($this->sectionId)->getRooms($this->roomSequence);

        $this->programs = $this->loader->getPrograms();
        
        
        $tt = new ProgramTimetable;
        $tt->programs = $this->programs;
        $this->timeTable = $tt->getTable();
        $this->slots = $tt->getSlots(strtotime($this->festivalStart), strtotime($this->festivalEnd));
        $this->programs = $tt->getIndex();
        
       // Debugger::dump($this->slots);
       // exit;
    }
    
    
   
    
    
    
    
}
