<?php

namespace Natsu\Presenters;

use Nette;
use App\Model;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
	/**
	 * @inject
	 * @var \DibiConnection
	 */
	public $dibi;

    public $toRender = array();

    public function add($key, $var){
        $this->toRender[$key] = $var;
    }

    protected function startup()
    {
		parent::startup();


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
        $menu = new \Natsu\Control\MainMenuControl();
        return $menu;
    }
}
