<?php

namespace Oktolab\Bundle\RentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Oktolab\Bundle\RentBundle\Entity\EventObject;

/**
 * Event
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Oktolab\Bundle\RentBundle\Entity\EventRepository")
 *
 * @Assert\GroupSequence({"Event", "Logic"})
 */
class Event
{
    const STATE_RENTED      = 0;
    const STATE_CANCELED    = 1;
    const STATE_PREPARED    = 2;
    const STATE_LENT        = 3;
    const STATE_RETURNED    = 4;

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
     * @Assert\Type("\DateTime")
     */
    private $begin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end", type="datetime")
     *
     * @Assert\NotBlank()
     * @Assert\Type("\DateTime")
     */
    private $end;

    /**
     * @var integer
     *
     * @ORM\Column(name="state", type="integer")
     */
    private $state;

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
    public function setEnd(\DateTime $end)
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
     * Get state
     *
     * @return integer
     */
    public function getState()
    {
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
        return self::STATE_RENTED == $this->getState();
    }

    /**
     * Begin must not after end.
     *
     * @Assert\True(message="Begin must not after end", groups={"Logic"})
     *
     * @return boolean
     */
    public function isEndAfterBegin()
    {
        return (null !== $this->begin && $this->end > $this->begin);
    }
}
