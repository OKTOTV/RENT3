<?php

namespace Oktolab\Bundle\RentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CostUnit
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Oktolab\Bundle\RentBundle\Entity\CostUnitRepository")
 */
class CostUnit
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
     * var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToOne(targetEntity="Contact")
     * @ORM\JoinColumn(name="mainContact_id", referencedColumnName="id")
     */
    private $mainContact;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Contact", mappedBy="costunits", cascade={"persist"})
     */
    private $contacts;

    /**
     * @var string
     *
     * @ORM\Column(name="guid", type="string", unique=true)
     */
    private $guid;

    /**
     * @ORM\OneToMany(targetEntity="Event", mappedBy="costunit")
     **/
    private $events;

    /**
     * @ORM\Column(name="abbreviation", type="string", nullable=true)
     * @var type string
     */
    private $abbreviation;

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
     * @return CostUnit
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
     * Set mainContact
     *
     * @param \Oktolab\Bundle\RentBundle\Entity\Contact $mainContact
     * @return CostUnit
     */
    public function setMainContact(\Oktolab\Bundle\RentBundle\Entity\Contact $mainContact = null)
    {
        $this->mainContact = $mainContact;

        return $this;
    }

    /**
     * Get mainContact
     *
     * @return \Oktolab\Bundle\RentBundle\Entity\Contact
     */
    public function getMainContact()
    {
        return $this->mainContact;
    }

    /**
     * Set contacts
     *
     * @param \Oktolab\Bundle\RentBundle\Entity\Contact $contacts
     * @return CostUnit
     */
    public function setContacts($contacts = null)
    {
        $this->contacts = $contacts;

        return $this;
    }

    /**
     * Get contacts
     *
     * @return \Oktolab\Bundle\RentBundle\Entity\Contact
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * Get Guid
     *
     * @return string
     */
    public function getGuid()
    {
        return $this->guid;
    }

    /**
     * @param int $guid
     * @return \Oktolab\Bundle\RentBundle\Entity\CostUnit
     */
    public function setGuid($guid)
    {
        $this->guid = $guid;
        return $this;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->contacts = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add contacts
     *
     * @param \Oktolab\Bundle\RentBundle\Entity\Contact $contacts
     * @return CostUnit
     */
    public function addContact(\Oktolab\Bundle\RentBundle\Entity\Contact $contacts)
    {
        $this->contacts[] = $contacts;

        return $this;
    }

    /**
     * Remove contacts
     *
     * @param \Oktolab\Bundle\RentBundle\Entity\Contact $contacts
     */
    public function removeContact(\Oktolab\Bundle\RentBundle\Entity\Contact $contacts)
    {
        $this->contacts->removeElement($contacts);
    }

    /**
     * Add events
     *
     * @param \Oktolab\Bundle\RentBundle\Entity\event $events
     * @return CostUnit
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
     * Set abbreviation
     *
     * @param string $abbreviation
     * @return CostUnit
     */
    public function setAbbreviation($abbreviation)
    {
        $this->abbreviation = $abbreviation;

        return $this;
    }

    /**
     * Get abbreviation
     *
     * @return string
     */
    public function getAbbreviation()
    {
        return $this->abbreviation;
    }
}