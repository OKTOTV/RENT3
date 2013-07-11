<?php

namespace Oktolab\Bundle\RentBundle\Entity\Inventory;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Set
 *
 * @ORM\Table(name="Item_Set")
 * @ORM\Entity
 */
class Set
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
     *
     * @Assert\NotBlank(message = "Du musst einen Titel angeben" )
     * @Assert\Length(
     *     max = 255,
     *     maxMessage = "Der Titel darf maximal 255 Zeichen lang sein"
     * )
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=500)
     *
     * @Assert\NotBlank(message = "Du musst eine Beschreibung angeben" )
     * @Assert\Length(
     *     max = 500,
     *     maxMessage = "Die Beschreibung darf maximal 500 Zeichen lang sein"
     *     )
     */
    private $description;

    /**
     *
     *@ORM\OneToMany(targetEntity="Item", mappedBy="set")
     */
    private $items;


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
     * @return Set
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
     * Set description
     *
     * @param string $description
     * @return Set
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
     * @return Set
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
}
