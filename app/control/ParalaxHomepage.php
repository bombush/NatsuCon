<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Natsu\Control;

/**
 * Description of ParalaxHomepage
 *
 * @author yasuri
 */
class ParalaxHomepage extends BaseControl {
    
    public function render(){
        $this->template->setFile(__DIR__."/templates/ParalaxHomepage.latte");
        $this->template->render();
    }
}
