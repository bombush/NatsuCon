<?php

namespace Natsu\Control;

use Nette\Application\UI;

class Test1Control extends ContentControl {

    private $key;
    private $value;
    public function setKey($key) {
        $this->key = $key;
    }

    public function setValue($value) {
        $this->value = $value;
    }

        
    
     public function render(){
        $this->template->setFile(__DIR__."/templates/Test1Control.latte");
        $this->template->key = $this->key;
        $this->template->value = $this->value;
        $this->template->render();
    }
}
?>
