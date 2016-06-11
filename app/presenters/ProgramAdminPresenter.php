<?php

namespace Natsu\Presenters;

use Grido\Grid;
use Natsu\Control\ProgramGridControl;
use Natsu\Model\PermissionModel;
use Natsu\Model\ProgramModel;
use Natsu\Forms\ProgramEditFormFactory;

class ProgramAdminPresenter extends BasePresenter {

    /**
     * @inject
     * @var \Natsu\Model\EntityModel
     */
    public $entityModel;

    /** @var ProgramEditFormFactory @inject */
    public $programEditComponent;

    //akce: default - vypis
    //      add/change
    //      remove
    //      confirmRemove!

    public function actionDefault()
    {
          if(!$this->canUserManageProgram()){
            $this->redirect( 'Sign:in' );

            return;
        }
    }

    public function actionPreview($id = 0){
        $pm = $this->entityModel->reflection("program");
        $data = $pm->getFormDefaults($id);
       // dump($data); exit;
        if(isset($data['contentId'])){
            $this->redirect("Content:view", $data['contentId']);
        }
        
    }

    /**
     * @TODO: make this a tiny bit more obvious
     */
    public function actionEdit()
    {

        if ( !$this->canUserManageProgram() ){
            $this->redirect('Sign:in');
            return;
        }



        $programId = $this->getParameter('program_id', NULL);
        if($programId)
            $this->programEditComponent->setProgramId($programId);
        //if()
    }
    
    public function actionRemove()
    {

        if( !$this->canUserManageProgram())
        {
            $this->redirect('Sign:in');
            return;
        }


        $programId = $this->getParameter('program_id', NULL);
        if($programId){
             $pm = $this->entityModel->reflection("program");
             $data = $pm->getFormDefaults($programId);
             
             if(isset($data['contentId'])){
                 $pm = $this->entityModel->reflection( "Permission" );
                 $rules = $pm->getContentRules( $this->getUser(), $data[ 'contentId' ] );

                 //dump($rules); exit;

              
                  if(isset($rules) && $rules->deletable == 1){
                      $cm = $this->entityModel->reflection("content");
                      $cm->deleteContent($data['contentId']);
                      $cm->log($this->getUser()->getId(), ['entity' => 'content', 'entityId' => $data['contentId'], 'column' => 'DELETE', 'value' => 'OK']);

                      if($this->isAjax()) {
                          $response = [
                              'result' => TRUE
                          ];
                          $this->sendJson($response);
                          return;

                      } else {
                          $this->flashMessage( "Deleted!" );
                          $this->redirect( "Program:default" );
                      }
                  }
             }
            
        }
                $this->flashMessage("Error 403: Delete operation was not proceed.");
     
        //if()
    }

    public function actionPublishSection()
    {
        if ( !$this->canUserPublishProgram() ) {
            $this->redirect( 'Sign:in' );

            return;
        }

        $sectionId = $this->context->getParameters()['sectionId'];
        $programModel = $this->entityModel->reflection('Program');

        $programModel->publishAllInSection($sectionId);

        if($this->isAjax()) {
            $this->sendJson([
                                'status' => true
                            ]);
        } else {
            echo 'OK'; exit;
        }
    }


    public function createComponentProgramGrid()
    {
        $programModel = $this->entityModel->reflection('Program');
        $programGrid = new ProgramGridControl($programModel);
        $programGrid->setProgramStart($this->context->getParameters()['programStart']);
        $programGrid->setProgramEnd( $this->context->getParameters()[ 'programEnd' ]);
        $programGrid->setSectionId( $this->context->getParameters()[ 'sectionId' ]);
        $programGrid->setCanUserPublish($this->canUserPublishProgram());
        return $programGrid;
    }

    public function createComponentProgramEditForm()
    {
        return $this->programEditComponent->create();
    }

    protected function canUserManageProgram()
    {
        return
        ( $this->user->loggedIn &&
            ( in_array( $this->user->identity->roleId, [ PermissionModel::ADMIN_ROLE, PermissionModel::FUHRER_ROLE, PermissionModel::SUPERVISOR_ROLE ] ) )
        );
    }

    protected function canUserPublishProgram()
    {
        return
            ( $this->user->loggedIn &&
                ( in_array( $this->user->identity->roleId, [ PermissionModel::FUHRER_ROLE ] ) )
            );
    }
}
?>
