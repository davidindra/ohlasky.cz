<?php
namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Kdyby\Doctrine\Entities\MagicAccessors;

/**
 * @ORM\Entity
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
     * @ORM\ManyToOne(targetEntity="Church")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $church;
}