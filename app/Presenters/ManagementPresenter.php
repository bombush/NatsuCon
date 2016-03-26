<?php

/**
 * useri + jejich management
 * emailove sablony
 * 
 * 
 * 
 */

namespace Natsu\Presenters;

use Mesour\DataGrid\NetteDbDataSource,
	Mesour\DataGrid\Grid;

/**
 * Description of ManagementPresenter
 *
 * @author campari
 */
class ManagementPresenter  extends BasePresenter {
    private $entityModel;

    public function inject(\Natsu\Model\EntityModel $entityModel){
        $this->entityModel = $entityModel;

    }
    
    public function createComponentProgram(){
      //  print_r($this->context->parameters); exit;
      //  print_r(date("Y-m-d G:i:s",$this->context->parameters['programStart']->getTimestamp()); exit;        
        
        $ctl = new \Natsu\Control\ProgramControl;
        $ctl->setLocations("1,2,3,4");
        $ctl->setFestivalStart(date("Y-m-d G:i:s",$this->context->parameters['programStart']->getTimestamp()));
        $ctl->setFestivalEnd(date("Y-m-d G:i:s",$this->context->parameters['programEnd']->getTimestamp()));
        $ctl->setSectionId($this->context->parameters['sectionId']);
        $ctl->setEm($this->entityModel);
        return $ctl;
        
        
    }
    
    
    public function handleSave(){
    require_once __DIR__ ."/../../vendor/fineuploader/Uploader/handler.php"; 
    $uploader = new \UploadHandler();
    $uploader->allowedExtensions = array("jpeg", "jpg", "png", "gif");
    $result = $uploader->handleUpload(__DIR__ . '/../../www/images/uploaded');
    $this->sendResponse(new \Nette\Application\Responses\JsonResponse($result));

    }
    
    public function createComponentMultiUpload(){
        $control = new \Nette\Forms\Form;
        $control->addMultiUpload("upload", "Upload souboru:");
        $control->addSubmit("save", "Uložit");
        $control->onSuccess[] = $this->uploadFormSucceeded;
        
        return $control;
        
    }
    
    public function uploadFormSucceeded($form, $values){
        
    }
    
    
     public function createComponentProgramListControl(){
        $ctl = new \Natsu\Control\ProgramListControl;
        $ctl->setSectionId(2015);
        $ctl->setTitle("Promítání");
        $ctl->setTypeId("3");
        $ctl->setEm($this->entityModel);
        return $ctl;
     }
        
     
protected function createComponentPagesList($name){
         $dataSource = $this->entityModel->reflection("datasource");
         $dibiSource = $dataSource->setTable("content")->table(NULL);
         
         $grid = new \Natsu\Control\GridControl;
         $grid->setName($name);
         $grid->setPk('id');
         $grid->setPresenter("Content");
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
    
    protected function createComponentUserList($name){
         $dataSource = $this->entityModel->reflection("datasource");
         $dibiSource = $dataSource->userList();
         
         $grid = new \Natsu\Control\GridControl;
         $grid->setName($name);
         $grid->setPk('id');
                 $grid->setPresenter("Sign");
         $grid->setDibiSource($dibiSource);
         $grid->setColumns(
                 array(
                     'id' => 'ID',
                     'username' => 'Username',
                     'fullname' => 'Fullname',
                     'email' => 'Email',
           
                     
                 )
         );
         
         
         $grid->setButtons(array(
                    'edit' => 'Změnit'
                ));
         
        $grid->run();
        return $grid->getGrid();
    }

}
?>
