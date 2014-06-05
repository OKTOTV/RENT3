<?php

namespace Oktolab\Bundle\RentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SeriesEvent
 *
 * validation groups:
 *   create: only the simple creation. who, when, what. no events needed.
 *   finalize: full series with all events and their event_objects.
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class SeriesEvent
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

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     * @Assert\Length(max = "255", groups={"create", "finalize"})
     */
    private $description;

    /**
     * When does the series end?
     * @var \DateTime
     * @Assert\NotBlank(groups={"create", "finalize"})
     * @ORM\Column(name="end", type="datetime")
     */
    private $end;

    /**
     * start of the first event in the series
     * @Assert\NotBlank(groups={"create"})
     */
    private $event_begin;

    /**
     * end of the first event in the series
     * @Assert\NotBlank(groups={"create"})
     */
    private $event_end;

    /**
     * all events for this series
     * @Assert\NotNull(groups={"finalize"})
     * @ORM\OneToMany(targetEntity="Event", mappedBy="seriesEvent", cascade={"persist"})
     */
    private $events;

    /**
     * What objects will be selected for the events?
     * @var type
     * @Assert\NotNull(message = "series_event.noObjects", groups={"create"})
     */
    private $objects;

    /**
     * What repetition schema is used?
     * @var type
     * @Assert\NotNull(groups={"create"})
     */
    private $repetition;

    /**
     * Who will get the events?
     * @var type
     * @Assert\NotNull(message = "series_event.noContact", groups={"create"})
     */
    private $contact;

    /**
     * Which costunt gets the events?
     * @var type
     * @Assert\NotNull(message = "series_event.noCostunit", groups={"create"})
     */
    private $costunit;

    public function __construct()
    {
        $this->events = new ArrayCollection();
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return SeriesEvent
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
     * @return SeriesEvent
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
     * Set description
     *
     * @param string $description
     * @return SeriesEvent
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
     * Set end
     *
     * @param \DateTime $end
     * @return SeriesEvent
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Get end
     *
     * @return \DateTime end of first event
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     *
     * @return \DateTime begin of first event
     */
    public function getEventBegin()
    {
        return $this->event_begin;
    }

    public function setEventBegin($event_begin)
    {
        $this->event_begin = $event_begin;

        return $this;
    }

    public function getEventEnd()
    {
        return $this->event_end;
    }

    public function setEventEnd($event_end)
    {
        $this->event_end = $event_end;

        return $this;
    }

    /**
     * Add events
     *
     * @param \Oktolab\Bundle\RentBundle\Entity\Event $events
     * @return SeriesEvent
     */
    public function addEvent(\Oktolab\Bundle\RentBundle\Entity\Event $events)
    {
        $this->events[] = $events;

        return $this;
    }

    /**
     * Remove events
     *
     * @param \Oktolab\Bundle\RentBundle\Entity\Event $events
     */
    public function removeEvent(\Oktolab\Bundle\RentBundle\Entity\Event $events)
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

    /**
     * @Assert\True(message = "series_event.beginAfterEnd", groups = {"create"})
     * @return type
     */
    public function isEventBeginForEnd()
    {
        return $this->event_begin < $this->event_end;
    }

    /**
     * @Assert\True(message = "series_event.stupidtimerange", groups = {"create"})
     * @return boolean
     */
    public function isSeriesTimerangeOkay()
    {
        if (!$this->getEventEnd() || !$this->getEventBegin()) {
            return false;
        }
        //maximum amount of seconds for a single event in the repetition
        $repetitionrange = $this->getRepetition() * 24 * 3600;
        //amount of seconds for a single event
        $eventrange = $this->getEventEnd()->getTimestamp() - $this->getEventBegin()->getTimestamp();
        if ($eventrange > $repetitionrange) {
            // event would overlap with next event
            return false;
        }
    }

    public function getContact()
    {
        return $this->contact;
    }

    public function setContact($contact)
    {
        $this->contact = $contact;
        return $this;
    }

    public function getCostUnit()
    {
        return $this->costunit;
    }

    public function setCostUnit($costunit)
    {
        $this->costunit = $costunit;
        return $this;
    }

    public function getRepetition()
    {
        return $this->repetition;
    }

    public function setRepetition($repetition)
    {
        $this->repetition = $repetition;
        return $this;
    }

    public function getObjects()
    {
        return $this->objects;
    }

    public function addObject($object)
    {
        $this->objects[] = $object;

        return $this;
    }

    public function removeObject($object)
    {
        $this->objects->removeObject($object);
    }

    public function setEvents($events = null)
    {
        $this->events = $events;
    }
}