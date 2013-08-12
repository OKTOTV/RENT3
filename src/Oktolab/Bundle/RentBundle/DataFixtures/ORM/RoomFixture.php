<?php

namespace Oktolab\Bundle\RentBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Room;

/**
 *  Loads a fixture Set.
 */
class RoomFixture extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $room = new Room();
        $room->setTitle('RoomTitle');
        $room->setDescription('RoomDescription');
        $room->setBarcode('ASDF');

        $manager->persist($room);
        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 3;
    }
}
