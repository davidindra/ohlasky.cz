<?php
namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Kdyby\Doctrine\Entities\MagicAccessors;

/**
 * @ORM\Entity
 */
class Church
{
    use MagicAccessors, Identifier;

    /**
     * @ORM\Column(type="string")
     */
    protected $abbreviation;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="string")
     */
    protected $nameHighlighted;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="churches")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $maintainer;
}