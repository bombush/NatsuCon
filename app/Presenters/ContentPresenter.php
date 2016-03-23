<?php

/*
 *  zadani prav
 *  zadani komponent
 * 
 * 
 */

namespace Natsu\Presenters;
use Natsu\Forms\BaseForm;
use Natsu\Forms\AttachmentFormFactory;
use Natsu\Forms\ContentComponentFormFactory;
use Natsu\Forms\PermissionFormFactory;







class ContentPresenter extends BasePresenter {

	/**
	 * @inject
	 * @var \Natsu\Model\EntityModel
	 */
    public $entityModel;
    private $controls;
    private $contentId;
    private $componentId;
    private $attachments;
    private $attachment;
    private $permission;
    private $permissionId;

    public $sectionId;
    
       /** @var AttachmentFormFactory @inject */
	public $factoryAttachment;
        
        /** @var ContentComponentFormFactory @inject */
	public $factoryContentComponent;
        /** @var PermissionFormFactory @inject */
        public $factoryPermission;

	/*
    public function inject(\Natsu\Model\EntityModel $entityModel){
        $this->entityModel = $entityModel;
        $this->entityModel->setTable("content");
    }*/
    
    public function actionView($id){



        $content = $this->entityModel;
        $content = $this->entityModel->getPrimary($id);
        
      //  print_r($content);

        $controlsModel = $this->entityModel->reflection("component");
        $this->controls = $controlsModel->getComponents($id);
        $this->entityModel->setTable("attachment");
        $this->attachments = $this->entityModel->fetchWhere(array("contentId" => $id));
        
        $model = $this->entityModel->reflection("program");
        $programs = $model->getProgramByContentId($id);
       // print_r($this->attachments);
        $this->setPermission($content);
        $this->add("content", $content);
      //  $this->add("routes", $routes);
        $this->add("attachments", $this->attachments);
        $this->add("programs", $programs);
        $this->prepare();
    }
    
    public function handleDelete($contentId){
        $content = $this->entityModel;
        $content = $this->entityModel->getPrimary($contentId);
        $this->setPermission($content);
        $rules = $this->data['rules'];
        if(isset($rules) && $rules->deletable == 1){
            $model = $this->entityModel->reflection("content");
            $model->deleteContent($contentId);
            
        }
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
        $this->add("rules", $pm->getRules());



    }
    
    
    public function actionPermissions($id = 0, $permissionId=0){
        $this->contentId = $id;
        $this->permissionId = $permissionId;
        $permissionModel = $this->entityModel->reflection("permission");
        $permissions = $permissionModel->getPermissions($id);
        $this->add("permissions", $permissions );
        $this->add("contentId", $id);
        $this->prepare();
        
        
        
    }
    
    public function actionComponents($id = 0, $componentId = 0){
        $this->contentId = $id;
        $this->componentId = $componentId;
        $controlsModel = $this->entityModel->reflection("component");
        $controls = $controlsModel->getComponents($id);
        $this->add("controls", $controls);
        $this->add("contentId", $id);
        $this->prepare();
        
        
        
    }
    
    
    public function actionAttachments($id = 0){
        $this->contentId = $id;
        $this->entityModel->setTable("attachment");
        $this->attachments = $this->entityModel->fetchWhere(array("contentId" => $this->contentId));
        
        $this->entityModel->setTable("content");
        $content = $this->entityModel->getPrimary($id);
        $this->setPermission($content);
        
        $this->add("contentId", $id);
        $this->add("pageTitle", "Přílohy k obsahu[$id]");
        $this->prepare();
        
    }

    public function actionForm($id = 0){
        if($id){
            $content = $this->entityModel->getPrimary($id);
            
            $this->entityModel->setTable("route");
            $routes = $this->entityModel->fetchWhere(array('contentId'=>$id));

            $this->setPermission($content);

            $this->add("content", $content);
            $this->add("routes", $routes);
            $this->add("pageTitle", $content->title." (Editace)");

        }
     //   print_R($content); exit;
    }

    public function renderForm(){
       if(isset($this->toRender['content'])){
        $content = $this->toRender['content'];
        $form = $this['contentForm']->setDefaults($content);
        print_r($this->toRender['routes']);
        /* 
        if(isset($this->toRender['routes']) && isset($this->toRender['routes'][0])){
             $this['contentForm']->setDefaults(array('routeUrl' => $this->toRender['routes'][0]->url));
         }
         * 
         */
       }
       $this->prepare();

    }
    
    
    
    

