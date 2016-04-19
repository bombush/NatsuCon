<?php

namespace Natsu\Presenters;

use Grido\Grid;
use Natsu\Control\ProgramGridControl;
use Natsu\Model\ProgramModel;
use Natsu\Forms\ProgramEditFormFactory;

class ProgramPresenter extends BasePresenter {

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
        if ( !( $this->user->loggedIn && ( $this->user->identity->roleId == 1 || $this->user->loggedIn && ( $this->user->identity->roleId == 2 ) ) ) ) {
            $this->redirect( 'Sign:in' );

            return;
        }
    }

    public function actionEdit()
    {

        if(!($this->user->loggedIn && ($this->user->identity->roleId == 1  || $this->user->loggedIn && ($this->user->identity->roleId == 2 )))){
            $this->redirect('Sign:in');
            return;
        }



        $programId = $this->getParameter('program_id', NULL);
        if($programId)
            $this->programEditComponent->setProgramId($programId);
        //if()
    }

    public function createComponentProgramGrid()
    {
        $programModel = $this->entityModel->reflection('Program');
        $programGrid = new ProgramGridControl($programModel);
        $programGrid->setProgramStart($this->context->getParameters()['programStart']);
        $programGrid->setProgramEnd( $this->context->getParameters()[ 'programEnd' ]);
        $programGrid->setSectionId( $this->context->getParameters()[ 'sectionId' ]);
        return $programGrid;
    }

    public function createComponentProgramEditForm()
    {
        return $this->programEditComponent->create();
    }
}
?>
