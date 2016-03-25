<?php
namespace Natsu\Forms;

use Nette;
use Nette\Application\UI\Form;
use Nette\Security\User;
use Natsu\Model\emailModel;

class ForgetFormFactory extends \Nette\Application\UI\Form{
    private $user;
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


    public function __construct(User $user)
    {
		$this->user = $user;
    }

    /**
	 * @return Form
	 */
	public function create()
	{
		$form = new \Natsu\Forms\BaseForm;
		$form->addText('username', 'Uživatelské jméno:')
			->setRequired('Zadejte své uživatelské jméno.');




		$form->addSubmit('request', 'Obnovit heslo');

		$form->onSuccess[] = array($this, 'formSucceeded');
		return $form;
	}


        public function setManager($manager){
            $this->userManager = $manager;
        }

         public function setEmailModel($emailModel){
            $this->emailModel = $emailModel;
        }


	public function formSucceeded(Form $form, $values)
	{
            $row = $this->userManager->identify($values->username);
            if($row === false){
                $form->addError("Nenalezen uživatel. Zkuste znovu.");
            }else{
                $hash = $this->userManager->createRequest($row);
                $template = $this->emailModel->getTemplate(emailModel::FORGET_PASSWD);
                $template->body = $this->emailModel->replace(array("%hash%", $hash), $template->body);
                $from="web@natsucon.cz";
                $to=$row->username;
                $this->emailModel->sendEmail($from, $to, $template);

            }
	}
}
?>
