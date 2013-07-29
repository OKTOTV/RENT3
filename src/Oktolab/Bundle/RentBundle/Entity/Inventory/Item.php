<?php

namespace Oktolab\Bundle\RentBundle\Entity\Inventory;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Oktolab\Bundle\RentBundle\Model\RentableInterface;

/**
 * Item
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Item implements RentableInterface
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
     * @param \Oktolab\Bundle\RentBundle\Entity\Inventory\Set $set
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
     * @param \DateTime $createdAt
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
     * @param \DateTime $updatedAt
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
     *  {@inheritDoc}
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
}
