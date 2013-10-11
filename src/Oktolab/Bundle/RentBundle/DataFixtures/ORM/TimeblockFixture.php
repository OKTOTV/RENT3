<?php

namespace Oktolab\Bundle\RentBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

use Oktolab\Bundle\RentBundle\Entity\Timeblock;

/**
 * Timeblock Fixtures
 */
class TimeblockFixture extends AbstractFixture
{
    /**
     * Loads two Timeblocks from 08:00 - 12:00 and 13:00 - 17:00.
     *
     * {@inheritDoc}
     */
    public function load(ObjectManager $om)
    {
        $timeblockA = new Timeblock();
        $timeblockA->setWeekdays(1016)   // all weekdays
                ->setIntervalBegin(new \DateTime('2013-01-01 00:00'))
                ->setIntervalEnd(new \DateTime('2020-12-31 23:59'))
                ->setBegin(new \DateTime('2013-01-01 08:00'))
                ->setEnd(new \DateTime('2013-01-01 12:00'))
                ->setTitle('timeblockA')
                ->setIsActive(true);

        $timeblockB = clone $timeblockA;
        $timeblockB->setBegin(new \DateTime('2013-01-01 13:00'))
                ->setEnd(new \DateTime('2013-01-01 17:00'))
                ->setTitle('timeblockB');

        $om->persist($timeblockA);
        $om->persist($timeblockB);

        $om->flush();
    }
}
