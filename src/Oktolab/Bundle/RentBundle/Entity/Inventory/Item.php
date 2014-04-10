<?php

namespace Oktolab\Bundle\RentBundle\Entity\Inventory;

use Doctrine\ORM\Mapping as ORM;
use Oktolab\Bundle\RentBundle\Model\UploadableInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Oktolab\Bundle\RentBundle\Model\RentableInterface;

/**
 * Item
 *
 * @ORM\Table(name="item")
 * @ORM\Entity(repositoryClass="Oktolab\Bundle\RentBundle\Entity\Repository\ItemRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Item implements RentableInterface, UploadableInterface
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
     * @Assert\NotBlank(message = "item.title.notblank" )
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "item.title.lengthMax"
     *      )
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=500)
     *
     * @Assert\NotBlank(message = "item.description.notblank" )
     * @Assert\Length
     *      (
     *      max = 500,
     *      maxMessage = "item.description.lengthMax"
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
     *      maxMessage = "item.barcode.lengthMax"
     *      )
     */
    private $barcode;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="buy_date", type="date", nullable=true)
     */
    private $buyDate;

    /**
     * @var string
     *
     * @ORM\Column(name="serial_number", type="string", length=255, nullable=true)
     */
    private $serialNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="vendor", type="string", length=100, nullable=true)
     */
    private $vendor;

    /**
     * @var string
     *
     * @ORM\Column(name="model_number", type="string", length=100, nullable=true)
     */
    private $modelNumber;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="warranty_date", type="date", nullable=true)
     */
    private $warrantyDate;

    /**
     * @var integer
     *
     *
     * @ORM\ManyToOne(targetEntity="Set", inversedBy="items")
     *
     */
    private $set;

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
     *      name="item_attachment",
     *      joinColumns={@ORM\JoinColumn(name="item_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="attachment_id", referencedColumnName="id", unique=true)}
     * )
     *
     */
    private $attachments;

    /**
     * @ORM\OneToOne(targetEntity="Attachment", cascade={"persist", "remove"} )
     * @ORM\JoinColumn(name="picture_id", referencedColumnName="id")
     *
     * @var type
     */
    private $picture;

    /**
     * @ORM\ManyToOne(targetEntity="Place", inversedBy="items")
     * @ORM\JoinColumn(name="place_id", referencedColumnName="id", nullable=false)
     *
     */
    private $place;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="items")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=true)
     *
     */
    private $category;

    /**
     * @ORM\Column(name="origin_value", type="decimal", scale=2, nullable=true)
     */
    private $origin_value;

    /**
     * @ORM\Column(name="daily_rent", type="decimal", scale=2, nullable=true)
     */
    private $daily_rent;

    /**
     * @ORM\Column(name="notice", type="string", length=255, nullable=true)
     */
    private $notice;

    /**
     * @ORM\OneToMany(targetEntity="Qms", mappedBy="item", cascade="persist")
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $qmss;

    /**
     * @ORM\Column(name="active", type="boolean", options={"default" = 1})
     * @var boolean
     */
    private $active;

    /**
     * @var type integer
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
     * Set Id
     *
     * @param int $id
     * @return Item
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set title
     *
     * @param  string $title
     * @return Item
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
     * @return Item
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
     * @param  string $barcode
     * @return Item
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
     * Set buyDate
     *
     * @param  \DateTime $buyDate
     * @return Item
     */
    public function setBuyDate($buyDate)
    {
        $this->buyDate = $buyDate;

        return $this;
    }

    /**
     * Get buyDate
     *
     * @return \DateTime
     */
    public function getBuyDate()
    {
        return $this->buyDate;
    }

    /**
     * Set serialNumber
     *
     * @param  string $serialNumber
     * @return Item
     */
    public function setSerialNumber($serialNumber)
    {
        $this->serialNumber = $serialNumber;

        return $this;
    }

    /**
     * Get serialNumber
     *
     * @return string
     */
    public function getSerialNumber()
    {
        return $this->serialNumber;
    }

    /**
     * Set vendor
     *
     * @param  string $vendor
     * @return Item
     */
    public function setVendor($vendor)
    {
        $this->vendor = $vendor;

        return $this;
    }

    /**
     * Get vendor
     *
     * @return string
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * Set modelNumber
     *
     * @param  string $modelNumber
     * @return Item
     */
    public function setModelNumber($modelNumber)
    {
        $this->modelNumber = $modelNumber;

        return $this;
    }

    /**
     * Get modelNumber
     *
     * @return string
     */
    public function getModelNumber()
    {
        return $this->modelNumber;
    }

    /**
     * Get warrantyDate
     *
     * @return \DateTime
     */
    public function getWarrantyDate()
    {
        return $this->warrantyDate;
    }

    /**
     * Set warrantyDate
     *
     * @param  \DateTime $warrantyDate
     * @return Item
     */
    public function setWarrantyDate($warrantyDate)
    {
        $this->warrantyDate = $warrantyDate;

        return $this;
    }

    /**
     * Return true if warranty of item is void
     *
     * @return boolean
     */
    public function isWarrantyVoid()
    {
        $now = new \DateTime();

        return ($this->warrantyDate <= $now);
    }

    /**
     * Set set
     *
     * @param  \Oktolab\Bundle\RentBundle\Entity\Inventory\Set $set
     * @return Item
     */
    public function setSet(\Oktolab\Bundle\RentBundle\Entity\Inventory\Set $set = null)
    {
        $this->set = $set;

        return $this;
    }

    /**
     * Get set
     *
     * @return \Oktolab\Bundle\RentBundle\Entity\Inventory\Set
     */
    public function getSet()
    {
        return $this->set;
    }

    /**
     * Set created_at
     * @ORM\PrePersist
     * @param  \DateTime $createdAt
     * @return Item
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
     * @return Item
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
        return '/item';
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->attachments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->active = true;
    }

    /**
     * Add attachment
     *
     * @param Attachment $attachment
     * @return Item
     */
    public function addAttachment(\Oktolab\Bundle\RentBundle\Entity\Inventory\Attachment $attachment)
    {
        $this->attachments[] = $attachment;
        return $this;
    }

    /**
     * Remove attachment
     *
     * @param Attachment $attachment
     */
    public function removeAttachment(\Oktolab\Bundle\RentBundle\Entity\Inventory\Attachment $attachment)
    {
        $this->attachments->removeElement($attachment);
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
     * @param Attachment $picture
     * @return Item
     */
    public function setPicture(\Oktolab\Bundle\RentBundle\Entity\Inventory\Attachment $picture = null)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     *
     * @return Attachment
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     *   {@inheritDoc}
     */
    public function getType()
    {
        return 'item';
    }

    /**
     *  {@inheritDoc}
     */
    public function getState()
    {
        return 0;
    }

    /**
     * Set place
     *
     * @param \Oktolab\Bundle\RentBundle\Entity\Inventory\Place $place
     * @return Item
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

    /**
     * Set category
     *
     * @param \Oktolab\Bundle\RentBundle\Entity\Inventory\Category $category
     * @return Item
     */
    public function setCategory(\Oktolab\Bundle\RentBundle\Entity\Inventory\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Oktolab\Bundle\RentBundle\Entity\Inventory\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set origin_value
     *
     * @param float $originValue
     * @return Item
     */
    public function setOriginValue($originValue)
    {
        $this->origin_value = $originValue;

        return $this;
    }

    /**
     * Get origin_value
     *
     * @return float
     */
    public function getOriginValue()
    {
        return $this->origin_value;
    }

    /**
     * Set daily_rent
     *
     * @param float $dailyRent
     * @return Item
     */
    public function setDailyRent($dailyRent)
    {
        $this->daily_rent = $dailyRent;

        return $this;
    }

    /**
     * Get daily_rent
     *
     * @return float
     */
    public function getDailyRent()
    {
        return $this->daily_rent;
    }

    /**
     * Set notice
     *
     * @param string $notice
     * @return Item
     */
    public function setNotice($notice)
    {
        $this->notice = $notice;

        return $this;
    }

    /**
     * Get notice
     *
     * @return string
     */
    public function getNotice()
    {
        return $this->notice;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return $this->getTitle().' '.$this->getBarcode();
    }

    /**
     * Add qmss
     *
     * @param \Oktolab\Bundle\RentBundle\Entity\Inventory\Qms $qmss
     * @return Item
     */
    public function addQms(\Oktolab\Bundle\RentBundle\Entity\Inventory\Qms $qmss)
    {
        $this->qmss[] = $qmss;

        return $this;
    }

    /**
     * Remove qmss
     *
     * @param \Oktolab\Bundle\RentBundle\Entity\Inventory\Qms $qmss
     */
    public function removeQms(\Oktolab\Bundle\RentBundle\Entity\Inventory\Qms $qmss)
    {
        $this->qmss->removeElement($qmss);
    }

    /**
     * Get qmss
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getQmss()
    {
        return $this->qmss;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return Item
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * returns true if item is in maintenance, false if otherwise
     * @return boolean
     */
    public function maintenance()
    {
        $in_maintenance = false;
        foreach ($this->getQmss() as $qms) {
            if ($qms->getActive() && $qms->getStatus() == Qms::STATE_MAINTENANCE) {
                $in_maintenance = true;
            }
        }
        return $in_maintenance;
    }

    /**
     * returns true if item is discarded, false if otherwise
     * @return boolean
     */
    public function discarded()
    {
        $discarded = false;
        foreach ($this->getQmss() as $qms) {
            if ($qms->getActive() && $qms->getStatus() == Qms::STATE_DISCARDED) {
                $discarded = true;
            }
        }
        return $discarded;
    }

    public function deferred()
    {
        $deferred = false;
        foreach ($this->getQmss() as $qms) {
            if ($qms->getActive() && $qms->getStatus() == Qms::STATE_DEFERRED) {
                $deferred = true;
            }
        }
        return $deferred;
    }

    /**
     * Set sortnumber
     *
     * @param integer $sortnumber
     * @return Item
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