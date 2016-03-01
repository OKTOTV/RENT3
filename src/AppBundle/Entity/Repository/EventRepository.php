<?php

namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Event;

class EventRepository extends EntityRepository
{

    public function findOutgoingEvents($start = null, $end = null)
    {
        if (!$start) {
            $start = new \DateTime();
        }
        if (!$end) {
            $start = new \DateTime('+8 hours');
        }
        // start of event must be in timeframe and state must be reserved
        return $this->getEntityManager()->createQuery('SELECT e FROM AppBundle:Event e WHERE e.startAt <= :end AND e.startAt >= :start AND e.state = :state')
            ->setParameter('end', $end)
            ->setParameter('start', $start)
            ->setParameter('state', Event::STATE_RESERVED)
            ->getResult();
    }

    public function findIncomingEvents($start = null, $end = null)
    {
        if (!$start) {
            $start = new \DateTime();
        }
        if (!$end) {
            $start = new \DateTime('+8 hours');
        }

        // end of event must be in timeframe and state must be lent
        return $this->getEntityManager()->createQuery('SELECT e FROM AppBundle:Event e WHERE e.endAt <= :end AND e.endAt >= :start AND e.state = :state')
            ->setParameter('end', $end)
            ->setParameter('start', $start)
            ->setParameter('state', Event::STATE_LENT)
            ->getResult();
    }

    public function findEventsInTime(\DateTime $start, \DateTime $end)
    {
        // end or start of event must be in the timeframe
        return $this->eventsInTimeQuery($start, $end)->getResult();
    }

    public function eventsInTimeQuery(\DateTime $start, \DateTime $end)
    {
        return $this->getEntityManager()->createQuery('SELECT e FROM AppBundle:Event e WHERE
            e.startAt >= :start AND e.startAt <= :end OR
            e.endAt >= :start AND e.endAt <= :end')
            ->setParameter('end', $end)
            ->setParameter('start', $start);
    }

    public function findEventsQuery()
    {
        return $this->getEntityManager()->createQuery('SELECT e FROM AppBundle:Event e');
    }

}
