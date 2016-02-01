<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Natsu\Forms;


use Nette;
use Natsu\Forms\BaseForm;

/**
 * Description of AttachmentFormFactory
 *
 * @author Josef Hruby
 */
class AttachmentFormFactory extends Nette\Object{
    //put your code here
    
    private $contentId;
    
    public function setContentId($contentId){
        $this->contentId = $contentId;  
    }
    
    public function create()
	{
		$form = new \Natsu\Forms\BaseForm;
                $form->addHidden("id")->setDefaultValue("0");
                $form->addHidden("contentId")->setDefaultValue($this->contentId);
                $form->addSelect("mime", "Typ", array("IMAGE" => "OBRAZEK (ALESPON 640x480)", "HEADIMAGE" => "HLAVNI OBRAZEK (100x700)", "YOUTUBE" => "YOUTUBE ODKAZ"));
                $form->addUpload("file", "Soubor (Pro typ obrázek:)");
                $form->addText("title", "Název");
                $form->addText("url", "Odkaz");
                $form->addSubmit("save", "Uložit");
                $form->onSuccess[] = array($this, 'formSucceeded');
		return $form;   
        } 
        
        public function formSucceeded(BaseForm $form, $values){


        }
    
}
