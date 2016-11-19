<?php
namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Kdyby\Doctrine\Entities\MagicAccessors;
use Nette\Utils\DateTime;

/**
 * @ORM\Entity
 */
class Announcement
{
    use MagicAccessors, Identifier;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $lastEdit;

    /**
     * @ORM\Column(type="string", length=10000)
     */
    protected $content;

    /**
     * @ORM\ManyToOne(targetEntity="Church", inversedBy="announcements")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $church;

    /**
     * @ORM\Column(type="integer")
     */
    protected $ordering;

    public function __construct()
    {
        $this->lastEdit = DateTime::from(time());
    }
}