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
     * @var boolean
     *
     * @ORM\Column(name="fee_payed", type="boolean")
     */
    private $feePayed;

    /**
     * @var string
     *
     * @ORM\Column(name="guid", type="string", length=255)
     */
    private $guid;

    /**
     * @ORM\ManyToOne(targetEntity="CostUnit", inversedBy="contacts")
     * @ORM\JoinColumn(name="costunit_id", referencedColumnName="id")
     */
    private $costunit;

    /**
     * @ORM\OneToMany(targetEntity="event", mappedBy="costunit")
     **/
    private $events;

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
     * Set feePayed
     *
     * @param boolean $feePayed
     * @return Contact
     */
    public function setFeePayed($feePayed)
    {
        $this->feePayed = $feePayed;

        return $this;
    }

    /**
     * Get feePayed
     *
     * @return boolean
     */
    public function getFeePayed()
    {
        return $this->feePayed;
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
     * Set costunit
     *
     * @param \Oktolab\Bundle\RentBundle\Entity\CostUnit $costunit
     * @return Contact
     */
    public function setCostunit(\Oktolab\Bundle\RentBundle\Entity\CostUnit $costunit = null)
    {
        $this->costunit = $costunit;

        return $this;
    }

    /**
     * Get costunit
     *
     * @return \Oktolab\Bundle\RentBundle\Entity\CostUnit
     */
    public function getCostunit()
    {
        return $this->costunit;
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
}