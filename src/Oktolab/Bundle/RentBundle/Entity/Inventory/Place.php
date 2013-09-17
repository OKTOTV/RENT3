<?php

namespace Oktolab\Bundle\RentBundle\Entity\Inventory;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Place
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Place
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
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\NotBlank(message = "place.title.notblank" )
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "place.title.lengthMax"
     *      )
     */
    private $title;

    /**
     * @ORM\OneToMany(targetEntity="Item", mappedBy="place")
     *
     */
    private $items;

    /**
     * @ORM\OneToMany(targetEntity="Set", mappedBy="place")
     */
    private $sets;

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
     * Set title
     *
     * @param string $title
     * @return Place
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add items
     *
     * @param \Oktolab\Bundle\RentBundle\Entity\Inventory\Item $items
     * @return Place
     */
    public function addItem(\Oktolab\Bundle\RentBundle\Entity\Inventory\Item $items)
    {
        $this->items[] = $items;

        return $this;
    }

    /**
     * Remove items
     *
     * @param \Oktolab\Bundle\RentBundle\Entity\Inventory\Item $items
     */
    public function removeItem(\Oktolab\Bundle\RentBundle\Entity\Inventory\Item $items)
    {
        $this->items->removeElement($items);
    }

    /**
     * Get items
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Add sets
     *
     * @param \Oktolab\Bundle\RentBundle\Entity\Inventory\Set $sets
     * @return Place
     */
    public function addSet(\Oktolab\Bundle\RentBundle\Entity\Inventory\Set $sets)
    {
        $this->sets[] = $sets;

        return $this;
    }

    /**
     * Remove sets
     *
     * @param \Oktolab\Bundle\RentBundle\Entity\Inventory\Set $sets
     */
    public function removeSet(\Oktolab\Bundle\RentBundle\Entity\Inventory\Set $sets)
    {
        $this->sets->removeElement($sets);
    }

    /**
     * Get sets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSets()
    {
        return $this->sets;
    }
}
