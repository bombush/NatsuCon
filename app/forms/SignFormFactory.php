<?php

namespace Natsu\Forms;

use Nette;
use Nette\Application\UI\Form;
use Nette\Security\User;


class SignFormFactory extends Nette\Object
{
	/** @var User */
	private $user;



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
			->setRequired('Zadej uživatelské jméno.');

		$form->addPassword('password', 'Heslo:')
			->setRequired('Zadej heslo.');

		$form->addCheckbox('remember', 'Zapamatovat přihlášení');

		//$form->addDateTimePicker('date', 'Datum');

		$form->addSubmit('send', 'Sign in');

		$form->onSuccess[] = array($this, 'formSucceeded');
		return $form;
	}


	public function formSucceeded(Form $form, $values)
	{
		if ($values->remember) {
			$this->user->setExpiration('14 days', FALSE);
		} else {
			$this->user->setExpiration('20 minutes', TRUE);
		}

		try {
			$this->user->login($values->username, $values->password);
		} catch (Nette\Security\AuthenticationException $e) {
			$form->addError($e->getMessage());
		}
	}

}
