<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Natsu\Control;

/**
 * Description of ProgramLinkControl
 *
 * @author yasuri
 */
class ProgramLinkControl extends BaseControl {
    private $linkLectures;
    private $sectionId;
    private $linkScreenings;
    private $linkWorkshops;
    private $linkDoprovod;
    private $linkTimeTable;
    private $linkCustom;
    private $linkEdo;
    private $linkNara;
    private $linkMeiji;
    private $linkWorkshop;
    private $linkCustomn;
    public function setLinkLectures($linkLectures) {
        $this->linkLectures = $linkLectures;
    }

    public function setSectionId($sectionId) {
        $this->sectionId = $sectionId;
    }

    public function setLinkScreenings($linkScreenings) {
        $this->linkScreenings = $linkScreenings;
    }

    public function setLinkWorkshops($linkWorkshops) {
        $this->linkWorkshops = $linkWorkshops;
    }
    
    public function setLinkDoprovod($linkDoprovod) {
        $this->linkDoprovod = $linkDoprovod;
    }

    public function setLinkTimeTable($linkTimeTable) {
        $this->linkTimeTable = $linkTimeTable;
    }

    public function setLinkCustom($linkCustom) {
        $this->linkCustom = $linkCustom;
    }

    public function setLinkEdo($linkEdo) {
        $this->linkEdo = $linkEdo;
    }

    public function setLinkNara($linkNara) {
        $this->linkNara = $linkNara;
    }

    public function setLinkMeiji($linkMeiji) {
        $this->linkMeiji = $linkMeiji;
    }

    public function setLinkWorkshop($linkWorkshop) {
        $this->linkWorkshop = $linkWorkshop;
    }

    public function setLinkCustomn($linkCustomn) {
        $this->linkCustomn = $linkCustomn;
    }

    public function render(){
        $this->template->setFile(__DIR__."/templates/ProgramLinkControl.latte");
        
        
        foreach(get_object_vars($this) as  $property => $value){
            $this->template->$property = $value;
        }
       
        
        $this->template->render();
    }
    
}
