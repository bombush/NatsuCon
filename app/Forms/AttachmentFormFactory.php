<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Natsu\Forms;


use Nette;
use Natsu\Forms\BaseForm;
use Nette\Http\FileUpload;

/**
 * Description of AttachmentFormFactory
 *
 * @author Josef Hruby
 */
class AttachmentFormFactory extends Nette\Object{
    //put your code here
    
    private $contentId;
    private $attachment;
    /**
     *
     * @var Natsu\Model\EntityModel
     */
    private $em;
    
    public function setAttachment($attachment){
        $this->attachment = $attachment;
    }
    
    public function setContentId($contentId){
        $this->contentId = $contentId;  
    }
    
    public function setEm($em){
        $this->em = $em;
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
                
                if($this->attachment){
                    $form->setDefaults($this->attachment);
                }
		return $form;   
        } 
        
        private function createAttachment($values){
        switch($values->mime){
            case 'MAINIMAGE':
                $att = new \Custom\Content\MainimageAttachment();
                $att->setRow($values);
                $att->create();
                break;
            case 'IMAGE': 
                $att = new \Custom\Content\ImageAttachment();
                $att->setRow($values);
                $att->create();
                break;
        }
        }
        
        public function formSucceeded(BaseForm $form, $values){
            
            
            
            if($values->file->isOk()){
                //$filename = $values->file->getSanitizedName();
                $this->createAttachment($values);
                $values->url = $this->values->contentId."-".$values->file->getSanitizedName();
                
              //  print_r($values); exit;
                
            }
            
           // print_r($values); exit;

            unset($values->file);
            
            $this->em->setTable("attachment");
            if($values->id){
                $this->em->update($values);
            }else{
                unset($values->id);
                $this->em->insert($values);
            }

        }
    
}
