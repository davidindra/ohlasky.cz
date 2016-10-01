<?php
namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Kdyby\Doctrine\Entities\MagicAccessors;

/**
 * @ORM\Entity
 */
class LiturgyTexts
{
    use MagicAccessors, Identifier;

    /**
     * @ORM\Column(type="date")
     */
    protected $date;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $heading;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $source;

    /**
     * @ORM\Column(type="string", length=10000)
     */
    protected $perex;

    /**
     * @ORM\Column(type="string", length=500)
     */
    protected $responsum;

    /**
     * @ORM\Column(type="string", length=10000)
     */
    protected $content;
}
