<?php

namespace Oktolab\Bundle\RentBundle\Model\Event\Calendar;

use Oktolab\Bundle\RentBundle\Model\Event\Calendar\EventAggregator;
use Oktolab\Bundle\RentBundle\Model\Event\EventManager;
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

    public function getFormattedActiveEvents(\DateTime $end, $type = 'inventory')
    {
        $this->guardActiveEvents($end);

        $aggregatedEvents = $this->aggregator->getActiveEvents($end, $type);
        $events = array();
        foreach ($aggregatedEvents as $aggregatedEvent) {
            $event = array(
                'id'            => $aggregatedEvent->getId(),
                'title'         => sprintf(
                    '%02d:<sup>%02d</sup> %s',
                    $aggregatedEvent->getBegin()->format('H'),
                    $aggregatedEvent->getBegin()->format('i'),
                    $aggregatedEvent->getName()
                ),
                'name'          => $aggregatedEvent->getName(),
                'begin'         => $aggregatedEvent->getBegin()->format('c'),
                'end'           => $aggregatedEvent->getEnd()->format('c'),
                'description'   => $aggregatedEvent->getDescription(),
                'state'         => $aggregatedEvent->getState(true),
                'objects'       => array(),
            );

            $event['uri'] = $this->router->generate(
                'OktolabRentBundle_Event_Edit',
                array('id' => $aggregatedEvent->getId())
            );

            $objects = $this->eventManager->convertEventObjectsToEntites($aggregatedEvent->getObjects());
            foreach ($objects as $object) {
                $event['objects'][] = array(
                    'uri'       => $this->router->generate('inventory_item_show', array('id' => $object->getId())),
                    'title'     => $object->getTitle(),
                    'object_id' => sprintf('%s:%s', $object->getType(), $object->getId()),
                );
            }

            $events[$aggregatedEvent->getId()] = $event;
        }

        return $events;
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
