<?php

namespace Oktolab\Bundle\RentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Contact
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Contact
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="guid", type="string", length=255)
     */
    private $guid;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="CostUnit", inversedBy="contacts")
     * @ORM\JoinTable(name="costunits_contacts")
     */
    private $costunits;

    /**
     * @ORM\OneToMany(targetEntity="Event", mappedBy="contact")
     **/
    private $events;

    public function __toString()
    {
        return $this->getName();
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
     * @return Contact
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
     * Set guid
     *
     * @param string $guid
     * @return Contact
     */
    public function setGuid($guid)
    {
        $this->guid = $guid;

        return $this;
    }

    /**
     * Get guid
     *
     * @return string
     */
    public function getGuid()
    {
        return $this->guid;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->events = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add events
     *
     * @param \Oktolab\Bundle\RentBundle\Entity\event $events
     * @return Contact
     */
    public function addEvent(\Oktolab\Bundle\RentBundle\Entity\event $events)
    {
        $this->events[] = $events;

        return $this;
    }

    /**
     * Remove events
     *
     * @param \Oktolab\Bundle\RentBundle\Entity\event $events
     */
    public function removeEvent(\Oktolab\Bundle\RentBundle\Entity\event $events)
    {
        $this->events->removeElement($events);
    }

    /**
     * Get events
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Add costunits
     *
     * @param \Oktolab\Bundle\RentBundle\Entity\CostUnit $costunits
     * @return Contact
     */
    public function addCostunit(\Oktolab\Bundle\RentBundle\Entity\CostUnit $costunits)
    {
        $this->costunits[] = $costunits;

        return $this;
    }

    /**
     * Remove costunits
     *
     * @param \Oktolab\Bundle\RentBundle\Entity\CostUnit $costunits
     */
    public function removeCostunit(\Oktolab\Bundle\RentBundle\Entity\CostUnit $costunits)
    {
        $this->costunits->removeElement($costunits);
    }

    /**
     * Get costunits
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCostunits()
    {
        return $this->costunits;
    }
}