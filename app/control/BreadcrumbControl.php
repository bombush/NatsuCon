<?php

namespace Natsu\Control;

class BreadcrumbControl extends BaseControl {
    private $content;
    private $components;
    private $sectionId;
    
    private $forProgram = false;

    public function setContent($content){
        $this->content = $content;
    }
    
    public function setComponents($components){
        $this->components = $components;
    }
    
    public function setSectionId($sectionId){
        $this->sectionId = $sectionId;
    }


    public function render(){

        $this->template->setFile(__DIR__."/templates/BreadcrumbControl.latte");
        $this->template->content = $this->content;
        $this->prepareProgrammLink();
        $this->template->sectionId = $this->sectionId;
        $this->template->render();
    }
    
    
    private function prepareProgrammLink(){
        foreach($this->components as $c){
            if($c->id == 2 || $c->id == 4){
                $this->forProgram = true;
                parse_str($c->params, $out);
                $this->setSectionId($out['sectionId']);
            }
        }
        
        
    }
}

interface IBreadcrumbControlFactory{

	/**
	 * @return MainMenuControl
	 */
	public function create();
}
?>
