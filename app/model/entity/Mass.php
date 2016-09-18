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
     * @ORM\Column(type="datetime", unique=true)
     */
    protected $datetime;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $dayModified;

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