<?php

namespace Oktolab\Bundle\RentBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Oktolab\Bundle\RentBundle\Entity\Event;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Item;

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
        $event1 = new Event();
        $event1->setName('12:00 - 13:00')
            ->setBegin(new \DateTime('12:00'))
            ->setEnd(new \DateTime('13:00'))
            ->setItem('asdf')
        ;

        $event2 = new Event();
        $event2->setName('15:00 - 16:00')
            ->setBegin(new \DateTime('15:00'))
            ->setEnd(new \DateTime('16:00'))
            ->setItem('asdf')
        ;

        $item = new Item();
        $item->setTitle('eventItem')
            ->setDescription('bar')
            ->setBarcode('ASDF')
        ;

        $manager->persist($event1);
        $manager->persist($event2);
        $manager->persist($item);
        $manager->flush();
    }
}
