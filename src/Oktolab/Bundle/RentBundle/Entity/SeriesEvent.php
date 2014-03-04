<?php

namespace Oktolab\Bundle\RentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * SeriesEvent
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
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var \DateTime
     * @Assert\NotBlank()
     * @ORM\Column(name="begin", type="datetime")
     */
    private $begin;

    /**
     * @var \DateTime
     * @Assert\NotBlank()
     * @ORM\Column(name="end", type="datetime")
     */
    private $end;

    /**
     * @ORM\OneToMany(targetEntity="Event", mappedBy="seriesEvent")
     */
    private $events;

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
     * Set begin
     *
     * @param \DateTime $begin
     * @return SeriesEvent
     */
    public function setBegin($begin)
    {
        $this->begin = $begin;

        return $this;
    }

    /**
     * Get begin
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
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
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

    public function isBeginForEnd()
    {
        return $this->begin < $this->end;
    }
}