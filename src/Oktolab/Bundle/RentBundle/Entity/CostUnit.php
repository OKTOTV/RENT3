<?php

namespace Oktolab\Bundle\RentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CostUnit
 *
 * @ORM\Table()
 * @ORM\Entity
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
     * @ORM\OneToOne(targetEntity="Contact")
     * @ORM\JoinColumn(name="mainContact_id", referencedColumnName="id")
     */
    private $mainContact;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToOne(targetEntity="Contact")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="id")
     */
    private $contacts;

    /**
     * @var string
     *
     * @ORM\Column(name="abbreviation", type="string", length=255)
     */
    private $abbreviation;


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
    public function setContacts(\Oktolab\Bundle\RentBundle\Entity\Contact $contacts = null)
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
     * Get Abbreviation
     *
     * @retun string
     */
    public function getAbbreviation()
    {
        return $this->abbreviation;
    }

    /**
     * @param string $abbreviation
     * @return \Oktolab\Bundle\RentBundle\Entity\CostUnit
     */
    public function setAbbreviation($abbreviation)
    {
        $this->abbreviation = $abbreviation;
        return $this;
    }
}