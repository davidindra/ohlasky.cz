<?php
namespace App\Model\Repository;

use App\Model\Entity\User;
use Nette;
use Kdyby\Doctrine\EntityManager;
use App\Model\Entity\Church;

class Churches extends Nette\Object
{
    private $em;
    private $churches;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->churches = $em->getRepository(Church::class);
    }

    public function create(Church $church)
    {
        if(!($church->maintainer instanceof User)){
            throw new \Exception('Each church must have a maintainer defined by User instance.');
        }
        $this->em->persist($church);
    }

    public function getAll(){
        return $this->churches->findAll();
    }

    public function getByMaintainer(User $maintainer){
        return $this->churches->findBy(['maintainer_id' => $maintainer]);
    }

    public function getByAbbreviation($abbreviation){
        return $this->churches->findOneBy(['abbreviation' => $abbreviation]);
    }

    public function __destruct()
    {
        $this->em->flush();
    }
}