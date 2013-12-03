<?php

namespace Oktolab\Bundle\RentBundle\Model\Event\Calendar;

use Oktolab\Bundle\RentBundle\Model\Event\Calendar\BaseAggregator;
use Oktolab\Bundle\RentBundle\Model\Event\Exception\RepositoryNotFoundException;

/**
 * Timeblock Aggregator.
 */
class TimeblockAggregator extends BaseAggregator
{

    /**
     * Returns Timeblocks for given Time-range.
     *
     * @param \DateTime $begin
     * @param \DateTime $end
     *
     * @throws RepositoryNotFoundException If Repository not found.
     *
     * @return array
     */
    public function getTimeblocks(\DateTime $begin = null, \DateTime $end = null, $type = 'Inventory')
    {
        if (null === $this->getRepository('Timeblock')) {
            throw new RepositoryNotFoundException('Repository "Timeblock" not found.');
        }
        if (null === $this->getRepository('EventType')) {
            throw new RepositoryNotFoundException('Repository "EventType" not found.');
        }

        $eventType = $this->getRepository('EventType')->findOneBy(array('name' => $type));
        return $this->getRepository('Timeblock')->findBy(array('eventType' => $eventType->getId()));
    }
}
