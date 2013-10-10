<?php

namespace Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

use Oktolab\Bundle\RentBundle\Entity\Timeblock;

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
        $timeblock = new Timeblock();
        $timeblock->setIntervalBegin(new \DateTime('2013-01-01 00:00'))
            ->setIntervalEnd(new \DateTime('2013-12-31 23:59'))
            ->setBegin(new \DateTime('2013-01-01 08:00'))
            ->setEnd(new \DateTime('2013-12-31 17:00'))
            ->setIsActive(true)
            ->setWeekdays(1016);    // All Weekdays

        $om->persist($timeblock);
        $om->flush();
    }
}
