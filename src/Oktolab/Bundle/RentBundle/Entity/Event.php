<?php

namespace Oktolab\Bundle\RentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Oktolab\Bundle\RentBundle\Entity\EventObject;
use Oktolab\Bundle\RentBundle\Model\Validator as OktolabAssert;

/**
 * Event Entity.
 *
 * @see http://intern.okto.tv/x/MQOm
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Oktolab\Bundle\RentBundle\Entity\EventRepository")
 * @OktolabAssert\AvailabilityConstrain()
 */
class Event
{

    const STATE_PREPARED  = 0;    // The Event is only prepared. All is open. The Event can be deleted.
    const STATE_RESERVED  = 1;    // The Event is reserved. The Event is "fixed" and can only be canceled.
    const STATE_LENT      = 2;    // The Event is confirmed and EventObjects are lent.
    const STATE_DELIVERED = 3;    // The EventObjects are delivered and are back again.
    const STATE_CHECKED   = 4;    // Each EventObjects is being checked against Checklists.
    const STATE_COMPLETED = 5;    // The Event is finished and completed.
    const STATE_CANCELED  = 6;    // The Event was canceled.
    const STATE_DEFERRED  = 7;    // On or more EventObjects are deferred.

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
     *
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=500, nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="begin", type="datetime")
     *
     * @Assert\NotBlank()
     * @Assert\DateTime()
     */
    private $begin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end", type="datetime")
     *
     * @Assert\NotBlank()
     * @Assert\DateTime()
     */
    private $end;

    /**
     * @var integer
     *
     * @ORM\Column(name="state", type="integer")
     */
    private $state;

    /**
     * @ORM\OneToMany(targetEntity="EventObject", mappedBy="event")
     */
    private $objects;

    /**
     * @Assert\NotBlank()
     *
     * @ORM\ManyToOne(targetEntity="CostUnit", inversedBy="events")
     * @ORM\JoinColumn(name="costunit_id", referencedColumnName="id")
     **/
    private $costunit;

    /**
     * @Assert\NotBlank()
     *
     * @ORM\ManyToOne(targetEntity="Contact", inversedBy="events")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="id")
     **/
    private $contact;

    /**
     *
     * @ORM\Column(name="barcode", type="string", length=20, nullable=true)
     *
     */
    private $barcode;

    /**
     * @ORM\ManyToOne(targetEntity="EventType")
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity="\Oktolab\Bundle\RentBundle\Entity\Inventory\Qms", mappedBy="event")
     */
    private $qmss;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->objects = new ArrayCollection();
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
     * @param  string $name
     * @return Event
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
     * Set Begin Date
     *
     * @param  \DateTime $begin
     * @return Event
     */
    public function setBegin($begin)
    {
        $this->begin = $begin;

        return $this;
    }

    /**
     * Get Begin Date
     *
     * @return \DateTime
     */
    public function getBegin()
    {
        return $this->begin;
    }

    /**
     * Set end
     *
     * @param  \DateTime $end
     * @return Event
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Get end
     *
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Add objects
     *
     * @param  \Oktolab\Bundle\RentBundle\Entity\EventObject $objects
     * @return Event
     */
    public function addObject(EventObject $objects)
    {
        $this->objects[] = $objects;

        return $this;
    }

    /**
     * Remove objects
     *
     * @param \Oktolab\Bundle\RentBundle\Entity\EventObject $objects
     */
    public function removeObject(EventObject $objects)
    {
        $this->objects->removeElement($objects);
    }

    /**
     * Get objects
     *
     * @Assert\Count(min="1", groups={"Rent"})
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getObjects()
    {
        return $this->objects;
    }

    /**
     * Set state
     *
     * @param  integer $state
     * @return Event
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get State. With $asString=true it returns State as string.
     *
     * @param boolean $asString
     *
     * @return integer|string
     */
    public function getState($asString = false)
    {
        if ($asString) {
            $states = array(
                0 => 'prepared',
                1 => 'reserved',
                2 => 'lent',
                3 => 'delivered',
                4 => 'checked',
                5 => 'completed',
                6 => 'canceled',
                7 => 'deferred',
            );

            return strtoupper($states[$this->state]);
        }

        return $this->state;
    }

    /**
     * Set description
     *
     * @param  string $description
     * @return Event
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Returns true if Event has STATE_RENTED
     *
     * @return boolean
     */
    public function isRented()
    {
        return self::STATE_LENT == $this->getState();
    }

    /**
     * Begin must not be after end.
     *
     * @Assert\True(message="event.begin_after_end", groups={"Logic"})
     *
     * @return boolean
     */
    public function isEndAfterBegin()
    {
        return (null !== $this->begin && null !== $this->end && $this->end > $this->begin);
    }

    /**
     * Set costunit
     *
     * @param \Oktolab\Bundle\RentBundle\Entity\costunit $costunit
     * @return Event
     */
    public function setCostunit(\Oktolab\Bundle\RentBundle\Entity\costunit $costunit = null)
    {
        $this->costunit = $costunit;

        return $this;
    }

    /**
     * Get costunit
     *
     * @return \Oktolab\Bundle\RentBundle\Entity\costunit
     */
    public function getCostunit()
    {
        return $this->costunit;
    }

    /**
     * Set contact
     *
     * @param \Oktolab\Bundle\RentBundle\Entity\contact $contact
     * @return Event
     */
    public function setContact(\Oktolab\Bundle\RentBundle\Entity\contact $contact = null)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get contact
     *
     * @return \Oktolab\Bundle\RentBundle\Entity\contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set barcode
     *
     * @param string $barcode
     * @return Event
     */
    public function setBarcode($barcode)
    {
        $this->barcode = $barcode;

        return $this;
    }

    /**
     * Get barcode
     *
     * @return string
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * Set type
     *
     * @param \Oktolab\Bundle\RentBundle\Entity\EventType $type
     * @return Event
     */
    public function setType(\Oktolab\Bundle\RentBundle\Entity\EventType $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \Oktolab\Bundle\RentBundle\Entity\EventType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Add qmss
     *
     * @param \Oktolab\Bundle\RentBundle\Entity\Qms $qmss
     * @return Event
     */
    public function addQms(\Oktolab\Bundle\RentBundle\Entity\Inventory\Qms $qmss)
    {
        $this->qmss[] = $qmss;

        return $this;
    }

    /**
     * Remove qmss
     *
     * @param \Oktolab\Bundle\RentBundle\Entity\Qms $qmss
     */
    public function removeQms(\Oktolab\Bundle\RentBundle\Entity\Inventory\Qms $qmss)
    {
        $this->qmss->removeElement($qmss);
    }

    /**
     * Get qmss
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getQmss()
    {
        return $this->qmss;
    }
}