<?php

namespace Oktolab\Bundle\RentBundle\Model\Event\Calendar;

use Oktolab\Bundle\RentBundle\Model\Event\Calendar\TimeblockAggregator;
use Doctrine\Common\Cache\Cache;

/**
 * Description of TimeblockTransformer
 *
 * @author meh
 */
class TimeblockTransformer
{

    const CACHE_ID = 'oktolab.calendar_timeblock_transformer';

    /**
     * Timeblock Aggregator.
     *
     * @var \Oktolab\Bundle\RentBundle\Model\Event\Calendar\TimeblockAggregator
     */
    protected $aggregator = null;

    /**
     * @var \Doctrine\Common\Cache\Cache;
     */
    protected $cache = null;

    public function construct(TimeblockAggregator $aggregator, Cache $cache)
    {
        $this->aggregator = $aggregator;
        $this->cache = $cache;
    }

    /**
     * Returns Timeblocks as Array for easy JSON access
     *
     * @param \DateTime $begin
     * @param \DateTime $end
     *
     * @return array
     */
    public function getTransformedTimeblocks(\DateTime $begin = null, \DateTime $end = null)
    {
        $this->guardTimeblockAggregation($begin, $end);
        // TODO: Aggregate Timeblocks from Repository.

        $timeblocks = array();
        return $timeblocks;
    }

    protected function getTimeblocks(\DateTime $begin, \DateTime $end)
    {
//        $this->getRepository()->
    }

    /**
     * Guards Timeblock Aggregation on invalid Begin/End Dates
     *
     * @param \DateTime $begin
     * @param \DateTime $end
     * @throws \LogicException
     */
    protected function guardTimeblockAggregation(\DateTime $begin = null, \DateTime $end = null)
    {
        if (null !== $begin && null !== $end) {
            if ($end < $begin) {
                throw new \LogicException('End date must be greater than Begin');
            }
        }
    }
}
