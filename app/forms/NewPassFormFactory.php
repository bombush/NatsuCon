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
            
           // dump($values);exit;
            
            if($form->getPresenter()->getUser()->loggedIn){
                $values->id = $form->getPresenter()->getUser()->getId();
            }elseif(!empty ($values->code)){
                $row = $this->userManager->identifyHash($values->code);
                if($row !== false){
                    $values->id = $row->id;
                }else{
                    $form->addError("Neni mozne zmenit heslo!");
                } 
            }else{
                    $form->addError("Neni mozne zmenit heslo!");
            }            
            unset($values->code);
            unset($values->confirm_password);
            
          //  dump($values); exit;
            
            $values->requesthash= ""; 
            $values->password = md5($values->password);
            $this->userManager->update($values);
            
            $form->getPresenter()->flashMessage("Heslo změměno.Přihlašte se.");
            $form->getPresenter()->redirect("Sign:in");
        }
}
?>
