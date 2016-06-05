<?php
namespace Natsu\Forms;

use Nette;
use Natsu\Forms\BaseForm;
use Nette\Security\User;
use Natsu\Model\UserManager;
use Nette\Utils\ArrayHash;
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
   private $em;

   private $userId;

    public function setManager($manager){
            $this->userManager = $manager;
    }

    public function setEmailModel($emailModel){
            $this->em = $emailModel;
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
                $form->addHidden("userId")->setDefaultValue($this->userId);
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

                $details = $this->userManager->getAccount($this->userId);
                $form->setDefaults($details);
                $form->addSubmit('submit', 'Změnit');
                $form->onSuccess[] = array($this, 'formSucceeded');
		return $form;
	}
        
        
         /**
	 * @return BaseForm
	 */
	public function createAdmin()
	{
		$form = new \Natsu\Forms\BaseForm;
                $form->addHidden("userId")->setDefaultValue($this->userId);
                $form->addSelect("roleId", "Role")->setItems(\Natsu\Model\PermissionModel::getRoleIds());
                $form->addSelect("statusId", "Status")->setItems(\Natsu\Model\PermissionModel::getStatusIds());

		$form->addText('fullname', 'Celé jméno:')
			->setRequired('Zadejte jméno a přijmení');
                $form->addText('nickname', 'Přezdívka:');
                $form->addText('email', 'E-mailová adresa:')->setRequired("Zadejte e-mailovou adresu");
                $form->addText('phone', 'Telefon:');
                $form->addText('skype', 'Skype:');
                $form->addText('FB', 'Facebook:');
                $form->addText('age', 'Věk:');

                $details = $this->userManager->getAccount($this->userId);
                $form->setDefaults($details);
                $form->addSubmit('submit', 'Změnit');
                $form->onSuccess[] = array($this, 'formAdminSucceeded');
		return $form;
	}



    public function formSucceeded(BaseForm $form, $values){
            //  print_R($values); exit;     
              $this->em->setTable("user");
              $contact = $this->em->fetchWhere(array("id"=> $values->userId));
              $values->id = $contact[0]->contactId;
              unset($values->userId);
              $this->em->setTable("contact");
              $this->em->update($values);

    }
    
    
    public function formAdminSucceeded(BaseForm $form, $values){
            //  print_R($values); exit;     
              $this->em->setTable("user");
             $this->em->update(ArrayHash::from(array('id' => $values->userId, 'roleId' => $values->roleId, 'statusId' => $values->statusId)));
              
              $contact = $this->em->fetchWhere(array("id"=> $values->userId));
              $values->id = $contact[0]->contactId;
              unset($values->userId);
              unset($values->roleId);
              unset($values->statusId);
              $this->em->setTable("contact");
              $this->em->update($values);

    }



}
?>
