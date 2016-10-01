<?php
namespace App\Model\Repository;

use App\Model\Entity\LiturgyDay;
use Nette;
use Kdyby\Doctrine\EntityManager;

class LiturgyDays extends Nette\Object
{
    private $em;
    private $liturgyDays;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->liturgyDays = $em->getRepository(LiturgyDay::class);
    }

    public function create(LiturgyDay $liturgyDay)
    {
        $this->em->persist($liturgyDay);
    }

    public function getAll(){
        return $this->liturgyDays->findAll();
    }

    public function getById($id){
        return $this->liturgyDays->findOneBy(['id' => $id]);
    }

    public function deleteById($id){
        $this->em->remove($this->getById($id));
    }

    public function __destruct()
    {
        $this->em->flush();
    }
}