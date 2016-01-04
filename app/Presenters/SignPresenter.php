<?php

namespace Natsu\Presenters;

use Nette;
use Natsu\Forms\SignFormFactory;
use Natsu\Forms\ForgetFormFactory;
use Natsu\Forms\NewPassFormFactory;
use Natsu\Forms\MyAccountFormFactory;
use Natsu\Model\UserManager;
use Natsu\Model\EntityModel;


class SignPresenter extends BasePresenter
{
	/** @var SignFormFactory @inject */
	public $factory;

        /** @var ForgetFormFactory @inject */
	public $factoryForget;

         /** @var NewPassFormFactory @inject */
	public $factoryNewPass;

         /** @var MyAccountFormFactory @inject */
	public $factoryMyAccount;

        /** @var UserManager @inject */
        public $userManager;

         /** @var EntityModel @inject */
        public $entityModel;


        private $code;


	/**
	 * Sign-in form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSignInForm()
	{
		$form = $this->factory->create();
		$form->onSuccess[] = function ($form) {
			$form->getPresenter()->redirect('Homepage:');
		};
		return $form;
	}

        /**
	 * Forget form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentForgetForm()
	{
		$form = $this->factoryForget->create();
                $this->factoryForget->setManager($this->userManager);

                $emailModel = $this->entityModel->reflection("email");
                $emailModel->setTable("emailtemplate");
                $this->factoryForget->setEmailModel($emailModel);
		$form->onSuccess[] = function ($form) {
			$form->getPresenter()->redirect('Homepage:');
		};
		return $form;
	}


        /**
	 * Forget form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentMyAccountForm()
	{      
                $this->factoryMyAccount->setManager($this->userManager);
                $this->factoryMyAccount->setUserId($this->getUser()->getId());
		$form = $this->factoryMyAccount->create();
                
                
		$form->onSuccess[] = function ($form) {
			$form->getPresenter()->redirect('Sign:in');
		};
		return $form;
	}

         /**
	 * Forget form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentNewPassForm()
	{
		$this->factoryNewPass->setManager($this->userManager);
          
                $this->factoryNewPass->setCode($this->code);
                $form = $this->factoryNewPass->create();
                

		$form->onSuccess[] = function ($form) {
			$form->getPresenter()->redirect('in');
		};
		return $form;
	}


        public function actionNewpass($id){
            $this->code = $id;
          //  print_r($this->code); exit;
        }


	public function actionOut()
	{
		$this->getUser()->logout();
		$this->flashMessage('Odhlášení proběhlo úspěšně.');
		$this->redirect('Homepage:');
	}

}
