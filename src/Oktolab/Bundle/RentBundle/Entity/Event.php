<?php

namespace Oktolab\Bundle\RentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Oktolab\Bundle\RentBundle\Entity\EventObject;

/**
 * Event
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Oktolab\Bundle\RentBundle\Entity\EventRepository")
 */
class Event
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
     * @var \DateTime
     *
     * @ORM\Column(name="begin", type="datetime")
     */
    private $begin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end", type="datetime")
     */
    private $end;

    /**
     *
     * @ORM\OneToMany(targetEntity="EventObject", mappedBy="event")
     */
    private $objects;

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
     * @param string $name
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
     * @param \DateTime $begin
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
     * @param \DateTime $end
     * @return Event
     */
    public function setEnd(\DateTime $end)
    {
        if (null !== $this->begin && $end < $this->begin) {
            throw new \InvalidArgumentException('End must be greater than Begin time');
        }

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
     * @param \Oktolab\Bundle\RentBundle\Entity\EventObject $objects
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
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getObjects()
    {
        return $this->objects;
    }
}