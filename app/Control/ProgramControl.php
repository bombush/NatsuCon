<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Natsu\Control;
use Custom\Program\ProgramContainer;

/**
 * Description of ProgramControl
 *
 * @author yasuri
 */
class ProgramControl extends ContentControl{
    //put your code here
    protected $sectionId = NULL;
    protected $locations = array();
    protected $festivalStart;
    protected $festivalEnd;
    protected $em;
    
    public function setEm($em){
        $this->em = $em;
    }
            
    
    public function setFestivalStart($festivalStart){
        $this->festivalStart = $festivalStart;
    }
    
    public function setFestivalEnd($festivalEnd){
        $this->festivalEnd = $festivalEnd;
    }
    
    public function setSectionId($sectionId){
        $this->sectionId = $sectionId;
    }
    
    public function setLocations($locations){
        $this->locations = $locations;
    }
    
     public function render(){
        $this->template->setFile(__DIR__."/templates/ProgramControl.latte");
        $this->prepare();
        $this->template->render();
    }
    
    
     
    protected function createComponentCustomProgram(){
        $list = new ProgramListControl;
        $list->setEm($this->em);
        $list->setSectionId($this->sectionId);
        $list->setRoomId(5);
        $list->setTemplateName("ProgramListSimpleControl");
        $list->setTitle("Doprovodný program");
        return $list;
        
        
        
        
        
        
    }
  
    
    private function prepare(){
        $container = new ProgramContainer;
        $container->setLoader($this->em);
        $container->roomSequence = implode(",",$this->locations);
        $container->sectionId = $this->sectionId;
        $container->festivalStart = $this->festivalStart;
        $container->festivalEnd = $this->festivalEnd;
        $container->run();
        $this->fill($container);
        
    }
    
    private function fill($container){
        $this->template->slots = $container->slots;
        $this->template->programs = $container->programs;
        $this->template->rooms = $container->rooms;
        $this->template->timetable = $container->timeTable;
        $this->template->map = $container->map;

        
    }
    
}
