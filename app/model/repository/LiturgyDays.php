<?php
namespace App\Model\Repository;

use App\Model\Entity\LiturgyDay;
use Nette;
use Kdyby\Doctrine\EntityManager;
use Nette\Utils\DateTime;

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

    public function getAll($futureOnly = true){
        $data = [];
        foreach($this->liturgyDays->findBy([], ['date' => 'ASC']) as $day){
            if(DateTime::from($day->date)->getTimestamp() > time() - 24*60*60 || !$futureOnly){
                $data[] = $day;
            }
        }
        return $data;
    }

    public function getById($id){
        return $this->liturgyDays->findOneBy(['id' => $id]);
    }

    public function getByDate(DateTime $date){
        return $this->liturgyDays->findOneBy(['date' => $date]);
    }

    public function deleteById($id){
        $this->em->remove($this->getById($id));
    }

    public function flush(){
        $this->em->flush();
    }

    public function __destruct()
    {
        $this->flush();
    }
}