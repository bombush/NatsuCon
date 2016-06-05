<?php

namespace Natsu\Control;

class BreadcrumbControl extends BaseControl {
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

interface IBreadcrumbControlFactory{

	/**
	 * @return MainMenuControl
	 */
	public function create();
}
?>
