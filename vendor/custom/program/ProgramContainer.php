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
    public $map;
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
        date_default_timezone_set('Europe/Prague');
        $this->slots = $tt->getSlots(strtotime($this->festivalStart), strtotime($this->festivalEnd));
        $this->programs = $tt->getIndex();
        $this->map = $this->getMap();
       
    }
    
    
   
    private function getMap(){
        $map = array();
        foreach($this->timeTable as $roomId => $roomLineUp){
            foreach($roomLineUp as $slotId => $program){
                
              
               
                
                $endTs = $program[0]->timeTo->getTimestamp();
              //  dump($program);
              //  dump($slotId);
              //  dump($endTs);
              //  exit;
                $slotBetween = $slotId;
                while($endTs-1800 > $slotBetween){
                    
                $slotBetween = $slotBetween + 1800;    
                $map[$roomId][$slotBetween] = 2;
                
                };
                
            }
        }
       // dump($map);
        return $map;
    }
    
    
    
}





















