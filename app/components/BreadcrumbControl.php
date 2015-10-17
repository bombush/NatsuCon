<?php

namespace Natsu\Control;
use Nette\Application\UI\Control;

class BreadcrumbControl extends Control {
    private $content;

    public function setContent($content){
        $this->content = $content;
    }


    public function render(){

        $this->template->setFile(__DIR__."/templates/BreadcrumbControl.latte");
        $this->template->content = $this->content;
        $this->template->render();
    }
}
?>
