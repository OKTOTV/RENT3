<?php

namespace Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Item;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Place;

/**
 *  Loads a fixture Item.
 */
class ItemFixture extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager, $number = 1)
    {
        $place = new Place();
        $place->setTitle('Test Place');
        $manager->persist($place);

        for ($index = 0; $index < $number; $index++) {
            $item = new Item();
            $item->setTitle('ItemTitle'.$index);
            $item->setDescription('ItemDescription'.$index);
            $item->setBarcode('ITEM'.$index);
            $item->setPlace($place);

            $manager->persist($item);
        }

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }
}
