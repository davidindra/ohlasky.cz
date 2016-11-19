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
        return $this->announcements->findBy(['church' => $church], ['ordering' => 'DESC']);
    }

    public function getByChurches(array $churches){
        return $this->announcements->findBy(['church' => $churches], ['church' => 'ASC', 'ordering' => 'DESC']);
    }

    public function getById($id){
        return $this->announcements->findOneBy(['id' => $id]);
    }

    public function delete(Announcement $announcement){
        $this->em->remove($announcement);
    }

    public function moveUp(Announcement $announcement){
        $announcements = $this->getByChurch($announcement->church);

        $last = null;
        foreach($announcements as $key => $item){
            if($announcement->getId() == $item->getId()){
                $upperOrder = $announcements[$last]->ordering;
                $lowerOrder = $item->ordering;

                $item->ordering = $upperOrder;
                $announcements[$last]->ordering = $lowerOrder;

                return;
            }

            $last = $key;
        }
    }

    public function moveDown(Announcement $announcement){
        $announcements = array_reverse($this->getByChurch($announcement->church));

        $last = null;
        foreach($announcements as $key => $item){
            if($announcement->getId() == $item->getId()){
                $upperOrder = $announcements[$last]->ordering;
                $lowerOrder = $item->ordering;

                $item->ordering = $upperOrder;
                $announcements[$last]->ordering = $lowerOrder;

                return;
            }

            $last = $key;
        }
    }

    public function flush(){
        $this->em->flush();
    }

    public function __destruct()
    {
        $this->flush();
    }
}