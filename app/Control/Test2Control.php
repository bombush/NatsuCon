<?php
namespace Natsu\Control;

use Nette\Application\UI;

class Test2Control extends ContentControl{
     public function render(){
        $this->template->setFile(__DIR__."/templates/Test2Control.latte");
        $this->template->render();
    }
}
?>
