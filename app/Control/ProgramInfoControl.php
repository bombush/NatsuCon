<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Natsu\Control;
use Natsu\Model\EntityModel;

/**
 * Description of ProgramInfoControl
 *
 * @author yasuri
 */
class ProgramInfoControl extends ContentControl{
    private $content;
    /**
     *
     * @var Natsu\Model\EntityModel 
     */
    private $em;
    
    public function setContent($content) {
        $this->content = $content;
    }

    public function setEm(\Natsu\Model\EntityModel $em) {
        $this->em = $em;
    }
    
    
    public function render(){
        $this->template->setFile(__DIR__."/templates/ProgramInfo.latte");
        $this->prepare();
        $this->template->render();
    }
    
    private function prepare(){
        $model = $this->em->reflection("program");
        $this->template->programs = $model->getProgramByContentId($this->content->id);
        $this->template->content = $this->content;
    }


    
    
    
}
