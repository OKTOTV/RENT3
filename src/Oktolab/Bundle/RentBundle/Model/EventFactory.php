<?php

namespace Oktolab\Bundle\RentBundle\Model;

use Oktolab\Bundle\RentBundle\Model\EventManager;
use Oktolab\Bundle\RentBundle\Entity\Event;

/**
 * Helps creating Events.
 */
class EventFactory
{
    /**
     * @var \Oktolab\Bundle\RentBundle\Model\EventManager
     */
    private $em = null;

    public function __construct(EventManager $em)
    {
        $this->em = $em;
    }

    /**
     * Create a new Event with $objects
     *
     * @param  array                                   $objects
     * @return \Oktolab\Bundle\RentBundle\Entity\Event
     */
    public function create(array $objects = null)
    {
        return new Event();
    }
}
