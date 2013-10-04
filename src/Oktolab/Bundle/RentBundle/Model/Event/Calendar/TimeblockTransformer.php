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

    public function __construct(TimeblockAggregator $aggregator, Cache $cache)
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
        $timeblocks = $this->aggregator->getTimeblocks($begin, $end);

        return $timeblocks;
    }

    /**
     * Seperates Timeblocks for easy use.
     *
     * @param \DateTime $begin Begin of interval
     * @param \DateTime $end   End of interval
     * @param int       $max   Maximum number of timeblocks to aggregate
     *
     * @return array
     */
    public function getSeparatedTimeblocks(\DateTime $begin = null, \DateTime $end = null, $max = 30)
    {
        $this->guardTimeblockAggregation($begin, $end);

        $timeblocks = array();
        foreach ($this->aggregator->getTimeblocks($begin, $end) as $timeblock) {
            $intervalDate = $begin;     // Start iteration by $begin

            do {
                if (!$timeblock->isActiveOnDate($intervalDate)) {
                    $intervalDate->modify('+1 day');    // Increase by one day to avoid infinity loop
                    continue;                           // Skip, because Timeblock is not active on this date|weekday
                }

                $timeblocks[] = array(
                    'begin'     => $timeblock->getBegin(),
                    'end'       => $timeblock->getEnd(),
                    'date'      => clone $intervalDate,
                    'timeblock' => $timeblock,
                );

                $intervalDate->modify('+1 day');
            } while (count($timeblocks) <= $max &&                  // Count of Timeblocks smaller than $max
                $intervalDate <= $timeblock->getIntervalEnd() &&    // Current Date smaller than Timeblock::IntervalEnd
                $intervalDate <= $end                               // Current Date smaller than overall $end
            );
        }

        return $timeblocks;
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
                throw new \LogicException('End date must be greater than Begin date');
            }
        }
    }
}
