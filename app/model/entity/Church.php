<?php
namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Kdyby\Doctrine\Entities\MagicAccessors;
use Nette\Utils\DateTime;

/**
 * @ORM\Entity
 */
class Church
{
    use MagicAccessors, Identifier;

    /**
     * @ORM\Column(type="integer")
     */
    protected $order;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $abbreviation;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $nameShort;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $nameHighlighted;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="churches")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $maintainer;

    /**
     * @ORM\OneToMany(targetEntity="Mass", mappedBy="church")
     * @ORM\OrderBy(value={"datetime" = "ASC"})
     */
    protected $masses;

    /**
     * @ORM\OneToMany(targetEntity="Announcement", mappedBy="church")
     */
    protected $announcements;

    public function nearestMass(){
        if(count($this->masses) != 0){
            foreach($this->masses as $mass){
                if(DateTime::from($mass->datetime) > DateTime::from(time())){
                    return $mass;
                }
            }
        }
    }
}