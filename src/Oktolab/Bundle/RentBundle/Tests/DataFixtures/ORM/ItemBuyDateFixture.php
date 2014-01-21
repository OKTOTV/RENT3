<?php

namespace Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Item;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Place;

/**
 *  Loads a fixture Item.
 */
class ItemBuyDateFixture extends AbstractFixture
{

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $place = new Place();
        $place->setTitle('Test Place');

        $itemA = new Item();
        $itemA->setTitle('With Buy Date')
                ->setDescription('An item with a buy date.')
                ->setBarcode('123456')
                ->setPlace($place)
                ->setBuyDate(new \DateTime('1991-10-27 12:00:00'));

        $itemB = new Item();
        $itemB->setTitle('Without Buy Date')
                ->setDescription('An item without a buy date.')
                ->setBarcode('QWERT')
                ->setPlace($place);

        $manager->persist($place);
        $manager->persist($itemA);
        $manager->persist($itemB);

        $manager->flush();
    }
}
