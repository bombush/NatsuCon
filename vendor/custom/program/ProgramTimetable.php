<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Custom\Program;

/**
 * Description of ProgramTimetable
 *
 * @author yasuri
 */
class ProgramTimetable {
    public $programs;
    public $table;
    public $interval = 1800;
    public $renderSettings = array();
    
    public function getTable(){
        $table = array();
        foreach($this->programs as $program){
            if($program->roomId == 0){
                continue;
            }
            $table[$program->roomId][$program->startTs][] = $program;
            
        }
        
        return $table;
        
    }
    
    
    public function getIndex(){
        $programs = array();
        foreach($this->programs as $program){
            $program->length = ceil(($program->timeTo->getTimestamp() - $program->timeFrom->getTimestamp())/1800);
            
            if(
                    (date("H", $program->timeFrom->getTimestamp()) >= 20 && date("H", $program->timeFrom->getTimestamp()) <= 23)
                    &&
                    (date("H", $program->timeTo->getTimestamp()) > 0 && date("H",$program->timeTo->getTimestamp()) <= 6))
                    
                    {
                    $program->length =  $program->length+1;
            }
            
            $programs[$program->id] = $program;
            
            
        }
        return $programs;
    }
    
    
    public function getSlots($startTs, $endTs){
        $slots = array();

        $slot = $startTs - 3600;
        do{
           // $slots[] = \Nette\Utils\DateTime::from($slot);
            $slots[] = $slot;
            $slot = $slot + $this->interval;
            
        }while($slot < $endTs);
        

        
        return $slots;
        
    }
    
    
}
