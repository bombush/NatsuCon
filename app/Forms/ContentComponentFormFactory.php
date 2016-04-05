<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Natsu\Forms;


use Nette;
use Natsu\Forms\BaseForm;

/**
 * Description of ContentComponentFormFactory
 *
 * @author yasuri
 */
class ContentComponentFormFactory extends Nette\Object{
    
    private $contentId;
    private $contentComponent;
    private $components;
    private $em;
    
    public function getContentId() {
        return $this->contentId;
    }

    public function getContentComponent() {
        return $this->contentComponent;
    }

    public function getEm() {
        return $this->em;
    }
    
    public function setComponents($components){
        $this->components = $components;
    }

    public function setContentId($contentId) {
        $this->contentId = $contentId;
    }

    public function setContentComponent($contentComponent) {
        $this->contentComponent = $contentComponent;
    }

    public function setEm($em) {
        $this->em = $em;
    }
    
    
    public function create(){
        $form = new \Natsu\Forms\BaseForm;
        $form->addHidden("id");
        $form->addHidden("contentId")->setDefaultValue($this->contentId);
        $form->addSelect("componentId", "Komponenta")->setItems($this->components)->setRequired();
        $form->addText("params", "Parametry");
        $form->addSubmit("save", "Uložit");
        
                $form->onSuccess[] = array($this, 'formSucceeded');
                
                if($this->contentComponent){
                    
                    $form->setDefaults($this->contentComponent);
                }
		return $form;   
        
        
        
    }

   public function formSucceeded(BaseForm $form, \Nette\Utils\ArrayHash $values){
       //print_r($values); exit;
       $this->em->setTable("contentcomponent");
       if($values->id){
           $this->em->update($values);
       }else{
           unset($values->id);
           $this->em->insert($values); 
           
       }
       
       
   }    
    
    //put your code here
}
