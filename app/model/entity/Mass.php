<?php
namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Kdyby\Doctrine\Entities\MagicAccessors;
use Nette\Utils\DateTime;

/**
 * @ORM\Entity
 * @property DateTime $datetime
 * @property string $celebration custom day name or NULL
 * @property boolean $highlighted is the mass a celebration?
 * @property string $intention
 * @property Church $church owning church
 * @property User $officiant priest which is going to celebrate the mass
 */
class Mass
{
    use MagicAccessors, Identifier;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $datetime;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $celebration;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $highlighted = false;

    /**
     * @ORM\Column(type="string")
     */
    protected $intention;

    /**
     * @ORM\ManyToOne(targetEntity="Church", inversedBy="masses")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $church;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="masses")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $officiant;
}