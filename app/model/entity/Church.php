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
    protected $linkAbbr;

    /**
     * @ORM\Column(type="string")
     */
    protected $fullName;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     */
    protected $maintainer;
}