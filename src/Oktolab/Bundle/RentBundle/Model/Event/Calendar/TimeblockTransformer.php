<?php

namespace Oktolab\Bundle\RentBundle\Model\Event\Calendar;

use Oktolab\Bundle\RentBundle\Model\Event\Calendar\TimeblockAggregator;
use Oktolab\Bundle\RentBundle\Entity\Timeblock;
use Doctrine\Common\Cache\Cache;

/**
 * Description of TimeblockTransformer
 *
 * @author meh
 */
class TimeblockTransformer
{

    /**
     * Used for caching identifier
     */
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

    /**
     * Constructor.
     *
     * @param \Oktolab\Bundle\RentBundle\Model\Event\Calendar\TimeblockAggregator $aggregator
     * @param \Doctrine\Common\Cache\Cache $cache
     */
    public function __construct(TimeblockAggregator $aggregator, Cache $cache)
    {
        $this->aggregator = $aggregator;
        $this->cache      = $cache;
    }

    /**
     * Returns Timeblocks as Array for easy JSON access
     *
     * @param \DateTime $begin
     * @param \DateTime $end
     * @param int       $max
     *
     * @return array
     */
    public function getTransformedTimeblocks(\DateTime $begin, \DateTime $end, $max = 30)
    {
        $this->guardTimeblockAggregation($begin, $end);

        // Look-Up cache
        if ($this->cache->contains(sprintf('%s::%s', self::CACHE_ID, $begin->format('z')))) {
            return $this->cache->fetch(sprintf('%s::%s', self::CACHE_ID, $begin->format('z')));
        }

        // Transform Timeblocks for use by Javascript
        $timeblocks = $this->getSeparatedTimeblocks($begin, $end, $max);
        array_walk(
            $timeblocks,
            function (array &$timeblock) {
                $timeblock['begin'] = $timeblock['begin']->format('c');
                $timeblock['end']   = $timeblock['end']->format('c');
                $timeblock['date']  = $timeblock['date']->format('c');
                unset($timeblock['timeblock']);
            }
        );

        // Store in cache for one day
        $this->cache->save(sprintf('%s::%s', self::CACHE_ID, $begin->format('z')), $timeblocks, 86400);

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
            $intervalDate = clone $begin;     // Start iteration by $begin

            do {
                if (!$timeblock->isActiveOnDate($intervalDate)) {
                    $intervalDate->modify('+1 day');    // Increase by one day to avoid infinity loop
                    continue;                           // Skip, because Timeblock is not active on this date|weekday
                }

                $timeblocks[] = $this->rebuildTimeblock($intervalDate, $timeblock);
                $intervalDate->modify('+1 day');
            } while (count($timeblocks) <= ($max - 1) &&            // Count of Timeblocks smaller than $max
                $intervalDate <= $timeblock->getIntervalEnd() &&    // Current Date smaller than Timeblock::IntervalEnd
                $intervalDate <= $end                               // Current Date smaller than overall $end
            );
        }

        return $this->sortTimeblocks($timeblocks);
    }

    /**
     * Rebuilds a Timeblock-Element in new array-format
     *
     * @param \DateTime                                                 $date
     * @param \Oktolab\Bundle\RentBundle\Model\Event\Calendar\Timeblock $timeblock
     * @return array
     */
    public function rebuildTimeblock(\DateTime $date, Timeblock $timeblock)
    {
        $timeblockBegin = clone $timeblock->getBegin();
        $timeblockBegin->setDate($date->format('Y'), $date->format('m'), $date->format('d'));

        $timeblockEnd = clone $timeblock->getEnd();
        $timeblockEnd->setDate($date->format('Y'), $date->format('m'), $date->format('d'));

        return array(
            'begin'     => $timeblockBegin,
            'end'       => $timeblockEnd,
            'date'      => clone $date,
            'timeblock' => $timeblock,
        );
    }

    /**
     * Sorts a Timeblock-Array created by TimeblockTransformer::rebuildTimeblock
     *
     * @param array $timeblocks
     * @return array
     */
    public function sortTimeblocks(array $timeblocks = array())
    {
        if (0 === count($timeblocks)) {
            return $timeblocks;
        }

        usort(
            $timeblocks,
            function ($a, $b) {
                return ($a['begin'] < $b['begin']) ? -1 : +1;   // Sort Timeblocks by Begin-Date
            }
        );

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
