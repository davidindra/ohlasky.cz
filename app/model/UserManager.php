<?php

namespace App\Model;

use Nette;
use Nette\Security\Passwords;
use App\Model\Entity\User;
use App\Model\Repository\Users;

/**
 * Users management.
 */
class UserManager implements Nette\Security\IAuthenticator
{
	use Nette\SmartObject;

	/** @var Users */
	private $users;

	public function __construct(Users $users)
	{
		$this->users = $users;
	}

	/**
	 * Performs an authentication.
	 * @param array $credentials [username, password]
	 * @return Nette\Security\Identity
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($username, $password) = $credentials;

		/** @var User $user */
		$user = $this->users->getByUsername($username);

		if (!$user) {
			throw new Nette\Security\AuthenticationException('Tento uživatel neexistuje.', self::IDENTITY_NOT_FOUND);

		} elseif (!Passwords::verify($password, $user->password)) {
			throw new Nette\Security\AuthenticationException('Bylo zadáno nesprávné heslo.', self::INVALID_CREDENTIAL);

		} elseif (Passwords::needsRehash($user->password)) {
			$user->password = Passwords::hash($password);
		}

		return new Nette\Security\Identity($user->getId(), $user->role, [
			'username' => $user->username,
			'fullname' => $user->fullName,
			'email' => $user->email
		]);
	}


	/**
	 * Adds new user.
	 * @param string $username
	 * @param string $password
	 * @param string $email
	 * @param string $role
	 * @param string $fullName
	 * @throws DuplicateNameException
	 */
	public function add($username, $password, $email, $role, $fullName)
	{
		try {
			$user = new User();
			$user->username = $username;
			$user->password = Passwords::hash($password);
			$user->email = $email;
			$user->role = $role;
			$user->fullName = $fullName;
			$this->users->add($user);
		} catch (\Exception $e) {
			throw new DuplicateNameException($e->getMessage());
		}
	}
}



class DuplicateNameException extends \Exception
{}
