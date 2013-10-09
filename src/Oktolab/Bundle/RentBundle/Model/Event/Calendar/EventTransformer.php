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
        foreach ($aggregatedEvents as $event) {
            $events[] = $this->router->generate('OktolabRentBundle_Event_Edit', array('id' => $event->getId()));
        }

        var_dump($events); die();

        //$this->get('router')->generate('blog_show', array('slug' => 'my-blog-post'));
        // EventManager to transform EventObjects to real Objects
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
