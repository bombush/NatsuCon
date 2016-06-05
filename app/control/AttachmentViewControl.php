<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Natsu\Control;

/**
 * Description of AttachmentViewControl
 *
 * @author Josef Hruby
 */
class AttachmentViewControl extends BaseControl{
    //put your code here
    private $attachments;
    
    public function setAttachments($attachments){
        $this->attachments = $attachments;
    }
    
    public function render(){
       $this->template->setFile(__DIR__."/templates/AttachmentViewControl.latte");
       $this->detectAttachments(); 
       $this->template->attachments = $this->mergeAttachments();
        $this->template->render(); 
        
    }
    
    private function detectAttachments(){
        foreach($this->attachments as $key => $attachment){
            if($attachment->mime == "IMAGE"){
                
                $image = new \Custom\Content\ImageAttachment();
                $image->setRow($attachment);
                $image->run();
                $this->attachments[$key]->data = $image;
            }
            
        }
    }
    
    private function mergeAttachments(){
        $out = array();
        foreach($this->attachments as $att){
            $out[$att->mime][] = $att;
        }
        $this->attachments = $out;
        return $this->attachments;
    }
}
