<?php
namespace App\Model\Repository;

<<<<<<< HEAD
use App\Model\Entity\User;
=======
>>>>>>> 84d61976f38c8034110fd9060e2f448e1762bb34
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
<<<<<<< HEAD
        if(!($church->maintainer instanceof User)){
            throw new \Exception('Each church must have a maintainer defined by User instance.');
        }
        $this->em->persist($church);
=======
        // validate that the article has title and content, or whatever you want to validate here
        //$church->published = TRUE;
        $this->em->persist($church);
        // don't forget to call $em->flush() in your presenter
>>>>>>> 84d61976f38c8034110fd9060e2f448e1762bb34
    }

    public function getAll(){
        return $this->churches->findAll();
    }

<<<<<<< HEAD
    public function getByMaintainer(User $maintainer){
        return $this->churches->findBy(['maintainer_id' => $maintainer]);
    }

=======
>>>>>>> 84d61976f38c8034110fd9060e2f448e1762bb34
    public function __destruct()
    {
        $this->em->flush();
    }
}