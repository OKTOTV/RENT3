<?php

namespace Oktolab\Bundle\RentBundle\Entity\Inventory;

use Doctrine\ORM\Mapping as ORM;
use Oktolab\Bundle\RentBundle\Model\UploadableInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Room
 *
 * @ORM\Table(name="room")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Room implements UploadableInterface
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
     *      max = 255,
     *      maxMessage = "Der Titel darf maximal 255 Zeichen lang sein"
     *      )
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=500)
     *
     * @Assert\NotBlank(message = "Du musst eine Beschreibung angeben" )
     * @Assert\Length
     *      (
     *      max = 500,
     *      maxMessage = "Die Beschreibung darf maximal 500 Zeichen lang sein"
     *      )
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="barcode", type="string", length=20)
     *
     * @Assert\NotBlank(message = "Du musst einen Barcode angeben" )
     * @Assert\Length
     *      (
     *      max = 20,
     *      maxMessage = "Der Barcode darf maximal 20 Zeichen lang sein"
     *      )
     */
    private $barcode;

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
     * @ORM\ManyToMany(targetEntity="Attachment", cascade={"persist"} )
     * @ORM\JoinTable(
     *      name="room_attachment",
     *      joinColumns={@ORM\JoinColumn(name="room_id", referencedColumnName="id")},
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
     * @return Room
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
     * @return Room
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
     * Set barcode
     *
     * @param string $barcode
     * @return Room
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
     * @param \DateTime $createdAt
     * @return Room
     */
    public function setCreatedAt()
    {
        $this->createdAt = new \DateTime();

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
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * @param \DateTime $updatedAt
     * @return Room
     */
    public function setUpdatedAt()
    {
        $this->updatedAt = new \DateTime();

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
     * Get Subfoldername for Attachments
     * @return string
     */
    public function getUploadFolder()
    {
        return '/room';
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->attachments = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add attachments
     *
     * @param \Oktolab\Bundle\RentBundle\Entity\Inventory\Attachment $attachments
     * @return Room
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
     * @return Room
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
}
