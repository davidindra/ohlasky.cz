<?php
namespace App\Model\Repository;

use Nette;
use Kdyby\Doctrine\EntityManager;

class LiturgyTexts extends Nette\Object
{
    private $em;
    private $liturgyTexts;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->liturgyTexts = $em->getRepository(\App\Model\Entity\LiturgyTexts::class);
    }

    public function create(\App\Model\Entity\LiturgyTexts $liturgyTexts)
    {
        $this->em->persist($liturgyTexts);
    }

    public function getAll(){
        return $this->liturgyTexts->findAll();
    }

    public function getById($id){
        return $this->liturgyTexts->findOneBy(['id' => $id]);
    }

    public function deleteById($id){
        $this->em->remove($this->getById($id));
    }

    public function __destruct()
    {
        $this->em->flush();
    }
}