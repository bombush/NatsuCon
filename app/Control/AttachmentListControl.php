<?php

    namespace Natsu\Control;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AttachmentListControl
 *
 * @author Josef Hruby
 */
class AttachmentListControl extends BaseControl{
    //put your code here
    private $attachments;
    
    public function setAttachments($attachments){
        $this->attachments = $attachments;
    }
    
    public function render(){
        $this->template->setFile(__DIR__."/templates/AttachmentListControl.latte");
        $this->template->attachments = $this->attachments;
        $this->template->render();
    }
}
