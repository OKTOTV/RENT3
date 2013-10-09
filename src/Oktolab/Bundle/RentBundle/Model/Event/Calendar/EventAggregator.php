<?php

namespace Oktolab\Bundle\RentBundle\Model\Event\Calendar;

use Oktolab\Bundle\RentBundle\Model\Event\Calendar\BaseAggregator;
use Oktolab\Bundle\RentBundle\Model\Event\Exception\RepositoryNotFoundException;

/**
 * Event Aggregator.
 */
class EventAggregator extends BaseAggregator
{
    const TYPE_INVENTORY = 'inventory';
    const TYPE_ROOM      = 'room';

    public function getActiveEvents(\DateTime $end, $type = 'inventory')
    {
        if (null === $this->getRepository('Event')) {
            throw new RepositoryNotFoundException('Repository "Event" not found.');
        }

        return $this->getRepository('Event')->findActiveUntilEnd($end);
    }
}
