<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entiy\Rentable;

/**
 * ItemCollection
 * @ORM\Entity
 */
class ItemCollection extends Rentable
{

    /**
     * @var integer
     *
     * @ORM\Column(name="count", type="integer")
     */
    private $count;

    public function __construct() {
        parent::__construct();
        $this->count = 1;
    }

    /**
     * Set count
     *
     * @param integer $count
     * @return ItemCollection
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Get count
     *
     * @return integer
     */
    public function getCount()
    {
        return $this->count;
    }
}
