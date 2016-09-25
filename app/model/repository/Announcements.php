<?php
namespace App\Model\Repository;

use Nette;
use Kdyby\Doctrine\EntityManager;
use App\Model\Entity\Church;
use App\Model\Entity\Announcement;

class Announcements extends Nette\Object
{
    private $em;
    private $announcements;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->announcements = $em->getRepository(Announcement::class);
    }

    public function create(Announcement $announcement)
    {
        if(!($announcement->church instanceof Church)){
            throw new \Exception('Each announcement must have a church it is bound to.');
        }
        $this->em->persist($announcement);
    }

    public function getAll(){
        return $this->announcements->findAll();
    }

    public function getByChurch(Church $church){
        return $this->announcements->findBy(['church' => $church]);
    }

    public function getByChurches(array $churches){
        return $this->announcements->findBy(['church' => $churches], ['lastEdit' => 'DESC']);
    }

    public function getById($id){
        return $this->announcements->findOneBy(['id' => $id]);
    }

    public function deleteById($id){
        $this->em->remove($this->getById($id));
    }

    public function __destruct()
    {
        $this->em->flush();
    }
}