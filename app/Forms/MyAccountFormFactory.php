<?php
namespace Natsu\Forms;

use Nette;
use Natsu\Forms\BaseForm;
use Nette\Security\User;
use Natsu\Model\UserManager;
/**
 * Description of RegistrationFormFactory
 *
 * @author campari
 */
class MyAccountFormFactory extends Nette\Object {

    /**
     *
     * @var Natsu\Model\UserManager;
     */
    private $userManager;


     /**
     *
     * @var Natsu\Model\emailModel;
     */
   private $emailModel;

   private $userId;

    public function setManager($manager){
            $this->userManager = $manager;
    }

    public function setEmailModel($emailModel){
            $this->emailModel = $emailModel;
    }

    public function setUserId($userId){
        $this->userId = $userId;
    }

    /**
	 * @return BaseForm
	 */
	public function create()
	{
		$form = new \Natsu\Forms\BaseForm;
                $form->addText("role_title", "Role")->setDisabled();
                $form->addText("status_title", "Status")->setDisabled();
		$form->addText('fullname', 'Celé jméno:')
			->setRequired('Zadejte jméno a přijmení');
                $form->addText('nickname', 'Přezdívka:');
                $form->addText('email', 'E-mailová adresa:')->setRequired("Zadejte e-mailovou adresu");
                $form->addText('phone', 'Telefon:');
                $form->addText('skype', 'Skype:');
                $form->addText('FB', 'Facebook:');
                $form->addText('age', 'Věk:');

                $details = $this->userManager->getMyAccount($this->userId);
                
                $form->setDefaults($details);



                $form->addSubmit('submit', 'Změnit');
                $form->onSuccess[] = array($this, 'formSucceeded');
		return $form;
	}


    public function formSucceeded(BaseForm $form, $values){


    }



}
?>
