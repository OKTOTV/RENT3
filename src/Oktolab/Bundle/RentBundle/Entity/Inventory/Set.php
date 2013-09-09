<?php

namespace Oktolab\Bundle\RentBundle\Entity\Inventory;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Oktolab\Bundle\RentBundle\Model\UploadableInterface;

/**
 * Set
 *
 * @ORM\Table(name="item_set")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Set implements UploadableInterface
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
     * @Assert\NotBlank(message = "set.title.notblank" )
     * @Assert\Length(
     *     max = 255,
     *     maxMessage = "set.title.lengthMax"
     * )
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=500)
     *
     * @Assert\NotBlank(message = "set.description.notblank" )
     * @Assert\Length(
     *     max = 500,
     *     maxMessage = "set.description.lengthMax"
     *     )
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="barcode", type="string", length=20)
     *
     * @Assert\NotBlank(message = "set.barcode.notblank" )
     * @Assert\Length
     *      (
     *      max = 20,
     *      maxMessage = "set.barcode.lengthMax"
     *      )
     */
    private $barcode;

    /**
     * var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Item", mappedBy="set", cascade="detach")
     */
    private $items;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     *
     */
    private $created_at;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updated_at;

    /**
     * @ORM\ManyToMany(targetEntity="Attachment", cascade={"persist"} )
     * @ORM\JoinTable(
     *      name="set_attachment",
     *      joinColumns={@ORM\JoinColumn(name="set_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="attachment_id", referencedColumnName="id", unique=true)}
     * )
     *
     */
    private $attachments;

    /**
     * @ORM\OneToOne(targetEntity="Attachment", cascade={"persist", "remove"} )
     * @ORM\JoinColumn(
     *      name="picture_id", referencedColumnName="id"
     * )
     * @var Attachment
     */
    private $picture;

    /**
     * @ORM\ManyToOne(targetEntity="Place", inversedBy="sets")
     * @ORM\JoinColumn(name="place_id", referencedColumnName="id", nullable=false)
     *
     */
    private $place;

    public function __toString()
    {
        return $this->title;
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
     * Set title
     *
     * @param  string $title
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
     * @param  string $description
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
     * @param  \Oktolab\Bundle\RentBundle\Entity\Inventory\Item $items
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

    /**
     * Set barcode
     *
     * @param  string $barcode
     * @return Set
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
     * Set created_at
     * @ORM\PrePersist
     * @param  \DateTime $createdAt
     * @return Set
     */
    public function setCreatedAt()
    {
        $this->created_at = new \DateTime();

        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updated_at
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * @param  \DateTime $updatedAt
     * @return Set
     */
    public function setUpdatedAt()
    {
        $this->updated_at = new \DateTime();

        return $this;
    }

    /**
     * Get updated_at
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Get Subfoldername for Attachments
     * @return string
     */
    public function getUploadFolder()
    {
        return '/set';
    }

    /**
     * Add attachments
     *
     * @param \Oktolab\Bundle\RentBundle\Entity\Inventory\Attachment $attachments
     * @return Set
     */
    public function addAttachment(\Oktolab\Bundle\RentBundle\Entity\Inventory\Attachment $attachments)
    {
        $this->attachments[] = $attachments;

        return $this;
    }

    /**
     * Remove attachments
     *
     * @param \Oktolab\Bundle\RentBundle\Entity\Inventory\Attachment $attachments
     */
    public function removeAttachment(\Oktolab\Bundle\RentBundle\Entity\Inventory\Attachment $attachments)
    {
        $this->attachments->removeElement($attachments);
    }

    /**
     * Get attachments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * Set picture
     *
     * @param \Oktolab\Bundle\RentBundle\Entity\Inventory\Attachment $picture
     * @return Set
     */
    public function setPicture(\Oktolab\Bundle\RentBundle\Entity\Inventory\Attachment $picture = null)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     *
     * @return \Oktolab\Bundle\RentBundle\Entity\Inventory\Attachment
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Set place
     *
     * @param \Oktolab\Bundle\RentBundle\Entity\Inventory\Place $place
     * @return Set
     */
    public function setPlace(\Oktolab\Bundle\RentBundle\Entity\Inventory\Place $place = null)
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place
     *
     * @return \Oktolab\Bundle\RentBundle\Entity\Inventory\Place
     */
    public function getPlace()
    {
        return $this->place;
    }
}
