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
class EventApiTimeblockFixture extends AbstractFixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $om)
    {
        $eventType = new EventType();
        $eventType->setName('Inventory');

        $timeblock = new Timeblock();
        $timeblock
            ->setIntervalBegin(new \DateTime('today 00:00'))
            ->setIntervalEnd(new \DateTime('+30days 23:59'))
            ->setBegin(new \DateTime('today 08:00'))
            ->setEnd(new \DateTime('today 17:00'))
            ->setIsActive(true)
            ->setEventType($eventType)
            ->setWeekdays(1016);    // All Weekdays

        $om->persist($timeblock);
        $om->persist($eventType);
        $om->flush();
    }
}
