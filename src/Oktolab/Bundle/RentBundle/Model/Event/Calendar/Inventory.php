<?php

namespace Oktolab\Bundle\RentBundle\Model\Event\Calendar;

use Oktolab\Bundle\RentBundle\Model\Event\EventCalendarManager as EventCalendar;
use Oktolab\Bundle\RentBundle\Model\Event\Exception\RepositoryNotFoundException;

/**
 * InventoryCalendar for rendering Inventory Items and Sets
 */
class Inventory extends EventCalendar
{
    /**
     * Aggregates the Inventory Objectives for Calendar.
     *
     * @throws RepositoryNotFoundException if repository is missing.
     * @return array
     */
    public function getObjectives($repository)
    {
        if (null === $this->getRepository($repository)) {
            throw new RepositoryNotFoundException(sprintf('Repository "%s" not found.', $repository));
        }

        return $this->getRepository($repository)->find(array());
    }

    /**
     * Aggregates Inventory\Item Categories
     *
     * @throws RepositoryNotFoundException if repository is missing.
     * @return array
     */
    public function getCategories()
    {
        if (null === $this->getRepository('Category')) {
            throw new RepositoryNotFoundException('Repository "Category" not found.');
        }

        return $this->getRepository('Category')->find(array());
    }

    /**
     * Builds the Inventory array.
     *
     * @return array
     */
    public function getInventory()
    {
        $inventory = array();
//        $inventory['Sets'] = $this->getObjectives('Set');

        $categories = $this->getCategories();
        foreach ($categories as $category) {
            $inventory[$category->getTitle()] = $category->getItems();
        }

        return $inventory;
    }
}