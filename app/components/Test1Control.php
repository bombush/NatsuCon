<?php

namespace Natsu\Control;

use Nette\Application\UI;

class Test1Control extends ContentControl {

     public function render(){
        $this->template->setFile(__DIR__."/templates/Test1Control.latte");
        $this->template->render();
    }
}
?>
