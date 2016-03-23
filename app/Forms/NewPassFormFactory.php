<?php

namespace Natsu\Forms;

use Nette;
use Natsu\Forms\BaseForm;
use Nette\Security\User;
use Natsu\Model\UserManager;

class NewPassFormFactory {
    private $code;


    public function setManager($manager){
            $this->userManager = $manager;
    }

    /**
     *
     * @var Natsu\Model\UserManager;
     */
    private $userManager;


        public function setCode($code){
            $this->code = $code;
           
        }

       /**
	 * @return BaseForm
	 */
	public function create()
	{
		// echo $this->code; exit;
                $form = new \Natsu\Forms\BaseForm;
                $form->addHidden("code", $this->code);
		$form->addPassword('password', 'Heslo:')
			->setRequired('Zadej nové heslo.');
                $form->addPassword('confirm_password', 'Potvrzení hesla:')
			->setRequired('Zadej nové heslo znovu.');


		$form->addSubmit('send', 'Uložit nové heslo');

		$form->onSuccess[] = array($this, 'formSucceeded');
		return $form;
	}

        public function formSucceeded($form, $values){

            if($values->password != $values->confirm_password){
                $form->addError("Hesla musí být shodná");
                return;
            }
            unset($values->code);
            unset($values->confirm_password);
            $values->id = $form->getPresenter()->getUser()->getId();
            
            $values->password = md5($values->password);
            $this->userManager->update($values);
        }
}
?>
