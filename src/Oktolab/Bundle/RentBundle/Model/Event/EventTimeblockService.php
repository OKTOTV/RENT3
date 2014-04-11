<?php

namespace Oktolab\Bundle\RentBundle\Model\Event;

use \Doctrine\ORM\EntityManager;
use Oktolab\Bundle\RentBundle\Entity\Event;

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
       $timeblocks = $this->getTimeblocksForEvent($event);
       $begin = $this->isEventBeginInATimeblockTime($event, $timeblocks);
       $end   = $this->isEventEndInATimeblockTime($event, $timeblocks);

       if (!$begin && !$end) {
           return $this::EVENT_BEGIN_END_OUTATIME;
       } else if (!$begin) {
           return $this::EVENT_BEGIN_OUTATIME;
       } else if (!$end) {
           return $this::EVENT_END_OUTATIME;
       }
       return $this::EVENT_IN_TIME;
    }

    private function getTimeblocksForEvent(Event $event)
    {
       $timeblocks = $this->em->getRepository('OktolabRentBundle:Timeblock')->findBy(array('eventType' => $event->getType()));
       return $timeblocks;
    }

    private function isEventBeginInATimeblockTime(Event $event, $timeblocks)
    {
        foreach ($timeblocks as $timeblock) {
            if ($timeblock->isActiveOnTime($event->getBegin())) {
                return true;
            }
        }
        return false;
    }

    private function isEventEndInATimeblockTime(Event $event, $timeblocks)
    {
        foreach ($timeblocks as $timeblock) {
            if ($timeblock->isActiveOnTime($event->getEnd())) {
                return true;
            }
        }
        return false;
    }
}
