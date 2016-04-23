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
        
        
        if($this->festivalStart == null){
           $min = reset($this->programs);
           $this->festivalStart = date("Y-m-d G:i:s",$min->timeFrom->getTimestamp());
        }
        
         if($this->festivalEnd == null){ 
           $max = end($this->programs);
         //  dump($max);
           $this->festivalEnd = date("Y-m-d G:i:s",$max->timeTo->getTimestamp());
         } 
        
         //dump($this->festivalEnd);
         
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
        //  Debugger::dump($this->timeTable);
        $stack = array();

        foreach($this->timeTable as $roomId => $roomLineUp){
            $lastEnd = 0;
            $lastStart = 0;    
            foreach($roomLineUp as $slotId => $program){
                $map[$roomId][$slotId][] = $program[0]->id;
              
               
                
                $endTs = $program[0]->timeTo->getTimestamp();
                

                

                $slotBetween = $slotId;
                while($endTs-1800 > $slotBetween){  
                    $slotBetween = $slotBetween + 1800;    
                    $map[$roomId][$slotBetween][] = $program[0]->id;
                   
                }; 

            }
        }
        
    foreach($map as $roomId => $roomLineUp){
        foreach($roomLineUp as $slotId => $programs){
            $programs = array_unique($programs);
            if(count($programs) > 1){
               $minError = 0;
               $maxError = 0;
               foreach($programs as $programId){
                 //   echo "$roomId ... $programId ---";
                   $errorProgram = $this->programs[$programId];
                   $endTs = $errorProgram->timeTo->getTimestamp();
                   $startTs = $errorProgram->timeFrom->getTimestamp();
                   $map[$roomId][$startTs][] = $errorProgram->id;
                   if($endTs > $maxError){$maxError = $endTs;}
                   if($startTs < $minError){$minError = $startTs;} elseif($minError == 0){$minError = $startTs;}
                   
               }   
               
                $slotBetween = $minError;  
                $map[$roomId][$slotBetween] = array_merge($programs,$map[$roomId][$slotBetween]);
                $map[$roomId][$slotBetween] = array_unique($map[$roomId][$slotBetween]);
            
                while($maxError-1800 > $slotBetween){  
                    $slotBetween = $slotBetween + 1800;
 
                        $map[$roomId][$slotBetween] = array_merge($programs,$map[$roomId][$slotBetween]);
                        $map[$roomId][$slotBetween] = array_unique($map[$roomId][$slotBetween]);
                    }
                    
                   
                };
               }
            }
        
        
        
 
        

   // Debugger::dump($map);exit;
        return $map;
    }
    
    
    
}





















