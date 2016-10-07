<?php
namespace App\Model\Repository;

use App\Model\Entity\LiturgyText;
use Nette;
use Kdyby\Doctrine\EntityManager;
use Nette\Utils\DateTime;

class LiturgyTexts extends Nette\Object
{
    private $em;
    private $liturgyTexts;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->liturgyTexts = $em->getRepository(LiturgyText::class);
    }

    public function create(LiturgyText $liturgyText)
    {
        $this->em->persist($liturgyText);
        $this->em->flush();
    }

    public function getAll($futureOnly = true){
        $data = [];
        foreach($this->liturgyTexts->findBy([], ['date' => 'ASC', 'order' => 'ASC']) as $text){
            if(DateTime::from($text->date)->getTimestamp() > time() - 24*60*60 || !$futureOnly){
                $data[] = $text;
            }
        }
        return $data;
    }

    public function getById($id){
        return $this->liturgyTexts->findOneBy(['id' => $id]);
    }

    public function getByDate(DateTime $date){
        return $this->liturgyTexts->findBy(['date' => $date], ['date' => 'ASC', 'order' => 'ASC']);
    }

    public function deleteById($id){
        $this->em->remove($this->getById($id));
    }

    public function __destruct()
    {
        $this->em->flush();
    }
}