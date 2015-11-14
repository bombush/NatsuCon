<?php

namespace App\Model;

use Nette;
use Nette\Security\Passwords;


/**
 * Users management.
 */
class UserManager extends Nette\Object implements Nette\Security\IAuthenticator
{
	const
		TABLE_NAME = 'user',
                TABLE_NAME_CONTACT = 'contact',
		COLUMN_ID = 'id',
                COLUMN_CONTACT_ID = 'contactId',
		COLUMN_NAME = 'username',
		COLUMN_PASSWORD_HASH = 'password',
		COLUMN_ROLE = 'roleId';


	/** @var \DibiConnection */
	private $database;


	public function __construct(\DibiConnection $database)
	{
		$this->database = $database;
	}


	/**
	 * Performs an authentication.
	 * @return Nette\Security\Identity
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($username, $password) = $credentials;

		$row = $this->database->query("SELECT * FROM ".self::TABLE_NAME." WHERE username=?", $username)->fetch();

               //  print_r($row);
               // exit;

		if (!$row) {
			throw new Nette\Security\AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);

		} elseif (md5($password) !==  $row[self::COLUMN_PASSWORD_HASH]) {
			throw new Nette\Security\AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);

		} /*elseif (Passwords::needsRehash($row[self::COLUMN_PASSWORD_HASH])) {
			$row->update(array(
				self::COLUMN_PASSWORD_HASH => Passwords::hash($password),
			));
		}*/

               

		$arr = $row->toArray();
                $contact = $this->database->query("SELECT * FROM ".self::TABLE_NAME_CONTACT." WHERE ".self::COLUMN_ID."=?", $arr[self::COLUMN_CONTACT_ID])->fetch();
                $contact = $contact->toArray();
                unset($contact[self::COLUMN_ID]);
                $arr = array_merge($arr, $contact);
                
		unset($arr[self::COLUMN_PASSWORD_HASH]);
                //print_R($arr); exit;
		return new Nette\Security\Identity($row[self::COLUMN_ID], $row[self::COLUMN_ROLE], $arr);
	}


	/**
	 * Adds new user.
	 * @param  string
	 * @param  string
	 * @return void
	 */
	public function add($username, $password)
	{
		try {
			$this->database->table(self::TABLE_NAME)->insert(array(
				self::COLUMN_NAME => $username,
				self::COLUMN_PASSWORD_HASH => Passwords::hash($password),
			));
		} catch (Nette\Database\UniqueConstraintViolationException $e) {
			throw new DuplicateNameException;
		}
	}

}



class DuplicateNameException extends \Exception
{}
