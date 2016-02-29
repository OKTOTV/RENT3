<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 * @ORM\Entity
 */
class Set
{
    /**
     * @ORM\ManyToMany(targetEntity="Rentable", inversedBy="sets")
     * @ORM\JoinTable(name="sets_rentables")
     */
    private $rentables;

    /**
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

    public function __construct()
    {
        $this->rentables = new \Doctrine\Common\Collections\ArrayCollection();
    }
}
