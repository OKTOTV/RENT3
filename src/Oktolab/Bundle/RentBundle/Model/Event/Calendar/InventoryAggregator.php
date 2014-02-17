<?php

namespace Oktolab\Bundle\RentBundle\Model\Event\Calendar;

use Oktolab\Bundle\RentBundle\Model\Event\Calendar\BaseAggregator;
use Oktolab\Bundle\RentBundle\Model\Event\Exception\RepositoryNotFoundException;

/**
 * InventoryCalendar for rendering Inventory Items and Sets
 */
class InventoryAggregator extends BaseAggregator
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

        return $this->getRepository($repository)->findAll();
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

        return $this->getRepository('Category')->findBy(array(), array('sortnumber' => 'asc'));
    }

    /**
     * Builds the Inventory array.
     *
     * @param bool $sets if set to true, the return value will contain Sets
     *
     * @return array
     */
    public function getInventory($sets = false)
    {
        $inventory = array();

        // Append Sets only if we need to.
        if ($sets) {
            $sets = $this->getObjectives('Set');
            $inventory['Sets'] = $sets;
        }

        $categories = $this->getCategories();
        foreach ($categories as $category) {
            $inventory[$category->getTitle()] = $category->getItems();
        }

        return $inventory;
    }
}
