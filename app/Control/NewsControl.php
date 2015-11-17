<?php
namespace Natsu\Control;

use Nette\Utils\ArrayHash;

class NewsControl extends BaseControl {

    public function render(){
        $this->template->setFile(__DIR__."/templates/NewsControl.latte");
        $this->template->render();

    }

}

interface INewsControlFactory{

	/**
	 * @return NewsControl
	 */
	public function create();
}
?>
