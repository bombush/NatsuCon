<?php
namespace Natsu\Control;

use Nette\Application\UI;
use Nette\Utils\ArrayHash;

class NewsControl extends UI\Control{

    public function render(){
        $this->template->setFile(__DIR__."/templates/NewsControl.latte");
        $this->template->render();

    }

}
?>
