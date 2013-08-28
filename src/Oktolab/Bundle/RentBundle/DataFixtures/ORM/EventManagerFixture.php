<?php

namespace Oktolab\Bundle\RentBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Oktolab\Bundle\RentBundle\Entity\Event;
use Oktolab\Bundle\RentBundle\Entity\EventObject;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Item;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Place;

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

        $manager->persist($eventObject);

        $event1 = new Event();
        $event1->setName('2013-08-28 12:00 - 13:00')
            ->setBegin(new \DateTime('2013-08-28 12:00'))
            ->setEnd(new \DateTime('2013-08-28 13:00'))
            ->addObject($eventObject)
            ->setState(Event::STATE_RENTED);

        $eventObject = new EventObject();
        $eventObject->setType($item->getType())
            ->setObject($item->getId());

        $manager->persist($eventObject);

        $event2 = new Event();
        $event2->setName('2013-08-28 15:00 - 16:00')
            ->setBegin(new \DateTime('2013-08-28 15:00'))
            ->setEnd(new \DateTime('2013-08-28 16:00'))
            ->addObject($eventObject)
            ->setState(Event::STATE_RENTED);

        $manager->persist($event1);
        $manager->persist($event2);

        $manager->flush();
    }
}
