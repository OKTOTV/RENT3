<?php

namespace Oktolab\Bundle\RentBundle\Model\Event;

use Oktolab\Bundle\RentBundle\Model\Event\EventCalendarManager as EventCalendar;
use Oktolab\Bundle\RentBundle\Model\Event\Exception\RepositoryNotFoundException;

/**
 * InventoryCalendar for rendering Inventory Items and Sets
 */
class InventoryCalendar extends EventCalendar
{
    /**
     * Aggregates the Inventory for Calendar.
     *
     * @throws RepositoryNotFoundException
     * @return array
     */
    public function getInventory($repository)
    {
        if (null === $this->getRepository($repository)) {
            throw new RepositoryNotFoundException(sprintf('Repository "%s" not found.', $repository));
        }

        return $this->getRepository($repository)->find(array());
    }
}