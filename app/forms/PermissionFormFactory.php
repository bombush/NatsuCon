<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Natsu\Forms;

/**
 * Description of PermissionFormFactory
 *
 * @author yasuri
 */
class PermissionFormFactory  extends BaseForm{
    private $contentId;
    private $contentPermission;
    private $roles;
    private $em;
    
    public function getContentId() {
        return $this->contentId;
    }

    public function getContentPermission() {
        return $this->contentPermission;
    }

    public function getRoles() {
        return $this->roles;
    }

    public function getEm() {
        return $this->em;
    }

    public function setContentId($contentId) {
        $this->contentId = $contentId;
    }

    public function setContentPermission($contentPermission) {
        $this->contentPermission = $contentPermission;
    }

    public function setRoles($roles) {
        $this->roles = $roles;
    }

    public function setEm($em) {
        $this->em = $em;
    }
    
    public function create(){
        $form = new \Natsu\Forms\BaseForm;
        $form->addHidden("id");
        $form->addHidden("contentId")->setValue($this->contentId);
        $form->addSelect("roleId", "Role")->setItems($this->roles);
        $form->addCheckbox("writable", "Zápis");
        $form->addCheckbox("deletable", "Smazani");
        $form->addCheckbox("forbidden", "Zákaz");
        $form->addSubmit("save", "Uložit");
        
                $form->onSuccess[] = array($this, 'formSucceeded');
                
                if($this->contentPermission){
                    
                    $form->setDefaults($this->contentPermission);
                }
		return $form;   
    }

    
     public function formSucceeded(BaseForm $form, \Nette\Utils\ArrayHash $values){
         $this->em->setTable("permission");
         if($values->id){
             $this->em->update($values);
         }else{
             unset($values->id);
             $this->em->insert($values);
         }
     }

}
