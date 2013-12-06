<?php

namespace Oktolab\Bundle\RentBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Oktolab\Bundle\RentBundle\Entity\EventType;

/**
 * Description of EventTypeFixture
 *
 * @author rs
 */
class EventTypeFixture extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Loads Inventory and Room EventType
     *
     * {@inheritDoc}
     */
    public function load(ObjectManager $om)
    {
        $eventTypeInventory = new EventType();
        $eventTypeInventory->setName('inventory');

        $eventTypeRoom = new EventType();
        $eventTypeRoom->setName('room');

        $om->persist($eventTypeInventory);
        $om->persist($eventTypeRoom);

        $om->flush();
    }

    public function getOrder()
    {
        return 10;
    }
}