    public function actionDelete($id = 0){
        $content = $this->entityModel;
        $content = $this->entityModel->getPrimary($id);
       // print_r($this->attachments);
        $this->setPermission($content);
        $this->add("contentId", $id);
        $this->prepare();
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
        
        private function getControls(){
            $this->entityModel->setTable("component");
            $controls = $this->entityModel->table();
            $out = [];
            foreach($controls as $control){
                $out[$control->id] = $control->title;
            }
            
            return $out;
        }
        
        private function getControlById($id){
            $this->entityModel->setTable("contentcomponent");
            return $this->entityModel->table($id);
            
        }
        

        
        protected function createComponentComponentForm()
	{
                $this->factoryContentComponent->setContentId($this->contentId);
                $this->factoryContentComponent->setEm($this->entityModel);
                $this->factoryContentComponent->setComponents($this->getControls());
                $this->factoryContentComponent->setContentComponent($this->getControlById($this->componentId));
                
		$form = $this->factoryContentComponent->create();
		$form->onSuccess[] = function ($form) {
                        $form->getPresenter()->flashMessage("Uloženo");    
			$form->getPresenter()->redirect('Content:components',$this->contentId);
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
            if($this->user->getIdentity()->roleId == 1 || $this->user->getIdentity()->roleId == 2){
           $form->addText("routeUrl", "Routa"); 
        }
        
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
    
    public function createComponentProgramLink(){
    
        
        $program = $this->toRender['programs'][0];
        
        $control = new \Natsu\Control\ProgramLinkControl;
        $control->setSectionId($program->sectionId);
        $control->setLinkTimeTable('program');
        
        
        
        switch($program->typeId){
            case 3: $control->setLinkScreenings('promitani'); break;
            case 1: $control->setLinkLectures('prednasky'); break;
            case 4: $control->setLinkWorkshops('workshopy'); break;
            case 2: case 5: case 7:  $control->setLinkWorkshops('doprovodny-program'); break;
        }
        
        switch($program->roomId){
            case 1: $control->setLinkEdo('mistnost-edo'); break;
            case 2: $control->setLinkNara('mistnost-nara'); break;
            case 3: $control->setLinkMeiji('mistnost-meiji'); break;
            case 4: $control->setLinkWorkshop('mistnost-workshop'); break;
        }
        
        
        return $control;
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
			$form->getPresenter()->redirect('Content:attachments', $this->contentId);
		};
		return $form;
        }



    protected function createComponentGrid($name){
         $dataSource = $this->entityModel->reflection("datasource");
         $dibiSource = $dataSource->setTable("content")->table(NULL);
         
         $grid = new \Natsu\Control\GridControl;
         $grid->setName($name);
         $grid->setPk('id');
         $grid->setDibiSource($dibiSource);
         $grid->setColumns(
                 array(
                     'id' => 'ID',
                     'sectionId' => 'Sekce',
                     'title' => 'Title',
                     'isNews' => 'Novinka',
                     
                 )
         );
         
         $grid->setCallBack(array(
             'type' => function($item){
                      if($item->isDraft){
                          return "Draft";
                      }else if($item->isNews){
                          return "Novinka";
                      }else{
                          return "Stránka";
                      }

                  }
         ));
         
         $grid->setButtons(array(
             'form' => 'Form',
             'view' => 'View'
             
             
         ));

        $grid->run();
        return $grid->getGrid();
    }





    public function contentFormSucceeded($form){
       $values = $form->getValues();
       $this->entityModel->setTable("content");
      // print_r($values); exit;
       $routeUrl = "";
       if(isset($values->routeUrl)){
           $routeUrl = $values->routeUrl;
           unset($values->routeUrl);
           
       }
       
       if($values->id){
           $this->entityModel->update($values);
           if(!empty($routeUrl)){
            $rm = $this->entityModel->reflection("Route");
  
            $rm->updateRoute($values->id, $routeUrl);
           }
           
       }else{
           $values->id = $this->entityModel->insert($values);
           $rm = $this->entityModel->reflection("Route");
           $pageTitle = (empty ($values->pageTitle))? $values->title : $values->pageTitle;
           $rm->createRoute($values->id, $pageTitle);
           if(!empty($routeUrl)){
               $rm->updateRoute($values->id, $routeUrl);
           }
       }
       $this->flashMessage("OK");
       $this->redirect("Content:view", $values->id);
    }
    
    
    public function createComponentPermissionControl(){
                $this->factoryPermission->setContentId($this->contentId);
                $this->factoryPermission->setEm($this->entityModel);
                $pM = $this->entityModel->reflection("permission");
                $this->factoryPermission->setRoles($pM->getRoles());
                $this->factoryPermission->setContentPermission($pM->getPermissionById($this->permissionId));
                
		$form = $this->factoryPermission->create();
		$form->onSuccess[] = function ($form) {
                        $form->getPresenter()->flashMessage("Uloženo");    
			$form->getPresenter()->redirect('Content:permissions',$this->contentId);
		};
		return $form;
    }


    public function createComponentContentControl(){
        $control = new \Natsu\Control\ContentControl();
        $control->setControls($this->controls);
        $control->setEm($this->entityModel);

        return $control;
    }
    
    
    public function renderList(){
        $this->add("pageTitle", "Výpis stránek");
        $this->prepare();
    }

   

}
?>
