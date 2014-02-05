<?php

namespace Oktolab\Bundle\RentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Oktolab\Bundle\RentBundle\Entity\Event;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * EventObject
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class EventObject
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
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="objects")
     */
    private $event;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=50)
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="object", type="integer")
     */
    private $object;

    /**
     * @var boolean
     * @Assert\True(groups={"Rent"})
     */
    private $scanned = false;

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
     * Set type
     *
     * @param  string      $type
     * @return EventObject
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set event
     *
     * @param  \Oktolab\Bundle\RentBundle\Entity\Event $event
     * @return EventObject
     */
    public function setEvent(Event $event = null)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return \Oktolab\Bundle\RentBundle\Entity\Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set object
     *
     * @param  integer     $object
     * @return EventObject
     */
    public function setObject($object)
    {
        $this->object = $object;

        return $this;
    }

    /**
     * Get object
     *
     * @return integer
     */
    public function getObject()
    {
        return $this->object;
    }

    public function isScanned()
    {
        return true == $this->scanned;
    }

    public function getScanned()
    {
        return $this->scanned;
    }

    public function setScanned($scanned)
    {
        $this->scanned = $scanned;

        return $this;
    }
}
