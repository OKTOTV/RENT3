<?php

namespace Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Oktolab\Bundle\RentBundle\Entity\EventType;

/**
 * Loads a EventTypeFixture
 *
 * @author rs
 */
class EventTypeFixture extends AbstractFixture
{
    /**
     * Loads Inventory and Room EventType
     *
     * {@inheritDoc}
     */
    public function load(ObjectManager $om)
    {
        $eventTypeInventory = new EventType();
        $eventTypeInventory->setName('Inventory');
        $eventTypeRoom = new EventType();
        $eventTypeRoom->setName('Room');

        $om->persist($eventTypeInventory);
        $om->persist($eventTypeRoom);

        $om->flush();
    }
}
