<?php

namespace Natsu\Presenters;
use Natsu\Forms\BaseForm;
use Natsu\Forms\AttachmentFormFactory;


use App\Controls\Grido\Grid;
use Nette\Utils\Html;




class ContentPresenter extends BasePresenter {

	/**
	 * @inject
	 * @var \Natsu\Model\EntityModel
	 */
    public $entityModel;
    private $controls;
    private $contentId;
    private $attachments;
    private $attachment;

    public $sectionId;
    
       /** @var AttachmentFormFactory @inject */
	public $factoryAttachment;

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
        $this->entityModel->setTable("attachment");
        $this->attachments = $this->entityModel->fetchWhere(array("contentId" => $id));
        
        $model = $this->entityModel->reflection("program");
        $programs = $model->getProgramByContentId($id);
       // print_r($this->attachments);
        $this->setPermission($content);
        $this->add("content", $content);
        $this->add("attachments", $this->attachments);
        $this->add("programs", $programs);
        $this->prepare();
    }
    
    
    public function actionAttachment($id){
        $this->entityModel->setTable("attachment");
        $this->attachment = $this->entityModel->table($id);
       // $ia = new \Custom\Content\ImageAttachment();
       // $ia->setRow($this->attachment);
       // $ia->run();
        
      //  print_r($ia);
        
        
        $this->add("attachment", $this->attachment);
      //  $this->add("attachments", $this->attachments);
     //   $this->add("programs", $programs);
        $this->prepare();
        
        //print_r($attachment); exit;
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
    
    public function actionAttachments($id = 0){
        $this->contentId = $id;
        $this->entityModel->setTable("attachment");
        $this->attachments = $this->entityModel->fetchWhere(array("contentId" => $this->contentId));
        
        $this->add("contentId", $id);
        $this->add("pageTitle", "Přílohy k obsahu[$id]");
        $this->prepare();
        
    }

    public function actionForm($id = 0){
        if($id){
            $content = $this->entityModel->getPrimary($id);
            $this->add("content", $content);
            $this->add("pageTitle", $content->title." (Editace)");

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
    
    /**
	 * Sign-in form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSignInForm()
	{
		$form = $this->factoryAttachment->create();
		$form->onSuccess[] = function ($form) {
			$form->getPresenter()->redirect('Content:attachment '.$this->contentId);
		};
		return $form;
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
        $pi->setAttachments($this->attachments);
        $pi->setContent($this->toRender['content']);
        return $pi;
    }
    
    
     /**
	 * Forget form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentAttachments()
	{
		$ctl = new \Natsu\Control\AttachmentListControl();
                $ctl->setAttachments($this->attachments);
                return $ctl;
		
	}
        
        protected function createComponentAttachmentsView(){
            $ctl = new \Natsu\Control\AttachmentViewControl;
            $ctl->setAttachments($this->attachments);
            return $ctl;
        }
        
        
        protected function createComponentAttachmentForm(){
            $this->factoryAttachment->setAttachment($this->attachment);
            $this->factoryAttachment->setContentId($this->contentId);
            $this->factoryAttachment->setEm($this->entityModel);
            $form = $this->factoryAttachment->create();
            $form->onSuccess[] = function ($form) {
                        $form->getPresenter()->flashMessage("Saved");
			$form->getPresenter()->redirect('Content:attachments', $this->attachment->contentId);
		};
		return $form;
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
    
    
    public function renderList(){
        $this->add("pageTitle", "Výpis stránek");
        $this->prepare();
    }

   

}
?>
