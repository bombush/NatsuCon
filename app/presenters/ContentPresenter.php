<?php

namespace Natsu\Presenters;
use Nette\Application\UI\Form;

class ContentPresenter extends BasePresenter {

	/**
	 * @inject
	 * @var \Natsu\Model\EntityModel
	 */
    public $entityModel;
    private $controls;

	/*
    public function inject(\Natsu\Model\EntityModel $entityModel){
        $this->entityModel = $entityModel;
        $this->entityModel->setTable("content");
    }*/
    
    public function actionView($id){
		$content = $this->entityModel;
        $content = $this->entityModel->getPrimary($id);
        $controlsModel = $this->entityModel->reflection("component");
        $this->controls = $controlsModel->getComponents($id);
        $this->setPermission($content);
        $this->add("content", $content);
        $this->prepare();
    }

    private function setPermission($content){
        $pm = $this->entityModel->reflection("Permission");
        $userId = $this->getUser()->loggedIn ? $this->getUser()->id : 0;
        $pm->setUserId($userId);
        $roleId = $userId ? $this->getUser()->getIdentity()->roleId : 0;
        $pm->setRoleId($roleId);
        $result = $pm->checkContent($content);
       
        $this->add("access", $result);



    }

    public function actionForm($id = 0){
        if($id){
            $content = $this->entityModel->getPrimary($id);
            $this->add("content", $content);

        }
        //print_R($content);
    }

    public function renderForm(){
       if(isset($this->toRender['content'])){
        $content = $this->toRender['content'];
        $form = $this['contentForm']->setDefaults($content);
       }

    }

    public function actionDelete($id = 0){

    }

    public function createComponentContentForm(){
        $form = new Form();
        $form->addHidden("id");
        $form->addHidden("userId")->setDefaultValue($this->getUser()->id);
        $form->addText("sectionId", "Sekce")->setDefaultValue(0);
        $form->addText("title", "Název")->setRequired();
        $form->addText("author", "Autor");
        $form->addText("pageTitle", "Název stránky");
        $form->addText("anotation", "Krátký text");
        $form->addTextArea("text", "Dlouhý text")->setAttribute('class', 'mceEditor');
        $form->addText("activeFrom", "Aktivní od:")->setDefaultValue(date('Y-m-d G:i:s', time()));
        $form->addText("activeUntil", "Aktivní do:");
        $form->addCheckbox("isDraft", "Draft")->setDefaultValue(1);
        $form->addCheckbox("isNews", "Novinka");
        $form->addCheckbox("isSticky", "Sticky bit");
        $form->addSubmit("save", "Uložit");
        $form->onSuccess[] = [$this, 'contentFormSucceeded'];
        return $form;
    }

     public function createComponentBreadCrumbs(){
        $bc = new \Natsu\Control\BreadcrumbControl();
        $bc->setContent($this->toRender['content']);
        return $bc;
    }

    public function contentFormSucceeded($form){
       $values = $form->getValues();
      // print_r($values); exit;
       if($values->id){
           $this->entityModel->update($values);
       }else{
           $values->id = $this->entityModel->insert($values);
           $rm = $this->entityModel->reflection("Route");
           $pageTitle = (empty ($values->pageTitle))? $values->title : $values->pageTitle;
           $rm->createRoute($values->id, $pageTitle);
       }
       $this->flashMessage("OK");
       $this->redirect("Content:view", $values->id);
    }


    public function createComponentContentControl(){
        $control = new \Natsu\Control\ContentControl();
        $control->setControls($this->controls);

        return $control;
    }

}
?>
