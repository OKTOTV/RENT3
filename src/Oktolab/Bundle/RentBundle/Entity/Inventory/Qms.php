<?php

namespace Oktolab\Bundle\RentBundle\Entity\Inventory;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Qms
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Qms
{
    const STATE_OKAY        = 0;    // Everything is fine
    const STATE_FLAW        = 1;    // Somethings not correct (battery empty, storage not formatted)
    const STATE_DAMAGED     = 2;    // Got damaged, but funtional
    const STATE_DESTROYED   = 3;    // Is fucked up beyond all recognition (fubar)
    const STATE_LOST        = 4;    // Item is lost or stolen
    const STATE_MAINTENANCE = 5;    // Is in repair
    const STATE_DISCARDED   = 6;    // Won't get used anymore

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Item", inversedBy="qmss")
     * @ORM\JoinColumn(name="item_id", referencedColumnName="id")
     */
    private $item;

    /**
     * @var string
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="\Oktolab\Bundle\RentBundle\Entity\Event", inversedBy="qmss")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id")
     */
    private $event;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    public function __construct()
    {
        $this->setActive(true);
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
     * Set itemId
     *
     * @param \stdClass $itemId
     * @return Qms
     */
    public function setItemId($itemId)
    {
        $this->itemId = $itemId;

        return $this;
    }

    /**
     * Get itemId
     *
     * @return \stdClass
     */
    public function getItemId()
    {
        return $this->itemId;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Qms
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
     * Set eventId
     *
     * @param \stdClass $eventId
     * @return Qms
     */
    public function setEventId($eventId)
    {
        $this->eventId = $eventId;

        return $this;
    }

    /**
     * Get eventId
     *
     * @return \stdClass
     */
    public function getEventId()
    {
        return $this->eventId;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return Qms
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Qms
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Qms
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Qms
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set item
     *
     * @param \Oktolab\Bundle\RentBundle\Entity\Inventory\Item $item
     * @return Qms
     */
    public function setItem(\Oktolab\Bundle\RentBundle\Entity\Inventory\Item $item = null)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * Get item
     *
     * @return \Oktolab\Bundle\RentBundle\Entity\Inventory\Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Set event
     *
     * @param \Oktolab\Bundle\RentBundle\Entity\Inventory\Event $event
     * @return Qms
     */
    public function setEvent(\Oktolab\Bundle\RentBundle\Entity\Event $event = null)
    {
        $this->event = $event;

        return $this;
    }

    public function getStatusString()
    {
        switch ($this->getStatus()) {
            case 0:
                return 'qms.okay';
            case 1:
                return 'qms.flaw';
            case 2:
                return 'qms.damaged';
            case 3:
                return 'qms.destroyed';
            case 4:
                return 'qms.lost';
            case 5:
                return 'qms.maintenance';
            case 6:
                return 'qms.discarded';
            default:
                return 'unknown';
        }
    }

    /**
     * Get event
     *
     * @return \Oktolab\Bundle\RentBundle\Entity\Inventory\Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @Assert\True(message="qms.descriptionNeeded")
     */
    public function isDescriptionValid()
    {
        //if an item is okay, there is no need for a description
        if ($this->getStatus() == Qms::STATE_OKAY) {
            return true;
        } else {
            return $this->getDescription() != "";
        }
    }

    /**
     * @ORM\PrePersist
     */
    public function setLifecycleCreatedAt()
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * @ORM\PreUpdate
     */
    public function setLifecycleUpdatedAt()
    {
        $this->setUpdatedAt(new \DateTime());
    }
}