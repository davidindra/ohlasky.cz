<?php

namespace App\Model;

use Nette;
use Nette\Security\Passwords;
<<<<<<< HEAD
use App\Model\Entity\User;
use App\Model\Repository\Users;
=======
use Kdyby\Doctrine\EntityManager;
use App\Model\Entity\User;
>>>>>>> 84d61976f38c8034110fd9060e2f448e1762bb34

/**
 * Users management.
 */
class UserManager implements Nette\Security\IAuthenticator
{
	use Nette\SmartObject;

<<<<<<< HEAD
	/** @var Users */
	private $users;

	public function __construct(Users $users)
	{
		$this->users = $users;
=======
	/** @var EntityManager */
	private $em;

	public function __construct(EntityManager $em)
	{
		$this->em = $em;
>>>>>>> 84d61976f38c8034110fd9060e2f448e1762bb34
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
<<<<<<< HEAD
		$user = $this->users->getByUsername($username);
=======
		$user = $this->em->getRepository(User::class)->findOneBy(['username' => $username]);
>>>>>>> 84d61976f38c8034110fd9060e2f448e1762bb34

		if (!$user) {
			throw new Nette\Security\AuthenticationException('Tento uživatel neexistuje.', self::IDENTITY_NOT_FOUND);

		} elseif (!Passwords::verify($password, $user->password)) {
			throw new Nette\Security\AuthenticationException('Bylo zadáno nesprávné heslo.', self::INVALID_CREDENTIAL);

		} elseif (Passwords::needsRehash($user->password)) {
			$user->password = Passwords::hash($password);
<<<<<<< HEAD
		}

		return new Nette\Security\Identity($user->getId(), $user->role, [
			$user->username,
			$user->fullName,
			$user->email
		]);
=======
			$this->em->flush();
		}

		return new Nette\Security\Identity($user->getId(), $user->role, $user);
>>>>>>> 84d61976f38c8034110fd9060e2f448e1762bb34
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
<<<<<<< HEAD
			$this->users->add($user);
=======
			$this->em->persist($user);
			$this->em->flush();
>>>>>>> 84d61976f38c8034110fd9060e2f448e1762bb34
		} catch (\Exception $e) {
			throw new DuplicateNameException($e->getMessage());
		}
	}

}



class DuplicateNameException extends \Exception
{}
