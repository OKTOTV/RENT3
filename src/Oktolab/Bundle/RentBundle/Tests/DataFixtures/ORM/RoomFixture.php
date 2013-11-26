<?php

namespace Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Room;

/**
 *  Loads a fixture Set.
 */
class RoomFixture extends AbstractFixture
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
}
