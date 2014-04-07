<?php

namespace Oktolab\Bundle\RentBundle\Model\Event\Calendar;

use Oktolab\Bundle\RentBundle\Model\Event\Calendar\TimeblockAggregator;
use Oktolab\Bundle\RentBundle\Entity\Timeblock;
use Doctrine\Common\Cache\Cache;
use Doctrine\ORM\EntityManager;

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
    const CACHE_ID_ROOM = 'oktolab.calendar_room_timeblock_transformer_cache';


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
    public function getTransformedTimeblocks(\DateTime $begin, \DateTime $end, $max = null, $type = 'inventory')
    {
        $this->guardTimeblockAggregation($begin, $end);

        // Look-Up cache
        $cache = $this->loadCache($type, $begin);
        if ($cache) {
            return $cache;
        }

        // Transform Timeblocks for use by Javascript
        $separatedTimeblocks = $this->getSeparatedTimeblocks($begin, $end, $max, $type);
        $timeblocks = array();
        $date = null;

        // @TODO: This is evil! Inject INTL/i18n service an do this right!
        $germanWeekdays = array(1 => 'Mo', 2 => 'Di', 3 => 'Mi', 4 => 'Do', 5 => 'Fr', 6 => 'Sa', 0 => 'So');

        foreach ($separatedTimeblocks as $timeblock) {
            if (null === $date || $date < $timeblock['date']) {
                $date = $timeblock['date'];
                $block = array(
                    'title'  => sprintf('%s, %s', $germanWeekdays[$date->format('w')], $date->format('d.m')),
                    'blocks' => array()
                );
            }

            $block['blocks'][] = array(
                'title' => sprintf(
                    '%s<sup>%s</sup> - %s<sup>%s</sup>', // @TODO: This is templating!
                    $timeblock['begin']->format('H'),
                    $timeblock['begin']->format('i'),
                    $timeblock['end']->format('H'),
                    $timeblock['end']->format('i')
                ),
                'begin' => $timeblock['begin']->format('c'),
                'end'   => $timeblock['end']->format('c'),
            );

            $timeblocks[$date->format('c')] = $block;
        }

        // Store in cache for one day
        $this->saveCache($type, $begin, $timeblocks);

        return $timeblocks;
    }

    /**
     * Seperates Timeblocks for easy use.
     * @TODO: remove max. Its not necessary
     * @param \DateTime $begin Begin of interval
     * @param \DateTime $end   End of interval
     * @param int       $max   Maximum number of timeblocks to aggregate
     *
     * @return array
     */
    public function getSeparatedTimeblocks(\DateTime $begin = null, \DateTime $end = null, $max = null, $type = 'Inventory')
    {
        $this->guardTimeblockAggregation($begin, $end);
        $aggregatedTimeblocks = $this->aggregator->getTimeblocks($begin, $end, $type);
        $max = (null === $max) ? count($aggregatedTimeblocks) * 30 : $max;

        $timeblocks = array();
        foreach ($aggregatedTimeblocks as $timeblock) {
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

    /**
     * Returns a JSON with all Timeblocks for given type in half hour steps. Used for Eventforms.
     * @param type $type
     * @return type
     */
    public function getBlockJsonForType($type = 'Inventory')
    {
        $timeblocks = $this->getSeparatedTimeblocks(new \DateTime(), new \DateTime('+90 days'), 200, $type);

        $json_timeblocks = array();
        foreach ($timeblocks as $timeblock) {
            while($timeblock['begin'] < $timeblock['end']) {
                $json_timeblocks[$timeblock['date']->format('Ymd')][$timeblock['begin']->format('H')] = array(
                    $timeblock['begin']->format('i'),
                    $timeblock['begin']->modify('+30min')->format('i')
                );
                $timeblock['begin'] = $timeblock['begin']->modify('+30min');
            }
            $json_timeblocks[$timeblock['date']->format('Ymd')][$timeblock['end']->format('H')] = array($timeblock['end']->format('i'));
        }

        return json_encode($json_timeblocks);
    }

    private function loadCache($type = 'inventory', $begin)
    {
        switch($type) {
        case 'inventory':
            if ($this->cache->contains(sprintf('%s::%s', self::CACHE_ID, $begin->format('z')))) {
                return $this->cache->fetch(sprintf('%s::%s', self::CACHE_ID, $begin->format('z')));
            }
            return false;
        case 'room':
            if ($this->cache->contains(sprintf('%s::%s', self::CACHE_ID_ROOM, $begin->format('z')))) {
                return $this->cache->fetch(sprintf('%s::%s', self::CACHE_ID_ROOM, $begin->format('z')));
            }
            return false;
        }
    }

    private function saveCache($type = 'inventory', $begin, $timeblocks)
    {
        switch($type) {
        case 'inventory':
            $this->cache->save(sprintf('%s::%s', self::CACHE_ID, $begin->format('z')), $timeblocks, 86400);
            break;
        case 'room':
            $this->cache->save(sprintf('%s::%s', self::CACHE_ID_ROOM, $begin->format('z')), $timeblocks, 86400);
            break;
        }
    }
}
