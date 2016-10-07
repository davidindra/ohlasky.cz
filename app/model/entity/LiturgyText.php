<?php
namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Kdyby\Doctrine\Entities\MagicAccessors;

/**
 * @ORM\Entity
 */
class LiturgyText
{
    use MagicAccessors, Identifier;

    /**
     * @ORM\Column(name="`date`", type="date", nullable=false)
     */
    protected $date;

    /**
     * @ORM\Column(name="`order`", type="integer", nullable=true)
     */
    protected $order;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    protected $heading;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $source;

    /**
     * @ORM\Column(type="string", length=10000, nullable=true)
     */
    protected $perex;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    protected $responsum;

    /**
     * @ORM\Column(type="string", length=10000, nullable=false)
     */
    protected $content;
}
