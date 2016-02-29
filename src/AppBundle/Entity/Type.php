<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 * @ORM\Entity
 */
class Type
{
    /**
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="Rentable", mappedBy="type")
     */
    private $rentables;
}
