<?php
namespace Natsu\Control;
use Nette\Application\UI\Control;

class MainMenuControl extends Control {

    public function render(){
        $this->template->setFile(__DIR__."/templates/MainMenuControl.latte");
        $this->template->render();
    }
}
?>
