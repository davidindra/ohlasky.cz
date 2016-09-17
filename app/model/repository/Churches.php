<?php
namespace App\Model\Repository;

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
        // validate that the article has title and content, or whatever you want to validate here
        //$church->published = TRUE;
        $this->em->persist($church);
        // don't forget to call $em->flush() in your presenter
    }

    public function getAll(){
        return $this->churches->findAll();
    }

    public function __destruct()
    {
        $this->em->flush();
    }
}