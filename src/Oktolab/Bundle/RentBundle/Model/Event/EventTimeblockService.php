<?php

namespace Oktolab\Bundle\RentBundle\Model\Event;

use \Doctrine\ORM\EntityManager;
use Oktolab\Bundle\RentBundle\Entity\Event;
use Oktolab\Bundle\RentBundle\Entity\SeriesEvent;
use Oktolab\Bundle\RentBundle\Entity\EventType;

/**
 * @author rs
 * EventTimeblockService tells you if a given event begin or end is the allowed rent times.
 */
class EventTimeblockService
{
    const EVENT_BEGIN_OUTATIME     = 1;
    const EVENT_END_OUTATIME       = 2;
    const EVENT_BEGIN_END_OUTATIME = 3;
    const EVENT_IN_TIME            = 4;

    /**
     * @var Doctrime\ORM\EntityManager
     */
    private $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function EventInTimeStatus(Event $event)
    {
      if (!$event->getBegin() || !$event->getEnd()) {
        return $this::EVENT_BEGIN_END_OUTATIME;
      }

       $timeblocks = $this->getTimeblocksForType($event->getType());
       $begin = $this->isDateInTimeblockTime($event->getBegin(), $timeblocks);
       $end   = $this->isDateInTimeblockTime($event->getEnd(), $timeblocks);

       if (!$begin && !$end) {
           return $this::EVENT_BEGIN_END_OUTATIME;
       } else if (!$begin) {
           return $this::EVENT_BEGIN_OUTATIME;
       } else if (!$end) {
           return $this::EVENT_END_OUTATIME;
       }
       return $this::EVENT_IN_TIME;
    }

    private function getTimeblocksForType(EventType $eventType)
    {
       $timeblocks = $this->em->getRepository('OktolabRentBundle:Timeblock')->findBy(array('eventType' => $eventType));
       return $timeblocks;
    }

    private function isDateInTimeblockTime(\DateTime $date, $timeblocks)
    {
        foreach ($timeblocks as $timeblock) {
            if ($timeblock->isActiveOnTime($date)) {
                return true;
            }
        }
        return false;
    }
}
