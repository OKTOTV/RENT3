<?php

namespace Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Oktolab\Bundle\RentBundle\Entity\Event;
use Oktolab\Bundle\RentBundle\Entity\EventObject;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Item;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Place;
use Oktolab\Bundle\RentBundle\Entity\EventType;

/**
 *  Loads a fixture Events
 */
class EventManagerFixture extends AbstractFixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $place = new Place();
        $place->setTitle('Test Place');
        $manager->persist($place);

        $eventType = new EventType();
        $eventType->setName('Inventory');
        $manager->persist($eventType);

        $item = new Item();
        $item->setTitle('eventItem')
            ->setDescription('bar')
            ->setBarcode('ASDF')
            ->setPlace($place);
        $manager->persist($item);
        $manager->flush();

        $eventObject = new EventObject();
        $eventObject->setType($item->getType())
            ->setObject($item->getId());

        $event1 = new Event();
        $event1->setName('2013-08-28 12:00 - 13:00')
            ->setBegin(new \DateTime('2013-08-28 12:00'))
            ->setEnd(new \DateTime('2013-08-28 13:00'))
            ->addObject($eventObject)
            ->setType($eventType)
            ->setState(Event::STATE_RESERVED);
        $eventObject->setEvent($event1);

        $manager->persist($eventObject);
        $manager->persist($event1);

        $eventObject2 = new EventObject();
        $eventObject2->setType($item->getType())
            ->setObject($item->getId());

        $event2 = new Event();
        $event2->setName('2013-08-28 15:00 - 16:00')
            ->setBegin(new \DateTime('2013-08-28 15:00'))
            ->setEnd(new \DateTime('2013-08-28 16:00'))
            ->addObject($eventObject2)
            ->setType($eventType)
            ->setState(Event::STATE_RESERVED);
        $eventObject2->setEvent($event2);

        $manager->persist($eventObject2);
        $manager->persist($event2);

        $manager->flush();
    }
}
