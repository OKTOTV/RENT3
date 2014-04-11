<?php

namespace Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

use Oktolab\Bundle\RentBundle\Entity\Timeblock;
use Oktolab\Bundle\RentBundle\Entity\EventType;

/**
 * Description of EventApiTimeblockFixture
 *
 * @author meh
 */
class TimeblockFixture extends AbstractFixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $om)
    {
        $eventType = new EventType();
        $eventType->setName('inventory');

        $eventTypeRoom = new EventType();
        $eventTypeRoom->setName('room');

        $timeblock = new Timeblock();
        $timeblock
            ->setIntervalBegin(new \DateTime('2012-01-01'))
            ->setIntervalEnd(new \DateTime('2015-01-01'))
            ->setBegin(new \DateTime('today 10:00'))
            ->setEnd(new \DateTime('today 22:00'))
            ->setIsActive(true)
            ->setEventType($eventType)
            ->setWeekdays(1016)    // All Weekdays
            ->setTitle('inventory timeblock');

        $timeblockRoom = new Timeblock();
        $timeblockRoom
            ->setIntervalBegin(new \DateTime('2012-01-01'))
            ->setIntervalEnd(new \DateTime('2015-01-01'))
            ->setBegin(new \DateTime('today 13:00'))
            ->setEnd(new \DateTime('today 22:00'))
            ->setIsActive(true)
            ->setEventType($eventTypeRoom)
            ->setWeekdays(1016)    // All Weekdays
            ->setTitle('room timeblock');

        $om->persist($timeblock);
        $om->persist($eventType);
        $om->persist($timeblockRoom);
        $om->persist($eventTypeRoom);
        $om->flush();
    }
}
