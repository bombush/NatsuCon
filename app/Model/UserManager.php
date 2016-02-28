<?php

namespace Natsu\Model;

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
                TABLE_NAME_ROLE = 'role',
                TABLE_NAME_STATUS = 'status',
		COLUMN_ID = 'id',
                COLUMN_CONTACT_ID = 'contactId',
                COLUMN_ROLE_ID = 'roleId',
                COLUMN_STATUS_ID = 'statusId',
		COLUMN_NAME = 'username',
                COLUMN_EMAIL = 'email',
                COLUMN_TITLE = 'title',
		COLUMN_PASSWORD_HASH = 'password',
                COLUMN_STATUS = 'statusId',
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

		$row = $this->database->select('*')->from(self::TABLE_NAME)->where(self::COLUMN_NAME . ' = ?', $username)->fetch();

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
                $contact = $this->database->select('*')->from(self::TABLE_NAME_CONTACT)->where(self::COLUMN_ID . ' = ?', $arr[self::COLUMN_CONTACT_ID])->fetch();
                $contact = $contact->toArray();
                unset($contact[self::COLUMN_ID]);
                $arr = array_merge($arr, $contact);
                
		unset($arr[self::COLUMN_PASSWORD_HASH]);
                //print_R($arr); exit;
		return new Nette\Security\Identity($row[self::COLUMN_ID], $row[self::COLUMN_ROLE], $arr);
	}


        public function identify($username){
            try {
                $row = $this->database->select('*')->from(self::TABLE_NAME)->where(self::COLUMN_NAME . ' = ?', $username)->fetch();
                if(!$row){
                    return false;
                }else{
                    return $row;
                }
            }catch(\Exception $e){
                return false;
            }

        }

        public function getMyAccount($userId){
            $row = $this->database->
                    select("contact.*, user.roleId, user.statusId, role.title AS role_title, status.title AS status_title")
                    ->from(self::TABLE_NAME)
                ->leftJoin(self::TABLE_NAME_CONTACT,"ON ". self::TABLE_NAME.".".self::COLUMN_CONTACT_ID."=".self::TABLE_NAME_CONTACT.".".self::COLUMN_ID)
                ->leftJoin(self::TABLE_NAME_ROLE,"ON ". self::TABLE_NAME.".".self::COLUMN_ROLE_ID."=".self::TABLE_NAME_ROLE.".".self::COLUMN_ID)
                ->leftJoin(self::TABLE_NAME_STATUS,"ON ". self::TABLE_NAME.".".self::COLUMN_STATUS_ID."=".self::TABLE_NAME_STATUS.".".self::COLUMN_ID)
                ->where(self::TABLE_NAME.".".self::COLUMN_ID."= ?", $userId)->fetch();
            return $row;

        }


        public function createRequest($row){
             $hash = substr(sha1($row->username.time()),0,20);
             $this->database->update(self::TABLE_NAME, array("requesthash" => $hash))->where(self::COLUMN_ID." = ?", $row->id)->execute();
             return $hash;
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
			$this->database->query('INSERT INTO [' . self::TABLE_NAME . ']', [
				self::COLUMN_NAME => $username,
				self::COLUMN_PASSWORD_HASH => md5($password),
                                self::COLUMN_STATUS => \Natsu\Model\PermissionModel::ACTIVE_STATUS,
                                self::COLUMN_ROLE_ID => \Natsu\Model\PermissionModel::REGISTERED_ROLE,
			]);
                        return $this->database->getInsertId();
		} catch (Nette\Database\UniqueConstraintViolationException $e) {
			throw new DuplicateNameException;
		}
	}
        
        public function update($user){
            try {
			  $this->database->update(self::TABLE_NAME, $user)->where(self::COLUMN_ID." = ?", $user->id)->execute();
                     
		} catch (Nette\Database\DriverException  $e) {
			throw new \Nette\Database\DriverException;
		}
            
        }
        
        
        public function addContact($contact){
            try {
			$this->database->query('INSERT INTO [' . self::TABLE_NAME_CONTACT . ']', [
				self::COLUMN_ID => $contact['id'],
				self::COLUMN_EMAIL => $contact['email'],
			]);
                     
		} catch (Nette\Database\DriverException  $e) {
			throw new \Nette\Database\DriverException;
		}
        }

}



class DuplicateNameException extends \Exception
{}
