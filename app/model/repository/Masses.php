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
        return $this->masses->findBy([], ['datetime' => 'ASC']);
    }

    public function getByChurch(Church $church){
        return $this->masses->findBy(['church' => $church], ['datetime' => 'ASC']);
    }

    public function getByChurches(array $churches){
        return $this->masses->findBy(['church' => $churches], ['datetime' => 'ASC']);
    }

    public function getById($id){
        return $this->masses->findOneBy(['id' => $id]);
    }

    public function deleteById($id){
        $this->em->remove($this->getById($id));
    }

    /*public function getByMaintainer(User $maintainer){
        return $this->masses->findBy(['maintainer_id' => $maintainer]);
    }*/

    public function flush(){
        $this->em->flush();
    }

    public function __destruct()
    {
        $this->flush();
    }
}