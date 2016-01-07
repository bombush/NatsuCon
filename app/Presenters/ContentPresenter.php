<?php

namespace Natsu\Presenters;
use Natsu\Forms\BaseForm;


use App\Controls\Grido\Grid;
use Nette\Utils\Html;




class ContentPresenter extends BasePresenter {

	/**
	 * @inject
	 * @var \Natsu\Model\EntityModel
	 */
    public $entityModel;
    private $controls;

    public $sectionId;

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
     //   print_R($content); exit;
    }

    public function renderForm(){
       if(isset($this->toRender['content'])){
        $content = $this->toRender['content'];
        $form = $this['contentForm']->setDefaults($content);
       }
       $this->prepare();

    }

    public function actionDelete($id = 0){

    }

    public function createComponentContentForm(){
        $form = new BaseForm();
        $form->addHidden("id");
        $form->addHidden("userId")->setDefaultValue($this->getUser()->id);
        $form->addText("sectionId", "Sekce")->setDefaultValue(0);
        $form->addText("title", "Název")->setRequired();
        $form->addText("author", "Autor");
        $form->addText("pageTitle", "Název stránky");
        $form->addText("anotation", "Krátký text");
        $form->addTextArea("text", "Dlouhý text")->setAttribute('class', 'wysiwyg');
        $form->addDateTimePicker("activeFrom", "Aktivní od:")->setDefaultValue(date('Y-m-d G:i', time()));
        $form->addDateTimePicker("activeUntil", "Aktivní do:");
        $form->addCheckbox("isDraft", "Draft")->setDefaultValue(1);
        $form->addCheckbox("isNews", "Novinka");
        $form->addCheckbox("isSticky", "Sticky bit");
        $form->addSubmit("save", "Uložit");
        $form->onSuccess[] = $this->contentFormSucceeded;
        return $form;
    }

     public function createComponentBreadCrumbs(){
        $bc = new \Natsu\Control\BreadcrumbControl();
        $bc->setContent($this->toRender['content']);
        return $bc;
    }
    
    public function createComponentProgramInfo(){
        $pi = new \Natsu\Control\ProgramInfoControl();
        $pi->setEm($this->entityModel);
        $pi->setContent($this->toRender['content']);
        return $pi;
    }



    protected function createComponentGrid($name){
         $dataSource = $this->entityModel->reflection("datasource");
         $dibiSource = $dataSource->setTable("content")->table(NULL);

         $grid = new Grid($this, $name);
         $grid->model = $dibiSource;
         $grid->setPrimaryKey("id");
         $grid->addColumnText('id', 'ID')
            ->setFilterText()
            ->setSuggestion();
          $grid->addColumnText('sectionId', 'Sekce')
            ->setFilterText()
            ->setSuggestion();
          
          $grid->addColumnText('title', 'Title')
            ->setFilterText()
            ->setSuggestion();
          $grid->addColumnText('type', 'Typ')
                  ->setColumn(function($item){
                      if($item->isDraft){
                          return "Draft";
                      }else if($item->isNews){
                          return "Novinka";
                      }else{
                          return "Stránka";
                      }

                  });

       


          
         $grid->addActionHref('form', 'Form');
         $grid->addActionHref('view', 'View');
        // $grid->addActionHref('form', 'Form');

        /*
        $grid->addActionHref('delete', 'Delete')
            ->setIcon('trash')
            ->setConfirm(function($item) {
                return "Are you sure you want to delete {$item->title}?";
        });
         *
         */
           
           

       // $grid->filterRenderType = $this->filterRenderType;
        $grid->setExport();

        return $grid;






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
