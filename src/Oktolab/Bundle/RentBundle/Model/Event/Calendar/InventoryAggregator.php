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

        return $this->getRepository($repository)->findBy(array(), array('sortnumber' => 'asc'));
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
     * Aggregates Inventory\Item s withoud Category or Set
     * @param  string $repository name of the repository
     * @return array
     */
    public function getItemWithoutCategories($repository = 'Item', $setItems = false)
    {
        if (null === $this->getRepository($repository)) {
            throw new RepositoryNotFoundException(sprintf('Repository "%s" not found.', $repository)); 
        }
        if ($setItems) {
            return $this->getRepository($repository)->findBy(array('category' => null), array('sortnumber' => 'asc'));
        }
        return $this->getRepository($repository)->findBy(array('category' => null, 'set' => null), array('sortnumber' => 'asc'));
    }

    /**
     * Builds the Inventory array.
     *
     * @param bool $sets if set to true, the return value will contain Sets
     *
     * @return array
     */
    public function getInventory($sets = false, $setItems = false)
    {
        $inventory = array();

        // Append Sets only if we need to.
        if ($sets) {
            $sets = $this->getObjectives('Set');
            $inventory['Sets'] = $sets;
        }

        $categories = $this->getCategories();
        foreach ($categories as $category) {
            $itemsInCat = array();
            foreach ($category->getItems() as $item) {
                if (!$item->getSet() || $setItems) {
                    $itemsInCat[] = $item;
                }
            }
            if (count($itemsInCat) != 0) {
                $inventory[$category->getTitle()] = $itemsInCat;
            }
        }
        $itemsWithoutCat = $this->getItemWithoutCategories('Item', $setItems);
        $inventory['Kategorielos'] = $itemsWithoutCat;

        return $inventory;
    }
}
