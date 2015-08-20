<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Item
 * @ORM\Entity
 */
class Item
{

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="bought_at", type="datetime")
     */
    private $boughtAt;

    /**
     * Set boughtAt
     *
     * @param \DateTime $boughtAt
     * @return Item
     */
    public function setBoughtAt($boughtAt)
    {
        $this->boughtAt = $boughtAt;

        return $this;
    }

    /**
     * Get boughtAt
     *
     * @return \DateTime
     */
    public function getBoughtAt()
    {
        return $this->boughtAt;
    }
}
