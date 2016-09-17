<?php
namespace App\Model\Repository;

use Nette;
use Kdyby\Doctrine\EntityManager;
use App\Model\Entity\User;

class Users extends Nette\Object
{
    private $em;
    private $users;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->users = $em->getRepository(User::class);
    }

    public function add(User $user)
    {
        if(!isset($user->username, $user->password, $user->role, $user->email, $user->fullName)){
            throw new \Exception('User must have filled all variables.');
        }
        $this->em->persist($user);
    }

    public function getAll(){
        return $this->users->findAll();
    }

    public function getByUsername($username){
        return $this->users->findOneBy(['username' => $username]);
    }

    public function __destruct()
    {
        $this->em->flush();
    }
}