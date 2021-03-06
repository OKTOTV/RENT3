<?php

namespace Oktolab\Bundle\RentBundle\Entity\Inventory;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Category
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Category
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
     * @Assert\NotBlank(message = "category.title.notblank" )
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "category.title.lengthMax"
     *      )
     */
    private $title;

    /**
     * @ORM\OneToMany(targetEntity="Item", mappedBy="category")
     * @ORM\OrderBy({"sortnumber" = "ASC"})
     */
    private $items;

    /**
     *
     * @var type int
     * @ORM\Column(name="sortnumber", type="integer", nullable=true)
     */
    private $sortnumber;

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
     * @return Category
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
     * @return Category
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
     * Set sortnumber
     *
     * @param integer $sortnumber
     * @return Category
     */
    public function setSortnumber($sortnumber)
    {
        $this->sortnumber = $sortnumber;

        return $this;
    }

    /**
     * Get sortnumber
     *
     * @return integer
     */
    public function getSortnumber()
    {
        return $this->sortnumber;
    }
}