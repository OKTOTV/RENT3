<?php

namespace Oktolab\Bundle\RentBundle\Model\SeriesEvent;

use Doctrine\ORM\EntityManager;
use Oktolab\Bundle\RentBundle\Entity\SeriesEvent;
use Oktolab\Bundle\RentBundle\Entity\Event;
use Oktolab\Bundle\RentBundle\Model\Event\EventManager;

use Symfony\Component\HttpFoundation\Request;
/**
 * @author rs
 * SeriesEventService prepares SeriesEvents with a response object and can save them
 *
 */
class SeriesEventService
{
    protected $em = null;

    public function __construct(EntityManager $manager)
    {
        $this->em = $manager;
    }

    /**
     * creates and returns a prepared seriesEvent made out of the request object
     * this still need binding by a form object to fill out everything else
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function prepareSeriesEvent(SeriesEvent $series_event, $type = 'Inventory')
    {
        return $this->createEvents($series_event, $type);
    }

    /**
     * saves given SeriesEvent to the database using the given EntityManager
     * @param \Oktolab\Bundle\RentBundle\Entity\SeriesEvent $series_event
     */
    public function save(SeriesEvent $series_event)
    {
        foreach ($series_event->getEvents() as $event) {
            $event->setState(Event::STATE_RESERVED);
            $event->setSeriesEvent($series_event);
            foreach($event->getObjects() as $eventObject) {
                $eventObject->setEvent($event);
            }
            $this->em->persist($event);
        }
        $this->em->persist($series_event);
        $this->em->flush();
        $this->em->clear();
    }

    /**
     * creates events and adds them to the series.
     * @param \Oktolab\Bundle\RentBundle\Entity\SeriesEvent $series_event
     * @param type $type
     * @return \Oktolab\Bundle\RentBundle\Entity\SeriesEvent
     */
    private function createEvents(SeriesEvent $series_event, $type = 'Inventory')
    {
        $series_event->setEvents();
        $type   = $this->em->getRepository('OktolabRentBundle:EventType')->findOneBy(array('name' => $type));
        $begin  = clone $series_event->getEventBegin();
        $end    = clone $series_event->getEventEnd();

        while ($begin < $series_event->getEnd()) {
            $event = new Event();
            $event->setContact($series_event->getContact());
            $event->setCostunit($series_event->getCostUnit());
            $event->setName($series_event->getCostUnit()->getName());
            $event->setState(Event::STATE_PREPARED);
            $event->setType($type);
            $event->setBegin(clone $begin);
            $event->setEnd(clone $end);
            $event->setSeriesEvent($series_event);

            foreach ($series_event->getObjects() as $object) {
                $event->addObject($object);
            }
            $series_event->addEvent($event);
            $begin->modify('+'.$series_event->getRepetition().' days');
            $end->modify('+'.$series_event->getRepetition().' days');
        }

        return $series_event;
    }
}
