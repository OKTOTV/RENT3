<?php

namespace Oktolab\Bundle\RentBundle\Entity\Inventory;

/**
 * Import
 */
class Import
{
    private $items;

    /**
     * Add Item
     *
     * @param Item $item
     * @return Import
     */
    public function addItem(\Oktolab\Bundle\RentBundle\Entity\Inventory\Item $item)
    {
        $this->items[] = $item;

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
