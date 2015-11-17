<?php
namespace Natsu\Control;

class MainMenuControl extends BaseControl {

    public function render(){
        $this->template->setFile(__DIR__."/templates/MainMenuControl.latte");
        $this->template->render();
    }
}

interface IMainMenuControlFactory
{

	/**
	 * @return MainMenuControl
	 */
	public function create();
}
?>
