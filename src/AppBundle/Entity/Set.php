<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="rentable_set")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\SetRepository")
 */
class Set
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

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

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Set
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add rentables
     *
     * @param \AppBundle\Entity\Rentable $rentables
     * @return Set
     */
    public function addRentable(\AppBundle\Entity\Rentable $rentables)
    {
        $this->rentables[] = $rentables;

        return $this;
    }

    /**
     * Remove rentables
     *
     * @param \AppBundle\Entity\Rentable $rentables
     */
    public function removeRentable(\AppBundle\Entity\Rentable $rentables)
    {
        $this->rentables->removeElement($rentables);
    }

    /**
     * Get rentables
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRentables()
    {
        return $this->rentables;
    }
}
