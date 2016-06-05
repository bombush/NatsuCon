<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Natsu\Control;

/**
 * Description of ProgramListControl
 *
 * @author Josef Hruby
 */
class ProgramListControl extends BaseControl {
    private $sectionId;
    private $typeId;
    private $startTime;
    private $endTime;
    private $title;
    private $roomId;
    private $templateName;
    
    /**
     *
     * @var Natsu\Model\EntityModel 
     */
    private $em;
    
    
    public function setEm($em){
        $this->em = $em;
    }
    
    public function setTemplateName($templateName){
        $this->templateName = $templateName;
    }
    
    
    function getTitle() {
        return $this->title;
    }

    function setTitle($title) {
        $this->title = $title;
    }
    
    function setRoomId($roomId){
        $this->roomId = $roomId;
    }

        
    function getSectionId() {
        return $this->sectionId;
    }

    function getTypeId() {
        return $this->typeId;
    }

    function getStartTime() {
        return $this->startTime;
    }

    function getEndTime() {
        return $this->endTime;
    }

    function setSectionId($sectionId) {
        $this->sectionId = $sectionId;
    }

    function setTypeId($typeId) {
        $this->typeId = $typeId;
    }

    function setStartTime($startTime) {
        $this->startTime = $startTime;
    }

    function setEndTime($endTime) {
        $this->endTime = $endTime;
    }
    
    
    public function render(){
        
        $templateName = "ProgramListControl2";
        if(isset($this->templateName)){
            $templateName = $this->templateName;
        }
        $this->template->setFile(__DIR__."/templates/".$templateName.".latte");
        $this->prepare();
        $this->template->render();
        
        
    }
    
     
    
    private function prepare(){
        $em = $this->em->reflection("program");
        $programs = $em->getProgramsList($this->sectionId,$this->typeId,$this->roomId);
       // \Tracy\Debugger::dump($programs);
        $this->template->title = $this->title;
        $this->template->programs = $programs;
    }

        
    //put your code here
}
