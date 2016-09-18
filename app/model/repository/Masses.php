<?php
namespace App\Model\Repository;

use Nette;
use Kdyby\Doctrine\EntityManager;
use App\Model\Entity\Church;
use App\Model\Entity\Mass;

class Masses extends Nette\Object
{
    private $em;
    private $masses;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->masses = $em->getRepository(Mass::class);
    }

    public function create(Mass $mass)
    {
        if(!($mass->church instanceof Church)){
            throw new \Exception('Each church must have a maintainer defined by User instance.');
        }
        $this->em->persist($mass);
    }

    public function getAll(){
        return $this->masses->findAll();
    }

    public function getByChurch(Church $church){
        return $this->masses->findBy(['church' => $church], ['datetime' => 'ASC']);
    }

    /*public function getByMaintainer(User $maintainer){
        return $this->masses->findBy(['maintainer_id' => $maintainer]);
    }*/

    public function __destruct()
    {
        $this->em->flush();
    }
}