<?php

namespace Oktolab\Bundle\RentBundle\Model\Event\Calendar;

use Oktolab\Bundle\RentBundle\Model\Event\Calendar\EventAggregator;
use Oktolab\Bundle\RentBundle\Model\Event\EventManager;
use Oktolab\Bundle\RentBundle\Entity\Event;
use Doctrine\Common\Cache\Cache;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as Router;

/**
 * Description of EventTransformer
 *
 * @author meh
 */
class EventTransformer
{

    /**
     * @var \Oktolab\Bundle\RentBundle\Model\Event\Calendar\EventAggregator
     */
    protected $aggregator = null;

    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    protected $cache = null;

    /**
     * @var \Oktolab\Bundle\RentBundle\Model\Event\EventManager
     */
    protected $eventManager = null;

    /**
     * @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface
     */
    protected $router = null;

    /**
     * Constructor.
     *
     * @param \Oktolab\Bundle\RentBundle\Model\Event\Calendar\EventAggregator   $aggregator
     * @param \Symfony\Component\Routing\Generator\UrlGeneratorInterface        $router
     * @param \Oktolab\Bundle\RentBundle\Model\Event\EventManager               $em
     * @param \Doctrine\Common\Cache\Cache                                      $cache
     */
    public function __construct(EventAggregator $aggregator, Router $router, EventManager $em, Cache $cache = null)
    {
        $this->aggregator   = $aggregator;
        $this->router       = $router;
        $this->eventManager = $em;
        $this->cache        = $cache;
    }

    /**
     * Returns Events as formatted array for easy JSON use.
     *
     * @param \DateTime $end
     * @param string    $type
     * @return array
     */
    public function getFormattedActiveEvents(\DateTime $end, $type = 'inventory')
    {
        $this->guardActiveEvents($end);

        $events = array();
        $aggregatedEvents = $this->aggregator->getActiveEvents($end, $type);

        foreach ($aggregatedEvents as $aggregatedEvent) {
            $events[$aggregatedEvent->getId()] = $this->transformAnEvent($aggregatedEvent);
        }

        return $events;
    }

    /**
     *
     * @param type $event
     * @return type
     */
    public function transformAnEvent(Event $event)
    {
        return array(
            'id'            => $event->getId(),
            'title'         => $event->getName(),
            'name'          => $event->getName(),
            'begin'         => $event->getBegin()->format('c'),
            'end'           => $event->getEnd()->format('c'),
            'description'   => $event->getDescription(),
            'state'         => $event->getState(true),
            'uri'           => $this->getARoute('OktolabRentBundle_Event_Edit', array('id' => $event->getId())),
            'objects'       => $this->transformEventObjects($event->getObjects()),
            'begin_view'    => $this->transformAnEventDate($event->getBegin()),
            'end_view'      => $this->transformAnEventDate($event->getEnd()),
        );
    }

    protected function transformAnEventDate(\DateTime $date)
    {
        // @TODO: This is evil! Inject INTL/i18n service an do this right!
        $germanWeekdays = array(1 => 'Mo', 2 => 'Di', 3 => 'Mi', 4 => 'Do', 5 => 'Fr', 6 => 'Sa', 0 => 'So');
        return sprintf('%s, %s', $germanWeekdays[$date->format('w')], $date->format('d.m. H:i'));
    }

    protected function getARoute($name, array $options = array())
    {
        return $this->router->generate($name, $options);
    }

    protected function transformEventObjects($objects)
    {
        $objects = $this->eventManager->convertEventObjectsToEntites($objects);
        $transformedObjects = array();

        foreach ($objects as $object) {
            $transformedObjects[] = array(
                'uri'       => $this->router->generate('inventory_item_show', array('id' => $object->getId())),
                'title'     => $object->getTitle(),
                'object_id' => sprintf('%s:%s', $object->getType(), $object->getId()),
            );
        }

        return $transformedObjects;
    }

    /**
     * Guards to only accept future dates.
     *
     * @param \DateTime $date
     *
     * @throws \LogicException
     */
    protected function guardActiveEvents(\DateTime $date)
    {
        if ($date < new \DateTime('now')) {
            throw new \LogicException(sprintf('Date must be greater than now, "%s" given.', $date->format('c')));
        }
    }
}
