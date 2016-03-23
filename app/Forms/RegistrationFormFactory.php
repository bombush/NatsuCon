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
class RegistrationFormFactory extends Nette\Object {

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
                $form->addPassword('password', "Heslo:")->setRequired('Zadejte heslo:');
                $form->addPassword('confirm_password', "Potvrzení hesla:")->setRequired('Potvrďte heslo:');

                $form->addSubmit('submit', 'Registrovat');
                $form->onSuccess[] = array($this, 'formSucceeded');
		return $form;
	}


    public function formSucceeded(BaseForm $form, $values){
        //return false;       
        if($values->password != $values->confirm_password){
            $form->addError("Hesla se neshodují");
            return;
        }
        $id = $this->userManager->add($values->email, $values->password);
        $contactId = $this->userManager->addContact(array('id' => $id, 'email' => $values->email));
        
        $this->userManager->update(Nette\Utils\ArrayHash::from(array('id' => $id, 'contactId' => $contactId)));
        
        $template = $this->emailModel->getTemplate(\Natsu\Model\emailModel::REGISTER_CMPL);
        
        
        $template->body = $this->emailModel->replace(array("%EMAIL%" => $values->email, '%PASSWORD%' => $values->password), $template->body);
        $this->emailModel->sendEmail(\Natsu\Model\EmailModel::FROM, $values->email, $template);
        
        
        
        
        
     //   print_r($values); exit;
                    

    }



}
?>
