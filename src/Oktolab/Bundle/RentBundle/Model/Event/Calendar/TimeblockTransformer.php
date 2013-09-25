<?php

namespace Oktolab\Bundle\RentBundle\Model\Event\Calendar;

/**
 * Description of TimeblockTransformer
 *
 * @author meh
 */
class TimeblockTransformer
{

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
