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
<<<<<<< HEAD
    protected $abbreviation;
=======
    protected $linkAbbr;
>>>>>>> 84d61976f38c8034110fd9060e2f448e1762bb34

    /**
     * @ORM\Column(type="string")
     */
<<<<<<< HEAD
    protected $name;

    /**
     * @ORM\Column(type="string")
     */
    protected $nameHighlighted;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=false)
=======
    protected $fullName;

    /**
     * @ORM\ManyToOne(targetEntity="User")
>>>>>>> 84d61976f38c8034110fd9060e2f448e1762bb34
     */
    protected $maintainer;
}