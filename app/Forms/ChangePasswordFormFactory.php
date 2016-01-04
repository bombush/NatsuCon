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
class ChangePasswordFormFactory extends Nette\Object {

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

    public function setManager($manager){
            $this->userManager = $manager;
    }

    public function setEmailModel($emailModel){
            $this->emailModel = $emailModel;
    }

    /**
	 * @return BaseForm
	 */
	public function create()
	{
		$form = new \Natsu\Forms\BaseForm;
		$form->addText('email', 'E-mail:')
			->setRequired('Zadejte e-mail');
                $form->addPassword('password')->setRequired('Zadejte heslo:');
                $form->addPassword('confirm_password')->setRequired('Potvrďte heslo:');

                $form->addSubmit('submit', 'Registrovat');
                $form->onSuccess[] = array($this, 'formSucceeded');
		return $form;
	}


    public function formSucceeded(BaseForm $form, $values){


    }



}
?>
