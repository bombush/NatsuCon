<?php

namespace Natsu\Presenters;

use Nette;
use App\Model;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{

	/** @var \Natsu\Control\IMainMenuControlFactory @inject */
    public $mainMenuContolFactory;

    public $toRender = array();
    public $sectionId;


   
    public function add($key, $var){
        $this->toRender[$key] = $var;
    }



    protected function startup()
    {
		parent::startup();

                $this->setSectionId($this->context->parameters['sectionId']);

		if (!$this->getUser()->isLoggedIn()) {
			if ($this->getUser()->logoutReason === Nette\Security\IUserStorage::INACTIVITY) {
				//$this->flashMessage('You have been signed out due to inactivity. Please sign in again.');
			}
			// $this->redirect('Sign:in', array('backlink' => $this->storeRequest()));
		}
    }


    public function prepare(){
        foreach($this->toRender as $key => $var){
        $this->template->$key = $var;
        }
    }

    public function createComponentMainMenu(){
        return $this->mainMenuContolFactory->create();
    }

     public function setSectionId($sectionId) {
        $this->sectionId = $sectionId;
    }
}
